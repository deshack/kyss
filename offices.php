<?php
/**
 * Render KYSS Offices view.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  
 */

require_once( 'load.php' );

global $hook;

$action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';

get_header();

get_sidebar();
?>

<?php
get_footer();