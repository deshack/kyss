<?php
/**
 * KYSS General Template.
 *
 * Holds a number of general template functions.
 *
 * @package KYSS
 * @subpackage  Template
 * @since  0.11.0
 */

// Add general-purpose meta tags to KYSS head.
global $hook;
$metas = array(
	'viewport_meta',
	'charset_meta'
);
foreach ( $metas as $meta )
	$hook->add( 'kyss_head', $meta );
unset( $metas );

/**
 * Display a noindex meta tag.
 *
 * Outputs a @link(noindex, http://en.wikipedia.org/wiki/Noindex) meta tag that tells
 * web robots not to index the page content. Typical usage is as a kyss_head callback.
 * @example
 * ```
 * $hook->add( 'kyss_head', 'no_robots' );
 * ```
 *
 * @since  0.11.0
 */
function no_robots() {
	echo '<meta name="robots" content="noindex,follow" />' . "\n";
}

/**
 * Display a viewport meta tag.
 *
 * Outputs a responsive meta tag to improve the experience on small-width devices.
 *
 * @since  0.11.0
 */
function viewport_meta() {
	echo '<meta name="viewport" content="width=device-width">' . "\n";
}

/**
 * Display a HTML5 charset meta tag.
 *
 * @since  0.11.0
 */
function charset_meta() {
	echo '<meta charset="utf-8">' . "\n";
}

/**
 * Display page header.
 *
 * @since  0.11.0
 */
function get_header() {
	require( VIEWS . 'header.php' );
}

/**
 * Display sidebar.
 *
 * @since  0.11.0
 */
function get_sidebar() {
	require( VIEWS . 'sidebar.php' );
}

/**
 * Display page footer.
 *
 * @since 0.11.0
 */
function get_footer() {
	require( VIEWS . 'footer.php' );
}