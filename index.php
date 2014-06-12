<?php
/**
 * Front to the KYSS application. This file doesn't do anything, but loads
 * load.php which actually loads the application.
 *
 * @package  KYSS
 */

// Used for debugging purposes.
// To be disabled before going into production.
ini_set('display_errors', '1');

if ( !isset($kyss_did_load) ) {
	$kyss_did_load = true;

	require_once( dirname(__FILE__) . '/load.php' );
}