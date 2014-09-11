<?php
/**
 * Render KYSS Courses (event) add and edit form.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

// Handle some errors in a dev-friendly way.
if ( ! in_array( $action, array( 'edit', 'add' ) ) )
	trigger_error( 'Unrecognised action' . $action, E_USER_ERROR );

if ( $action == 'edit' && empty( $id ) ) {
	$message = 'Evento da modificare non specificato!';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

switch( $action ) {
	case 'edit' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			validate_course_data();
		}
		break;
	case 'add' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			$data = array();
			foreach ($_POST as $key => $value) {
				if ( $key == 'submit' || empty( $value ) )
					continue;
				$data[$key] = $value;
			}

			$id = KYSS_Course::create( $data );
			$hook->add( 'kyss_headers', function() {
				kyss_redirect( get_site_url( '/courses.php' ) );
			});
		}
		break;
}

$course = KYSS_Course::get_course_by_id( $id );
$users = KYSS_User::get_users_list();
?>

<?php if ( $action == 'edit' ) : ?>
	<h1 class="page-title">Modifica corso <?php if ( isset( $event->nome ) ) : ?><small><?php echo $event->nome; ?></small><?php endif; ?></h1>
<?php elseif ( $action == 'add' ) : ?>
	<h1 class="page-title">Nuovo corso</h1>
<?php endif; ?>

<?php
$form_action = '';
switch( $action ) {
	case 'edit':
		$form_action = 'action=edit&id=' . $id . '&save=true';		
		break;
	case 'add':
		$form_action = 'action=add&save=true';
		break;
}
?>

<form id="<?php echo $action; ?>-course" method="post" action="courses.php?<?php echo $form_action; ?>">
	<div class="row">
		<div class="medium-12 columns">
			<label for="nome">Nome</label>
			<input id="nome" name="nome" type="text"<?php echo isset( $course->nome ) ? get_value_html( $course->nome ) : '' ?>>
		</div>
	</div>
	<div class="row">
		<div class="medium-2 columns">
			<label for="livello">Livello</label>
			<select name="livello">
			<?php foreach ( array(
				'base' => 'Base',
				'medio' => 'Medio',
				'avanzato' => 'Avanzato'
			) as $type => $name ) : ?>
				<option value="<?php echo $type; ?>"<?php echo isset( $course->tipo ) ? selected( $course->livello, $type, false ) : ''; ?>><?php echo $name; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="medium-5 columns">
			<label for="data_inizio">Inizio</label>
			<input type="text" class="datepicker" id="data_inizio" name="data_inizio"<?php echo isset( $course->data_inizio ) ? get_value_html( $course->data_inizio ) : '' ?> required>
		</div>
		<div class="medium-5 columns">
			<label for="data_fine">Fine</label>
			<input id="data_fine" name="data_fine" class="datepicker" type="text"<?php echo isset( $course->data_fine ) ? get_value_html( $course->data_fine ) : '' ?>>
		</div>
	</div>
	<div class="row">
		<div class="small-12 columns">
			<label for="luogo">Luogo</label>
			<input id="luogo" name="luogo" type="text"<?php echo isset( $course->luogo ) ? get_value_html( $course->luogo ) : ''; ?>>
		</div>
	</div>
	<div class="row action-buttons text-center">
		<div class="small-6 columns">
			<input type="submit" class="button" name="submit" value="Salva">
		</div>
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'courses.php' ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php
/**
 *
 * Validate Course (event) input data.
 *
 * @since  0.12.0
 *
 * @global id
 * @global kyssdb
 *
 * @return array Associative array of event data ready to be saved.
 */
function validate_course_data() {
	global $id, $kyssdb;

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );

	$valid = array();
	foreach ($_POST as $key => $value) {
		if ( empty( $value ) )
			continue;
		$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
	}

	KYSS_Course::update( $id, $valid );
}