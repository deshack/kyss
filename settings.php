<?php
/**
 * Used to set up common variables and include all
 * necessary files and libraries needed by KYSS.
 *
 * Allows for some configurations in config.php.
 *
 * @package  KYSS
 * @subpackage  Loader
 */

/**
 * Stores the location of the KYSS directory of functions, classes, and core content.
 *
 * @since  0.6.0
 */
define( 'INC', ABSPATH . 'inc/' );

/**
 * Stores the location of the KYSS directory of classes.
 *
 * @since  0.6.0
 */
define( 'CLASSES', INC . 'classes/' );

// Include files required for initialization.
require( INC . 'load.php' );
require( INC . 'default-constants.php' );

/**
 * This can't be directly globalized in version.php. When updating,
 * we're including version.php from another install and don't want
 * this value to be overridden if already set.
 */
global $kyss_version;
require( INC . 'version.php' );

// Disable magic quotes at runtime. Magic quotes are added using kyssdb later.
@ini_set( 'magic_quotes_runtime', 0 );
@ini_set( 'magic_quotes_sybase', 0 );

// Set initial default constants including DEBUG.
initial_constants();

// Check PHP version
check_php_version();

// Start loading timer.
//timer_start();

// Check if we're in debug mode.
debug_mode();

// Load early KYSS files.
require( INC . 'functions.php' );
require( CLASSES . 'kyss-error.php' );
require( CLASSES . 'user.php' );

// Include the kyssdb class.
load_kyssdb();

// Populate groups.
// We need to do it here, so groups are available in the install process.
populate_groups();

// Run the installer if KYSS is not installed.
kyss_not_installed();

// Load most of KYSS.
// ToDo: Require all needed files.
require(INC . 'formatting.php');
require(CLASSES . 'hook.php');

// Load pluggable functions.
require(INC . 'pluggable.php');

// Set internal encoding.
set_internal_encoding();

// Add magic quotes and set up $_REQUEST ( $_GET + $_POST ).
kyss_magic_quotes();

/**
 * KYSS Object
 *
 * @since  x.x.x
 */
//$GLOBALS['kyss'] = new KYSS();

// Set up current user.
//$GLOBALS['kyss']->init();