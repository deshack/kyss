<?php
/**
 * Render KYSS Reports view.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  
 */

require_once( 'load.php' );

global $hook;

$action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
$prot = isset( $_GET['prot'] ) ? $_GET['prot'] : '';

// Add filter to the title.
$hook->add( 'kyss_title', function( $title ) {
	global $action, $prot;
	
	$title .= ' &rsaquo; ';
	if ( $action == 'edit' || ( $action == 'add' && isset( $_GET['save'] ) && $_GET['save'] == 'true' ) )
		$title .= 'Modifica verbale';
	elseif ( $action == 'add' )
		$title .= 'Nuovo verbale';
	elseif ( $action == 'veiw' )
		$title .= 'Dettagli verbale';
	else 
		$title .= 'Verbali';

	return $title;
});

get_header();

get_sidebar();
?>

<?php
switch ( $action ) {
	case 'view':
		require( VIEWS . '/partials/_report_details.php' );
		break;
	case 'edit':
		require( VIEWS . '/partials/_report_form.php' );
		break;
	case 'add':
		require( VIEWS . '/partials/_report_form.php' );
		break;
	case 'list':	
	default:
		require( VIEWS . '/partials/_report_table.php' );
		break;
}
get_footer();