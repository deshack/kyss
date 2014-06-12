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
 * @param  bool $create Whether to create a new database. It will be passed to the kyssdb constructor. Default <false>.
 */
function load_kyssdb( $create = false ) {
	global $kyssdb;

	require_once( ABSPATH . KYSSINC . '/kyss-db.php' );

	if ( isset( $kyssdb ) )
		return;

	$kyssdb = new kyssdb( DB_HOST, DB_NAME, DB_USER, DB_PASS, $create );
}

/**
 * Redirect to the installer if KYSS is not installed.
 *
 * @since 0.6.0
 */
function kyss_not_installed() {
	if ( is_blog_installed() || false === strpos( $_SERVER['PHP_SELF'], 'install.php' ) || defined( 'KYSS_INSTALLING' ) )
		return;

	$link = kyss_guess_url() . '/admin/install.php';
	kyss_redirect( $link );
	die();
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
 * Add magic quotes to $_GET, $_POST, $_COOKIE, and $_SERVER.
 *
 * Also forces $_REQUEST to be $_GET + $_POST. If $_SERVER, $_COOKIE,
 * or $_ENV are needed, use those superglobals directly.
 *
 * @since  0.6.0
 */
function kyss_magic_quotes() {
	// If already slashed, strip.
	if ( get_magic_quotes_gpc() ) {
		$_GET = stripslashes_deep( $_GET );
		$_POST = stripslashes_deep( $_POST );
		$_COOKIE = stripslashes_deep( $_COOKIE );
	}

	// Escape with kyssdb.
	$_GET = add_magic_quotes( $_GET );
	$_POST = add_magic_quotes( $_POST );
	$_COOKIE = add_magic_quotes( $_COOKIE );
	$_SERVER = add_magic_quotes( $_SERVER );

	// Forse REQUEST to be GET + POST.
	$_REQUEST = array_merge( $_GET, $_POST );
}