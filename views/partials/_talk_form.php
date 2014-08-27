<?php
/**
 * Render KYSS Talk add and edit form.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

// Handle some errors in a dev-friendly way.
if ( ! in_array( $action, array( 'edit', 'add' ) ) )
	trigger_error( 'Unrecognised action' . $action, E_USER_ERROR );

if ( $action == 'edit' && empty( $id ) ) {
	$message = 'Talk da modificare non specificato!';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

switch( $action ) {
	case 'edit' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			validate_talk_data();
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

			$id = KYSS_Talk::create( $data );
			kyss_redirect( get_site_url( '/other-events.php?' ) );
		}
		break;
}

$talk = KYSS_Talk::get_talk_by( 'id', $id );
$users = KYSS_User::get_users_list();
$events = KYSS_Event::get_events_list();
?>

<?php if ( $action == 'edit' ) : ?>
	<h1 class="page-title">Modifica talk <?php if ( isset( $talk->titolo ) ) : ?><small><?php echo $talk->titolo; ?></small><?php endif; ?></h1>
<?php elseif ( $action == 'add' ) : ?>
	<h1 class="page-title">Nuovo talk</h1>
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

<form id="<?php echo $action; ?>-talk" method="post" action="talks.php?<?php echo $form_action; ?>" data-abide>
	<div class="row">
		<div class="medium-8 columns">
			<label for="titolo">Titolo</label>
			<input id="titolo" name="titolo" type="text" autofocus required<?php echo isset( $talk->titolo ) ? get_value_html( $talk->titolo ) : '' ?>>
			<?php field_error(); ?>
		</div>
		<div class="medium-4 columns">
			<label for="data">Data</label>
			<input type="date" id="data" name="data"<?php echo isset( $talk->data ) ? get_value_html( $talk->data ) : '' ?>>
		</div>
	</div>
	<div class="row">
		<div class="medium-12 columns">
			<label for="argomenti">Argomenti</label>
			<textarea id="argomenti" name="argomenti" rows="5"><?php echo isset( $talk->argomenti ) ? $talk->argomenti : ''; ?></textarea>
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="relatore">Relatore</label>
			<select name="relatore">
				<option value="NULL"<?php echo !isset( $talk->relatore ) ? selected( true, true, false ) : ''; ?>>
					Nessuno
				</option>
			<?php foreach ( $users as $user ) : ?>
				<option value="<?php echo $user->ID; ?>"<?php echo isset( $talk->relatore ) ? selected( $talk->relatore, $user->ID, false ) : ''; ?>>
 					<?php echo $user->nome . ' ' . $user->cognome; ?>
 				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="medium-6 columns">
			<label for="evento">Evento</label>
			<select name="evento">
				<option value="NULL"<?php echo !isset( $talk->evento ) ? selected( true, true, false ) : ''; ?>>
					Nessuno
				</option>
			<?php foreach ( $events as $event ) : ?>
				<option value="<?php echo $event->ID; ?>"<?php echo isset( $talk->evento ) ? selected( $talk->evento, $event->ID, false ) : ''; ?>>
 					<?php echo isset( $event->nome ) ? $event->nome : 'Evento ' . $event->ID; ?>
 				</option>
			<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="row action-buttons text-center">
		<div class="small-6 columns">
			<input type="submit" class="button" name="submit" value="Salva">
		</div>
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'talks.php' ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php
/**
 *
 * Validate Talk input data.
 *
 * @since  0.12.0
 *
 * @global id
 * @global kyssdb
 *
 * @return array Associative array of talk data ready to be saved.
 */
function validate_talk_data() {
	global $id, $kyssdb;

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );

	$valid = array();
	foreach ($_POST as $key => $value) {
		if ( empty( $value ) )
			continue;
		$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
	}

	KYSS_Talk::update( $id, $valid );
}