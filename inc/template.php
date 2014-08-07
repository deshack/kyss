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
