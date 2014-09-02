<?php
/**
 * Render KYSS Events view.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.11.0
 */

require_once( 'load.php' );

global $hook;

$action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
$id = isset( $_GET['id'] ) ? $_GET['id'] : '';

// Add filter to the title.
$hook->add( 'kyss_title', function( $title ) {
	global $action, $id;
	
	$title .= ' &rsaquo; ';
	if ( $action == 'edit' || ( $action == 'add' && isset( $_GET['save'] ) && $_GET['save'] == 'true' ) )
		$title .= 'Modifica evento';
	elseif ( $action == 'add' )
		$title .= 'Nuovo evento';
	elseif ( $action == 'view' )
		$title .= 'Dettagli evento';
	else
		$title .= 'Eventi';
		
	return $title;
});

get_header();

get_sidebar();
?>

<?php
require( VIEWS . '/partials/_event_table.php' );

get_footer();