<?php
/**
 * Render KYSS Meetings (event) view.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  
 */

require_once( 'load.php' );

global $hook;

$action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
$event_id = isset( $_GET['id'] ) ? $_GET['id'] : '';

// Add filter to the title.
$hook->add( 'kyss_title', function( $title ) {
	global $action, $event_id;
	
	$title .= ' &rsaquo; ';
	if ( $action == 'edit' || ( $action == 'add' && isset( $_GET['save'] ) && $_GET['save'] == 'true' ) )
		$title .= 'Modifica riunione';
	elseif ( $action == 'add' )
		$title .= 'Nuova riunione';
	elseif ( $action == 'veiw' )
		$title .= 'Dettagli riunione';
	else 
		$title .= 'Riunioni';

	return $title;
});

get_header();

get_sidebar();
?>

<?php
switch ( $action ) {
	case 'view':
		require( VIEWS . '/partials/_meeting_details.php' );
		break;
	case 'edit':
		require( VIEWS . '/partials/_meeting_form.php' );
		break;
	case 'add':
		require( VIEWS . '/partials/_meeting_form.php' );
		break;
	case 'list':	
	default:
		require( VIEWS . '/partials/_meeting_table.php' );
		break;
}
get_footer();