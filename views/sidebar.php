<?php
/**
 * Render KYSS sidebar.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.11.0
 */
?>

<section id="sidebar" class="medium-3 columns">
	<ul class="side-nav">
		<li><a href="<?php echo get_option( 'siteurl' ); ?>">Home</a></li>
		<li><a href="<?php echo get_option( 'siteurl' ); ?>/users.php">Utenti</a></li>
		<li class="divider"></li>
		<li class="active"><a href="#">Link active</a></li>
	</ul>
</section><!-- #sidebar -->

<main id="content" class="medium-9 columns" role="main">