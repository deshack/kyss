<?php
/**
 * Handler for KYSS Subscription form submit through jQuery Ajax.
 *
 * @package  KYSS
 * @subpackage  Ajax
 * @since  0.13.0
 */

require_once( dirname( __FILE__ ) . '/load.php' );

$data['corso'] = $_POST['corso'];
$data['utente'] = $_POST['utente'];
$action = $_POST['action'];

switch( $action ) {
	case 'add':
		KYSS_Subscription::create( $data );
		break;
	case 'edit':
		KYSS_Subscription::update( $data );
		break;
	case 'delete':
		KYSS_Subscription::delete( $data );
		break;
}