<?php
/**
 * Render KYSS Events table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.11.0
 */

$events = KYSS_Event::get_events_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Event::get_users_list()` adds a NULL element.
if ( is_array( $events ) )
	$events = array_filter( $events ); ?>

<h1 class="page-title">Eventi <small><a href="<?php echo get_site_url( 'events.php?action=add'); ?>">Aggiungi</a></small></h1>

<?php if( ! empty( $events ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Nome</th>
			<th>Inizio</th>
			<th>Fine</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $events as $event ) : ?>
		<tr>
			<td><?php echo isset( $event->nome ) ? $event->nome : ''; ?></td>
			<td><?php echo isset( $event->inizio ) ? $event->inizio : ''; ?></td>
			<td><?php echo isset( $event->fine ) ? $event->fine : ''; ?></td>
			<td>
				<a href="<?php echo get_site_url( 'events.php?action=view&id=' . $event->ID ); ?>">Dettagli</a>
				<a href="<?php echo get_site_url( 'events.php?action=edit&id=' . $event->ID ); ?>">Modifica</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
endif;
