<?php
/**
 * Render KYSS Courses view.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.12.0
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
		$title .= 'Modifica corso';
	elseif ( $action == 'add' )
		$title .= 'Nuovo corso';
	elseif ( $action == 'veiw' )
		$title .= 'Dettagli corso';
	else 
		$title .= 'Corsi';

	return $title;
});

get_header();

get_sidebar();
?>

<?php
switch ( $action ) {
	case 'view':
		require( VIEWS . '/partials/_course_details.php' );
		break;
	case 'edit':
		require( VIEWS . '/partials/_course_form.php' );
		break;
	case 'add':
		require( VIEWS . '/partials/_course_form.php' );
		break;
	case 'list':	
	default:
		require( VIEWS . '/partials/_course_table.php' );
		break;
}
get_footer();