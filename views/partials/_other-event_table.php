<?php
/**
 * Render KYSS Other-event table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  
 */

$other_events = KYSS_Event::get_events_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Event::get_other_events_list()` adds a NULL element.
if ( is_array( $other_events ) )
	$other_events = array_filter( $other_events ); 
?>

<h1>Altri 
	<small><a href="<?php echo get_site_url( 'other-events.php?action=add'); ?>">
		<span class="dashicons dashicons-plus"></span>
	</a></small>
</h1>

<?php
if ( ! empty( $other_events ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Nome</th>
			<th>Data inizio</th>
			<th>Data fine</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $other_events as $other_event ) : ?>
		<tr>
			<td><?php echo isset( $other_event->nome ) ? $other_event->nome : ''; ?></td>
			<td><?php echo isset( $other_event->data_inizio ) ? $other_event->data_inizio : ''; ?></td>
			<td><?php echo isset( $other_event->data_fine ) ? $other_event->data_fine : ''; ?></td>
			<td>
				<a href="<?php echo get_site_url( 'other-events.php?action=view&id=' . $other_event->ID ); ?>" title="Dettagli">
					<span class="dashicons dashicons-visibility"></span>
				</a>
				<a href="<?php echo get_site_url( 'other-events.php?action=edit&id=' . $other_event->ID ); ?>" title="Modifica">
					<span class="dashicons dashicons-edit"></span>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono altri eventi da visualizzare.';
endif;