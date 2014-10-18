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
	$path = ABSPATH . 'core/lib/' . strtolower($class) . '.class.php';
	if ( file_exists( $path ) )
		include_once $path;
}

/**
 * Autoloader for controller classes.
 *
 * @since  0.15.0
 * 
 * @param  string $class Class name.
 */
function controller_autoloader( $class ) {
	$class = strtolower( str_replace('Controller', '', $class) );
	$path = PATH_CONTROLLERS . $class . '.php';
	if ( file_exists( $path ) )
		include_once $path;
}

/**
 * Autoloader for module classes.
 *
 * @since  0.15.0
 * 
 * @param  string $class Class name.
 */
function model_autoloader( $class ) {
	$path = PATH_MODELS . strtolower( $class ) . '.php';
	if ( file_exists( $path ) )
		include_once $path;
}

/**
 * Autoloader for exception classes.
 *
 * @since  0.15.0
 *
 * @param  string $class Class name.
 */
function exception_autoloader( $class ) {
	$class = strtolower( str_replace( 'Exception', '', $class ) );
	$path = PATH_EXCEPTIONS . $class . '.php';
	if ( file_exists( $path ) )
		include_once $path;
}

// Register autoloaders.
spl_autoload_register( 'lib_autoloader' );
spl_autoload_register( 'controller_autoloader' );
spl_autoload_register( 'model_autoloader' );
spl_autoload_register( 'exception_autoloader' );

// Start KYSS.
$kyss = new KYSS;