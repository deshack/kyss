<?php
/**
 * Render KYSS Office details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since 0.12.0
 */

if ( ! defined( 'ABSPATH' ) )
	kyss_die( 'You cannot access this file directly!', '', array( 'back_link' => true ) );

if ( empty( $slug ) || empty( $start ) ) {
	$message = 'Stai tentando di visualizzare i dettagli di una carica, ma non hai specificato quale.';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$office = KYSS_Office::get( $slug, $start );
?>

<h1 class="page-title">Dettagli carica <small><?php echo ucfirst( $office->carica ); ?></small></h1>

<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>Utente</dt>
			<dd>
				<a href="<?php echo get_site_url( 'users.php?action=view&id=' . $office->utente->ID ); ?>">
					<?php echo $office->utente->nome . ' ' . $office->utente->cognome; ?>
				</a>
			</dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Dal</dt>
			<dd><?php echo $office->inizio; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Al</dt>
			<dd><?php echo isset( $office->fine ) ? $office->fine : '-'; ?></dd>
		</dl>
	</div>
</div>
<footer class="entry-meta text-center">
	<div class="row">
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'offices.php?action=edit&office=' . $office->carica . '&start=' . $office->inizio ); ?>" class="button" title="Modifica">
				<span class="dashicons dashicons-edit"></span>
			</a>
		</div>
		<div class="small-6 columns">
			<?php back_button(); ?>
		</div>
	</div>
</footer>