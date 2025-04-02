<?php
class HRA_Dashboard {
    public function __construct() {
        add_filter('houzez_my_account_menu', array($this, 'add_dashboard_menu'));
        add_action('houzez_my_account_content', array($this, 'render_dashboard_content'));
    }

    // Add menu item to Houzez dashboard
    public function add_dashboard_menu($menu) {
        $menu['rental_applications'] = array(
            'title' => 'Rental Applications',
            'icon' => 'fa-file-alt',
        );
        return $menu;
    }

    // Render dashboard content
    public function render_dashboard_content($active_tab) {
        if ($active_tab === 'rental_applications') {
            global $wpdb;
            $user_id = get_current_user_id();
            $table_name = $wpdb->prefix . 'hra_applications';

            if (current_user_can('manage_options')) {
                // Landlord view
                $applications = $wpdb->get_results("SELECT * FROM $table_name");
                foreach ($applications as $app) {
                    echo "<p>Application for Property #{$app->property_id} by {$app->name} - Status: {$app->status}</p>";
                    // Add approve/reject buttons here
                }
            } else {
                // Applicant view
                $applications = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE applicant_id = %d", $user_id));
                foreach ($applications as $app) {
                    echo "<p>Application for Property #{$app->property_id} - Status: {$app->status}</p>";
                }
            }
        }
    }
}