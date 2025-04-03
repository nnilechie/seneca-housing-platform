<?php
class HRA_Application {
    public function __construct() {
        add_action('houzez_after_property_description', array($this, 'add_apply_now_button'));
        add_shortcode('hra_application_form', array($this, 'render_application_form'));
        add_action('wp', array($this, 'handle_application_submission'));
    }

    // Add "Apply Now" button to property pages
    public function add_apply_now_button() {
        if (is_singular('property')) {
            $property_id = get_the_ID();
            echo '<button class="hra-apply-now" data-property-id="' . esc_attr($property_id) . '">Apply Now</button>';
            echo do_shortcode('[hra_application_form property_id="' . $property_id . '"]');
        }
    }

    // Render application form
    public function render_application_form($atts) {
        $atts = shortcode_atts(array('property_id' => 0), $atts);
        ob_start();
        ?>
        <form method="post" enctype="multipart/form-data" class="hra-application-form">
            <input type="hidden" name="property_id" value="<?php echo esc_attr($atts['property_id']); ?>">
            <h3>Rental Application</h3>
            
            <!-- Personal Details -->
            <label>Name: <input type="text" name="applicant_name" required></label>
            <label>Email: <input type="email" name="applicant_email" required></label>
            
            <!-- User Type Conditional Fields -->
            <label>User Type:
                <select name="user_type" id="user-type">
                    <option value="employed">Employed Professional</option>
                    <option value="student">Student</option>
                    <option value="expat">Expat</option>
                </select>
            </label>
            
            <div id="conditional-fields">
                <!-- Fields populated via JS based on user type -->
            </div>

            <!-- Document Uploads -->
            <label>Upload Documents: <input type="file" name="documents[]" multiple></label>

            <input type="submit" name="hra_submit_application" value="Submit Application">
        </form>
        <?php
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

            // Handle file uploads
            $this->handle_file_uploads($wpdb->insert_id);

            // Send email notification
            $this->send_status_email($data['email'], 'Pending', $data['property_id']);
        }
    }

    // File upload handler
    private function handle_file_uploads($application_id) {
        if (!empty($_FILES['documents']['name'][0])) {
            $upload_dir = wp_upload_dir();
            $target_dir = $upload_dir['basedir'] . '/hra-documents/' . $application_id . '/';
            wp_mkdir_p($target_dir);

            foreach ($_FILES['documents']['name'] as $key => $value) {
                $file_path = $target_dir . basename($_FILES['documents']['name'][$key]);
                move_uploaded_file($_FILES['documents']['tmp_name'][$key], $file_path);
            }
        }
    }

    // Send email notification
    private function send_status_email($email, $status, $property_id) {
        $subject = "Application Status Update: Property #$property_id";
        $message = "Your application status is now: $status.";
        wp_mail($email, $subject, $message);
    }
}