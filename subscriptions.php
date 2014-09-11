<?php
/**
 * KYSS Subscriptions views handler.
 *
 * @package  KYSS
 * @subpackage Views
 * @since  0.13.0
 */

require_once( 'load.php' );

if ( isset( $_POST['action'] ) )
	$action = $_POST['action'];

switch ( $action ) {
	case 'form':
		require_once( VIEWS . '/partials/_subscription_form.php' );
		break;
	case 'add':
		require_once( VIEWS . '/partials/_subscription_details.php' );
		break;
}