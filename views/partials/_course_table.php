<?php
/**
 * Render KYSS Course table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  
 */

$courses = KYSS_Course::get_courses_list();

// Small workaround to remove array elements that evaluate to false.
// Useful if `KYSS_Event::get_courses_list()` adds a NULL element.
if ( is_array( $courses ) )
	$courses = array_filter( $courses ); 
?>

<h1>Corsi <small><a href="<?php echo get_site_url( 'courses.php?action=add'); ?>">Aggiungi</a></small></h1>

<?php
if ( ! empty( $courses ) ) : ?>

<table>
	<thead>
		<tr>
			<th>Nome</th>
			<th>Data inizio</th>
			<th>Data fine</th>
			<th>Titolo</th>
			<th>Livello</th>
			<th>Luogo</th>
			<th>Lezioni</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $courses as $course ) : ?>
		<tr>
			<td><?php echo isset( $course->nome ) ? $course->nome : ''; ?></td>
			<td><?php echo isset( $course->data_inizio ) ? $course->data_inizio : ''; ?></td>
			<td><?php echo isset( $course->data_fine ) ? $course->data_fine : ''; ?></td>
			<td><?php echo isset( $course->titolo ) ? $course->titolo : ''; ?></td>
			<td><?php echo isset( $course->livello ) ? $course->livello : ''; ?></td>
			<td><?php echo isset( $course->luogo ) ? $course->luogo : ''; ?></td>
			<td><?php echo isset( $course->lezioni ) ? $course->lezioni : ''; ?></td>

			<td>
				<a href="<?php echo get_site_url( 'courses.php?action=view&id=' . $course->ID ); ?>">Dettagli</a>
				<a href="<?php echo get_site_url( 'courses.php?action=edit&id=' . $course->ID ); ?>">Modifica</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php
else:
	echo 'Non ci sono corsi da visualizzare.';
endif;