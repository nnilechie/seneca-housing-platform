<?php
/**
 * Template Name: User Dashboard Rental Applications
 * Description: Displays the rental applications for the logged-in user in the Houzez dashboard.
 */

get_header();

global $houzez_options, $current_user;

wp_get_current_user();
$userID = $current_user->ID;

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_template_part('template-parts/dashboard/title');

?>

<div class="dashboard-content-wrapper dashboard-rental-applications">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <h2>Rental Applications</h2>
                <?php
                $shortcode_output = do_shortcode('[raf_applicant_dashboard]');
                error_log('Shortcode output in template: ' . $shortcode_output);
                echo $shortcode_output;
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>