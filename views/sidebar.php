<?php
/**
 * Render KYSS sidebar.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.11.0
 */
?>

<section id="sidebar" class="medium-2 columns">
	<ul class="side-nav">
		<li><a href="<?php echo get_option( 'siteurl' ); ?>">
			<span class="dashicons dashicons-dashboard"></span> Dashboard
		</a></li>
		<li>
			<a href="<?php echo get_option( 'siteurl' ); ?>/users.php">
				<span class="dashicons dashicons-groups"></span> Utenti
			</a>
		<?php if ( ( strpos( $_SERVER['PHP_SELF'], 'offices' ) || strpos( $_SERVER['PHP_SELF'], 'users' ) ) !== false ) : ?>
			<ul>
				<li><a href="<?php echo get_site_url( 'offices.php' ); ?>">
					<span class="dashicons dashicons-arrow-right"></span> Cariche
				</a></li>
			</ul>
		<?php endif; ?>
		</li>
		<li><a href="<?php echo get_option( 'siteurl' ); ?>/events.php">
			<span class="dashicons dashicons-calendar"></span> Eventi
		</a>
		<?php if ( ( strpos( $_SERVER['PHP_SELF'], 'events' ) ||
			strpos( $_SERVER['PHP_SELF'], 'meetings' ) ||
			strpos( $_SERVER['PHP_SELF'], 'courses' ) ||
			strpos( $_SERVER['PHP_SELF'], 'talks' ) ||
			strpos( $_SERVER['PHP_SELF'], 'lessons' ) ) !== false ) : ?>
			<ul>
				<li><a href="<?php echo get_option( 'siteurl' ); ?>/meetings.php">
					<span class="dashicons dashicons-arrow-right"></span> Riunioni
				</a></li>
				<li><a href="<?php echo get_option( 'siteurl' ); ?>/courses.php">
					<span class="dashicons dashicons-arrow-right"></span> Corsi
				</a></li>
				<li>
					<a href="<?php echo get_option( 'siteurl' ); ?>/other-events.php">
						<span class="dashicons dashicons-arrow-right"></span> Altri
					</a></li>
				<li><a href="<?php echo get_option( 'siteurl' ); ?>/talks.php">
					<span class="dashicons dashicons-arrow-right"></span> Talk
				</a></li>
			</ul>
		<?php endif; ?>
		</li>
		<li><a href="<?php echo get_option( 'siteurl' ); ?>/documents.php">
			<span class="dashicons dashicons-format-aside"></span> Documenti
		</a>
		<?php if ( ( strpos( $_SERVER['PHP_SELF'], 'documents' ) ||
			strpos( $_SERVER['PHP_SELF'], 'practices' ) ||
			strpos( $_SERVER['PHP_SELF'], 'reports' ) ||
			strpos( $_SERVER['PHP_SELF'], 'budgets' ) ) !== false ) : ?>
			<ul>
				<li><a href="<?php echo get_option( 'siteurl' ); ?>/practices.php">
					<span class="dashicons dashicons-arrow-right"></span> Pratiche
				</a></li>
				<li><a href="<?php echo get_option( 'siteurl' ); ?>/reports.php">
					<span class="dashicons dashicons-arrow-right"></span> Verbali
				</a></li>
				<li><a href="<?php echo get_option( 'siteurl' ); ?>/budgets.php">
					<span class="dashicons dashicons-arrow-right"></span> Bilanci
				</a></li>
			</ul>
		<?php endif; ?>
		</li>
		<li><a href="<?php echo get_option( 'siteurl' ); ?>/movements.php">
			<span class="dashicons dashicons-randomize"></span> Movimenti
		</a></li>
	</ul>
</section><!-- #sidebar -->

<main id="content" class="medium-10 columns" role="main">
