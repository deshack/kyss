<?php
/**
 * Render KYSS Document view.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  
 */

require_once( 'load.php' );

global $hook;

$action = isset( $_GET['action'] ) ? $_GET['action'] : 'list';
$prot = isset( $_GET['prot']) ? $_GET['prot'] : 'list';
$id = isset( $_GET['id']) ? $_GET['id'] : '';

// Add filter to the title
$hook->add( 'kyss_title', function( $title ) {
	global $action, $prot, $id;

	$title .= ' &rsaquo; ';
	if ( $action == 'edit' || ( $action == 'add' && isset( $_GET['save'] ) && $_GET['save'] == 'true' ) )
		$title .= 'Modifica documento';
	elseif ( $action == 'add' )
		$title .= 'Nuovo documento';
	elseif ( $action == 'view' )
		$title .= 'Dettagli documento';
	else
		$title .= 'Documenti';
		
	return $title;
});

get_header();

get_sidebar();
?>

<?php
require( VIEWS . '/partials/_document_table.php' );

get_footer();