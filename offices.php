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
$slug = isset( $_GET['office'] ) ? $_GET['office'] : '';
$start = isset( $_GET['start'] ) ? $_GET['start'] : '';

// Add filter to the title.
$hook->add( 'kyss_title', function( $title ) {
	global $action;

	$title .= ' &rsaquo; ';
	if ( $action == 'edit' || ($action == 'add' && isset( $_GET['save'] ) && $_GET['save'] == 'true' ) )
		$title .= 'Modifica carica';
	elseif ( $action == 'add' )
		$title .= 'Nuova carica';
	elseif ( $action == 'view' )
		$title .= 'Dettagli carica';
	else
		$title .= 'Cariche';

	return $title;
} );

get_header();

get_sidebar();

switch( $action ) {
	case 'view':
		require( VIEWS . '/partials/_office_details.php' );
		break;
	case 'edit':
	case 'add':
		require( VIEWS . '/partials/_office_form.php' );
		break;
	case 'list':
	default:
		require( VIEWS . '/partials/_offices_table.php' );
		break;
}

get_footer();