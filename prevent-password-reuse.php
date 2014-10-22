<?php
/**
 * Plugin Name: Prevent Password Reuse
 * Plugin URI: http://www.simonbrown.com.au
 * Description: A Simple plugin that securely keeps track of all your users previous passwords and prevents their reuse.
 * Version: 1.1
 * Author: Simon Brown
 * Author URI: http://www.simonbrown.com.au
 * License: GPLv2 or later
 * Copyright: Simon Brown
 */

global $ppr_db_version;
$ppr_db_version = "1.1";

include( plugin_dir_path( __FILE__ ) . 'ppr-class.php');

register_activation_hook( __FILE__, 'ppr_install' );

//Lets Install the plugin
function ppr_install()
{
    global $wpdb;
    global $ppr_db_version;
    $table_name = $wpdb->prefix . "password_log";

    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    user mediumint(9) NOT NULL,
    password VARCHAR(256) NOT NULL,
    UNIQUE KEY id (id)
    );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( "ppr_db_version", $ppr_db_version );
}


//Lets check the password doesn't match the current password or any of our stored passwords on password reset
add_action( 'validate_password_reset', 'check_password_history', 10, 2 );
function check_password_history( $errors, $user )
{

    if ( !empty($_POST['pass1']) && $_POST['pass1'] == $_POST['pass2'] ) {

        $ppr = new ppr($user);

        //Check against current password
        if (! $ppr->check_current_password($_POST['pass1'])) {
            $errors->add( 'duplicate_password', __( '<strong>ERROR</strong>: Your password has previously been used, you must select a unqiue password' ) );
        }

        //Check against all previous passwords
        if (! $ppr->check_previous_passwords($_POST['pass1'])) {
            $errors->add( 'duplicate_password', __( '<strong>ERROR</strong>: Your password has previously been used, you must select a unqiue password' ) );
        }
    }
}

//Lets check the password doesn't match the current password or any of our stored passwords on profile update
add_filter('user_profile_update_errors', 'check_fields', 10, 3);
function check_fields($errors, $update, $user) {

    if ($update && isset($_POST['pass1']) && !empty($_POST['pass1']) && $_POST['pass1'] == $_POST['pass2']) {

        $ppr = new ppr($user);

        //Check against current password
        if (! $ppr->check_current_password($_POST['pass1'])) {
            $errors->add( 'duplicate_password', __( '<strong>ERROR</strong>: Your password has previously been used, you must select a unqiue password' ) );
        }

        //Check against all previous passwords
        if (! $ppr->check_previous_passwords($_POST['pass1'])) {
            $errors->add( 'duplicate_password', __( '<strong>ERROR</strong>: Your password has previously been used, you must select a unqiue password' ) );
        }

        if ( empty($errors->errors) ) {
            $ppr->store_old_password();
        }
    }
}

//Lets store the users old password each time a new one is saved
add_action( 'password_reset', 'store_hashed_password', 10, 2 );
function store_hashed_password( $user, $new_pass )
{
    $ppr = new ppr($user);
    $ppr->store_old_password();
}
