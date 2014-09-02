<?php
/**
 * Render KYSS Talk details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( ! defined( 'ABSPATH' ) )
	kyss_die( 'Non puoi accedere direttamente a questo file!', '', array( 'back_link' => true ) );

if ( empty( $id ) ) {
	$message = 'Stai tentando di visualizzare i dettagli di un corso, ma non hai specificato quale.';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$talk = KYSS_Talk::get_talk_by_id( $id );
?>

<h1 class="page-title">
	Dettagli talk <small><?php echo $talk->titolo; ?></small>
</h1>

<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>Evento</dt>
			<dd>
			<?php if ( isset( $talk->evento ) ) :
				$event = KYSS_Event::get_event_by( 'id', $talk->evento ); ?>
				<a href="<?php echo get_site_url( 'events.php?type=other&action=view&id=' . $talk->evento ); ?>">
					<?php echo isset( $event->nome ) ? $event->nome : 'Evento senza nome'; ?>
				</a>
			<?php else : ?>
				<?php echo '-'; ?>
			<?php endif; ?>
			</dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Inizio</dt>
			<dd><?php echo isset( $talk->data ) ? date( 'd/m/Y H:i', strtotime( $talk->data ) ) : '-'; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Relatore</dt>
			<dd>
			<?php if ( isset( $talk->relatore ) ) :
				$relatore = KYSS_User::get_user_by( 'id', $talk->relatore ); ?>
				<a href="<?php echo get_site_url( 'users.php?action=view&id=' . $talk->relatore ); ?>">
					<?php echo $relatore->nome . ' ' . $relatore->cognome; ?>
				</a>
			<?php else : ?>
				<?php echo '-'; ?>
			<?php endif; ?>
			</dd>
		</dl>
	</div>
</div>
<div class="row">
	<div class="medium-12 columns">
		<dl>
			<dt>Argomenti</dt>
			<dd><?php echo isset( $talk->argomenti ) ? $talk->argomenti : '-'; ?></dd>
		</dl>
	</div>
</div>
<footer class="entry-meta text-center">
	<div class="row">
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'talks.php?action=edit&id=' . $id ); ?>" class="button" title="Modifica">
				<span class="dashicons dashicons-edit"></span>
			</a>
		</div>
		<div class="small-6 columns">
			<?php back_button(); ?>
		</div>
	</div>
</footer>