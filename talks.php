<?php
/**
 * Render KYSS Talks view.
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
		$title .= 'Modifica talk';
	elseif ( $action == 'add' )
		$title .= 'Nuovo talk';
	elseif ( $action == 'veiw' )
		$title .= 'Dettagli talk';
	else 
		$title .= 'Talk';

	return $title;
});

get_header();

get_sidebar();
?>

<?php
switch ( $action ) {
	case 'view':
		require( VIEWS . '/partials/_talk_details.php' );
		break;
	case 'edit':
		require( VIEWS . '/partials/_talk_form.php' );
		break;
	case 'add':
		require( VIEWS . '/partials/_talk_form.php' );
		break;
	case 'list':	
	default:
		require( VIEWS . '/partials/_talk_table.php' );
		break;
}
get_footer();