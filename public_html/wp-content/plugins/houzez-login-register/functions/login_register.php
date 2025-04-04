<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 16/01/16
 * Time: 6:11 PM
 */

/*-----------------------------------------------------------------------------------*/
// Login
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_houzez_login', 'houzez_login' );
add_action( 'wp_ajax_nopriv_houzez_login', 'houzez_login' );

if( !function_exists('houzez_login') ) {
    function houzez_login() {


        $allowed_html = array();

        $allowed_html_array = array('strong' => array());
        $username = wp_kses( $_POST['username'], $allowed_html );
        $pass = isset( $_POST['password'] ) ? $_POST['password'] : "";
        $is_submit_listing = isset($_POST['is_submit_listing']) ? wp_kses( $_POST['is_submit_listing'], $allowed_html ) : "";
        $response = isset( $_POST["g-recaptcha-response"] ) ? $_POST["g-recaptcha-response"] : "";

        do_action('houzez_before_login');

        if( $is_submit_listing == 'yes' ) {
            check_ajax_referer('houzez_register_nonce2', 'houzez_register_security2');
        } else {
            check_ajax_referer( 'houzez_login_nonce', 'houzez_login_security' );
        }

        if( isset( $_POST['remember'] ) ) {
            $remember = wp_kses( $_POST['remember'], $allowed_html );
        } else {
            $remember = '';
        }

        if( empty( $username ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username or email field is empty.', 'houzez-login-register') ) );
            wp_die();
        }
        if( empty( $pass ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The password field is empty.', 'houzez-login-register') ) );
            wp_die();
        }
        if( !username_exists( $username ) && !email_exists($username)) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username or email', 'houzez-login-register') ) );
            wp_die();
        }


        if($is_submit_listing != 'yes') {
            houzez_google_recaptcha_callback();
        }

        $remember = ($remember == 'on') ? true : false;

        if(is_email($username)) {
            $user = get_user_by( 'email', $username );
            $username = $user->user_login;
        }

        $creds = array();
        $creds['user_login'] = $username;
        $creds['user_password'] = $pass;
        $creds['remember'] = $remember;
        $user = wp_signon( $creds, false );


        if ( is_wp_error( $user ) ) {

            $error_code = $user->get_error_code();

            if( $error_code == 'incorrect_password' ) {

                echo json_encode( array(
                    'success' => false,
                    'msg' => sprintf( wp_kses(__('The password you entered for the username <strong>%s</strong> is incorrect.', 'houzez-login-register'), $allowed_html_array), $username )
                ) );

            } else {

                echo json_encode( array(
                    'success' => false,
                    'msg' => $user->get_error_message()
                ) );

            }
            
            wp_die();
        } else {


            $user_id = $user->ID;
            $email_verified = get_user_meta( $user_id, 'houzez_email_verified', true );

            if ( metadata_exists( 'user', $user_id, 'houzez_email_verified' ) && !$email_verified ) {
                echo json_encode( array(
                    'success' => false,
                    'msg' => esc_html__( 'Please verify your email address before logging in. A verification email has been sent to your email address.', 'houzez-login-register' )
                ) );
                wp_logout();
                wp_die();
            } else {

                wp_set_current_user($user->ID); // Set the current user detail
                wp_set_auth_cookie($user->ID, true); // Set auth cookies
                
                echo json_encode( array( 
                    'success' => true,
                    'redirect_to' => esc_url($_POST['redirect_to']),
                    'msg' => esc_html__('Login successful, redirecting...', 'houzez-login-register') 
                ) );
            }

            do_action('houzez_after_login');

        }
        wp_die();
    }
}


/*-----------------------------------------------------------------------------------*/
// Register
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_houzez_register', 'houzez_register' );

if( !function_exists('houzez_register') ) {
    function houzez_register() {
        
        check_ajax_referer('houzez_register_nonce', 'houzez_register_security');

        $allowed_html = array();

        $usermane          = trim( sanitize_text_field( wp_kses( $_POST['username'], $allowed_html ) ));
        $email             = trim( sanitize_text_field( wp_kses( $_POST['useremail'], $allowed_html ) ));
        $term_condition    = isset( $_POST['term_condition'] ) ? wp_kses( $_POST['term_condition'], $allowed_html ) : "off";
        
        $enable_password   = houzez_option('enable_password');

        $response = isset( $_POST["g-recaptcha-response"] ) ? $_POST["g-recaptcha-response"] : "";

        $user_roles = array ( 'houzez_agency', 'houzez_agent', 'houzez_buyer', 'houzez_seller', 'houzez_owner' );

        $user_role = get_option( 'default_role' );

        if( $user_role == 'administrator' ) {
            $user_role = 'subscriber';
        }

        
        if( isset( $_POST['role'] ) && $_POST['role'] != '' && in_array( $_POST['role'], $user_roles ) ) {
            $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;
        } else {
            $user_role = $user_role;
        }

        if( houzez_option('header_register') != 1 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Access denied.', 'houzez-login-register') ) );
            wp_die();
        }

        if( get_option('users_can_register') != 1 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Access denied.', 'houzez-login-register') ) );
            wp_die();
        }


        $firstname = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
        if( empty($firstname) && houzez_option('register_first_name', 0) == 1 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The first name field is empty.', 'houzez-login-register') ) );
            wp_die();
        }

        $lastname = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
        if( empty($lastname) && houzez_option('register_last_name', 0) == 1 && $user_role != 'houzez_agency' ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The last name field is empty.', 'houzez-login-register') ) );
            wp_die();
        }

        if( empty( $usermane ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username field is empty.', 'houzez-login-register') ) );
            wp_die();
        }
        if( strlen( $usermane ) < 3 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Minimum 3 characters required', 'houzez-login-register') ) );
            wp_die();
        }
        if (preg_match("/^[0-9A-Za-z_]+$/", $usermane) == 0) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username (do not use special characters or spaces)!', 'houzez-login-register') ) );
            wp_die();
        }

        if( username_exists( $usermane ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('This username is already registered.', 'houzez-login-register') ) );
            wp_die();
        }
        
        if( empty( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The email field is empty.', 'houzez-login-register') ) );
            wp_die();
        }

        if( email_exists( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('This email address is already registered.', 'houzez-login-register') ) );
            wp_die();
        }

        if( !is_email( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid email address.', 'houzez-login-register') ) );
            wp_die();
        }

        $phone_number = isset( $_POST['phone_number'] ) ? $_POST['phone_number'] : '';
        if( empty($phone_number) && houzez_option('register_mobile', 0) == 1 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Please enter your number', 'houzez-login-register') ) );
            wp_die();
        }

        if( $enable_password == 'yes' ){
            $user_pass         = trim( sanitize_text_field(wp_kses( $_POST['register_pass'] ,$allowed_html) ) );
            $user_pass_retype  = trim( sanitize_text_field(wp_kses( $_POST['register_pass_retype'] ,$allowed_html) ) );

            if ($user_pass == '' || $user_pass_retype == '' ) {
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('One of the password field is empty!', 'houzez-login-register') ) );
                wp_die();
            }

            if ($user_pass !== $user_pass_retype ){
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('Passwords do not match', 'houzez-login-register') ) );
                wp_die();
            }
        }

        $term_condition = ( $term_condition == 'on') ? true : false;

        if( !$term_condition ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('You need to agree with terms & conditions.', 'houzez-login-register') ) );
            wp_die();
        }

        do_action('houzez_before_register');

        houzez_google_recaptcha_callback();

        if($enable_password == 'yes' ) {
            $user_password = $user_pass;
        } else {
            $user_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
        }

        $user_id = wp_create_user( $usermane, $user_password, $email );

        if ( is_wp_error($user_id) ) {
            echo json_encode( array( 'success' => false, 'msg' => $user_id ) );
            wp_die();
        } else {

            wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ) );

            if( $enable_password =='yes' ) {
                echo json_encode( array( 'success' => true, 'msg' => esc_html__('Your account was created and you can login now!', 'houzez-login-register') ) );
            } else {
                echo json_encode( array( 'success' => true, 'msg' => esc_html__('An email with the generated password was sent!', 'houzez-login-register') ) );
            }

            update_user_meta( $user_id, 'first_name', $firstname);
            update_user_meta( $user_id, 'last_name', $lastname);


            if( $user_role == 'houzez_agency' ) {
                update_user_meta( $user_id, 'fave_author_phone', $phone_number);
            } else {
                update_user_meta( $user_id, 'fave_author_mobile', $phone_number);
            }
 
            $user_as_agent = houzez_option('user_as_agent');

            if( $user_as_agent == 'yes' ) {

                if( !empty($firstname) && !empty($lastname) ) {
                    $usermane = $firstname.' '.$lastname;
                }

                if ($user_role == 'houzez_agent' || $user_role == 'author') {
                    houzez_register_as_agent($usermane, $email, $user_id, $phone_number);

                } else if ($user_role == 'houzez_agency') {
                    houzez_register_as_agency($usermane, $email, $user_id, $phone_number);
                }
            }
            houzez_wp_new_user_notification( $user_id, $user_password, $phone_number );

            do_action('houzez_after_register', $user_id);
        }
        wp_die();

    }
}

