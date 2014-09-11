<?php
/**
 * Render KYSS Movement add and edit form.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.13.0
 */

// Handle some errors in a dev-friendly way.
if ( ! in_array( $action, array( 'edit', 'add' ) ) )
	trigger_error( 'Unrecognised action' . $action, E_USER_ERROR );

if ( $action == 'edit' && empty( $id ) ) {
	$message = 'Movimento da modificare non specificato!';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

switch( $action ) {
	case 'edit' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			validate_movement_data();
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

			$id = KYSS_Movement::create( $data );
			kyss_redirect( get_site_url( '/movements.php' ) );
		}
		break;
}

$movement = KYSS_Movement::get( $id );
$users = KYSS_User::get_users_list();
$budgets = KYSS_Budget::get_list();
$events = KYSS_Event::get_events_list();
?>

<?php if ( $action == 'edit' ) : ?>
	<h1 class="page-title">Modifica movimento 
		<small>
			<?php echo ( isset( $movement->ID ) ? $movement->ID : '' ); ?>
		</small>
	</h1>
<?php elseif ( $action == 'add' ) : ?>
	<h1 class="page-title">Nuovo movimento</h1>
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

<form id="<?php echo $action; ?>-movement" method="post" action="movements.php?<?php echo $form_action; ?>" data-abide>
	<div class="row">
		<div class="medium-6 columns">
			<label for="utente">Utente</label>
			<select name="utente">
				<option value="NULL"<?php echo !isset( $movement->utente ) ? selected( true, true, false ) : ''; ?>>
					-
				</option>
			<?php foreach ( $users as $user ) : ?>
				<option value="<?php echo $user->ID; ?>"<?php echo isset( $movement->utente ) ? selected( $movement->utente, $user->ID, false ) : ''; ?>><?php echo $user->nome . ' ' . $user->cognome; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="medium-6 columns">
			<label for="causale">Causale</label>
			<select name="causale">
			<?php foreach ( array('quota', 'donazione', 'iscrizione') as $type ) : ?>
				<option value="<?php echo $type; ?>"<?php echo isset( $movement->causale ) ? selected( $movement->causale, $type, false ) : ''; ?>><?php echo $type; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="importo">Importo</label>
			<input id="importo" name="importo" type="text"<?php echo isset( $movement->importo ) ? $movement->importo : ''; ?>>
		</div>
		<div class="medium-6 columns">
			<label for="data">Data</label>
			<input id="data" name="data" class="datepicker" type="text"<?php echo isset( $movement->data ) ? $movement->data : ''; ?>>
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="bilancio">Bilancio</label>
			<select name="bilancio">
				<option value="NULL"<?php echo !isset( $movement->bilancio ) ? selected( true, true, false ) : ''; ?>>
					-
				</option>
			<?php foreach ( $budgets as $budget ) : ?>
				<option value="<?php echo $budget->ID; ?>"<?php echo isset( $movement->bilancio ) ? selected( $movement->bilancio, $budget->ID, false ) : ''; ?>><?php echo 'Bilancio ' . ( isset( $budget->mese ) ? $budget->mese . ' ' : '' ) . $budget->anno; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="medium-6 columns">
			<label for="evento">Evento</label>
			<select name="evento">
				<option value="NULL"<?php echo !isset( $movement->evento ) ? selected( true, true, false ) : ''; ?>>
					-
				</option>
			<?php foreach ( $events as $event ) : ?>
				<option value="<?php echo $event->ID; ?>"<?php echo isset( $movement->evento ) ? selected( $movement->evento, $event->ID, false ) : ''; ?>><?php echo isset( $event->nome ) ? $event->nome : ( 'Evento' . $event->ID ); ?>
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
			<a href="<?php echo get_site_url( 'movements.php' ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php
/**
 *
 * Validate Movement input data.
 *
 * @since  0.13.0
 *
 * @global id
 * @global kyssdb
 *
 * @return array Associative array of movement data ready to be saved.
 */
function validate_movement_data() {
	global $id, $kyssdb;

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );

	$valid = array();
	foreach ($_POST as $key => $value) {
		if ( empty( $value ) )
			continue;
		$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
	}

	KYSS_Movement::update( $id, $valid );
}