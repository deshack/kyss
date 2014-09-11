<?php
/**
 * Render KYSS Practice add and edit form.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

// Handle some errors in a dev-friendly way.
if ( ! in_array( $action, array( 'edit', 'add' ) ) )
	trigger_error( 'Unrecognised action' . $action, E_USER_ERROR );

if ( $action == 'edit' && empty( $prot ) ) {
	$message = 'Pratica da modificare non specificato!';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

switch( $action ) {
	case 'edit' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			$practice = validate_practice_data();
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

			$prot = KYSS_Practice::create( $data );
			$practice = KYSS_Practice::get( $prot );
		}
		break;
}

if ( isset( $practice ) )
	$after_save = $practice;
if( ! isset( $practice ) || is_kyss_error( $practice ) )
	$practice = KYSS_Practice::get( $prot );
$users = KYSS_User::get_users_list();
?>

<?php if ( $action == 'edit' ) : ?>
	<h1 class="page-title">Modifica pratica 
		<small>
			<?php echo $practice->protocollo; ?>
		</small>
	</h1>
<?php elseif ( $action == 'add' ) : ?>
	<h1 class="page-title">Nuova pratica</h1>
<?php endif; ?>

<?php
$form_action = '';
switch( $action ) {
	case 'edit':
		$form_action = 'action=edit&prot=' . $prot . '&save=true';		
		break;
	case 'add':
		$form_action = 'action=add&save=true';
		break;
}

if ( isset( $after_save ) )
	alert_save( $after_save );
?>

<form id="<?php echo $action; ?>-practice" method="post" action="practices.php?<?php echo $form_action; ?>" data-abide>
	<div class="row">
		<?php if ( $action == 'add' ) : ?>
		<div class="medium-6 columns">
			<label for="protocollo">Protocollo</label>
			<input id="protocollo" name="protocollo" type="text"<?php echo isset( $pracice->protocollo ) ? get_value_html( $pracice->protocollo ) : ''; ?> required>
			<?php field_error(); ?>
		</div>
	</div>
	<div class="row">
		<?php endif; ?>
		<div class="medium-6 columns">
			<label for="utente">Utente</label>
			 <select name="utente">
			<?php foreach ( $users as $user ) : ?>
				<option value="<?php echo $user->ID; ?>"<?php echo isset( $practice->utente ) ? selected( $practice->utente, $user->ID, false ) : ''; ?>>
				<?php echo $user->nome . ' ' . $user->cognome; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="medium-3 columns">
			<label for="tipo">Tipo</label>
			<select name="tipo">
			<?php foreach ( array(
					'adesione' => 'Richiesta di adesione',
					'liberatoria' => 'Liberatoria'
					) as $type => $name ) : ?>
				<option value="<?php echo $type; ?>"<?php echo isset( $practice->tipo ) ? selected( $practice->tipo, $type, false ) : ''; ?>><?php echo $name; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="medium-3 columns">
			<label for="approvata">Stato</label>
			<select name="approvata">
				<option value="NULL"<?php echo isset( $practice->approvata ) ? get_value_html( $practice->approvata ) : ''; ?>>
					-
				</option>
			<?php foreach ( array(
				'1' => 'Approvata',
				'0' => 'Non approvata'
				) as $type => $name ) : ?>
				<option value="<?php echo $type; ?>"<?php echo isset( $practice->approvata ) ? selected( $practice->approvata, $type, false ) : ''; ?>><?php echo $name; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="data">Data</label>
			<input id="data" name="data" class="datepicker" type="text"<?php echo isset( $practice->data ) ? get_value_html( $practice->data ) : ''; ?> required>
			<?php field_error(); ?>
		</div>
		<div class="medium-6 columns">
			<label for="data">Data ricezione</label>
			<input id="ricezione" name="ricezione" class="datepicker" type="text"<?php echo isset( $practice->ricezione ) ? get_value_html( $practice->ricezione ) : ''; ?> required>
			<?php field_error(); ?>
		</div>
	</div>
	<div class="row">
		<div class="medium-12 columns">
			<label for="note">Note</label>
			<input id="note" name="note" type="text"<?php echo isset( $practice->note ) ? get_value_html( $practice->note ) : ''; ?>>
		</div>
	</div>
	<div class="row action-buttons text-center">
		<div class="small-6 columns">
			<input type="submit" class="button" name="submit" value="Salva">
		</div>
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'practices.php' ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php
/**
 *
 * Validate Practice input data.
 *
 * @since  0.12.0
 *
 * @global prot
 * @global kyssdb
 *
 * @return array Associative array of practice data ready to be saved.
 */
function validate_practice_data() {
	global $prot, $kyssdb;

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );

	$valid = array();
	foreach ($_POST as $key => $value) {
		if ( empty( $value ) )
			continue;
		$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
	}

	$practice = KYSS_Practice::get( $prot );
	return $practice->update( $valid );
}