<?php
/**
 * Houzez Child Theme Functions
 *
 * This file contains custom functions and modifications for the Houzez child theme.
 */

/**
 * Enqueue parent and child theme styles
 */
add_action('wp_enqueue_scripts', 'houzez_child_enqueue_styles');
function houzez_child_enqueue_styles() {
    // Enqueue the parent theme's style
    wp_enqueue_style('houzez-parent-style', get_template_directory_uri() . '/style.css');

    // Enqueue the child theme's style (optional, if you have a custom style.css)
    wp_enqueue_style('houzez-child-style', get_stylesheet_uri(), array('houzez-parent-style'), wp_get_theme()->get('Version'));
}

/**
 * Force add Rental Applications as a top-level menu item using a filter
 */
add_filter('houzez_dashboard_side_menu', 'force_add_rental_applications_to_board');
function force_add_rental_applications_to_board($side_menu) {
    error_log('Running force_add_rental_applications_to_board filter on page: ' . basename(get_page_template()));
    $dashboard_rental_applications = 'https://staging2.nicholasi1.sg-host.com/user-dashboard-rental-applications/';
    $ac_rental_applications = (is_page_template('template/user_dashboard_rental_applications.php')) ? 'class="active"' : '';

    $rental_item = '<li class="side-menu-item">
        <a ' . esc_attr($ac_rental_applications) . ' href="' . esc_url($dashboard_rental_applications) . '">
            <i class="houzez-icon icon-file-cabinet mr-2"></i> Rental Applications
        </a>
    </li>';

    // Add the Rental Applications item just before the Log Out item
    $logout_pos = strpos($side_menu, '<i class="houzez-icon icon-lock-5 mr-2"></i>');
    if ($logout_pos !== false) {
        // Check if the item already exists to avoid duplication
        if (strpos($side_menu, 'href="' . esc_url($dashboard_rental_applications) . '"') === false) {
            $side_menu = substr_replace($side_menu, $rental_item, $logout_pos, 0);
            error_log('Rental Applications top-level item added via filter on page: ' . basename(get_page_template()));
        } else {
            error_log('Rental Applications top-level item already present in side_menu on page: ' . basename(get_page_template()));
        }
    } else {
        error_log('Log Out menu item not found in side_menu on page: ' . basename(get_page_template()));
    }

    return $side_menu;
}

/**
 * Force add Rental Applications as a top-level menu item using JavaScript as a fallback
 */
add_action('wp_footer', 'force_add_rental_applications_tab');
function force_add_rental_applications_tab() {
    if (is_user_logged_in() && (strpos($_SERVER['REQUEST_URI'], '/my-profile') !== false || 
                                strpos($_SERVER['REQUEST_URI'], '/my-properties') !== false || 
                                strpos($_SERVER['REQUEST_URI'], '/favorite-properties') !== false || 
                                strpos($_SERVER['REQUEST_URI'], '/saved-search') !== false || 
                                strpos($_SERVER['REQUEST_URI'], '/invoices') !== false)) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var sideMenu = document.querySelector('.side-menu');
                if (sideMenu) {
                    var rentalTab = document.createElement('li');
                    rentalTab.className = 'side-menu-item';
                    rentalTab.innerHTML = '<a href="https://staging2.nicholasi1.sg-host.com/user-dashboard-rental-applications/"><i class="houzez-icon icon-file-cabinet mr-2"></i> Rental Applications</a>';
                    var logoutItem = sideMenu.querySelector('a[href*="wp-login.php?action=logout"]');
                    if (logoutItem) {
                        // Check if the item already exists to avoid duplication
                        if (!sideMenu.querySelector('a[href="https://staging2.nicholasi1.sg-host.com/user-dashboard-rental-applications/"]')) {
                            logoutItem.parentElement.insertAdjacentElement('beforebegin', rentalTab);
                            console.log('Rental Applications top-level item added via JavaScript');
                        } else {
                            console.log('Rental Applications top-level item already exists');
                        }
                    } else {
                        console.log('Log Out menu item not found');
                    }
                } else {
                    console.log('Side menu not found');
                }
            });
        </script>
        <?php
    }
}

/**
 * Add custom CSS to ensure menu items are visible
 */
add_action('wp_head', 'add_custom_dashboard_css');
function add_custom_dashboard_css() {
    if (is_user_logged_in() && (strpos($_SERVER['REQUEST_URI'], '/my-profile') !== false || 
                                strpos($_SERVER['REQUEST_URI'], '/my-properties') !== false || 
                                strpos($_SERVER['REQUEST_URI'], '/favorite-properties') !== false || 
                                strpos($_SERVER['REQUEST_URI'], '/saved-search') !== false || 
                                strpos($_SERVER['REQUEST_URI'], '/invoices') !== false)) {
        ?>
        <style>
            .side-menu .side-menu-item {
                display: block !important;
                visibility: visible !important;
            }
        </style>
        <?php
    }
}

?>