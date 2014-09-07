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
require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/load.php' );
?>

<td><?php echo $lesson->argomento; ?></td>
<td><?php echo $lesson->data; ?></td>
<td>
	<input type="hidden" name="action" value="update">
	<a class="edit" title="Modifica">
		<span class="dashicons dashicons-edit"></span>
	</a>
</td>