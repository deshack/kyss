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
 */
function load_kyssdb() {
	global $kyssdb;

	require_once( ABSPATH . KYSSINC . '/kyss-db.php' );

	if ( isset( $kyssdb ) )
		return;

	$kyssdb = new kyssdb( DB_HOST, DB_NAME, DB_USER, DB_PASS );
}