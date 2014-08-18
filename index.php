<?php
/**
 * Front to the KYSS application.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.1.0
 */

require_once( 'load.php' );

$hook->add( 'kyss_title', function( $title ) {
	return $title . ' &rsaquo; ' . get_option( 'sitename' );
});

get_header();

get_sidebar();

echo "index.php";

get_footer();