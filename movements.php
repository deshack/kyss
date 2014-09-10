<?php
/**
 * Render KYSS Movements view.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  
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
		$title .= 'Modifica movimenti';
	elseif ( $action == 'add' )
		$title .= 'Nuovo movimento';
	elseif ( $action == 'veiw' )
		$title .= 'Dettagli movimento';
	else 
		$title .= 'Movimenti';

	return $title;
});

get_header();

get_sidebar();
?>

<?php
switch ( $action ) {
	case 'view':
		require( VIEWS . '/partials/_movement_details.php' );
		break;
	case 'edit':
	case 'add':
		require( VIEWS . '/partials/_movement_form.php' );
		break;
	case 'list':	
	default:
		require( VIEWS . '/partials/_movement_table.php' );
		break;
}
get_footer();