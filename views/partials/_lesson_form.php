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
$save = false;
if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
	$save = true;
	$lesson = validate_lesson_data();
}

if ( ! isset( $lesson ) && ! empty( $course ) && ! empty( $date ) )
	$lesson = KYSS_Lesson::get_lesson( $course, $date );
?>

<h1 class="page-title">
	<a href="<?php echo get_site_url( 'courses.php?action=view&id='.$course ); ?>"><span class="dashicons dashicons-arrow-left-alt2"></span></a>
<?php if ( $action == 'edit' ) : ?>
	Modifica lezione
<?php elseif ( $action == 'add' ) : ?>
	Nuova lezione
<?php endif; ?>
</h1>

<?php $form_action = '';
switch( $action ) {
	case 'edit':
		$form_action = 'action=edit&course='.$course.'&date='.$date.'&save=true';
		break;
	case 'add':
		$form_action = 'action=add&course='.$course.'&save=true';
		break;
}

$course_obj = KYSS_Course::get_course_by_id( $course );

if ( $save )
	alert_save( $lesson );
?>

<form id="<?php echo $action; ?>-lesson" method="post" action="lessons.php?<?php echo $form_action; ?>" data-abide>
	<div class="row">
		<div class="medium-4 columns end">
			<label for="data">Data e ora</label>
			<input id="data" name="data" type="text" class="datetimepicker"<?php echo isset( $lesson->data ) ? get_value_html( $lesson->data ) : ''; ?>>
		</div>
	</div>
	<div class="row">
		<div class="medium-12 columns">
			<label for="argomento">Argomento</label>
			<textarea id="argomento" name="argomento" rows="5"><?php echo isset( $lesson->argomento ) ? $lesson->argomento : ''; ?></textarea>
		</div>
	</div>
	<div class="row action-buttons text-center">
		<div class="small-6 columns">
			<input type="submit" class="button" name="submit" value="Salva">
		</div>
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'courses.php?action=view&id='.$course ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php
function validate_lesson_data() {
	global $action, $course, $date;

	if ( isset( $date ) )
		$lesson = KYSS_Lesson::get_lesson( $course, $date );

	$valid = array( 'corso' => $course );
	foreach ( $_POST as $key => $value ) {
		if ( $key == 'submit' || ( empty( $value ) && $action == 'add' ) || ($action == 'edit' && $value == $lesson->{$key} ) )
			continue;
		$valid[$key] = $value;
	}

	switch( $action ) {
		case 'add':
			if ( empty( $valid ) )
				return new KYSS_Error( 'invalid_data', 'I dati che hai inserito non solo validi.' );
			$result = KYSS_Lesson::create( $valid );
			if ( $result && ! is_kyss_error( $result ) )
				$result = KYSS_Lesson::get_lesson( $valid['corso'], $valid['data'] );
			return $result;
			break;
		case 'edit':
			if ( empty( $valid ) )
				return $lesson;
			$result = $lesson->update( $valid );
			if ( ! $result )
				return false;
			return $result;
			break;
	}
}