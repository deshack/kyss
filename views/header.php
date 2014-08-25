<?php
/**
 * Render KYSS page header.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since 0.11.0
 */

global $hook;

$title = 'KYSS';

/**
 * Filter page title.
 *
 * @since  0.11.0
 *
 * @param  string $title The default title.
 * @param  string $page The script currently being run.
 * @return  string The maybe filtered title.
 */
$title = $hook->run( 'kyss_title', $title, $_SERVER['PHP_SELF'] );

header( 'Content-Type: text/html; charset=utf-8' );
?>

<!doctype html>
<!--[if IE 8]>
<html class="ie8" lang="it">
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html lang="it">
<head>
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" id="dashicons" href="<?php echo get_site_url( 'lib/dashicons/css/dashicons.min.css' ); ?>" media="all" />
	<?php kyss_css( 'kyss', true ); ?>
	<script type="text/javascript" src="<?php echo get_site_url( 'lib/jquery/dist/jquery.min.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo get_site_url( 'lib/jquery-placeholder/jquery.placeholder.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo get_site_url( 'lib/jquery.cookie/jquery.cookie.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo get_site_url( 'lib/fastclick/lib/fastclick.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo get_site_url( 'lib/modernizr/modernizr.js' ); ?>"></script>
	<script type="text/javascript" src="<?php echo get_site_url( 'lib/foundation/js/foundation.min.js' ); ?>"></script>

	<?php
	/**
	 * Fires in the KYSS page header.
	 *
	 * @since  0.11.0
	 */
	$hook->run( 'kyss_head' );
	?>
</head>
<body>

<nav class="top-bar" data-topbar>
	<ul class="title-area">
		<li class="name">
			<h1><a href="<?php echo get_option( 'siteurl' ); ?>"><?php echo get_option( 'sitename' ); ?></a></h1>
		</li>
	</ul>
	<section class="top-bar-section">
		<ul class="right">
			<li class="has-form">
				<a href="<?php echo get_site_url( 'logout.php' ); ?>" class="button secondary">Logout</a>
			</li>
		</ul>
	</section>
</nav>

<div class="row">