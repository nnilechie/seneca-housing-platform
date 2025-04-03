<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Debug: Confirm functions.php is loaded
error_log('Rental Application Form: functions.php loaded successfully at ' . date('Y-m-d H:i:s'));

// Debug: Confirm raf_handle_failed_email is available
if (function_exists('raf_handle_failed_email')) {
    error_log('raf_handle_failed_email function is defined.');
} else {
    error_log('raf_handle_failed_email function is NOT defined.');
}

// Remove any conflicting wp_mail_failed hooks
add_action('init', 'raf_remove_conflicting_mail_failed_hooks');
function raf_remove_conflicting_mail_failed_hooks() {
    global $wp_filter;
    if (isset($wp_filter['wp_mail_failed'])) {
        foreach ($wp_filter['wp_mail_failed']->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $callback_key => $callback) {
                if (isset($callback['function']) && $callback['function'] === 'handle_failed_email') {
                    remove_action('wp_mail_failed', 'handle_failed_email', $priority);
                    error_log('Removed conflicting wp_mail_failed hook for handle_failed_email at priority ' . $priority);
                }
            }
        }
    }
}

// Validate Seneca Email
add_action('wpforms_process_before', 'raf_validate_seneca_email', 10, 2);
function raf_validate_seneca_email($entry, $form_data) {
    error_log('raf_validate_seneca_email function called for form ID: ' . $form_data['id']);
    
    $applicant_type = '';
    $email = '';
    
    // Extract Applicant Type (Field ID 2) and Email (Field ID 5)
    if (isset($entry['fields'][2])) {
        $applicant_type = $entry['fields'][2];
    }
    if (isset($entry['fields'][5])) {
        $email = $entry['fields'][5];
    }
    
    error_log('Applicant Type: ' . $applicant_type . ', Email: ' . $email);
    
    if ($applicant_type === 'Seneca Student' && !preg_match('/@(mycampus\.seneca\.ca|myseneca\.ca)$/', $email)) {
        wpforms()->process->errors[$form_data['id']]['5'] = 'Email Address must be a @myseneca.ca or @mycampus.seneca.ca email for Seneca Students.';
        error_log('Seneca email validation failed for email: ' . $email);
    }
}