/*-----------------------------------------------------------------------------------*/
// Register
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_houzez_social_create_account', 'houzez_social_create_account' );

if( !function_exists('houzez_social_create_account') ) {
    function houzez_social_create_account() {
        
        check_ajax_referer('houzez_social_register_nonce', 'houzez_social_register_security');

        $allowed_html = array();

        $usermane          = trim( sanitize_text_field( wp_kses( $_POST['username'], $allowed_html ) ));
        $email             = trim( sanitize_text_field( wp_kses( $_POST['useremail'], $allowed_html ) ));
        $term_condition    = isset( $_POST['term_condition'] ) ? wp_kses( $_POST['term_condition'], $allowed_html ) : "off";
        $fid = isset( $_POST['id'] ) ? $_POST['id'] : "";
        
        do_action('houzez_before_social_create_account');

        $user_roles = array ( 'houzez_agency', 'houzez_agent', 'houzez_buyer', 'houzez_seller', 'houzez_owner', 'houzez_manager' );

        $user_role = get_option( 'default_role' );

        if( $user_role == 'administrator' ) {
            $user_role = 'subscriber';
        }

        
        if( isset( $_POST['role'] ) && $_POST['role'] != '' && in_array( $_POST['role'], $user_roles ) ) {
            $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;
        } else {
            $user_role = $user_role;
        }

        if( houzez_option('header_register') != 1 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Access denied.', 'houzez-login-register') ) );
            wp_die();
        }

        if( get_option('users_can_register') != 1 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Access denied.', 'houzez-login-register') ) );
            wp_die();
        }


        $term_condition = ( $term_condition == 'on') ? true : false;

        if( !$term_condition ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('You need to agree with terms & conditions.', 'houzez-login-register') ) );
            wp_die();
        }

        if( empty( $usermane ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username field is empty.', 'houzez-login-register') ) );
            wp_die();
        }
        if( strlen( $usermane ) < 3 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Minimum 3 characters required', 'houzez-login-register') ) );
            wp_die();
        }
        if (preg_match("/^[0-9A-Za-z_]+$/", $usermane) == 0) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username (do not use special characters or spaces)!', 'houzez-login-register') ) );
            wp_die();
        }
        if( username_exists( $usermane ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('This username is already registered.', 'houzez-login-register') ) );
            wp_die();
        }
        if( empty( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The email field is empty.', 'houzez-login-register') ) );
            wp_die();
        }

        if( email_exists( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('This email address is already registered.', 'houzez-login-register') ) );
            wp_die();
        }

        if( !is_email( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid email address.', 'houzez-login-register') ) );
            wp_die();
        }


        $user_password = wp_generate_password( $length=12, $include_standard_special_chars=false );

        $user_id = wp_create_user( $usermane, $user_password, $email );

        if ( is_wp_error($user_id) ) {
            echo json_encode( array( 'success' => false, 'msg' => $user_id ) );
            wp_die();
        } else {

            wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ) );

            update_user_meta( $user_id, 'houzez_user_facebook_id', $fid);
            update_option( 'houzez_user_facebook_id_'.$fid, $email );

            if( $user_role == 'houzez_agency' ) {
                update_user_meta( $user_id, 'fave_author_phone', $phone_number);
            } else {
                update_user_meta( $user_id, 'fave_author_mobile', $phone_number);
            }
 
            $user_as_agent = houzez_option('user_as_agent');

            if( $user_as_agent == 'yes' ) {

                if( !empty($firstname) && !empty($lastname) ) {
                    $usermane = $firstname.' '.$lastname;
                }

                if ($user_role == 'houzez_agent' || $user_role == 'author') {
                    houzez_register_as_agent($usermane, $email, $user_id, $phone_number);

                } else if ($user_role == 'houzez_agency') {
                    houzez_register_as_agency($usermane, $email, $user_id, $phone_number);
                }
            }
            houzez_wp_new_user_notification( $user_id, $user_password, $phone_number );

            echo json_encode( array( 
                'success' => true,
                'redirect_to' => esc_url($_POST['redirect_to']),
                'msg' => esc_html__('Login successful, redirecting...', 'houzez-login-register') 
            ) );

            do_action('houzez_after_social_create_account', $user_id);

            wp_die();
        }
        wp_die();

    }
}

