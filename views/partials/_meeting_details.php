<?php
/**
 * Render KYSS Meeting details.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

if ( ! defined( 'ABSPATH' ) )
	kyss_die( 'You cannot access this file directly!', '', array( 'back_link' => true ) );

if ( empty( $id ) ) {
	$message = 'Stai tentando di visualizzare i dettagli di una riunione, ma non hai specificato quale.';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

$meeting = KYSS_Meeting::get_meeting_by_id( $id );
?>

<h1 class="page-title">
	Dettagli riunione <small><?php echo $meeting->nome; ?></small>
</h1>

<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>Inizio</dt>
			<dd><?php echo isset( $meeting->data_inizio ) ? date( 'd/m/Y', strtotime( $meeting->data_inizio ) ) : ''; ?><br>
				<?php echo isset( $meeting->ora_inizio ) ? date( 'H:i', strtotime( $meeting->ora_inizio ) ) : ''; ?>
			</dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Fine</dt>
			<dd>
				<?php echo isset( $meeting->data_fine ) ? date( 'd/m/Y', strtotime( $meeting->data_fine ) ) : ''; ?><br>
				<?php echo isset( $meeting->ora_fine ) ? date( 'H:i', strtotime( $meeting->ora_fine ) ) : ''; ?>
			</dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Luogo</dt>
			<dd><?php echo isset( $meeting->luogo ) ? $meeting->luogo : '-'; ?></dd>
		</dl>
	</div>
</div>
<div class="row">
	<div class="medium-4 columns">
		<dl>
			<dt>Tipo</dt>
			<dd><?php if ( isset( $meeting->tipo ) ) : ?>
				<?php echo ($meeting->tipo == 'CD' ) ? 'Consiglio Direttivo' : 'Assemblea degli Associati'; ?>
				<?php endif; ?>
			</dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Presidente</dt>
			<dd>
			<?php if ( isset( $meeting->presidente ) ) {
				$user = KYSS_User::get_user_by( 'id', $meeting->presidente ); ?>
				<a href="<?php echo get_site_url( 'users.php?action=view&id=' . $user->ID ); ?>">
					<?php echo $user->nome . ' ' . $user->cognome; ?>
				</a>
				<?php
			} else {
				echo '-';
			} ?>
			</dd>
		</dl>
	</div>
	<div class="medium-4 columns">
		<dl>
			<dt>Segretario</dt>
			<dd>
			<?php if ( isset( $meeting->segretario ) ) {
				$user = KYSS_User::get_user_by( 'id', $meeting->segretario ); ?>
				<a href="<?php echo get_site_url( 'users.php?action=view&id=' . $user->ID ); ?>">
					<?php echo $user->nome . ' ' . $user->cognome; ?>
				</a>
			<?php
			} else {
				echo '-';
			} ?>
			</dd>
		</dl>
	</div>
</div>

<footer class="entry-meta text-center">
	<div class="row">
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'meetings.php?action=edit&id=' . $id ); ?>" class="button" title="Modifica">
				<span class="dashicons dashicons-edit"></span>
			</a>
		</div>
		<div class="medium-6 columns">
			<a href="<?php echo get_site_url( 'meetings.php' ); ?>" class="button">
				<span class="dashicons dashicons-undo"></span>
			</a>
		</div>
	</div>
</footer>