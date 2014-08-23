<?php
/**
 * Render KYSS Event details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( empty( $id ) ) {
	$message = 'Evento da visualizzare non specificato.';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$event = KYSS_Event::get_event_by( 'id', $id );
?>

<h1 class="page-title">Dettagli evento <?php if ( isset( $event->nome ) ) : ?><small><?php echo $event->nome; ?></small><?php endif; ?></h1>

<div class="row">
	<div class="medium-6 small-centered columns">
		<ul class="vcard">
			<li class="fn"><?php echo isset( $event->nome ) ? $event->nome : ' '; ?></li>
			<?php echo '<li class="date">' . $event->inizio . '</li>';
			echo isset( $event->fine ) ? '<li class="date">' . $event->fine . '</li>' : ''; ?>
		</ul>
	</div>
</div>