<?php
/**
 * Render KYSS Course table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  
 */

$courses = KYSS_Course::get_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Event::get_list()` adds a NULL element.
if ( is_array( $courses ) )
	$courses = array_filter( $courses ); 
?>

<h1>Corsi 
	<small><a href="<?php echo get_site_url( 'courses.php?action=add'); ?>">
		<span class="dashicons dashicons-plus"></span>
	</a></small></h1>

<?php
if ( ! empty( $courses ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Nome</th>
			<th>Data inizio</th>
			<th>Data fine</th>
			<th>Livello</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $courses as $course ) : ?>
		<tr>
			<td><?php echo isset( $course->nome ) ? $course->nome : ''; ?></td>
			<td><?php echo isset( $course->data_inizio ) ? date( 'd/m/Y', strtotime( $course->data_inizio ) ) : ''; ?></td>
			<td><?php echo isset( $course->data_fine ) ? date( 'd/m/Y', strtotime( $course->data_fine ) ) : ''; ?></td>
			<td><?php echo isset( $course->livello ) ? $course->livello : ''; ?></td>

			<td>
				<a href="<?php echo get_site_url( 'courses.php?action=view&id=' . $course->ID ); ?>" title="Dettagli">
					<span class="dashicons dashicons-visibility"></span>
				</a>
				<a href="<?php echo get_site_url( 'courses.php?action=edit&id=' . $course->ID ); ?>" title="Modifica">
					<span class="dashicons dashicons-edit"></span>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono corsi da visualizzare.';
endif;