<?php
/**
 * Render KYSS User details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( empty( $id ) ) {
	$message = 'Stai tentando di visualizzare i dettagli di un utente, ma non hai specificato quale.';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$user = KYSS_User::get_user_by( 'id', $id );
?>

<h1 class="page-title">Dettagli utente <small><?php echo $user->nome . ' ' . $user->cognome; ?></small></h1>

<div class="row">
	<div class="medium-4 small-centered columns">
		<ul class="vcard">
			<li class="fn"><?php echo join( ' ', array( $user->nome, $user->cognome ) ); ?></li>
			<?php echo isset( $user->via ) ? '<li class="street-address">' . $user->via . '</li>' : ''; ?>
			<?php echo isset( $user->citta ) ? '<li class="locality">' . $user->citta . '</li>' : ''; ?>
			<?php if ( isset( $user->CAP ) || isset( $user->provincia ) ) : ?>
				<li>
					<?php echo isset( $user->provincia ) ? '<span class="state">' . $user->provincia . '</span>' : ''; ?>
					<?php echo isset( $user->CAP ) ? '<span class="zip">' . $user->CAP . '</span>' : ''; ?>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</div>

<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>Nome</dt>
			<dd><?php echo $user->nome; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Cognome</dt>
			<dd><?php echo $user->cognome; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Codice Fiscale</dt>
			<dd><?php echo isset( $user->codice_fiscale ) ? $user->codice_fiscale : '-'; ?></dd>
		</dl>
	</div>
</div>
<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>Nato a</dt>
			<dd><?php echo isset( $user->nato_a ) ? $user->nato_a : '-'; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Nato il</dt>
			<dd><?php echo isset( $user->nato_il ) ? $user->nato_il : '-'; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Cittadinanza</dt>
			<dd><?php echo isset( $user->cittadinanza ) ? $user->cittadinanza : '-'; ?></dd>
		</dl>
	</div>
</div>

<footer class="entry-meta text-center">
	<div class="row">
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'users.php?action=edit&id=' . $id ); ?>" class="button">
				<span class="dashicons dashicons-edit"></span>
			</a>
		</div>
		<div class="medium-6 columns">
			<?php back_button(); ?>
		</div>
	</div>
</footer>