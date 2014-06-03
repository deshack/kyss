<?php
/**
 * Front to the KYSS application. This file doesn't do anything, but loads
 * load.php which actually loads the application.
 *
 * @package  KYSS
 */

if ( !isset($kyss_did_load) ) {
	$kyss_did_load = true;

	require_once( dirname(__FILE__) . '/load.php' );
}