// Process Application with API Integrations
add_action('wpforms_process_complete', 'raf_process_rental_application', 10, 4);
function raf_process_rental_application($fields, $entry, $form_data, $entry_id) {
    error_log('wpforms_process_complete hook fired for form ID: ' . $form_data['id']);
    error_log('Fields array: ' . print_r($fields, true));
    
    // Require user to be logged in
    if (!is_user_logged_in()) {
        wpforms()->process->errors[$form_data['id']]['general'] = 'You must be logged in to submit an application.';
        error_log('User not logged in. Aborting application processing.');
        return;
    }

    $user_id = get_current_user_id();
    $property_id = '';
    $applicant_type = '';
    $name = '';
    $email = '';
    $phone = '';
    $uploaded_files = [];

    // Map WPForms fields to variables using field IDs (use integers for comparison)
    foreach ($fields as $field_id => $field) {
        if ($field_id === 1) { // Property ID
            $property_id = $field['value'];
        } elseif ($field_id === 2) { // Applicant Type
            $applicant_type = $field['value'];
        } elseif ($field_id === 4) { // Full Name
            $name = $field['value'];
        } elseif ($field_id === 5) { // Email Address
            $email = $field['value'];
        } elseif ($field_id === 6) { // Phone Number
            $phone = $field['value'];
        } elseif (in_array($field_id, [
            10, // Government-Issued ID
            11, // Proof of Seneca Enrollment
            16, // Sponsor Bank Statement
            17, // Sponsor Pledge Document
            19, // Canadian Bank Account Deposit Proof
            24, // Paystub (Canada)
            26, // Self-Employed Proof
            29, // Paystub (Abroad)
            30, // Residency Proof
            31  // Rental History
        ])) {
            if (!empty($field['value'])) {
                $uploaded_files[$field_id] = $field['value'];
            }
        }
    }

    // Debug: Log the values of each required field
    error_log('Property ID (Field 1): ' . ($property_id ?: 'empty'));
    error_log('Applicant Type (Field 2): ' . ($applicant_type ?: 'empty'));
    error_log('Full Name (Field 4): ' . ($name ?: 'empty'));
    error_log('Email Address (Field 5): ' . ($email ?: 'empty'));
    error_log('Phone Number (Field 6): ' . ($phone ?: 'empty'));

    if (!$property_id || !$applicant_type || !$name || !$email || !$phone) {
        error_log('Missing required basic data. Aborting application processing.');
        return;
    }

    // Step 1: Upload Files to S3 (Mocked for now)
    $s3_urls = [];
    foreach ($uploaded_files as $field_id => $file_path) {
        $s3_urls[$field_id] = raf_upload_to_s3($file_path);
        if (!$s3_urls[$field_id]) {
            error_log('Failed to upload file for field ID: ' . $field_id);
            $sent = wp_mail($email, 'Application Error', "Failed to upload your file for field ID $field_id. Please try again.");
            if ($sent) {
                error_log('File upload error email sent to: ' . $email);
            } else {
                error_log('Failed to send file upload error email to: ' . $email);
            }
            return;
        }
    }

    // Step 2: Verify Seneca Enrollment (for students)
    $seneca_verified = true; // Default for expats
    if ($applicant_type === 'Seneca Student') {
        $seneca_verified = raf_verify_seneca_enrollment($email, $s3_urls['11'] ?? null); // Field ID 11: Proof of Seneca Enrollment
        error_log('Seneca enrollment verification result: ' . ($seneca_verified ? 'true' : 'false'));
        if (!$seneca_verified) {
            $sent = wp_mail($email, 'Application Error', 'Failed to verify your Seneca enrollment. Please ensure your email and enrollment proof are correct.');
            if ($sent) {
                error_log('Seneca enrollment error email sent to: ' . $email);
            } else {
                error_log('Failed to send Seneca enrollment error email to: ' . $email);
            }
            return;
        }
    }

    // Step 3: Verify ID with Onfido
    $id_verified = false;
    if (isset($s3_urls['10']) && $s3_urls['10']) { // Field ID 10: Government-Issued ID
        $id_result = raf_verify_id_with_onfido($s3_urls['10']);
        $id_verified = ($id_result['status'] === 'legit');
        error_log('Onfido ID verification result: ' . ($id_verified ? 'true' : 'false'));
        if (!$id_verified) {
            $sent = wp_mail($email, 'Application Error', 'Failed to verify your ID. Please upload a valid government-issued ID.');
            if ($sent) {
                error_log('ID verification error email sent to: ' . $email);
            } else {
                error_log('Failed to send ID verification error email to: ' . $email);
            }
            return;
        }
    }

    // Verify sponsor ID if applicable
    $sponsor_id_verified = true; // Default to true if no sponsor
    $payment_option = '';
    foreach ($fields as $field_id => $field) {
        if ($field_id === 9) { // Field ID 9: Payment Assurance Option
            $payment_option = $field['value'];
            break;
        }
    }
    if ($applicant_type === 'Seneca Student' && $payment_option === 'Sponsor Support' && isset($s3_urls['17'])) { // Field ID 17: Sponsor Pledge Document
        $sponsor_id_result = raf_verify_id_with_onfido($s3_urls['17']);
        $sponsor_id_verified = ($sponsor_id_result['status'] === 'legit');
        error_log('Onfido Sponsor ID verification result: ' . ($sponsor_id_verified ? 'true' : 'false'));
        if (!$sponsor_id_verified) {
            $sent = wp_mail($email, 'Application Error', 'Failed to verify your sponsor’s ID. Please upload a valid document.');
            if ($sent) {
                error_log('Sponsor ID verification error email sent to: ' . $email);
            } else {
                error_log('Failed to send sponsor ID verification error email to: ' . $email);
            }
            return;
        }
    }

    // Step 4: Verify Employment/Credit
    $credit_score = null;
    $credit_auth = false;
    foreach ($fields as $field_id => $field) {
        if ($field_id === 32 || $field_id === 33) { // Field IDs 32 and 33: Credit Check Authorization (Student/Expat)
            $credit_auth = !empty($field['value']);
            break;
        }
    }
    if ($credit_auth) {
        $credit_score = raf_check_credit_score($name, $phone, $email);
        error_log('Credit score result: ' . ($credit_score ? $credit_score['score'] : 'none'));
        if ($credit_score && $credit_score['score'] < 650) {
            $sent = wp_mail($email, 'Application Review Needed', 'Your credit score does not meet the minimum requirement of 650.');
            if ($sent) {
                error_log('Credit score review email sent to: ' . $email);
            } else {
                error_log('Failed to send credit score review email to: ' . $email);
            }
        }
    }

    // Step 5: Scoring System
    $score = 0;
    $feedback = [];

    // ID Verification (30%)
    if ($id_verified) {
        $score += 30;
    } else {
        $feedback[] = "Invalid ID.";
    }

    // Payment Proof (40%)
    if ($applicant_type === 'Seneca Student') {
        if ($payment_option === 'Sponsor Support' && isset($s3_urls['16']) && isset($s3_urls['17']) && $sponsor_id_verified) { // Field IDs 16 and 17: Sponsor Bank Statement and Pledge Document
            $score += 40;
        } elseif ($payment_option === 'Canadian Bank Deposit' && isset($s3_urls['19'])) { // Field ID 19: Canadian Bank Account Deposit Proof
            $score += 40;
        } else {
            $feedback[] = "Missing payment assurance proof.";
        }
    } elseif ($applicant_type === 'Expat (US, UK, EU, Canada)') {
        if (isset($s3_urls['24']) || isset($s3_urls['26']) || isset($s3_urls['29'])) { // Field IDs 24, 26, 29: Paystub (Canada), Self-Employed Proof, Paystub (Abroad)
            $score += 40;
        } else {
            $feedback[] = "Missing employment proof.";
        }
    }

    // Credit/Rental History (30%)
    if ($credit_score && $credit_score['score'] >= 650) {
        $score += 20;
    }
    if (isset($s3_urls['31'])) { // Field ID 31: Rental History
        $score += 10; // Bonus for rental history
    }

    // Residency for Expats
    if ($applicant_type === 'Expat (US, UK, EU, Canada)' && !isset($s3_urls['30'])) { // Field ID 30: Residency Proof
        $feedback[] = "Missing residency proof.";
    }

    error_log('Application score: ' . $score);
    error_log('Application feedback: ' . implode(', ', $feedback));

    // Store application
    $application_id = wp_insert_post([
        'post_title' => "Application #$property_id by $name",
        'post_type' => 'rental_application',
        'post_status' => 'pending',
        'post_author' => $user_id, // Associate with the logged-in user
    ]);
    if (!$application_id) {
        error_log('Failed to create application post. WP Error: ' . print_r($wp_error, true));
        return;
    }
    error_log('Application post created with ID: ' . $application_id);

    update_post_meta($application_id, 'applicant_data', $fields);
    update_post_meta($application_id, 's3_urls', $s3_urls);
    update_post_meta($application_id, 'score', $score);
    update_post_meta($application_id, 'feedback', $feedback);
    update_post_meta($application_id, 'application_status', 'pending'); // Initial status

    // Store the score in a transient for the confirmation message
    set_transient('raf_application_score_' . $entry_id, $score, 60); // Expires in 60 seconds

    // Step 6: Notify Landlord
    $landlord_email = get_post_meta($property_id, 'fave_agent_email', true) ?: get_option('admin_email');
    // Debug: Log the landlord email
    error_log('Attempting to send landlord notification to: ' . $landlord_email);
    // Validate email address
    if (!is_email($landlord_email)) {
        error_log('Invalid landlord email address: ' . $landlord_email);
        $landlord_email = get_option('admin_email'); // Fallback to admin email again
        error_log('Falling back to admin email: ' . $landlord_email);
        if (!is_email($landlord_email)) {
            error_log('Invalid admin email address: ' . $landlord_email . '. Skipping landlord notification.');
            $landlord_email = null;
        }
    }
    if ($landlord_email) {
        $feedback_url = admin_url("post.php?post=$application_id&action=edit");
        $message = "New application for Property #$property_id\nName: $name\nEmail: $email\nScore: $score/100\nFeedback: " . implode(', ', $feedback) . "\nReview: $feedback_url";
        $sent = wp_mail($landlord_email, 'New Rental Application', $message);
        if ($sent) {
            error_log('Landlord notification sent to: ' . $landlord_email);
        } else {
            error_log('Failed to send landlord notification to: ' . $landlord_email);
        }
    }

    // Notify applicant if rejected
    if ($score < 70 || !empty($feedback)) {
        $applicant_message = "Your application scored $score/100. Issues: " . implode(', ', $feedback) . "\nResubmit with corrections: " . get_permalink(get_page_by_path('apply-for-rental')) . "?property_id=$property_id";
        $sent = wp_mail($email, 'Application Review Needed', $applicant_message);
        if ($sent) {
            error_log('Applicant rejection email sent to: ' . $email);
        } else {
            error_log('Failed to send applicant rejection email to: ' . $email);
        }
        update_post_meta($application_id, 'application_status', 'rejected');
        return;
    }

    // Step 7: Process Payment (if approved)
    $property_price = get_post_meta($property_id, 'fave_property_price', true);
    if (!$property_price) {
        error_log('Property price not found for property ID: ' . $property_id);
        $sent = wp_mail($email, 'Payment Error', "Property price not found. Please contact support.\nYour application score: $score/100");
        if ($sent) {
            error_log('Payment error email sent to: ' . $email);
        } else {
            error_log('Failed to send payment error email to: ' . $email);
        }
        update_post_meta($application_id, 'application_status', 'error');
        return;
    }

    $payment_url = raf_process_payment_with_stripe($email, $property_price, $application_id);
    if ($payment_url) {
        $sent = wp_mail($email, 'Complete Your Payment', "Your application scored $score/100.\nPlease complete your payment to proceed: $payment_url");
        if ($sent) {
            error_log('Payment email sent to: ' . $email . ' with URL: ' . $payment_url);
        } else {
            error_log('Failed to send payment email to: ' . $email);
        }
        update_post_meta($application_id, 'application_status', 'awaiting_payment');
    } else {
        $sent = wp_mail($email, 'Payment Error', "There was an issue processing your payment. Please contact support.\nYour application score: $score/100");
        if ($sent) {
            error_log('Payment error email sent to: ' . $email);
        } else {
            error_log('Failed to send payment error email to: ' . $email);
        }
        update_post_meta($application_id, 'application_status', 'error');
    }
}

