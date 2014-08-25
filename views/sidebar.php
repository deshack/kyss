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
		<li><a href="<?php echo get_option( 'siteurl' ); ?>/events.php">Eventi</a>
			<ul><a href="<?php echo get_option( 'siteurl' ); ?>/meetings.php">Riunioni</a></ul>
			<ul><a href="<?php echo get_option( 'siteurl' ); ?>/courses.php">Corsi</a></ul>
			<ul><a href="<?php echo get_option( 'siteurl' ); ?>/other-events.php">Altri</a></ul>
		</li>
		<li><a href="<?php echo get_option( 'siteurl' ); ?>/talks.php">Documenti</a></li>
		<li><a href="<?php echo get_option( 'siteurl' ); ?>/documents.php">Documenti</a>
			<ul><a href="<?php echo get_option( 'siteurl' ); ?>/practices.php">Pratiche</a></ul>
			<ul><a href="<?php echo get_option( 'siteurl' ); ?>/reports.php">Verbali</a></ul>
			<ul><a href="<?php echo get_option( 'siteurl' ); ?>/budgets.php">Bilanci</a></ul>
		</li>
		<li><a href="<?php echo get_option( 'siteurl' ); ?>/movements.php">Movimenti</a></li>
		<li><a href="<?php echo get_option( 'siteurl' ); ?>/offices.php">Cariche</a></li>
		<li class="divider"></li>
		<li class="active"><a href="#">Link active</a></li>
	</ul>
</section><!-- #sidebar -->

<main id="content" class="medium-9 columns" role="main">