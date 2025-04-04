<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 08/08/16
 * Time: 10:38 PM
 */

if( !function_exists('houzez_add_theme_caps') ) {
    function houzez_add_theme_caps() {
        
        // gets the author role
        $role = get_role('administrator');

        $role->add_cap('create_properties');
        $role->add_cap('publish_properties');
        $role->add_cap('read_property');
        $role->add_cap('delete_property');
        $role->add_cap('edit_property');
        $role->add_cap('edit_properties');
        $role->add_cap('delete_properties');
        $role->add_cap('edit_published_properties');
        $role->add_cap('delete_published_properties');
        $role->add_cap('read_private_properties');
        $role->add_cap('delete_private_properties');
        $role->add_cap('edit_others_properties');
        $role->add_cap('delete_others_properties');
        $role->add_cap('edit_private_properties');
        $role->add_cap('delete_private_properties');
        $role->add_cap('edit_published_properties');

        $role->add_cap('delete_user_package');
        $role->add_cap('delete_user_packages');
        $role->add_cap('edit_user_packages');
        $role->add_cap('delete_others_user_packages');

        $role->add_cap('read_testimonial');
        $role->add_cap('edit_testimonial');
        $role->add_cap('delete_testimonial');
        $role->add_cap('create_testimonials');
        $role->add_cap('publish_testimonials');
        $role->add_cap('edit_testimonials');
        $role->add_cap('edit_published_testimonials');
        $role->add_cap('delete_published_testimonials');
        $role->add_cap('delete_testimonials');
        $role->add_cap('delete_private_testimonials');
        $role->add_cap('delete_others_testimonials');
        $role->add_cap('edit_others_testimonials');
        $role->add_cap('edit_private_testimonials');
        $role->add_cap('edit_published_testimonials');

        $role->add_cap('read_agent');
        $role->add_cap('delete_agent');
        $role->add_cap('edit_agent');
        $role->add_cap('create_agents');
        $role->add_cap('edit_agents');
        $role->add_cap('edit_others_agents');
        $role->add_cap('publish_agents');
        $role->add_cap('read_private_agents');
        $role->add_cap('delete_agents');
        $role->add_cap('delete_private_agents');
        $role->add_cap('delete_published_agents');
        $role->add_cap('delete_others_agents');
        $role->add_cap('edit_private_agents');
        $role->add_cap('edit_published_agents');

        // gets the author role
        $role = get_role('editor');

        if( $role !== null ) {
            $role->add_cap('create_properties');
            $role->add_cap('read_property');
            $role->add_cap('delete_property');
            $role->add_cap('edit_property');
            $role->add_cap('publish_properties');
            $role->add_cap('edit_properties');
            $role->add_cap('edit_published_properties');
            $role->add_cap('delete_published_properties');
            $role->add_cap('read_private_properties');
            $role->add_cap('delete_private_properties');
            $role->add_cap('edit_others_properties');
            $role->add_cap('delete_others_properties');
            $role->add_cap('edit_private_properties');
            $role->add_cap('edit_published_properties');

            $role->add_cap('read_testimonial');
            $role->add_cap('delete_testimonial');
            $role->add_cap('edit_testimonial');
            $role->add_cap('create_testimonials');
            $role->add_cap('delete_testimonial');
            $role->add_cap('publish_testimonials');
            $role->add_cap('edit_testimonials');
            $role->add_cap('edit_published_testimonials');
            $role->add_cap('delete_published_testimonials');
            $role->add_cap('delete_testimonials');
            $role->add_cap('delete_private_testimonials');
            $role->add_cap('delete_others_testimonials');
            $role->add_cap('edit_others_testimonials');
            $role->add_cap('edit_private_testimonials');
            $role->add_cap('edit_published_testimonials');

            $role->add_cap('read_agent');
            $role->add_cap('delete_agent');
            $role->add_cap('edit_agent');
            $role->add_cap('create_agents');
            $role->add_cap('edit_agents');
            $role->add_cap('edit_others_agents');
            $role->add_cap('publish_agents');
            $role->add_cap('read_private_agents');
            $role->add_cap('delete_agents');
            $role->add_cap('delete_private_agents');
            $role->add_cap('delete_published_agents');
            $role->add_cap('delete_others_agents');
            $role->add_cap('edit_private_agents');
            $role->add_cap('edit_published_agents');
        }


        $manager_role = get_role('houzez_manager');

        if( $manager_role !== null ) {
            $manager_role->add_cap('create_properties');
            $manager_role->add_cap('read_property');
            $manager_role->add_cap('delete_property');
            $manager_role->add_cap('edit_property');
            $manager_role->add_cap('publish_properties');
            $manager_role->add_cap('edit_properties');
            $manager_role->add_cap('edit_published_properties');
            $manager_role->add_cap('delete_published_properties');
            $manager_role->add_cap('read_private_properties');
            $manager_role->add_cap('delete_private_properties');
            $manager_role->add_cap('edit_others_properties');
            $manager_role->add_cap('delete_others_properties');
            $manager_role->add_cap('edit_private_properties');
            $manager_role->add_cap('edit_published_properties');

            $manager_role->add_cap('read_testimonial');
            $manager_role->add_cap('delete_testimonial');
            $manager_role->add_cap('edit_testimonial');
            $manager_role->add_cap('create_testimonials');
            $manager_role->add_cap('delete_testimonial');
            $manager_role->add_cap('publish_testimonials');
            $manager_role->add_cap('edit_testimonials');
            $manager_role->add_cap('edit_published_testimonials');
            $manager_role->add_cap('delete_published_testimonials');
            $manager_role->add_cap('delete_testimonials');
            $manager_role->add_cap('delete_private_testimonials');
            $manager_role->add_cap('delete_others_testimonials');
            $manager_role->add_cap('edit_others_testimonials');
            $manager_role->add_cap('edit_private_testimonials');
            $manager_role->add_cap('edit_published_testimonials');

            $manager_role->add_cap('read_agent');
            $manager_role->add_cap('delete_agent');
            $manager_role->add_cap('edit_agent');
            $manager_role->add_cap('create_agents');
            $manager_role->add_cap('edit_agents');
            $manager_role->add_cap('edit_others_agents');
            $manager_role->add_cap('publish_agents');
            $manager_role->add_cap('read_private_agents');
            $manager_role->add_cap('delete_agents');
            $manager_role->add_cap('delete_private_agents');
            $manager_role->add_cap('delete_published_agents');
            $manager_role->add_cap('delete_others_agents');
            $manager_role->add_cap('edit_private_agents');
            $manager_role->add_cap('edit_published_agents');

            // Add capability to upload files and edit posts
            $manager_role->add_cap('upload_files');
            $manager_role->add_cap('edit_posts');
        }

        $agent_role = get_role('houzez_agent');
        if( $agent_role !== null ) {
            $agent_role->add_cap('level_2');
        }

        $agency_role = get_role('houzez_agency');
        if( $agency_role !== null ) {
            $agency_role->add_cap('level_2');
        }

        $owner_role = get_role('houzez_owner');
        if( $owner_role !== null ) {
            $owner_role->add_cap('level_2');
        }

        $seller_role = get_role('houzez_seller');
        if( $seller_role !== null ) {
            $seller_role->add_cap('level_2');
        }


    }

    add_action('admin_init', 'houzez_add_theme_caps');
}
?>