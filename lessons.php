<?php
/**
 * Render KYSS Lessons view.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.13.0
 */

require_once( 'load.php' );

global $hook;

$action = isset( $_GET['action'] ) ? $_GET['action'] : '';
$course = isset( $_GET['course'] ) ? $_GET['course'] : '';
$date = isset( $_GET['date'] ) ? $_GET['date'] : '';

if ( empty( $action ) || ( $action == 'edit' && (empty( $course ) || empty( $date ) ) ) )
	kyss_die( 'Non hai specificato la lezione che vuoi vedere.' );

$hook->add( 'kyss_title', function( $title ) {
	global $action;

	$title .= ' &rsaquo; ';
	switch( $action ) {
		case 'add':
			$title .= 'Nuova lezione';
			break;
		case 'edit':
			$title .= 'Modifica lezione';
			break;
	}
	return $title;
});

get_header();

get_sidebar();

switch( $action ) {
	case 'edit':
	case 'add':
		require( VIEWS . '/partials/_lesson_form.php' );
		break;
}

get_footer();