<?php
/**
 * Render KYSS Lessons details.
 *
 * Note: this file generates the details to be put in the
 * lessons list table. Therefore it is different from
 * all the other *_details partials.
 *
 * $lesson is a KYSS_Lesson object defined in
 * _lesson_table.php.
 *
 * @package KYSS
 * @subpackage Partials
 * @since  0.13.0
 */
?>

<tr>
	<td><?php echo $lesson->argomento; ?></td>
	<td><time><?php echo $lesson->data; ?></time></td>
	<td>
		<input type="hidden" name="action" value="update">
		<a href="<?php echo get_site_url( 'lessons.php?action=edit&course='.$id.'&date='.$lesson->data ); ?>" class="edit" title="Modifica">
			<span class="dashicons dashicons-edit"></span>
		</a>
		<a class="delete" title="Elimina">
			<span class="dashicons dashicons-trash"></span>
		</a>
	</td>
</tr>