add_action('houzez_after_social_create_account', 'houzez_social_auto_login');
if( ! function_exists('houzez_social_auto_login') ) {
    function houzez_social_auto_login($user_id) {

        $fid = get_user_meta( $user_id, 'houzez_user_facebook_id', true);
        $fb_info = get_option('houzez_user_facebook_info_'.$fid);
        //$username = houzez_generate_unique_username( $username );

        $picture_url = isset($fb_info['picture_url']) ? $fb_info['picture_url'] : '';
        $first_name = isset($fb_info['first_name']) ? $fb_info['first_name'] : '';
        $last_name = isset($fb_info['last_name']) ? $fb_info['last_name'] : '';
        $display_name = $first_name.' '.$last_name;

        wp_update_user(array('ID' => $user_id, 'display_name' => $display_name, 'first_name' => $first_name, 'last_name' => $last_name));
        update_user_meta($user_id, 'fave_author_custom_picture', $picture_url);

        if( ! empty($fid) ) {
            wp_set_current_user($user_id); // Set the current user to the new user
            wp_set_auth_cookie($user_id); // Set the auth cookie for the new user
        }

        return true;
    }
}

/*-----------------------------------------------------------------------------------*/
// Link Account
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_houzez_link_account', 'houzez_link_account' );

if( !function_exists('houzez_link_account') ) {
    function houzez_link_account() {
        
        check_ajax_referer('link_account_nonce', 'link_account_security');

        $allowed_html = array();

        $username = wp_kses( $_POST['lusername'], $allowed_html );
        $password = isset( $_POST['lpassword'] ) ? $_POST['lpassword'] : "";
        $id = isset( $_POST['lid'] ) ? $_POST['lid'] : "";

        if( empty( $username ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username or email field is empty.', 'houzez-login-register') ) );
            wp_die();
        }
        if( empty( $password ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The password field is empty.', 'houzez-login-register') ) );
            wp_die();
        }
        if( !username_exists( $username ) && !email_exists($username)) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username or email', 'houzez-login-register') ) );
            wp_die();
        }


        wp_clear_auth_cookie();

        $remember = ($remember == 'on') ? true : false;

        if(is_email($username)) {
            $user = get_user_by( 'email', $username );
            $username = $user->user_login;
        }

        $creds = array();
        $creds['user_login'] = $username;
        $creds['user_password'] = $password;
        $creds['remember'] = $remember;
        $user = wp_signon( $creds, false );


        if ( is_wp_error( $user ) ) {

            $error_code = $user->get_error_code();

            if( $error_code == 'incorrect_password' ) {

                echo json_encode( array(
                    'success' => false,
                    'msg' => sprintf( wp_kses(__('The password you entered for the username <strong>%s</strong> is incorrect.', 'houzez-login-register'), $allowed_html_array), $username )
                ) );

            } else {

                echo json_encode( array(
                    'success' => false,
                    'msg' => $user->get_error_message()
                ) );

            }
            
            wp_die();
        } else {

            $user_id = $user->ID;
            $user_email = $user->user_email;
            $email_verified = get_user_meta( $user_id, 'houzez_email_verified', true );
            update_option( 'houzez_user_facebook_id_'.$id, $user_email );
            update_user_meta( $user_id, 'houzez_user_facebook_id', $id);

            if ( metadata_exists( 'user', $user_id, 'houzez_email_verified' ) && !$email_verified ) {
                echo json_encode( array(
                    'success' => false,
                    'msg' => esc_html__( 'Please verify your email address before logging in. A verification email has been sent to your email address.', 'houzez-login-register' )
                ) );
                wp_logout();
                wp_die();
            } else {

                wp_set_current_user ( $user->ID ); // Set the current user detail
                echo json_encode( array( 
                    'success' => true,
                    'redirect_to' => esc_url($_POST['redirect_to']),
                    'msg' => esc_html__('Login successful, redirecting...', 'houzez-login-register') 
                ) );
            }

            do_action('houzez_after_login');

        }
        wp_die();
    }
}


/*-----------------------------------------------------------------------------------*/
// Register
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_houzez_agency_agent', 'houzez_agency_agent' );

if( !function_exists('houzez_agency_agent') ) {
    function houzez_agency_agent() {

        check_ajax_referer('houzez_agency_agent_ajax_nonce', 'houzez-security-agency-agent');

        $allowed_html = array();

        $username       = trim( sanitize_text_field( wp_kses( $_POST['aa_username'], $allowed_html ) ));
        $email          = sanitize_email( $_POST['aa_email'] );
        $firstname      = trim( sanitize_text_field( wp_kses( $_POST['aa_firstname'], $allowed_html ) ));
        $lastname       = trim( sanitize_text_field( wp_kses( $_POST['aa_lastname'], $allowed_html ) ));
        $agent_agency   = trim( sanitize_text_field( wp_kses( $_POST['agency_id'], $allowed_html ) ));
        $agency_ids_cpt   = $_POST['agency_ids_cpt'];
        $agency_id_cpt   = trim( sanitize_text_field( wp_kses( $_POST['agency_id_cpt'], $allowed_html ) ));
        $user_password  = trim( sanitize_text_field( wp_kses( $_POST['aa_password'], $allowed_html ) ));
        $aa_notification   = isset($_POST['aa_notification']) ? $_POST['aa_notification'] : false;

        $user_role = 'houzez_agent';

        $aa_notification = ( $aa_notification == 'on') ? true : false;


        if( empty( $username ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username field is empty.', 'houzez-login-register') ) );
            wp_die();
        }
        if( username_exists( $username ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('This username is already registered.', 'houzez-login-register') ) );
            wp_die();
        }
        if( strlen( $username ) < 3 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Minimum 3 characters required', 'houzez-login-register') ) );
            wp_die();
        }
        if (preg_match("/^[0-9A-Za-z_]+$/", $username) == 0) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username (do not use special characters or spaces)!', 'houzez-login-register') ) );
            wp_die();
        }
        if( empty( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The email field is empty.', 'houzez-login-register') ) );
            wp_die();
        }

        if( email_exists( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('This email address is already registered.', 'houzez-login-register') ) );
            wp_die();
        }

        if( !is_email( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid email address.', 'houzez-login-register') ) );
            wp_die();
        }

        if( empty( $user_password ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The passowrd field is empty.', 'houzez-login-register') ) );
            wp_die();
        }

        $user_id = wp_create_user( $username, $user_password, $email );

        if ( is_wp_error($user_id) ) {
            echo json_encode( array( 'success' => false, 'msg' => $user_id ) );
            wp_die();
        } else {

            $update_args = array(
                'ID' => $user_id,
                'role' => $user_role,
                'first_name' => $firstname,
                'last_name' => $lastname
            );
            wp_update_user( $update_args );

            update_user_meta( $user_id, 'fave_agent_agency', $agent_agency) ; // used for get user created by agency

            echo json_encode( array( 'success' => true, 'msg' => esc_html__('Agent account created!', 'houzez-login-register') ) );

            $user_as_agent = houzez_option('user_as_agent');

            if( $user_as_agent == 'yes' ) {

                $agent_category = isset( $_POST['agent_category'] ) ? sanitize_text_field( $_POST['agent_category'] ) : '';
                $agent_city = isset( $_POST['agent_city'] ) ? sanitize_text_field( $_POST['agent_city'] ) : '';

                houzez_register_agency_agent($username, $email, $user_id, $agency_id_cpt, $agency_ids_cpt, $agent_agency, $firstname, $lastname, $agent_category, $agent_city);
            }

            if($aa_notification) {
                houzez_wp_new_user_notification( $user_id, $user_password );
            }
        }
        wp_die();

    }
}

if( !function_exists('houzez_update_agency_user_agent_02') ) {
    function houzez_update_agency_user_agent_02($agency_user_agent_id, $firstname, $lastname, $useremail, $agent_category, $agent_city)
    {
        if (!empty($firstname) || !empty($lastname) || !empty($agency_user_agent_id) ) {
            $agr = array(
                'ID' => $agency_user_agent_id,
                'post_title' => $firstname . ' ' . $lastname
            );
            $post_id = wp_update_post($agr);

            if( ! empty( $agent_category ) ) {
                $agent_category = intval($agent_category);
                wp_set_object_terms( $post_id, $agent_category, 'agent_category' );
            } else {
                wp_set_object_terms( $post_id, '', 'agent_category' );
            }
            if( ! empty( $agent_city ) ) {
                $agent_city = intval($agent_city);
                wp_set_object_terms( $post_id, $agent_city, 'agent_city' );
            } else {
                wp_set_object_terms( $post_id, '', 'agent_city' );
            }
        }
        update_post_meta( $post_id, 'fave_agent_email', $useremail );
    }
}

/*-----------------------------------------------------------------------------------*/
// New register user notification
/*-----------------------------------------------------------------------------------*/
if( !function_exists('houzez_wp_new_user_notification') ) {

    function houzez_wp_new_user_notification( $user_id, $randonpassword = '', $phone_number = '' ) {

        $user = new WP_User( $user_id );

        $user_login = stripslashes( $user->user_login );
        $user_email = stripslashes( $user->user_email );

        // Send notification to admin
        if( houzez_option('nru_admin_email', 1) != 0 ) {
            $args = array(
                'user_login_register' => $user_login,
                'user_email_register' => $user_email,
                'user_phone_register' => $phone_number
            );
            houzez_register_email_type( get_option('admin_email'), 'admin_new_user_register', $args );
        }


        // Return if password in empty
        if ( empty( $randonpassword ) ) {
            return;
        }

        // Send notification to registered user
        $args = array(
            'user_login_register'  =>  $user_login,
            'user_email_register'  =>  $user_email,
            'user_pass_register'   => $randonpassword,
            'user_phone_register'  => $phone_number,
        );
        houzez_register_email_type( $user_email, 'new_user_register', $args );

    }
}

//add_action( 'houzez_after_register', 'houzez_email_verification', 10, 1 );
if( ! function_exists('houzez_email_verification') ) {
    function houzez_email_verification($user_id) {

        $user = new WP_User( $user_id );
        $user_email = stripslashes( $user->user_email );

        // Send verification email
        $email_verification_token = md5( time() . $user_id . $user_email );
        update_user_meta( $user_id, 'houzez_email_verification_token', $email_verification_token );
    
        // Set email_verified to false
        update_user_meta( $user_id, 'houzez_email_verified', false );

        $template = houzez_get_template_link('template/template-login.php');

        $email_verification_link = $template . '?verrify-email='.intval($user_id).'&token=' . $email_verification_token;

        $args = array(
            'email_verification_link'  => '<a href="'.esc_url($email_verification_link).'">'.$email_verification_link.'</a>',
        );
        houzez_register_email_type( $user_email, 'user_register_verification', $args );

    }
}

add_action( 'wp_ajax_nopriv_houzez_reset_password', 'houzez_reset_password' );
add_action( 'wp_ajax_houzez_reset_password', 'houzez_reset_password' );
if( !function_exists('houzez_reset_password') ) {
    function houzez_reset_password() {
        check_ajax_referer('fave_resetpassword_nonce', 'security');

        $allowed_html = array();
        $user_login = wp_kses( $_POST['user_login'], $allowed_html );

        if ( empty( $user_login ) ) {
            echo json_encode(array( 'success' => false, 'msg' => esc_html__('Enter a username or email address.', 'houzez-login-register') ) );
            wp_die();
        }

        if ( strpos( $user_login, '@' ) ) {
            $user_data = get_user_by( 'email', trim( $user_login ) );
            if ( empty( $user_data ) ) {
                echo json_encode(array('success' => false, 'msg' => esc_html__('There is no user registered with that email address.', 'houzez-login-register')));
                wp_die();
            }
        } else {
            $login = trim( $user_login );
            $user_data = get_user_by('login', $login);

            if ( !$user_data ) {
                echo json_encode(array( 'success' => false, 'msg' => esc_html__('Invalid username', 'houzez-login-register') ) );
                wp_die();
            }
        }

        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        $key = get_password_reset_key( $user_data );

        if ( is_wp_error( $key ) ) {
            echo json_encode(array( 'success' => false, 'msg' => $key ) );
            wp_die();
        }



        $message = esc_html__('Someone has requested a password reset for the following account:', 'houzez-login-register' ) . "\r\n\r\n";
        $message .= network_home_url( '/' ) . "\r\n\r\n";
        $message .= sprintf(esc_html__('Username: %s', 'houzez-login-register'), $user_login) . "\r\n\r\n";
        $message .= esc_html__('If this was a mistake, just ignore this email and nothing will happen.', 'houzez-login-register') . "\r\n\r\n";
        $message .= esc_html__('To reset your password, visit the following address:', 'houzez-login-register') . "\r\n\r\n";
        $message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login'). "\r\n";

        if ( is_multisite() )
            $blogname = $GLOBALS['current_site']->site_name;
        else
            /*
             * The blogname option is escaped with esc_html on the way into the database
             * in sanitize_option we want to reverse this for the plain text arena of emails.
             */
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

            $title = sprintf( esc_html__('[%s] Password Reset', 'houzez-login-register'), $blogname );

        /**
         * Filter the subject of the password reset email.
         *
         * @since 2.8.0
         * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
         *
         * @param string  $title      Default email title.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

        /**
         * Filter the message body of the password reset mail.
         *
         * @since 2.8.0
         * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
         *
         * @param string  $message    Default mail message.
         * @param string  $key        The activation key.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */

        $message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
        $headers = 'From: No Reply <noreply@'.$_SERVER['HTTP_HOST'].'>' . "\r\n";
        if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message, $headers ) ) {
            echo json_encode(array('success' => false, 'msg' => esc_html__('The email could not be sent.', 'houzez-login-register') . "<br />\n" . esc_html__('Possible reason: your host may have disabled the mail() function.', 'houzez-login-register')));
            wp_die();
        } else {
            echo json_encode(array('success' => true, 'msg' => esc_html__('Check your email', 'houzez-login-register') ));
            wp_die();
        }
        return true;


    }
}