// AWS S3 Upload (Mocked for now)
function raf_upload_to_s3($file_path) {
    return 'https://mock-s3-url.com/' . basename($file_path);
}

// Seneca Enrollment Verification
function raf_verify_seneca_enrollment($email, $enrollment_proof_url) {
    if (!preg_match('/@(mycampus\.seneca\.ca|myseneca\.ca)$/', $email)) {
        return false;
    }
    if ($enrollment_proof_url) {
        return true;
    }
    return false;
}

// Onfido ID Verification (Mocked for now)
function raf_verify_id_with_onfido($s3_url) {
    // Mocked implementation for now
    if (!$s3_url) {
        return ['status' => 'fake'];
    }
    // Simulate a successful verification
    return ['status' => 'legit'];
}

// Credit Check (Mocked for now)
function raf_check_credit_score($name, $phone, $email) {
    return ['score' => 700];
}

// Stripe Payment (Mocked for now)
function raf_process_payment_with_stripe($email, $amount, $application_id) {
    // Mocked implementation for now
    return 'https://mock-stripe-payment-url.com/session/' . $application_id;
}

// DocuSign Contract Generation (Mocked for now)
function raf_generate_contract_with_docusign($application_id) {
    // Mocked implementation for now
    return 'https://mock-docusign-url.com/contract/' . $application_id;
}

