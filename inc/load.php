<?php
/**
 * Functions needed to load KYSS.
 *
 * @package  KYSS
 * @subpackage  Loader
 */

/**
 * Loads the database class.
 *
 * This function is used to load the database class file either at runtime
 * or by admin/setup-config.php. Globalize $kyssdb to ensure that it is defined
 * globally by the inline code in kyss-db.php.
 *
 * @since  0.3.0
 * @global  $kyssdb KYSS Database Object.
 *
 * @return null
 */
function load_kyssdb() {
	global $kyssdb;

	require_once( INC . 'classes/kyss-db.php' );

	if ( isset( $kyssdb ) )
		return;

	$kyssdb = new KYSS_DB( DB_HOST, DB_USER, DB_PASS, (defined('DB_NAME') ? DB_NAME : '') );
}

/**
 * Redirect to the installer if KYSS is not installed.
 *
 * @todo  Create function is_installed().
 *
 * @since 0.6.0
 */
function kyss_not_installed() {
	if ( is_installed() || false !== strpos( $_SERVER['PHP_SELF'], 'install.php' ) || defined( 'INSTALLING' ) )
		return;
	
	$link = kyss_guess_url() . '/admin/install.php';
	kyss_redirect( $link );
	die();
}

/**
 * Check if KYSS is installed.
 *
 * Looks at the database to find if there are KYSS tables.
 * If there are only some tables, this function raises an error.
 *
 * @since  0.9.0
 * @global  kyssdb
 *
 * @return  bool True if KYSS is installed, false otherwise.
 */
function is_installed() {
	global $kyssdb;

	// Returns a mysqli_result
	$present = $kyssdb->query( "SHOW TABLES" );
	// Database empty.
	if ( 0 == $present->num_rows )
		return false;

	$found = array();
	while ( $row = $present->fetch_row() ) {
		$found[] = $row[0];
	}
	unset( $present );

	// Database not empty, check against KYSS table names.
	$tables = $kyssdb->get_tables();
	$count = 0;
	foreach ( $tables as $table ) {
		foreach ( $found as $tbl ) {
			if ( $tbl == $table ) {
				$count++;
				break;
			}
		}
	}

	if ( $count == 0 )
		return false;
	elseif ( $count == count( $tables ) )
		return true;
	else
		kyss_die( '<h1>KYSS Installation Broken</h1><p>We have detected a broken installation of KYSS. Remove the tables prefixed by <code>kyss_</code> from your database.</p>' );
}

/**
 * Set internal encoding using mb_internal_encoding().
 *
 * In most cases the default internal encoding is latin1, which is of no use,
 * since we want to use the mb_ functions for utf-8 strings.
 *
 * @since  0.6.0
 */
function set_internal_encoding() {
	if ( function_exists('mb_internal_encoding') ) {
		if ( ! @mb_internal_encoding( 'UTF-8' ) )
			mb_internal_encoding( 'UTF-8' );
	}
}

/**
 * Set PHP error handling and handle KYSS debug mode.
 *
 * Uses `DEBUG` constant, that can be defined in config.php.
 *
 * @example
 * ```
 * <code>define( 'DEBUG', true );</code>
 * ```
 *
 * When `DEBUG` is true, all PHP notices are reported. KYSS will also display
 * notices, including one when a deprecated KYSS function, function argument,
 * or file is used. Deprecated code may be removed from a later version.
 *
 * It is strongly recommended that `DEBUG` is used only in development environment.
 *
 * All errors will be displayed and logged to log/kyss.log.
 *
 * `DEBUG` defaults to false.
 *
 * @access private
 * @since  0.8.0
 */
function debug_mode() {
	if ( DEBUG ) {
		error_reporting( E_ALL );
		ini_set( 'display_errors', 1);
		ini_set( 'log_errors', 1);
		ini_set( 'error_log', ABSPATH . 'log/kyss.log' );
	} else {
		error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
	}
}
