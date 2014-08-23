<?php
/**
 * Render KYSS Users view.
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
	if ( ! empty( $id ) ) {
		$user = KYSS_User::get_user_by( 'id', $id );
	}

	$title .= ' &rsaquo; ';
	if ( $action == 'edit' || ( $action == 'add' && isset( $_GET['save']) && $_GET['save'] == 'true' ) )
		$title .= 'Modifica utente';
	elseif ( $action == 'add' )
		$title .= 'Nuovo utente';
	else
		$title .= 'Utenti';
	
	return $title;
});

get_header();

get_sidebar();

?>

<?php
switch( $action ) {
	case 'edit':
		require( VIEWS . '/partials/_user_form.php' );
		break;
	case 'add':
		require( VIEWS . '/partials/_user_form.php' );
		break;
	case 'list':
	default:
		require( VIEWS . '/partials/_users_table.php' );
		break;
}

get_footer();