<?php
/**
 * Bootstrap file for setting the ABSPATH constant and
 * loading the config.php file. The config.php file will
 * then load the settings.php file, which will then
 * set up the KYSS environment.
 *
 * If the config.php file is not found then an error
 * will be displayed asking the user to set up the file.
 *
 * @package  KYSS
 * @subpackage Setup
 */

/**
 * Absolute path to KYSS.
 *
 * @since  0.3.0
 * @var string
 */
define( 'ABSPATH', dirname(__FILE__) . '/' );

// Set which error levels are reported. Suppress notices.
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );

if ( file_exists( ABSPATH . 'config.php' ) ) {
	require_once( ABSPATH . 'config.php' );
} else {
	// A config file doesn't exist.
	define( 'INC', ABSPATH . 'inc/' );
	require_once( INC . 'load.php' );
	require_once( INC . 'functions.php' );
	require_once( INC . 'version.php' );
	require_once( INC . 'classes/kyss-error.php' );

	check_php_version();

	// PHP version is equal or higher than the required one
	
	$path = kyss_guess_url() . '/setup-config.php';

	// Die with an error message
	$die = '<h1>Missing config file</h1>';
	$die .= "<p>There doesn't seem to be a <code>config.php</code> file. I need this before we can get started.</p>";
	$die .= '<p>You can create a <code>config.php</code> file through a web interface or manually.</p>';
	$die .= '<p><a href="' . $path . '" class="button primary">Create a configuration file</a></p>';

	kyss_die( $die, 'KYSS Error' );
}