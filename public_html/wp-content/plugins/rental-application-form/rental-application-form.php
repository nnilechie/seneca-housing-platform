<?php
/*
Plugin Name: Rental Application Form
Description: A plugin to manage a rental application form using WPForms.
Version: 1.0.1
Author: Your Name
License: GPL-2.0+
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('RAF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RAF_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the functions file for backend logic
require_once RAF_PLUGIN_DIR . 'includes/functions.php';

// Create a shortcode to embed the form
add_shortcode('rental_application_form', 'raf_render_form');
function raf_render_form($atts) {
    // Look for the "Rental Application" form
    $form = get_page_by_title('Rental Application', OBJECT, 'wpforms');
    if (!$form || !function_exists('wpforms')) {
        return '<p>Error: WPForms is not installed, or the "Rental Application" form does not exist.</p>';
    }
    return do_shortcode('[wpforms id="' . esc_attr($form->ID) . '"]');
}