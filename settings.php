<?php
/**
 * Used to set up common variables and include all
 * necessary files and libraries needed by KYSS.
 *
 * Allows for some configurations in config.php.
 *
 * @package  KYSS
 * @subpackage  Loader
 */

/**
 * Stores the location of the KYSS directory of functions, classes, and core content.
 *
 * @since  0.6.0
 */
define( 'INC', ABSPATH . 'inc/' );

/**
 * Stores the location of the KYSS directory of classes.
 *
 * @since  0.6.0
 */
define( 'CLASSES', INC . 'classes/' );

/**
 * Stores the location of the KYSS directory of views.
 *
 * @since  0.10.0
 */
define( 'VIEWS', ABSPATH . 'views/' );

/**
 * Stores the location of the KYSS directory of admin.
 *
 * @since  0.14.0
 */
define( 'ADMIN', ABSPATH . 'admin/' );

// Include files required for initialization.
require( INC . 'load.php' );
require( INC . 'default-constants.php' );

/**
 * This can't be directly globalized in version.php. When updating,
 * we're including version.php from another install and don't want
 * this value to be overridden if already set.
 */
global $kyss_version;
require( INC . 'version.php' );

// Disable magic quotes at runtime. Magic quotes are added using kyssdb later.
@ini_set( 'magic_quotes_runtime', 0 );
@ini_set( 'magic_quotes_sybase', 0 );

// Set initial default constants including DEBUG.
initial_constants();

// Check if we're in debug mode.
debug_mode();

// Load early KYSS files.
require( CLASSES . 'hook.php');
require( INC . 'functions.php' );
require( CLASSES . 'kyss-error.php' );
require( CLASSES . 'user.php' );

// Check PHP version
check_php_version();

// Include the kyssdb class.
load_kyssdb();

// Populate groups.
// We need to do it here, so groups are available in the install process.
KYSS_Groups::populate_defaults();

// Run the installer if KYSS is not installed.
kyss_not_installed();

// Load most of KYSS.
// TODO: Require all needed files.
require( CLASSES . 'filesystem.php' );
require( INC . 'formatting.php' );
require( CLASSES . 'kyss-pass.php' );
require( INC . 'options.php' );
require( INC . 'template.php' );
require( CLASSES . 'event.php' );
require( CLASSES . 'document.php' );
require( CLASSES . 'subscription.php' );
require( CLASSES . 'movement.php' );

// Load pluggable functions.
require(INC . 'pluggable.php');

// Load admin functions.
// TODO: Load only for admins.
require( ADMIN . 'inc/upgrade.php' );

// Set internal encoding.
set_internal_encoding();

// Execute the following code only if we are not installing.
if ( false === strpos( $_SERVER['PHP_SELF'], 'install.php' ) || ! defined( 'INSTALLING' ) ) {
	session_name( 'kyss_logged_in' );
	session_start();

	// Check for already logged in user.
	$login = isset( $_SESSION['login'] ) ? $_SESSION['login'] : '';

	if ( empty( $login ) && isset( $_COOKIE['kyss_login'] ) ) {
		$login = KYSS_Pass::verify_auth_cookie( $_COOKIE['kyss_login'] );
		// Set session variable.
		$_SESSION['login'] = $login;
		// Renew cookie lifetime.
		setcookie('kyss_login', $_COOKIE['kyss_login'], time() + 15 * DAY_IN_SECONDS );
	}

	if ( empty( $login ) && false === strpos( $_SERVER['PHP_SELF'], 'login.php' ) ) {
		trigger_error( (string) strpos( $_SERVER['PHP_SELF'], 'login.php' ) );
		$link = get_option( 'siteurl' ) . '/login.php';
		kyss_redirect( $link );
		die();
	}

	if ( ! empty( $login ) ) {
		$GLOBALS['current_user'] = KYSS_User::get_user_by( 'id', $login );
	}

	/**
	 * Check for updates.
	 *
	 * @todo  Do this only for admin users.
	 */
	if ( false === strpos( $_SERVER['PHP_SELF'], 'login' ) && ping( UPDATE_URI ) )
		check_updates();

	$hook->run( 'after_init' );
}