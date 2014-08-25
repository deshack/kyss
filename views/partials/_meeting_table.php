<?php
/**
 * Render KYSS Meetings (event) table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  
 */

$meetings = KYSS_Meeting::get_meetings_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Event::get_meetings_list()` adds a NULL element.
if ( is_array( $meetings ) ) 
	$meetings = array_filter( $meetings );
?>

<h1>Riunoni <small><a href="<?php echo get_site_url( 'meetings.php?action=add'); ?>">Aggiungi</a></small></h1>

<?php
if ( ! empty( $meetings ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Nome</th>
			<th>Data inizio</th>
			<th>Data fine</th>
			<th>Tipo</th>
			<th>Ora inizio</th>
			<th>Ora fine</th>
			<th>Luogo</th>
			<th>Presidente</th>
			<th>Segretario</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $meetings as $meeting ) : ?>
		<tr>
			<td><?php echo isset( $meeting->nome ) ? $meeting->nome : ''; ?></td>
			<td><?php echo isset( $meeting->data_inizio ) ? $meeting->data_inizio : ''; ?></td>
			<td><?php echo isset( $meeting->data_fine ) ? $meeting->data_fine : ''; ?></td>
			<td><?php echo isset( $meeting->tipo ) ? $meeting->tipo : ''; ?></td>
			<td><?php echo isset( $meeting->ora_inizio ) ? $meeting->ora_inizio : ''; ?></td>
			<td><?php echo isset( $meeting->ora_fine ) ? $meeting->ora_fine : ''; ?></td>
			<td><?php echo isset( $meeting->luogo ) ? $meeting->luogo : ''; ?></td>
			<td><?php echo isset( $meeting->presidente ) ? $meeting->presidente : ''; ?></td>
			<td><?php echo isset( $meeting->segretario ) ? $meeting->segretario : ''; ?></td>
			<td>
				<a href="<?php echo get_site_url( 'meetings.php?action=view&id=' . $meeting->ID ); ?>">Dettagli</a>
				<a href="<?php echo get_site_url( 'meetings.php?action=edit&id=' . $meeting->ID ); ?>">Modifica</a>
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