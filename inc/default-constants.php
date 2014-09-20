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
	if ( defined('DEBUG') )
		trigger_error( "The DEBUG function has been deprecated in KYSS 0.14.0. Use ENVIRONMENT instead." );
	if ( !defined('DEBUG') )
		/**
		 * Enable or disable debug.
		 *
		 * @since  0.8.0
		 * @var  bool
		 * @deprecated  0.14.0 Use ENVIRONMENT instead.
		 */
		define( 'DEBUG', false );

	/**
	 * Execution environment.
	 *
	 * @since  0.14.0
	 * @var  string
	 */
	if ( ! defined('ENVIRONMENT') )
		define( 'ENVIRONMENT', 'production' );

	if ( !defined('DAY_IN_SECONDS') )
		/**
		 * One day in seconds.
		 *
		 * @since  0.10.0
		 * @var  int
		 */
		define( 'DAY_IN_SECONDS', 86400 );

	if ( !defined( 'TEMP_DIR' ) )
		/**
		 * Temporary directory.
		 *
		 * @since  0.14.0
		 * @var  string
		 */
		define( 'TEMP_DIR', ABSPATH . 'tmp/' );

	if ( !defined( 'UPDATE_URI' ) )
		/**
		 * URI from which to fetch updates.
		 *
		 * @since  0.14.0
		 * @var string
		 */
		define( 'UPDATE_URI', 'http://deshack.net/kyss' );
}