/*-----------------------------------------------------------------------------------*/
// Save Front-end user as agency
/*-----------------------------------------------------------------------------------*/

if( !function_exists('houzez_register_as_agency') ) {

    function houzez_register_as_agency( $username, $email, $user_id, $phone_number = null ) {
        // Create post object
        $args = array(
            'post_title'    => $username,
            'post_type' => 'houzez_agency',
            'post_status'   => 'publish'
        );

        // Insert the post into the database
        $post_id =  wp_insert_post( $args );
        update_post_meta( $post_id, 'houzez_user_meta_id', $user_id);  // used when agent custom post type updated
        update_user_meta( $user_id, 'fave_author_agency_id', $post_id);
        update_post_meta( $post_id, 'fave_agency_email', $email) ;
        update_post_meta( $post_id, 'fave_agency_phone', $phone_number);

        if( houzez_option('realtor_visible', 0) ) {
            update_post_meta( $post_id, 'fave_agency_visible', 1);
        }
    }
}

/*-----------------------------------------------------------------------------------*/
// Save Front-end user as agent
/*-----------------------------------------------------------------------------------*/

if( !function_exists('houzez_register_as_agent') ) {

    function houzez_register_as_agent( $username, $email, $user_id, $mobile_num = null, $image_url = null ) {

        // Create post object
        $args = array(
            'post_title'    => $username,
            'post_type' => 'houzez_agent',
            'post_status'   => 'publish'
        );

        // Insert the post into the database
        $post_id =  wp_insert_post( $args );
        update_post_meta( $post_id, 'houzez_user_meta_id', $user_id);  // used when agent custom post type updated
        update_user_meta( $user_id, 'fave_author_agent_id', $post_id);
        update_post_meta( $post_id, 'fave_agent_email', $email);
        update_post_meta( $post_id, 'fave_agent_mobile', $mobile_num);

        if( houzez_option('realtor_visible', 0) ) {
            update_post_meta( $post_id, 'fave_agent_visible', 1);
        }

        if( !empty($image_url) ) {
            houzez_set_image_from_url($post_id, $image_url);
        }

    }
}

