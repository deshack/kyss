<?php
/**
 * KYSS Upgrade API
 *
 * Most of the functions are pluggable and can be overridden.
 *
 * @package  KYSS
 * @subpackage  API
 */

/**
 * KYSS Administration API
 *
 * @todo  Add Administration API
 */
//require_once(ABSPATH . 'admin/inc/admin.php');

/**
 * KYSS Schema API
 *
 * @todo  Add Schema API
 */
require_once(ABSPATH . 'admin/inc/schema.php');

/**
 * Installs the application.
 *
 * {@internal Missing Long Description}
 *
 * @since  0.7.0
 * @todo  Create not existing functions.
 *
 * @param  string $title Site title.
 * @param  string $user_name First user's name.
 * @param  string $user_email First user's email.
 * @param  string $user_password Optional. First user's password. Defaults to a random password.
 * @return  array Array keys 'url', 'user_id', 'password', 'password_message'.
 */
function kyss_install( $title, $user_name, $user_surname, $user_email, $user_password = '' ) {
	// The following functions have to be defined in /admin/inc/schema.php
	//populate_options(); // Create KYSS options and set default values.
	//populate_roles(); // Create KYSS roles.
	
	// Define update_options() in /inc/options.php
	//update_option('sitename', $title);
	//update_option('admin_email', $user_email);
	
	$guessurl = kyss_guess_url();

	//update_option('siteurl', $guessurl);
	
	// Create default user.
	$user_password = trim($user_password);
	if ( empty( $user_password ) ) {
		$user_password = generate_password(); // Defaults to 10 char long, with special chars.
		$message = '<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you.';
		//$user_id = create_user($user_name, $user_password, $user_email);
	}

	$user = new KYSS_User($user_id);
	$user->set_role('owner');

	//install_defaults($user_id);
	
	/**
	 * Fires after the application is fully installed.
	 *
	 * @since  x.x.x
	 *
	 * @param  KYSS_User $user The site owner.
	 */
	//run_hook( 'kyss_install', $user );
	
	return array('url' => $guessurl, 'user_id' => $user_id, 'password' => $user_password, 'password_message' => $message );
}