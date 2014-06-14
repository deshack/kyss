<?php
/**
 * Default constants and global variables that can be overridden, generally in config.php.
 *
 * @package  KYSS
 */

/**
 * Define initial KYSS constants.
 *
 * @see  debug_mode()
 *
 * @since  0.8.0
 */
function initial_constants() {
	// Add `define( 'DEBUG', true);` to config.php to enable display of notices during development.
	if ( !defined('DEBUG') )
		define( 'DEBUG', false );
}