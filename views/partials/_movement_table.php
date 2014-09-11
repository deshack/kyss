<?php
/**
 * Render KYSS Movement table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.13.0
 */

if ( isset( $_GET['q'] ) )
	$movements = KYSS_Movement::search( $_GET['q'] );
elseif ( ! empty( $id ) )
	$movements = KYSS_Movement::get_list( $id, 'ASC' );
else
	$movements = KYSS_Movement::get_list();

if ( is_kyss_error( $movements ) )
	trigger_error($movements->get_error_message());
?>

<h1>Movimenti
	<small><a href="<?php echo get_site_url( 'movements.php?action=add'); ?>">
		<span class="dashicons dashicons-plus"></span>
	</a></small>
</h1>

<?php if ( strpos( $_SERVER['PHP_SELF'], 'movements' ) !== false ) search_form(); ?>

<?php
if ( ! empty( $movements ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Utente</th>
			<th>Causale</th>
			<th>Data</th>
			<th>Importo</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $movements as $movement ) : ?>
		<tr>
			<td><?php 
				$user = KYSS_User::get_user_by('id', $movement->utente);
				echo $user->nome . ' ' . $user->cognome;
			?></td>
			<td><?php echo $movement->causale; ?></td>
			<td><?php echo date( 'd/m/Y', strtotime( $movement->data ) ); ?></td>
			<td>&euro;<?php echo $movement->importo; ?></td>
			<td>
				<a href="<?php echo get_site_url( 'movements.php?action=view&id=' . $movement->ID ); ?>" title="Dettagli">
					<span class="dashicons dashicons-visibility"></span>
				</a>
				<a href="<?php echo get_site_url( 'movements.php?action=edit&id=' . $movement->ID ); ?>" title="Modifica">
					<span class="dashicons dashicons-edit"></span>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono movimenti da visualizzare.';
endif;
?>