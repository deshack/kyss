<?php
/**
 * Initialize KYSS.
 *
 * @package  KYSS
 * @subpackage  Bootstrap
 * @since  0.15.0
 */

namespace KYSS\Bootstrap;

/**
 * Include the autoloader.
 */
require 'Autoloader.php';

// Instantiate the autoloader.
$loader = new Autoloader;

// Register the autoloader.
$loader->register();

// Register the base directory for KYSS Core.
// TODO: Replace with ABSPATH constant.
$loader->add_namespace( 'KYSS', dirname( dirname( __FILE__ ) ) );