/*-----------------------------------------------------------------------------------*/
// Upload image from url and set for post
/*-----------------------------------------------------------------------------------*/

if( !function_exists('houzez_set_image_from_url') ) {
    function houzez_set_image_from_url($post_id, $image_url) {

        include_once( ABSPATH . 'wp-admin/includes/image.php' );
        
        $response = wp_remote_get($image_url, array( 'timeout' => 8 ) );
        if( !is_wp_error( $response ) ){
         $bits = wp_remote_retrieve_body( $response );
         $filename = strtotime("now").'_'.uniqid().'.jpg';
         $upload = wp_upload_bits( $filename, null, $bits );
         $data['guid'] = $upload['url'];
         $data['post_mime_type'] = 'image/jpeg';
         $attach_id = wp_insert_attachment( $data, $upload['file'], 0 );
         $profile_attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
         wp_update_attachment_metadata( $attach_id, $profile_attach_data );
         
         update_post_meta( $post_id, '_thumbnail_id', $attach_id );
        }

    }
}

/*-----------------------------------------------------------------------------------*/
// Save Agency Agent
/*-----------------------------------------------------------------------------------*/

if( !function_exists('houzez_register_agency_agent') ) {

    function houzez_register_agency_agent( $username, $email, $user_id, $agency_id_cpt, $agency_ids_cpt, $agent_agency, $firstname, $lastname, $agent_category, $agent_city ) {

        if( !empty($firstname) || !empty($lastname) ) {
            $username = $firstname.' '.$lastname;
        }
        // Create post object
        $args = array(
            'post_title'    => $username,
            'post_type' => 'houzez_agent',
            'post_status'   => 'publish'
        );

        // Insert the post into the database
        $post_id =  wp_insert_post( $args );
        update_post_meta( $post_id, 'houzez_user_meta_id', $user_id);  // used when agent custom post type updated
        update_user_meta( $user_id, 'fave_author_agent_id', $post_id) ;
        update_user_meta( $user_id, 'fave_author_agency_id', $agency_id_cpt) ;
        update_user_meta( $user_id, 'fave_author_company', get_the_title($agency_id_cpt)) ;
        update_user_meta( $user_id, 'fave_agent_agency', $agent_agency) ; // used for get user created by agency
        //update_user_meta( $user_id, 'fave_author_agency_id', $agent_agency) ; // used for get user created by agency
        update_post_meta( $post_id, 'fave_agent_email', $email) ;
        update_post_meta( $post_id, 'fave_agent_company', get_the_title($agency_id_cpt)) ;
        update_post_meta( $post_id, 'fave_agent_agencies', $agency_id_cpt) ;
        delete_post_meta( $agency_id_cpt, 'fave_agency_cpt_agent' );

        array_push($agency_ids_cpt, $post_id);

        foreach ( $agency_ids_cpt as $agentID ) {
            if( !empty($agentID))
            add_post_meta( $agency_id_cpt, 'fave_agency_cpt_agent', $agentID );
        }

        if( ! empty( $agent_category ) ) {
            $agent_category = intval($agent_category);
            wp_set_object_terms( $post_id, $agent_category, 'agent_category' );
        } else {
            wp_set_object_terms( $post_id, '', 'agent_category' );
        }
        if( ! empty( $agent_city ) ) {
            $agent_city = intval($agent_city);
            wp_set_object_terms( $post_id, $agent_city, 'agent_city' );
        } else {
            wp_set_object_terms( $post_id, '', 'agent_city' );
        }
    }
}