// Stripe Webhook
add_action('rest_api_init', function () {
    register_rest_route('rental/v1', '/stripe-webhook', [
        'methods' => 'POST',
        'callback' => 'raf_handle_stripe_webhook',
        'permission_callback' => '__return_true',
    ]);
});

function raf_handle_stripe_webhook($request) {
    $payload = $request->get_body();
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
    $endpoint_secret = 'YOUR_STRIPE_WEBHOOK_SECRET';

    // Mocked webhook handling for now
    $application_id = 'mock_application_id'; // Simulate extracting from payload
    update_post_meta($application_id, 'payment_status', 'completed');
    update_post_meta($application_id, 'application_status', 'payment_completed');

    $contract_url = raf_generate_contract_with_docusign($application_id);
    if ($contract_url) {
        $applicant_data = get_post_meta($application_id, 'applicant_data', true);
        $sent = wp_mail($applicant_data['5']['value'] ?? '', 'Sign Your Rental Contract', "Please sign your contract: $contract_url");
        if ($sent) {
            error_log('Contract email sent to: ' . ($applicant_data['5']['value'] ?? 'unknown'));
        } else {
            error_log('Failed to send contract email to: ' . ($applicant_data['5']['value'] ?? 'unknown'));
        }
        update_post_meta($application_id, 'application_status', 'awaiting_contract');
    } else {
        $sent = wp_mail($applicant_data['5']['value'] ?? '', 'Contract Error', 'There was an issue generating your contract. Please contact support.');
        if ($sent) {
            error_log('Contract error email sent to: ' . ($applicant_data['5']['value'] ?? 'unknown'));
        } else {
            error_log('Failed to send contract error email to: ' . ($applicant_data['5']['value'] ?? 'unknown'));
        }
        update_post_meta($application_id, 'application_status', 'error');
    }

    return new WP_REST_Response('Webhook received', 200);
}

