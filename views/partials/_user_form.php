<?php
/**
 * Render KYSS User add and edit form.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.11.0
 */

// Handle some errors in a dev-friendly way.
if ( ! in_array( $action, array( 'edit', 'add' ) ) )
	trigger_error( 'Unrecognized action ' . $action, E_USER_ERROR );

if ( $action == 'edit' && empty( $id ) ) {
	$message = 'Stai tentando di modificare un utente, ma non hai specificato quale.';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

switch( $action ) {
	case 'edit' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			$user = validate_user_data();
		}
		break;
	case 'add' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			$name = $_POST['nome'];
			$surname = $_POST['cognome'];
			if ( $_POST['password'] == $_POST['pass-confirm'] )
				$password = $_POST['password'];
			$data = array();
			$to_remove = array( 'nome', 'cognome', 'password', 'pass-confirm', 'submit' );
			foreach ( $_POST as $key => $value ) {
				if ( in_array( $key, $to_remove ) )
					continue;
				elseif ( $key == 'gruppo' )
					$value = join( ',', $value );
				$data[$key] = $value;
			}
			
			$id = KYSS_User::create( $name, $surname, $password, $data );
			$user = KYSS_User::get_user_by( 'id', $id );
		}
		break;
}

if ( isset( $user ) )
	$after_save = $user;
if ( ! isset( $user ) || is_kyss_error( $user ) )
	$user = KYSS_User::get_user_by('id', $id);
?>

<h1 class="page-title">
<?php if ( $action == 'edit' ) : ?>
	Modifica utente <small><?php echo $user->nome . ' ' . $user->cognome; ?></small>
<?php elseif ( $action == 'add' ) : ?>
	Nuovo utente
<?php endif; ?>
	<a href="<?php echo get_site_url( 'users.php?action=add'); ?>" title="Aggiungi nuovo">
		<span class="dashicons dashicons-plus"></span>
	</a>
</h1>

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

$group_list = KYSS_Groups::get_defaults();

if ( isset( $after_save ) )
	alert_save( $after_save );
