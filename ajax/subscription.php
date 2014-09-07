<?php
/**
 * Handler for KYSS Subscription form submit through jQuery Ajax.
 *
 * @package  KYSS
 * @subpackage  Ajax
 * @since  0.13.0
 */

require_once( dirname( dirname( __FILE__ ) ) . '/load.php' );

$data['corso'] = $_POST['id'];
$data['utente'] = $_POST['utente'];

KYSS_Subscription::create( $data );