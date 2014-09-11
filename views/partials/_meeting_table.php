<?php
/**
 * Render KYSS Meetings (event) table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  
 */

if ( isset( $_GET['q'] ) )
	$meetings = KYSS_Meeting::search( $_GET['q'] );
else
	$meetings = KYSS_Meeting::get_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Meeting::get_list()` adds a NULL element.
if ( is_array( $meetings ) ) 
	$meetings = array_filter( $meetings );
?>

<h1>Riunioni
	<small><a href="<?php echo get_site_url( 'meetings.php?action=add'); ?>">
		<span class="dashicons dashicons-plus"></span>
	</a></small>
</h1>

<?php if ( strpos( $_SERVER['PHP_SELF'], 'events' ) === false ) search_form(); ?>

<?php
if ( ! empty( $meetings ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Nome</th>
			<th>Inizio</th>
			<th>Fine</th>
			<th>Tipo</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $meetings as $meeting ) : ?>
		<tr>
			<td><?php echo isset( $meeting->nome ) ? $meeting->nome : ''; ?></td>
			<td>
				<?php echo isset( $meeting->data_inizio ) ? date( 'd/m/Y', strtotime( $meeting->data_inizio ) ) : ''; ?>
				<?php echo isset( $meeting->ora_inizio ) ? date( 'H:i', strtotime( $meeting->ora_inizio ) ) : ''; ?>
			</td>
			<td>
				<?php echo isset( $meeting->data_fine ) ? date( 'd/m/Y', strtotime( $meeting->data_fine ) ) : ''; ?>
				<?php echo isset( $meeting->ora_fine ) ? date( 'H:i', strtotime( $meeting->ora_fine ) ) : ''; ?>
			</td>
			<td><?php echo isset( $meeting->tipo ) ? $meeting->tipo : ''; ?></td>
			<td>
				<a href="<?php echo get_site_url( 'meetings.php?action=view&id=' . $meeting->ID ); ?>" title="Dettagli">
					<span class="dashicons dashicons-visibility"></span>
				</a>
				<a href="<?php echo get_site_url( 'meetings.php?action=edit&id=' . $meeting->ID ); ?>" title="Modifica">
					<span class="dashicons dashicons-edit"></span>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono riunioni da visualizzare.';
endif;
?>