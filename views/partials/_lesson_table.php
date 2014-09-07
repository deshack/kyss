<?php
/**
 * Render KYSS Lesson table.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.13.0
 */

$lessons = KYSS_Lesson::get_list( $id );
?>

<h1>Lezioni</h1>

<?php
if ( ! empty( $lessons ) ) : ?>
<table id="lessons">
	<thead>
		<tr>
			<th>Argomento</th>
			<th>Data e ora</th>
			<th>Azioni</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $lessons as $lesson ) {
			echo '<tr>';
			include( VIEWS . '/partials/_lesson_details.php' );
			echo '</tr>';
		} ?>
		<tr>
			<td>
				<input type="hidden" name="action" value="create">
			</td>
			<td>
				<input type="hidden" name="corso" value="<?php echo $course->ID; ?>">
			</td>
			<td>
				<a id="add-lesson" title="Aggiungi">
					<span class="dashicons dashicons-plus"></span>
				</a>
			</td>
		</tr>
		<tr class="new"></tr>
	</tbody>
</table>
<?php
endif;