<?php
/**
 * Render KYSS Lesson add form.
 *
 * Note: this file generates the form to be rendered in the
 * lessons list table. Therefore it is different from
 * all the other *_form partials.
 *
 * $lesson is a KYSS_Lesson object defined in
 * _lesson_table.php.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.13.0
 */

require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/load.php' );

$action = $_POST['action'];
?>

<td>Aggiungi lezione</td>
<td>
	<form>
		<input type="hidden" name="action" value="<?php echo $action; ?>">
		<input type="hidden" name="corso" value="<?php echo $_POST['corso']; ?>">
		<label for="argomento">Argomento</label>
		<input id="argomento" name="argomento" type="text"<?php echo isset( $lesson->argomento ) ? $lesson->argomento : ''; ?>>
		<label for="data">Argomento</label>
		<input id="data" name="data" type="date"<?php echo isset( $lesson->data ) ? $lesson->data : ''; ?>>
	</form>
</td>
<td>
	<a class="submit" title="Salva">
		<span class="dashicons dashicons-yes"></span>
	</a>
	<a class="remove" title="Annulla">
		<span class="dashicons dashicons-no"></span>
	</a>
</td>