// DocuSign Webhook
add_action('rest_api_init', function () {
    register_rest_route('rental/v1', '/docusign-webhook', [
        'methods' => 'POST',
        'callback' => 'raf_handle_docusign_webhook',
        'permission_callback' => '__return_true',
    ]);
});

function raf_handle_docusign_webhook($request) {
    $payload = $request->get_json_params();
    if (isset($payload['status']) && $payload['status'] === 'completed') {
        $application_id = $payload['metadata']['application_id'] ?? '';
        update_post_meta($application_id, 'contract_status', 'signed');
        update_post_meta($application_id, 'application_status', 'contract_signed');
        $applicant_data = get_post_meta($application_id, 'applicant_data', true);
        $sent = wp_mail($applicant_data['5']['value'] ?? '', 'Contract Signed', 'Your rental contract has been signed successfully.');
        if ($sent) {
            error_log('Contract signed email sent to: ' . ($applicant_data['5']['value'] ?? 'unknown'));
        } else {
            error_log('Failed to send contract signed email to: ' . ($applicant_data['5']['value'] ?? 'unknown'));
        }
    }
    return new WP_REST_Response('Webhook received', 200);
}

// Custom Post Type for Applications
add_action('init', 'raf_register_rental_application_cpt');
function raf_register_rental_application_cpt() {
    error_log('Registering rental_application custom post type');
    register_post_type('rental_application', [
        'labels' => ['name' => 'Rental Applications'],
        'public' => false,
        'show_ui' => true,
        'supports' => ['title', 'author'], // Add support for author
    ]);
}

// Handle wp_mail_failed to prevent fatal errors
add_action('wp_mail_failed', 'raf_handle_failed_email', 10, 1);
function raf_handle_failed_email($wp_error) {
    if (is_wp_error($wp_error)) {
        error_log('wp_mail failed: ' . $wp_error->get_error_message());
    }
}

// Add Meta Box for Application Details
add_action('add_meta_boxes', 'raf_add_application_meta_box');
function raf_add_application_meta_box() {
    add_meta_box(
        'raf_application_details',
        'Application Details',
        'raf_display_application_details',
        'rental_application',
        'normal',
        'high'
    );
}

function raf_display_application_details($post) {
    $applicant_data = get_post_meta($post->ID, 'applicant_data', true);
    $score = get_post_meta($post->ID, 'score', true);
    $feedback = get_post_meta($post->ID, 'feedback', true);
    $s3_urls = get_post_meta($post->ID, 's3_urls', true);
    $status = get_post_meta($post->ID, 'application_status', true);

    echo '<p><strong>Score:</strong> ' . esc_html($score) . '/100</p>';
    echo '<p><strong>Status:</strong> ' . esc_html($status) . '</p>';
    echo '<p><strong>Feedback:</strong> ' . esc_html(implode(', ', $feedback)) . '</p>';
    echo '<p><strong>Uploaded Files:</strong></p>';
    if ($s3_urls) {
        echo '<ul>';
        foreach ($s3_urls as $field_id => $url) {
            echo '<li>Field ID ' . esc_html($field_id) . ': <a href="' . esc_url($url) . '" target="_blank">' . esc_html($url) . '</a></li>';
        }
        echo '</ul>';
    }
    echo '<p><strong>Applicant Data:</strong></p>';
    echo '<pre>' . esc_html(print_r($applicant_data, true)) . '</pre>';
}

// Custom WPForms Smart Tag for Application Score
add_filter('wpforms_smart_tags', 'raf_register_application_score_smart_tag');
function raf_register_application_score_smart_tag($tags) {
    $tags['application_score'] = 'Application Score';
    return $tags;
}

add_filter('wpforms_smart_tag_process', 'raf_process_application_score_smart_tag', 10, 3);
function raf_process_application_score_smart_tag($content, $tag, $form_data) {
    if ($tag === 'application_score') {
        // Get the entry ID from the form submission
        $entry_id = isset($_POST['wpforms']['entry_id']) ? absint($_POST['wpforms']['entry_id']) : 0;
        if ($entry_id) {
            $score = get_transient('raf_application_score_' . $entry_id);
            if ($score !== false) {
                $content = str_replace('{application_score}', esc_html($score), $content);
                // Delete the transient after use
                delete_transient('raf_application_score_' . $entry_id);
            } else {
                $content = str_replace('{application_score}', 'N/A', $content);
            }
        } else {
            $content = str_replace('{application_score}', 'N/A', $content);
        }
    }
    return $content;
}

