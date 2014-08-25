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
$anagrafica = isset( $user->anagrafica ) ? $user->anagrafica : '';

// Anagrafica is a two-dimensions array, if not empty.
// It may be necessary to unserialize it twice.
if ( ! empty( $anagrafica ) )
	$anagrafica = unserialize( $anagrafica );
if ( ! is_array( $anagrafica ) )
	$anagrafica = unserialize( $anagrafica );
?>

<h1 class="page-title">Dettagli utente <small><?php echo $user->nome . ' ' . $user->cognome; ?></small></h1>

<div class="row">
	<div class="medium-4 small-centered columns">
		<ul class="vcard">
			<li class="fn"><?php echo join( ' ', array( $user->nome, $user->cognome ) ); ?></li>
			<?php echo isset( $anagrafica['residenza']['via'] ) ? '<li class="street-address">' . $anagrafica['residenza']['via'] . '</li>' : ''; ?>
			<?php echo isset( $anagrafica['residenza']['city'] ) ? '<li class="locality">' . $anagrafica['residenza']['city'] . '</li>' : ''; ?>
			<?php if ( isset( $anagrafica['residenza']['CAP'] ) || isset( $anagrafica['residenza']['provincia'] ) ) : ?>
				<li>
					<?php echo isset( $anagrafica['residenza']['provincia'] ) ? '<span class="state">' . $anagrafica['residenza']['provincia'] . '</span>' : ''; ?>
					<?php echo isset( $anagrafica['residenza']['CAP'] ) ? '<span class="zip">' . $anagrafica['residenza']['CAP'] . '</span>' : ''; ?>
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
			<dd><?php echo isset( $anagrafica['CF'] ) ? $anagrafica['CF'] : '-'; ?></dd>
		</dl>
	</div>
</div>
<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>Nato a</dt>
			<dd><?php echo isset( $anagrafica['nato_a'] ) ? $anagrafica['nato_a'] : '-'; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Nato il</dt>
			<dd><?php echo isset( $anagrafica['nato_il'] ) ? $anagrafica['nato_il'] : '-'; ?></dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Cittadinanza</dt>
			<dd><?php echo isset( $anagrafica['cittadinanza'] ) ? $anagrafica['cittadinanza'] : '-'; ?></dd>
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
			<a href="<?php echo get_site_url( 'users.php' ); ?>" class="button">
				<span class="dashicons dashicons-undo"></span>
			</a>
		</div>
	</div>
</footer>