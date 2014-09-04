<?php
/**
 * Render KYSS Offices table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

$offices = KYSS_Office::get_list();
?>

<h1 class="page-title">Cariche<a href="<?php echo get_site_url( 'offices.php?action=add' ); ?>" title="Aggiungi nuovo">
	<span class="dashicons dashicons-plus"></span>
</a></h1>

<?php if ( ! empty( $offices ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Carica</th>
			<th>Inizio</th>
			<th>Fine</th>
			<th>Utente</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ( $offices as $office ) : ?>
		<tr>
			<td><?php echo $office->carica; ?></td>
			<td><?php echo $office->inizio; ?></td>
			<td><?php echo isset( $office->fine ) ? $office->fine : '-'; ?></td>
			<td>
				<a href="<?php echo get_site_url( 'users.php?action=view&id=' . $office->utente->ID ); ?>">
					<?php echo ( isset( $office->utente->nome ) ? $office->utente->nome : '' ) . ' ' . ( isset( $office->utente->cognome ) ? $office->utente->cognome : '' ); ?>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php else : ?>
<p>Non ci sono cariche da visualizzare</p>
<?php
endif;