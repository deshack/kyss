<?php
/**
 * Render KYSS Other event details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( ! defined( 'ABSPATH' ) )
	kyss_die( 'You cannot access this file directly!', '', array( 'back_link' => true ) );

if ( empty( $id ) ) {
	$message = 'Stai tentando di visualizzare i dettagli di un evento, ma non hai specificato quale.';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$event = KYSS_Event::get_event_by( 'id', $id );
?>

<h1 class="page-title">
	Dettagli evento <small><?php echo $event->nome; ?></small>
</h1>

<div class="row">
	<div class="medium-6 columns">
		<dl>
			<dt>Inizio</dt>
			<dd><?php echo isset( $event->data_inizio ) ? date( 'd/m/Y', strtotime( $event->data_inizio ) ) : ''; ?>
			</dd>
		</dl>
	</div>
	<div class="medium-6 columns">
		<dl>
			<dt>Fine</dt>
			<dd>
				<?php echo isset( $event->data_fine ) ? date( 'd/m/Y', strtotime( $event->data_fine ) ) : ''; ?>
			</dd>
		</dl>
	</div>
</div>
<footer class="entry-meta text-center">
	<div class="row">
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'other-events.php?action=edit&id=' . $id ); ?>" class="button" title="Modifica">
				<span class="dashicons dashicons-edit"></span>
			</a>
		</div>
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'other-events.php' ); ?>" class="button">
				<span class="dashicons dashicons-undo"></span>
			</a>
		</div>
	</div>
</footer>