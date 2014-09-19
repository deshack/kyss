<?php
/**
 * KYSS Update page.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.14.0
 */

require_once( '../load.php' );

get_header();

get_sidebar();

if ( $updates->has_updates() )
	echo $updates->get_updates();

get_footer();