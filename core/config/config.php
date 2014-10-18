<?php
/**
 * Configuration.
 *
 * Includes custom configuration constants and defines general configurations.
 *
 * @package  KYSS
 * @subpackage  Config
 * @since  0.4.0
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Application absolute path.
 *
 * @since  0.4.0
 * @var string
 */
if ( ! defined('ABSPATH') )
	define('ABSPATH', dirname( dirname( dirname(__FILE__) ) ) . '/' );

if ( file_exists( ABSPATH . 'config/kyss.php' ) )
	include_once ABSPATH . 'config/kyss.php';

/**
 * Application environment.
 *
 * Accepts 'development', 'test', 'production'.
 *
 * @since  0.14.0
 * @var string
 */
if ( ! defined('ENVIRONMENT') )
	define('ENVIRONMENT', 'production');

/**
 * Path to library files.
 *
 * @since  0.15.0
 * @var string
 */
define('PATH_LIBRARY', ABSPATH . 'core/lib/');

/**
 * Path to controller files.
 *
 * @since  0.15.0
 * @var string
 */
define('PATH_CONTROLLERS', ABSPATH . 'core/controllers/');

/**
 * Path to model files.
 *
 * @since  0.15.0
 * @var  string
 */
define('PATH_MODELS', ABSPATH . 'core/models/');

/**
 * Path to view files.
 *
 * @since  0.15.0
 * @var string
 */
define('PATH_VIEWS', ABSPATH . 'core/views/');

/**
 * Path to exception files.
 *
 * @since  0.15.0
 * @var  string
 */
define('PATH_EXCEPTIONS', ABSPATH . 'core/exceptions/');