<?php
// Ensure this script runs only once and in the correct environment
if (!defined('ABSPATH')) {
    exit('This script must be run within WordPress.');
}

// Check if WPForms is active
if (!class_exists('WPForms')) {
    exit('WPForms is not active. Please install and activate WPForms.');
}

// Function to create the rental application form
function create_rental_application_form() {
    // Check if the form already exists to avoid duplicates
    $existing_form = get_page_by_title('Rental Application', OBJECT, 'wpforms');
    if ($existing_form) {
        echo 'Form already exists with ID: ' . $existing_form->ID;
        return;
    }

    // Define the form structure as an array (WPForms JSON format)
    $form_data = [
        'field_id' => 20, // Start with a high enough field ID to avoid conflicts
        'fields' => [
            // Property ID (Hidden, pre-populated)
            [
                'id' => '1',
                'type' => 'text',
                'label' => 'Property ID',
                'name' => 'property-id',
                'size' => 'medium',
                'required' => '1',
                'css' => 'hidden-field',
                'default_value' => isset($_GET['property_id']) ? sanitize_text_field($_GET['property_id']) : '',
            ],
            // Applicant Type (Dropdown)
            [
                'id' => '2',
                'type' => 'select',
                'label' => 'Applicant Type',
                'name' => 'applicant-type',
                'choices' => [
                    ['label' => 'Seneca Student', 'value' => 'Seneca Student'],
                    ['label' => 'Expat (US, UK, EU, Canada)', 'value' => 'Expat (US, UK, EU, Canada)'],
                ],
                'required' => '1',
                'css' => 'applicant-type',
            ],
            // Full Name
            [
                'id' => '3',
                'type' => 'text',
                'label' => 'Full Name',
                'name' => 'your-name',
                'size' => 'medium',
                'required' => '1',
            ],
            // Email Address
            [
                'id' => '4',
                'type' => 'email',
                'label' => 'Email Address',
                'name' => 'your-email',
                'size' => 'medium',
                'required' => '1',
            ],
            // Phone Number
            [
                'id' => '5',
                'type' => 'phone',
                'label' => 'Phone Number',
                'name' => 'your-phone',
                'format' => 'international',
                'required' => '1',
            ],
            // Desired Move-In Date
            [
                'id' => '6',
                'type' => 'date-time',
                'label' => 'Desired Move-In Date',
                'name' => 'move-in-date',
                'format' => 'date',
                'required' => '1',
            ],
            // Preferred Lease Term (Dropdown)
            [
                'id' => '7',
                'type' => 'select',
                'label' => 'Preferred Lease Term',
                'name' => 'lease-term',
                'choices' => [
                    ['label' => '6 Months', 'value' => '6 Months'],
                    ['label' => '12 Months', 'value' => '12 Months'],
                ],
                'required' => '1',
            ],
            // Payment Assurance Option (Radio Buttons, shown for Seneca Student)
            [
                'id' => '8',
                'type' => 'radio',
                'label' => 'Payment Assurance Option',
                'name' => 'payment-option',
                'choices' => [
                    ['label' => 'Canadian Bank Deposit', 'value' => 'Canadian Bank Deposit'],
                    ['label' => 'Sponsor Support', 'value' => 'Sponsor Support'],
                ],
                'required' => '1',
                'css' => 'student-fields payment-option',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '2', // applicant-type
                            'operator' => '==',
                            'value' => 'Seneca Student',
                        ],
                    ],
                ],
            ],
            // Government-Issued ID (File Upload)
            [
                'id' => '9',
                'type' => 'file-upload',
                'label' => 'Government-Issued ID',
                'name' => 'id-upload',
                'allowed_file_types' => 'jpg,png,pdf',
                'max_file_size' => '5', // 5MB
                'required' => '1',
                'css' => 'required-field',
            ],
            // Proof of Seneca Enrollment (File Upload, shown for Seneca Student)
            [
                'id' => '10',
                'type' => 'file-upload',
                'label' => 'Proof of Seneca Enrollment',
                'name' => 'enrollment-proof',
                'allowed_file_types' => 'jpg,png,pdf',
                'max_file_size' => '5',
                'required' => '1',
                'css' => 'student-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '2', // applicant-type
                            'operator' => '==',
                            'value' => 'Seneca Student',
                        ],
                    ],
                ],
            ],
            // Sponsor Name (Text, shown for Sponsor Support)
            [
                'id' => '11',
                'type' => 'text',
                'label' => 'Sponsor Name',
                'name' => 'sponsor-name',
                'size' => 'medium',
                'required' => '1',
                'css' => 'sponsor-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '8', // payment-option
                            'operator' => '==',
                            'value' => 'Sponsor Support',
                        ],
                    ],
                ],
            ],
            // Sponsor Address (Text, shown for Sponsor Support)
            [
                'id' => '12',
                'type' => 'text',
                'label' => 'Sponsor Address',
                'name' => 'sponsor-address',
                'size' => 'medium',
                'required' => '1',
                'css' => 'sponsor-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '8', // payment-option
                            'operator' => '==',
                            'value' => 'Sponsor Support',
                        ],
                    ],
                ],
            ],
            // Sponsor Phone (Phone, shown for Sponsor Support)
            [
                'id' => '13',
                'type' => 'phone',
                'label' => 'Sponsor Phone',
                'name' => 'sponsor-phone',
                'format' => 'international',
                'required' => '1',
                'css' => 'sponsor-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '8', // payment-option
                            'operator' => '==',
                            'value' => 'Sponsor Support',
                        ],
                    ],
                ],
            ],
            // Sponsor Email (Email, shown for Sponsor Support)
            [
                'id' => '14',
                'type' => 'email',
                'label' => 'Sponsor Email',
                'name' => 'sponsor-email',
                'size' => 'medium',
                'required' => '1',
                'css' => 'sponsor-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '8', // payment-option
                            'operator' => '==',
                            'value' => 'Sponsor Support',
                        ],
                    ],
                ],
            ],
            // Sponsor Bank Statement (File Upload, shown for Sponsor Support)
            [
                'id' => '15',
                'type' => 'file-upload',
                'label' => 'Sponsor Bank Statement',
                'name' => 'sponsor-bank',
                'allowed_file_types' => 'jpg,png,pdf',
                'max_file_size' => '5',
                'required' => '1',
                'css' => 'sponsor-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '8', // payment-option
                            'operator' => '==',
                            'value' => 'Sponsor Support',
                        ],
                    ],
                ],
            ],
            // Sponsor Pledge Document (File Upload, shown for Sponsor Support)
            [
                'id' => '16',
                'type' => 'file-upload',
                'label' => 'Sponsor Pledge Document',
                'name' => 'sponsor-pledge-doc',
                'allowed_file_types' => 'jpg,png,pdf',
                'max_file_size' => '5',
                'required' => '1',
                'css' => 'sponsor-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '8', // payment-option
                            'operator' => '==',
                            'value' => 'Sponsor Support',
                        ],
                    ],
                ],
            ],
            // Sponsor Pledge Agreement (Checkbox, shown for Sponsor Support)
            [
                'id' => '17',
                'type' => 'checkbox',
                'label' => 'Sponsor Pledge Agreement',
                'name' => 'sponsor-pledge',
                'choices' => [
                    ['label' => 'I confirm that the sponsor agrees to support the applicant financially.', 'value' => 'I confirm that the sponsor agrees to support the applicant financially.'],
                ],
                'required' => '1',
                'css' => 'sponsor-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '8', // payment-option
                            'operator' => '==',
                            'value' => 'Sponsor Support',
                        ],
                    ],
                ],
            ],
            // Canadian Bank Account Deposit Proof (File Upload, shown for Canadian Bank Deposit)
            [
                'id' => '18',
                'type' => 'file-upload',
                'label' => 'Canadian Bank Account Deposit Proof',
                'name' => 'deposit-proof',
                'allowed_file_types' => 'jpg,png,pdf',
                'max_file_size' => '5',
                'required' => '1',
                'css' => 'deposit-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '8', // payment-option
                            'operator' => '==',
                            'value' => 'Canadian Bank Deposit',
                        ],
                    ],
                ],
            ],
            // Expat Country (Dropdown, shown for Expat)
            [
                'id' => '19',
                'type' => 'select',
                'label' => 'Expat Country',
                'name' => 'expat-country',
                'choices' => [
                    ['label' => 'US', 'value' => 'US'],
                    ['label' => 'UK', 'value' => 'UK'],
                    ['label' => 'EU', 'value' => 'EU'],
                    ['label' => 'Canada', 'value' => 'Canada'],
                ],
                'required' => '1',
                'css' => 'expat-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '2', // applicant-type
                            'operator' => '==',
                            'value' => 'Expat (US, UK, EU, Canada)',
                        ],
                    ],
                ],
            ],
            // Employment Status (Radio Buttons, shown for Expat)
            [
                'id' => '20',
                'type' => 'radio',
                'label' => 'Employment Status',
                'name' => 'employment-status',
                'choices' => [
                    ['label' => 'Employed in Canada', 'value' => 'Employed in Canada'],
                    ['label' => 'Self-Employed', 'value' => 'Self-Employed'],
                    ['label' => 'Employed Abroad', 'value' => 'Employed Abroad'],
                ],
                'required' => '1',
                'css' => 'expat-fields required-field employment-status',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '2', // applicant-type
                            'operator' => '==',
                            'value' => 'Expat (US, UK, EU, Canada)',
                        ],
                    ],
                ],
            ],
            // Employer Name (Canada) (Text, shown for Employed in Canada)
            [
                'id' => '21',
                'type' => 'text',
                'label' => 'Employer Name (Canada)',
                'name' => 'employer-name-canada',
                'size' => 'medium',
                'required' => '1',
                'css' => 'canada-employed-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '20', // employment-status
                            'operator' => '==',
                            'value' => 'Employed in Canada',
                        ],
                    ],
                ],
            ],
            // Employer Phone (Canada) (Phone, shown for Employed in Canada)
            [
                'id' => '22',
                'type' => 'phone',
                'label' => 'Employer Phone (Canada)',
                'name' => 'employer-phone-canada',
                'format' => 'international',
                'required' => '1',
                'css' => 'canada-employed-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '20', // employment-status
                            'operator' => '==',
                            'value' => 'Employed in Canada',
                        ],
                    ],
                ],
            ],
            // Paystub (Canada) (File Upload, shown for Employed in Canada)
            [
                'id' => '23',
                'type' => 'file-upload',
                'label' => 'Paystub (Canada)',
                'name' => 'paystub-canada',
                'allowed_file_types' => 'jpg,png,pdf',
                'max_file_size' => '5',
                'required' => '1',
                'css' => 'canada-employed-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '20', // employment-status
                            'operator' => '==',
                            'value' => 'Employed in Canada',
                        ],
                    ],
                ],
            ],
            // Business Name (Text, shown for Self-Employed)
            [
                'id' => '24',
                'type' => 'text',
                'label' => 'Business Name',
                'name' => 'business-name',
                'size' => 'medium',
                'required' => '1',
                'css' => 'self-employed-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '20', // employment-status
                            'operator' => '==',
                            'value' => 'Self-Employed',
                        ],
                    ],
                ],
            ],
            // Self-Employed Proof (File Upload, shown for Self-Employed)
            [
                'id' => '25',
                'type' => 'file-upload',
                'label' => 'Self-Employed Proof',
                'name' => 'self-employed-proof',
                'allowed_file_types' => 'jpg,png,pdf',
                'max_file_size' => '5',
                'required' => '1',
                'css' => 'self-employed-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '20', // employment-status
                            'operator' => '==',
                            'value' => 'Self-Employed',
                        ],
                    ],
                ],
            ],
            // Employer Name (Abroad) (Text, shown for Employed Abroad)
            [
                'id' => '26',
                'type' => 'text',
                'label' => 'Employer Name (Abroad)',
                'name' => 'employer-name-abroad',
                'size' => 'medium',
                'required' => '1',
                'css' => 'abroad-employed-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '20', // employment-status
                            'operator' => '==',
                            'value' => 'Employed Abroad',
                        ],
                    ],
                ],
            ],
            // Employer Phone (Abroad) (Phone, shown for Employed Abroad)
            [
                'id' => '27',
                'type' => 'phone',
                'label' => 'Employer Phone (Abroad)',
                'name' => 'employer-phone-abroad',
                'format' => 'international',
                'required' => '1',
                'css' => 'abroad-employed-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '20', // employment-status
                            'operator' => '==',
                            'value' => 'Employed Abroad',
                        ],
                    ],
                ],
            ],
            // Paystub (Abroad) (File Upload, shown for Employed Abroad)
            [
                'id' => '28',
                'type' => 'file-upload',
                'label' => 'Paystub (Abroad)',
                'name' => 'paystub-abroad',
                'allowed_file_types' => 'jpg,png,pdf',
                'max_file_size' => '5',
                'required' => '1',
                'css' => 'abroad-employed-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '20', // employment-status
                            'operator' => '==',
                            'value' => 'Employed Abroad',
                        ],
                    ],
                ],
            ],
            // Residency Proof (File Upload, shown for Expat)
            [
                'id' => '29',
                'type' => 'file-upload',
                'label' => 'Residency Proof',
                'name' => 'residency-proof',
                'allowed_file_types' => 'jpg,png,pdf',
                'max_file_size' => '5',
                'required' => '1',
                'css' => 'expat-fields required-field',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '2', // applicant-type
                            'operator' => '==',
                            'value' => 'Expat (US, UK, EU, Canada)',
                        ],
                    ],
                ],
            ],
            // Rental History (File Upload, shown for Expat)
            [
                'id' => '30',
                'type' => 'file-upload',
                'label' => 'Rental History',
                'name' => 'rental-history',
                'allowed_file_types' => 'jpg,png,pdf',
                'max_file_size' => '5',
                'css' => 'expat-fields',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '2', // applicant-type
                            'operator' => '==',
                            'value' => 'Expat (US, UK, EU, Canada)',
                        ],
                    ],
                ],
            ],
            // Credit Check Authorization (Student) (Checkbox, shown for Seneca Student)
            [
                'id' => '31',
                'type' => 'checkbox',
                'label' => 'Credit Check Authorization (Optional for Students)',
                'name' => 'credit-auth-student',
                'choices' => [
                    ['label' => 'I authorize a credit check (optional for students)', 'value' => 'I authorize a credit check (optional for students)'],
                ],
                'css' => 'student-fields',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '2', // applicant-type
                            'operator' => '==',
                            'value' => 'Seneca Student',
                        ],
                    ],
                ],
            ],
            // Credit Check Authorization (Expat) (Checkbox, shown for Expat)
            [
                'id' => '32',
                'type' => 'checkbox',
                'label' => 'Credit Check Authorization (Optional for Expats)',
                'name' => 'credit-auth-expat',
                'choices' => [
                    ['label' => 'I authorize a credit check (optional for expats)', 'value' => 'I authorize a credit check (optional for expats)'],
                ],
                'css' => 'expat-fields',
                'conditional_logic' => [
                    'action' => 'show',
                    'logic' => [
                        [
                            'field' => '2', // applicant-type
                            'operator' => '==',
                            'value' => 'Expat (US, UK, EU, Canada)',
                        ],
                    ],
                ],
            ],
            // Damage Deposit Commitment (Checkbox)
            [
                'id' => '33',
                'type' => 'checkbox',
                'label' => 'Damage Deposit Commitment',
                'name' => 'damage-deposit',
                'choices' => [
                    ['label' => 'I agree to pay a $500 refundable damage deposit to cover potential property damage.', 'value' => 'I agree to pay a $500 refundable damage deposit to cover potential property damage.'],
                ],
            ],
            // Tenant Agreement (Checkbox)
            [
                'id' => '34',
                'type' => 'checkbox',
                'label' => 'Tenant Agreement',
                'name' => 'tenant-agreement',
                'choices' => [
                    ['label' => 'I agree to make timely rent payments, maintain the property in good condition, and comply with Ontario rental laws. I understand quarterly terms auto-renew unless canceled.', 'value' => 'I agree to make timely rent payments, maintain the property in good condition, and comply with Ontario rental laws. I understand quarterly terms auto-renew unless canceled.'],
                ],
                'required' => '1',
                'css' => 'required-field',
            ],
        ],
        'settings' => [
            'form_title' => 'Rental Application',
            'form_desc' => 'Apply for a rental property.',
            'submit_text' => 'Submit Application',
            'submit_text_processing' => 'Processing...',
            'antispam' => '1',
            'ajax_submit' => '1',
            'notification_enable' => '1',
            'notifications' => [
                '1' => [
                    'email' => '{admin_email}',
                    'subject' => 'New Rental Application Submission',
                    'sender_name' => get_bloginfo('name'),
                    'sender_address' => '{admin_email}',
                    'message' => "A new rental application has been submitted.\n\nProperty ID: {field_id=\"1\"}\nApplicant Type: {field_id=\"2\"}\nName: {field_id=\"3\"}\nEmail.Concurrent: {field_id=\"4\"}\nPhone: {field_id=\"5\"}",
                ],
            ],
            'confirmation_type' => 'message',
            'confirmation_message' => 'Thank you for submitting your application! We will review it and get back to you soon.',
        ],
        'meta' => [
            'template' => 'blank',
        ],
    ];

    // Create the form
    $form_id = wp_insert_post([
        'post_title' => 'Rental Application',
        'post_content' => wp_json_encode($form_data),
        'post_status' => 'publish',
        'post_type' => 'wpforms',
    ]);

    if (is_wp_error($form_id)) {
        echo 'Error creating form: ' . $form_id->get_error_message();
    } else {
        echo 'Form created successfully with ID: ' . $form_id;
    }
}

// Hook the function to run on admin init (or run it manually)
add_action('admin_init', 'create_rental_application_form');