?>
<form id="<?php echo $action; ?>-user" method="post" action="users.php?<?php echo $form_action; ?>" data-abide>
	<div class="row">
		<div class="medium-4 columns">
			<label for="nome">Nome</label>
			<input id="nome" name="nome" type="text"<?php echo isset( $user->nome ) ? get_value_html( $user->nome ) : '' ?> required autocomplete="off" autofocus>
			<?php field_error(); ?>
		</div>
		<div class="medium-4 columns">
			<label for="cognome">Cognome</label>
			<input id="cognome" name="cognome" type="text"<?php echo isset( $user->cognome ) ? get_value_html( $user->cognome ) : '' ?> required autocomplete="off">
			<?php field_error(); ?>
		</div>
		<div class="medium-4 columns">
			<label for="codice_fiscale">Codice Fiscale</label>
			<input id="codice_fiscale" name="codice_fiscale" type="text"<?php echo isset( $user->codice_fiscale ) ? get_value_html( strtoupper( $user->codice_fiscale ) ) : ''; ?> autocomplete="off">
		</div>
	</div>
	<div class="row">
		<div class="medium-4 medium-offset-2 columns">
			<label for="email">Email</label>
			<input id="email" name="email" type="email"<?php echo isset( $user->email ) ? get_value_html( $user->email ) : '' ?> autocomplete="off">
		</div>
		<div class="medium-4 columns end">
			<label for="telefono">Telefono</label>
			<input id="telefono" name="telefono" type="tel"<?php echo isset( $user->telefono ) ? get_value_html( $user->telefono ) : '' ?> autocomplete="off">
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="password">Password</label>
			<input id="password" name="password" type="password" autocomplete="off">
		</div>
		<div class="medium-6 columns">
			<label for="pass-confirm">Conferma password</label>
			<input id="pass-confirm" name="pass-confirm" type="password" autocomplete="off">
		</div>
	</div>
	<div class="row">
		<div class="medium-4 columns">
			<label for="nato_a">Nato a</label>
			<input id="nato_a" name="nato_a" type="text"<?php echo isset( $user->nato_a ) ? get_value_html( $user->nato_a ) : ''; ?>>
		</div>
		<div class="medium-4 columns">
			<label for="nato_il">Nato il</label>
			<input id="nato_il" name="nato_il" class="datepicker" type="text"<?php echo isset( $user->nato_il ) ? get_value_html( $user->nato_il ) : ''; ?> autocomplete="off">
		</div>
		<div class="medium-4 columns">
			<label for="cittadinanza">Cittadinanza</label>
			<input id="cittadinanza" name="cittadinanza" type="text"<?php echo isset( $user->cittadinanza ) ? get_value_html( $user->cittadinanza ) : ''; ?>>
		</div>
	</div>
	<fieldset>
		<legend>Residenza</legend>
		<div class="row">
			<div class="medium-6 columns">
				<label for="via">Via</label>
				<input id="via" name="via" type="text"<?php echo isset( $user->via ) ? get_value_html( $user->via ) : ''; ?> autocomplete="off">
			</div>
			<div class="medium-6 columns">
				<label for="citta">Citt&agrave;</label>
				<input id="citta" name="citta" type="text"<?php echo isset( $user->citta ) ? get_value_html( $user->citta ) : ''; ?>>
			</div>
		</div>
		<div class="row">
			<div class="medium-6 columns">
				<label for="provincia">Provincia</label>
				<input id="provincia" name="provincia" type="text"<?php echo isset( $user->provincia ) ? get_value_html( $user->provincia ) : ''; ?>>
			</div>
			<div class="medium-6 columns">
				<label for="CAP">CAP</label>
				<input id="CAP" name="CAP" type="text"<?php echo isset( $user->CAP ) ? get_value_html( $user->CAP ) : ''; ?>>
			</div>
		</div>
	</fieldset>
	<div class="row">
		<div class="medium-4 medium-offset-4 columns end">
			<fieldset>
				<legend>Gruppo</legend>
			<?php foreach ( $group_list as $slug => $name ) : ?>
				<label><input type="checkbox" name="gruppo[]" value="<?php echo $slug; ?>"<?php echo isset( $user->groups ) && in_array( $slug, $user->groups ) ? checked( true, true, false ) : ''; ?>> <?php echo $name; ?></label>
			<?php endforeach; ?>
			</fieldset>
		</div>
	</div>
	<div class="row action-buttons text-center">
		<div class="small-6 columns">
			<input type="submit" class="button" name="submit" value="Salva">
		</div>
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'users.php' ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php
/**
 * Validate user input data.
 *
 * @since  0.11.0
 *
 * @global  id
 * @global  kyssdb
 *
 * @return array Associative array of user data ready to be saved.
 */
function validate_user_data() {
	global $id, $kyssdb;

	$user = KYSS_User::get_user_by( 'id', $id );

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );
	if ( isset( $_POST['password'] ) ) {
		if ( empty( $_POST['password'] ) || ! isset( $_POST['pass-confirm'] ) ) {
			unset( $_POST['password'] );
			// TODO: raise error.
		} elseif ( $_POST['password'] != $_POST['pass-confirm'] ) {
			unset( $_POST['password'], $_POST['pass-confirm'] );
			// Raise error.
		} else {
			unset( $_POST['pass-confirm'] );
		}
	}
	$valid = array();
	foreach ( $_POST as $key => $value ) {
		if ( $key == 'gruppo' ) {
			$valid[$key] = join( ',', $value );
		} elseif ( ( isset( $user->{$key} ) && $user->{$key} == $value ) || empty( $value ) ) {
			continue;
		} else {
			$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
		}
	}

	return $user->update( $valid );
}