if (!function_exists('houzez_register_email_type')) {
    function houzez_register_email_type( $email, $email_type, $args ) {

        $value_message = houzez_option('houzez_' . $email_type, '');
        $value_subject = houzez_option('houzez_subject_' . $email_type, '');

        $value_message = apply_filters('wpml_translate_single_string', $value_message, 'admin_texts_houzez_options', '[houzez_options]houzez_email_' . $value_message );
        $value_subject = apply_filters('wpml_translate_single_string', $value_subject, 'admin_texts_houzez_options', '[houzez_options]houzez_email_subject_' . $value_subject );

        houzez_register_emails_filter_replace( $email, $value_message, $value_subject, $args);
    }
}

if( !function_exists('houzez_register_emails_filter_replace')):
    function  houzez_register_emails_filter_replace( $email, $message, $subject, $args ) {
        $args ['website_url'] = get_option('siteurl');
        $args ['website_name'] = get_option('blogname');
        $args ['user_email'] = $email;
        $user = get_user_by( 'email', $email );
        $args ['username'] = $user->user_login;

        foreach( $args as $key => $val){
            $subject = str_replace( '%'.$key, $val, $subject );
            $message = str_replace( '%'.$key, $val, $message );
        }
        houzez_register_send_emails( $email, $subject, $message );
    }
