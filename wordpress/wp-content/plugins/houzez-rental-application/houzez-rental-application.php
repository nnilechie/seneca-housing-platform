<?php
class HRA_Application {
    public function __construct() {
        // Original Houzez hook
        add_action('houzez_after_property_description', array($this, 'add_apply_now_button'));
        // Fallback to the_content for testing
        add_filter('the_content', array($this, 'add_apply_now_button_to_content'));
        add_shortcode('hra_application_form', array($this, 'render_application_form'));
        add_action('wp', array($this, 'handle_application_submission'));
        
        // Debug: Log when class is initialized
        error_log('HRA_Application class initialized');
    }

    // Add "Apply Now" button (Houzez hook)
    public function add_apply_now_button() {
        if (is_singular('property')) {
            $property_id = get_the_ID();
            $output = '<button class="hra-apply-now" data-property-id="' . esc_attr($property_id) . '">Apply Now</button>';
            $output .= do_shortcode('[hra_application_form property_id="' . $property_id . '"]');
            echo $output;
            error_log('add_apply_now_button fired for property ID: ' . $property_id);
        } else {
            error_log('add_apply_now_button: Not a property page');
        }
    }

    // Fallback: Add button to content
    public function add_apply_now_button_to_content($content) {
        if (is_singular('property')) {
            $property_id = get_the_ID();
            $button = '<button class="hra-apply-now" data-property-id="' . esc_attr($property_id) . '">Apply Now</button>';
            $form = do_shortcode('[hra_application_form property_id="' . $property_id . '"]');
            error_log('add_apply_now_button_to_content fired for property ID: ' . $property_id);
            return $content . $button . $form;
        }
        return $content;
    }

    // Render application form
    public function render_application_form($atts) {
        $atts = shortcode_atts(array('property_id' => 0), $atts);
        ob_start();
        ?>
        <form method="post" enctype="multipart/form-data" class="hra-application-form">
            <input type="hidden" name="property_id" value="<?php echo esc_attr($atts['property_id']); ?>">
            <h3>Rental Application</h3>
            <label>Name: <input type="text" name="applicant_name" required></label>
            <label>Email: <input type="email" name="applicant_email" required></label>
            <label>User Type:
                <select name="user_type" id="user-type">
                    <option value="employed">Employed Professional</option>
                    <option value="student">Student</option>
                    <option value="expat">Expat</option>
                </select>
            </label>
            <div id="conditional-fields"></div>
            <label>Upload Documents: <input type="file" name="documents[]" multiple></label>
            <input type="submit" name="hra_submit_application" value="Submit Application">
        </form>
        <?php
        error_log('Rendering application form for property ID: ' . $atts['property_id']);
        return ob_get_clean();
    }

    // Handle form submission
    public function handle_application_submission() {
        if (isset($_POST['hra_submit_application'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'hra_applications';

            $data = array(
                'property_id' => intval($_POST['property_id']),
                'applicant_id' => get_current_user_id(),
                'name' => sanitize_text_field($_POST['applicant_name']),
                'email' => sanitize_email($_POST['applicant_email']),
                'user_type' => sanitize_text_field($_POST['user_type']),
                'status' => 'Pending',
                'submission_date' => current_time('mysql')
            );

            $wpdb->insert($table_name, $data);
            $this->handle_file_uploads($wpdb->insert_id);
            $this->send_status_email($data['email'], 'Pending', $data['property_id']);
            error_log('Application submitted for property ID: ' . $data['property_id']);
        }
    }

    private function handle_file_uploads($application_id) {
        if (!empty($_FILES['documents']['name'][0])) {
            $upload_dir = wp_upload_dir();
            $target_dir = $upload_dir['basedir'] . '/hra-documents/' . $application_id . '/';
            wp_mkdir_p($target_dir);

            foreach ($_FILES['documents']['name'] as $key => $value) {
                $file_path = $target_dir . basename($_FILES['documents']['name'][$key]);
                move_uploaded_file($_FILES['documents']['tmp_name'][$key], $file_path);
            }
            error_log('Files uploaded for application ID: ' . $application_id);
        }
    }

    private function send_status_email($email, $status, $property_id) {
        $subject = "Application Status Update: Property #$property_id";
        $message = "Your application status is now: $status.";
        wp_mail($email, $subject, $message);
        error_log('Email sent to ' . $email . ' for property ID: ' . $property_id);
    }
}