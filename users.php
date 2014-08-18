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

// Add filter to the title.
$hook->add( 'kyss_title', function( $title ) {
	return $title . ' &rsaquo; Utenti';
});

get_header();

get_sidebar();

?>

<h1 class="page-title">Utenti</h1>

<?php

require( VIEWS . '/partials/_users_table.php' );

get_footer();