endif;


if( !function_exists('houzez_register_send_emails') ):
    function houzez_register_send_emails( $user_email, $subject, $message ){
        
        $headers = array();
        $headers[] = 'From: No Reply <noreply@'.$_SERVER['HTTP_HOST'].'>';

        $enable_html_emails = houzez_option('enable_html_emails');
        $enable_email_header = houzez_option('enable_email_header');
        $enable_email_footer = houzez_option('enable_email_footer');

        if( $enable_html_emails != 0 ) {
            $headers[] = "Content-Type: text/html; charset=UTF-8";
        }
        $headers = apply_filters( "houzez_send_register_mails_header", $headers );// Filter for modify the header in child theme

        $email_head_logo = houzez_option('email_head_logo', false, 'url');
        $email_head_bg_color = houzez_option('email_head_bg_color');
        $email_foot_bg_color = houzez_option('email_foot_bg_color');
        $email_footer_content = houzez_option('email_footer_content');

        $social_1_icon = houzez_option('social_1_icon', false, 'url');
        $social_1_link = houzez_option('social_1_link');
        $social_2_icon = houzez_option('social_2_icon', false, 'url');
        $social_2_link = houzez_option('social_2_link');
        $social_3_icon = houzez_option('social_3_icon', false, 'url');
        $social_3_link = houzez_option('social_3_link');
        $social_4_icon = houzez_option('social_4_icon', false, 'url');
        $social_4_link = houzez_option('social_4_link');

        $message = stripslashes( $message );
        $message = wp_kses_post( wpautop( wptexturize( $message ) ) );

        $socials = '';
        if( !empty($social_1_icon) || !empty($social_2_icon) || !empty($social_3_icon) || !empty($social_4_icon) ) {
            $socials = '<div style="font-size: 0; text-align: center; padding-top: 20px;">';
            $socials .= '<p style="margin:0;margin-bottom: 10px; text-align: center; font-size: 14px; color:#777777;">'.esc_html__('Follow us on', 'houzez').'</p>';

            if( !empty($social_1_icon) ) {
                $socials .= '<a href="'.esc_url($social_1_link).'" style="margin-right: 5px"><img src="'.esc_url($social_1_icon).'" width="" height="" alt=""> </a>';
            }
            if( !empty($social_2_icon) ) {
                $socials .= '<a href="'.esc_url($social_2_link).'" style="margin-right: 5px"><img src="'.esc_url($social_2_icon).'" width="" height="" alt=""> </a>';
            }
            if( !empty($social_3_icon) ) {
                $socials .= '<a href="'.esc_url($social_3_link).'" style="margin-right: 5px"><img src="'.esc_url($social_3_icon).'" width="" height="" alt=""> </a>';
            }
            if( !empty($social_4_icon) ) {
                $socials .= '<a href="'.esc_url($social_4_link).'" style="margin-right: 5px"><img src="'.esc_url($social_4_icon).'" width="" height="" alt=""> </a>';
            }

            $socials .= '</div>';
        }

        if( $enable_email_header != 0 ) {
            $email_content = '<div style="text-align: center; background-color: ' . esc_attr($email_head_bg_color) . '; padding: 16px 0;">
                            <img src="' . esc_url($email_head_logo) . '" alt="logo">
                        </div>';
        }

        $email_content .= '<div style="background-color: #F6F6F6; padding: 30px;">
                            <div style="margin: 0 auto; width: 620px; background-color: #fff;border:1px solid #eee; padding:30px;">
                                <div style="font-family:\'Helvetica Neue\',\'Helvetica\',Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6em;display:block;max-width:600px;margin:0 auto;padding:0">
                                '.$message.'
                                </div>
                            </div>
                        </div>';

        if( $enable_email_footer != 0 ) {
            $email_content .= '<div style="padding-top: 30px; padding-bottom: 30px; font-family:\'Helvetica Neue\',\'Helvetica\',Helvetica,Arial,sans-serif;">

                            <div style="width: 640px; background-color: ' . $email_foot_bg_color . '; margin: 0 auto;">
                                ' . $email_footer_content . '
                            </div>
                            ' . $socials . '
                        </div>';
        }

        if( $enable_html_emails != 0 ) {
            $email_messages = $email_content;
        } else {
            $email_messages = $message;
        }

        @wp_mail(
            $user_email,
            $subject,
            $email_messages,
            $headers
        );
    };
