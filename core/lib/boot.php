<?php
/**
 * KYSS Bootstrap.
 *
 * Loads configurations, defines autoloaders and instantiates
 * the `KYSS` class.
 *
 * @package  KYSS
 * @subpackage  Bootstrap
 * @since  0.15.0
 */

if ( file_exists( 'core/config/config.php' ) )
	/**
	 * Load application config.
	 */
	require 'core/config/config.php';

/**
 * Autoloader for library classes.
 *
 * @since  0.15.0
 * 
 * @param  string $class Class name
 */
function lib_autoloader( $class ) {
	@include_once ABSPATH . 'core/lib/' . strtolower($class) . '.class.php';
}

/**
 * Autoloader for controller classes.
 *
 * @since  0.15.0
 * 
 * @param  string $class Class name.
 */
function controller_autoloader( $class ) {
	$class = str_replace('Controller', '', $class);
	@include_once PATH_CONTROLLERS . strtolower($class) . '.php';
}

/**
 * Autoloader for module classes.
 *
 * @since  0.15.0
 * 
 * @param  string $class Class name.
 */
function model_autoloader( $class ) {
	include_once PATH_MODELS . strtolower($class) . '.php';
}

// Register autoloaders.
spl_autoload_register( 'lib_autoloader' );
spl_autoload_register( 'controller_autoloader' );
spl_autoload_register( 'model_autoloader' );

// Start KYSS.
$kyss = new KYSS;