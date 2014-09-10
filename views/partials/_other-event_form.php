<?php
/**
 * Render KYSS Other event add and edit form.
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
			validate_event_data();
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

			$id = KYSS_Event::create( $data );
			kyss_redirect( get_site_url( '/other-events.php' ) );
		}
		break;
}

$event = KYSS_Event::get_event_by( 'id', $id );
$users = KYSS_User::get_users_list();
?>

<?php if ( $action == 'edit' ) : ?>
	<h1 class="page-title">Modifica evento <?php if ( isset( $event->nome ) ) : ?><small><?php echo $event->nome; ?></small><?php endif; ?></h1>
<?php elseif ( $action == 'add' ) : ?>
	<h1 class="page-title">Nuovo evento</h1>
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

<form id="<?php echo $action; ?>-other-event" method="post" action="other-events.php?<?php echo $form_action; ?>" data-abide>
	<div class="row">
		<div class="medium-12 columns">
			<label for="nome">Nome</label>
			<input id="nome" name="nome" type="text" autofocus<?php echo isset( $event->nome ) ? get_value_html( $event->nome ) : '' ?>>
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="data_inizio">Inizio</label>
			<input type="text" class="datepicker" id="data_inizio" name="data_inizio"<?php echo isset( $event->data_inizio ) ? get_value_html( $event->data_inizio ) : '' ?> required>
			<?php field_error(); ?>
		</div>
		<div class="medium-6 columns">
			<label for="data_fine">Fine</label>
			<input id="data_fine" name="data_fine" class="datepicker" type="text"<?php echo isset( $event->data_fine ) ? get_value_html( $event->data_fine ) : '' ?>>
		</div>
	</div>
	<div class="row">
		<div class="medium-12 columns">
			<label for="luogo">Luogo</label>
			<input id="luogo" name="luogo" type="text"<?php echo isset( $event->luogo ) ? get_value_html( $event->luogo ) : '' ?>>
		</div>
	</div>
	<div class="row action-buttons text-center">
		<div class="small-6 columns">
			<input type="submit" class="button" name="submit" value="Salva">
		</div>
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'other-events.php' ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php
/**
 *
 * Validate Other event input data.
 *
 * @since  0.12.0
 *
 * @global id
 * @global kyssdb
 *
 * @return array Associative array of event data ready to be saved.
 */
function validate_event_data() {
	global $id, $kyssdb;

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );

	$valid = array();
	foreach ($_POST as $key => $value) {
		if ( empty( $value ) )
			continue;
		$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
	}

	KYSS_Event::update( $id, $valid );
}