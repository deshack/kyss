<?php
/**
 * KYSS Update page.
 *
 * @package  KYSS
 * @subpackage  Views
 * @since  0.14.0
 */

require_once( '../load.php' );

get_header();

get_sidebar();
?>

<h1>Aggiorna KYSS</h1>

<?php if ( ! isset( $_GET['series'] ) ) : ?>
	<?php if ( ! $updates->has_updates() ) : ?>
		<p class="lead">KYSS &egrave; aggiornato, non devi fare nulla!</p>
	<?php else : ?>
		<div class="row">
		<?php if ( $updates->has_old_update() ) : ?>
			<div class="medium-6 columns">
				<p class="lead">Aggiorna alla versione <?php echo $updates->get_old_update(); ?></p>
				<a class="button" href="<?php echo get_site_url( 'admin/update.php?series=old' ); ?>">Aggiorna</a>
			</div>
		<?php endif; ?>
		<?php if ( $updates->has_new_update() && ! $updates->has_old_update() ) : ?>
			<div class="medium-12 columns">
				<p class="lead">Aggiorna alla versione <?php echo $updates->get_new_update(); ?></p>
				<a class="button" href="<?php echo get_site_url( 'admin/update.php?series=latest' ); ?>">Aggiorna</a>
			</div>
		<?php elseif ( $updates->has_new_update() ) : ?>
			<div class="medium-6 columns">
				<p class="lead">Avanza alla nuova versione <?php echo $updates->get_new_update(); ?></p>
				<a class="button" href="<?php echo get_site_url( 'admin/update.php?series=latest' ); ?>">Avanza</a>
			</div>
		<?php endif; ?>
		</div>
	<?php endif; ?>
<?php elseif ( $_GET['series'] == 'old' ) : ?>

	<div id="updating">
		<?php $updates->update( 'old' ); ?>
	</div>

<?php elseif ( $_GET['series'] == 'latest' ) : ?>

	<div id="updating">
		<?php $updates->update( 'latest' ); ?>
	</div>

<?php else : ?>
	<?php alert_error( 'Nome della serie invalido.' ); ?>
<?php endif; ?>

<?php
get_footer();