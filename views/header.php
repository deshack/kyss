<?php
/**
 * Render KYSS page header.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since 0.11.0
 */

global $hook;

header( 'Content-Type: text/html; charset=utf-8' );
?>

<!doctype html>
<!--[if IE 8]>
<html class="ie8" lang="it">
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html lang="it">
<head>
	<title>KYSS &rsaquo; <?php echo get_option( 'sitename' ); ?></title>
	<?php kyss_css( 'kyss', true ); ?>

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
</nav>

<div class="row">