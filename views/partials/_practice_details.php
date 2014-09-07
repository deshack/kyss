<?php
/**
 * Render KYSS Pracice details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( ! defined( 'ABSPATH' ) )
	kyss_die( 'You cannot access this file directly!', '', array( 'back_link' => true ) );

if ( empty( $prot ) ) {
	$message = 'Pratica non specificata!';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$practice = KYSS_Practice::get( $prot );
?>

<h1 class="page-title">
	Dettagli pratica <small><?php echo $practice->protocollo; ?></small>
</h1>

<div class="row">
	<div class="medium-6 columns">
		<dl>
			<dt>Tipo</dt>
			<dd><?php echo $practice->tipo; ?></dd>
		</dl>
	</div>
	<div class="medium-6 columns">
		<dl>
			<dt>Utente</dt>
			<dd><?php 
				$user = KYSS_USER::get_user_by('id', $practice->utente );
				echo $user->nome . ' ' . $user->cognome; ?></dd>
		</dl>
	</div>
</div>
<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>Data</dt>
			<dd><?php echo $practice->data; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Data ricezione</dt>
			<dd><?php echo $practice->ricezione; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Stato</dt>
			<dd><?php switch ( $practice->approvata ) {
					case '1':
						echo 'Approvata';
						break;
					case '0':
						echo 'Non approvata';
						break;
					case '':
					default:
						echo 'In attesa di giudizio';
						break;
					}?></dd>
		</dl>
	</div>
</div>

<footer class="entry-meta text-center">
	<div class="row">
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'practices.php?action=edit&prot=' . $prot ); ?>" class="button" title="Modifica">
				<span class="dashicons dashicons-edit"></span>
			</a>
		</div>
		<div class="medium-6 columns">
			<?php back_button(); ?>
		</div>
	</div>
</footer>