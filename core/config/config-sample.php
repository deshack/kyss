<?php
/**
 * Configuration.
 *
 * Defines constants for the database connection and application ABSPATH.
 *
 * @package  KYSS
 * @subpackage  Config
 * @since  0.4.0
 */

/**
 * Project URL.
 *
 * @since  0.15.0
 * @var string
 */
define('URL', 'http://www.example.com/');

/**
 * Database constants.
 *
 * These define the database host, name, username, and password.
 *
 * @since  0.4.0
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'kyss');
define('DB_PASS', 'kysspass');
define('DB_NAME', 'kyss');

/**
 * Path to view files.
 *
 * @since  0.15.0
 * @var string
 */
define('PATH_VIEWS', 'core/views/');

/**
 * Application environment.
 *
 * Accepts 'development', 'test', 'production'.
 *
 * @since  0.14.0
 * @var string
 */
define('ENVIRONMENT', 'development');

/**
 * Application absolute path.
 *
 * @since  0.4.0
 * @var string
 */
if ( ! defined('ABSPATH') )
	define('ABSPATH', dirname( dirname( dirname(__FILE__) ) ) . '/' );