// Applicant Dashboard Shortcode (Modified for Houzez Integration)
function raf_render_applicant_dashboard() {
    error_log('raf_render_applicant_dashboard function called');

    if (!is_user_logged_in()) {
        error_log('User not logged in - returning login message');
        return '<div class="houzez-dashboard-message"><p>' . esc_html__('Please log in to view your applications.', 'houzez') . '</p></div>';
    }

    $user_id = get_current_user_id();
    error_log('Current user ID: ' . $user_id);

    $args = [
        'post_type' => 'rental_application',
        'post_status' => 'any',
        'author' => $user_id,
        'posts_per_page' => -1,
    ];

    $applications = new WP_Query($args);
    error_log('WP_Query args: ' . print_r($args, true));
    error_log('Found posts: ' . $applications->found_posts);

    if (!$applications->have_posts()) {
        error_log('No applications found for user ID: ' . $user_id);
        return '<div class="houzez-dashboard-message"><p>' . esc_html__('You have not submitted any applications yet.', 'houzez') . '</p></div>';
    }

    ob_start();
    ?>
    <div class="dashboard-content-area">
        <div class="dashboard-table-wrapper">
            <table class="dashboard-table table-lined responsive-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Application ID', 'houzez'); ?></th>
                        <th><?php esc_html_e('Property ID', 'houzez'); ?></th>
                        <th><?php esc_html_e('Score', 'houzez'); ?></th>
                        <th><?php esc_html_e('Status', 'houzez'); ?></th>
                        <th><?php esc_html_e('Feedback', 'houzez'); ?></th>
                        <th><?php esc_html_e('Date Submitted', 'houzez'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($applications->have_posts()) : $applications->the_post(); ?>
                        <?php
                        $application_id = get_the_ID();
                        $property_id = get_the_title();
                        $property_id = preg_match('/#(\d+)/', $property_id, $matches) ? $matches[1] : 'N/A';
                        $score = get_post_meta($application_id, 'score', true);
                        $status = get_post_meta($application_id, 'application_status', true);
                        $feedback = get_post_meta($application_id, 'feedback', true);
                        $date = get_the_date();
                        error_log('Processing application ID: ' . $application_id);
                        ?>
                        <tr>
                            <td data-label="<?php esc_html_e('Application ID', 'houzez'); ?>"><?php echo esc_html($application_id); ?></td>
                            <td data-label="<?php esc_html_e('Property ID', 'houzez'); ?>"><?php echo esc_html($property_id); ?></td>
                            <td data-label="<?php esc_html_e('Score', 'houzez'); ?>"><?php echo esc_html($score); ?>/100</td>
                            <td data-label="<?php esc_html_e('Status', 'houzez'); ?>"><?php echo esc_html($status); ?></td>
                            <td data-label="<?php esc_html_e('Feedback', 'houzez'); ?>"><?php echo esc_html(implode(', ', $feedback)); ?></td>
                            <td data-label="<?php esc_html_e('Date Submitted', 'houzez'); ?>"><?php echo esc_html($date); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    wp_reset_postdata();
    $output = ob_get_clean();
    error_log('Shortcode output: ' . $output);
    return $output;
}

// Explicitly register the shortcode
add_action('init', 'raf_register_applicant_dashboard_shortcode');
function raf_register_applicant_dashboard_shortcode() {
    add_shortcode('raf_applicant_dashboard', 'raf_render_applicant_dashboard');
    error_log('raf_applicant_dashboard shortcode registered');
}

// Function to get the Rental Applications page URL
function raf_get_rental_applications_page_url() {
    // Query the page directly
    $pages = get_pages([
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template/user_dashboard_rental_applications.php',
        'number' => 1,
    ]);

    if (!empty($pages)) {
        $url = get_permalink($pages[0]->ID);
        error_log('Found Rental Applications page URL: ' . $url);
        return $url;
    }

    error_log('Could not find Rental Applications page URL.');
    return '';
}

// Require Login Message for WPForms
add_action('wpforms_display_before_form', 'raf_require_login_message', 10, 2);
function raf_require_login_message($form_data, $form) {
    if ($form_data['id'] == 19036 && !is_user_logged_in()) {
        echo '<p>You must be logged in to submit an application. <a href="' . esc_url(wp_login_url(get_permalink())) . '">Log in</a> or <a href="' . esc_url(wp_registration_url()) . '">register</a>.</p>';
    }
}