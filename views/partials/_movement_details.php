<?php
/**
 * Render KYSS Movement details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.13.0
 */

if ( ! defined( 'ABSPATH' ) )
	kyss_die( 'You cannot access this file directly!', '', array( 'back_link' => true ) );

if ( empty( $id ) ) {
	$message = 'Movimento non specificato!';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$movement = KYSS_Movement::get( $id );
?>

<h1 class="page-title">
	Dettagli movimento 
		<small><?php
			$user = KYSS_User::get_user_by('id', $movement->utente );
			echo $user->nome . ' ' .$user->cognome; 
		?></small>
</h1>

<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>Causale</dt>
			<dd><?php echo $movement->causale; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Importo</dt>
			<dd><?php echo $movement->importo . ' €'; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Data</dt>
			<dd><?php echo date( 'd/m/Y', strtotime( $movement->data ) ); ?></dd>
		</dl>
	</div>
</div>
<div class="row">
	<div class="medium-6 columns">
		<dl>
			<dt>Bilancio<dt>
			<dd><?php 
				if ( isset( $movement->bilancio ) ) {
					$budget = KYSS_Budget::get( $movement->bilancio );
					echo ( isset( $budget->mese ) ? $budget->mese : '' ) . $budget->anno;
				}
				else
					echo '-';
			?><dd>
		</dl>
	</div>
	<div class="medium-6 columns">
		<dl>
			<dt>Evento<dt>
			<dd><?php 
				if ( isset( $movement->evento ) ) {
					$event = KYSS_Event::get_event_by('id', $movement->evento );
					echo isset( $event->nome ) ? $event->nome : 'Evento ' . $event->ID;
				} 
				else 
					echo '-';
				?></dd>
		</dl>
	</div>
</div>

<footer class="entry-meta text-center">
	<div class="row">
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'movements.php?action=edit&id=' . $id ); ?>" class="button" title="Modifica">
				<span class="dashicons dashicons-edit"></span>
			</a>
		</div>
		<div class="medium-6 columns">
			<?php back_button(); ?>
		</div>
	</div>
</footer>