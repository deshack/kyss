<?php
/**
 * Handler for KYSS Lesson deletion through jQuery Ajax.
 *
 * @package  KYSS
 * @subpackage  Ajax
 * @since  0.13.0
 */

require_once( dirname( __FILE__ ) . '/load.php' );

KYSS_Lesson::delete( $_POST['corso'], $_POST['data'] );