endif;

// Add the 'status' column
//add_filter( 'manage_users_columns', 'houzez_add_user_status_column' );
function houzez_add_user_status_column( $columns ) {
    $columns['status'] = __( 'Status', 'houzez-login-register' );
    return $columns;
}

// Display the status value in the column
//add_filter( 'manage_users_custom_column', 'display_user_status_column', 10, 3 );
function display_user_status_column( $value, $column_name, $user_id ) {
    if ( 'status' !== $column_name ) {
        return $value;
    }

    $verified = get_user_meta( $user_id, 'houzez_email_verified', true );
    
    $activate_url = wp_nonce_url( admin_url( "admin-ajax.php?action=activate_user&user_id={$user_id}" ), 'activate_user_' . $user_id );
    $deactivate_url = wp_nonce_url( admin_url( "admin-ajax.php?action=deactivate_user&user_id={$user_id}" ), 'deactivate_user_' . $user_id );
    $activate_button = '<a href="' . $activate_url . '" class="button">' . __( 'Click to verify', 'houzez-login-register' ) . '</a>';
    $deactivate_button = '<a href="' . $deactivate_url . '" class="button">' . __( 'Deactivate', 'houzez-login-register' ) . '</a>';


    if ( metadata_exists( 'user', $user_id, 'houzez_email_verified' ) && $verified ) {
        $button = esc_html__('Verified', 'houzez-login-register');
    } else {
        $button = $activate_button;
    }
    //$button = ( $verified === 'active' ) ? $deactivate_button : $activate_button;

    return $button;
}

// Activate/deactivate user
//add_action( 'wp_ajax_activate_user', 'activate_user' );
//add_action( 'wp_ajax_deactivate_user', 'deactivate_user' );
function activate_user() {
    $user_id = isset( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : 0;
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    check_admin_referer( 'activate_user_' . $user_id );
    update_user_meta( $user_id, 'houzez_email_verified', true );
    wp_redirect( admin_url( 'users.php' ) );
    exit;
}
function deactivate_user() {
    $user_id = isset( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : 0;
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    check_admin_referer( 'deactivate_user_' . $user_id );
    update_user_meta( $user_id, 'houzez_email_verified', false );
    wp_redirect( admin_url( 'users.php' ) );
    exit;
}