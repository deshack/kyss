<?php
/**
 * KYSS Installer
 *
 * @package  KYSS
 * @subpackage  Installer
 */

// Sanity check.
if ( false ) :
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Error: PHP is not running</title>
</head>
<body>
	<h1 id="logo">KYSS</h1>
	<h2>Error: PHP is not running</h2>
	<p>KYSS requires that your web server is running PHP. Your server does not have PHP installed, or PHP is turned off.</p>
</body>
</html>
<?php
endif;

/**
 * We are installing KYSS.
 *
 * @since  0.6.0
 * @var  bool
 */
define( 'INSTALLING', true );

/**
 * Load KYSS Bootstrap.
 */
require_once( dirname(dirname(__FILE__)) . '/load.php' );

/**
 * Load KYSS Administration Upgrade API.
 */
require_once( ABSPATH . 'admin/inc/upgrade.php');

/**
 * Load kyssdb.
 */
require_once( CLASSES . 'kyss-db.php' );

$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 0;

/**
 * Display install header.
 *
 * @since  0.6.0
 */
function install_header() {
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>KYSS &rsaquo; Installation</title>
	<?php kyss_css( 'install', true ); ?>
</head>
<body>
<div class="container">
	<h1 id="logo">KYSS</h1>
<?php
} // End install_header()

/**
 * Display install footer.
 *
 * @since  0.6.0
 */
function install_footer() {
?>
</div>
</body>
</html>
<?php
} // End install_footer()