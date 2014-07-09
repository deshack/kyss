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
 * @global  hook
 *
 * @param  string $title Site title.
 * @param  string $user_name First user's name.
 * @param  string $user_surname First user's surname.
 * @param  string $user_email First user's email.
 * @param  string $user_password Optional. First user's password. Defaults to a random password.
 * @return  array Array keys 'url', 'user_id', 'password', 'password_message'.
 */
function kyss_install( $title, $user_name, $user_surname, $user_email, $user_password = '' ) {
	global $hook;

	// Create database tables.
	populate_db();
	// Populate first options.
	populate_options();
	// Create KYSS groups.
	populate_groups();
	
	// Update options based on user input.
	update_option('sitename', trim($title));
	update_option('admin_email', trim($user_email));
	
	$guessurl = kyss_guess_url();

	update_option('siteurl', trim($guessurl));
	
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
	 * @since  0.9.0
	 *
	 * @param  KYSS_User $user The site owner.
	 */
	$hook->run( 'kyss_install', $user );
	
	return array('url' => $guessurl, 'user_id' => $user_id, 'password' => $user_password, 'password_message' => $message );
}