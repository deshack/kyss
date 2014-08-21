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
	switch ( $action ) {
		case 'edit':
			$title .= 'Modifica ';
			$title .= (isset($user)) ? $user->nome . ' ' . $user->cognome : 'utente';
			break;
		case 'list':
		default:
			$title .= 'Utenti';
			break;
	}
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
	case 'list':
	default:
		require( VIEWS . '/partials/_users_table.php' );
		break;
}

get_footer();