<?php
/**
 * Front to the KYSS application. This file doesn't do anything, but loads
 * load.php which actually loads the application.
 *
 * @package  KYSS
 */

require_once( dirname(__FILE__) . '/load.php' );

get_header();

get_sidebar();

echo "index.php";

get_footer();