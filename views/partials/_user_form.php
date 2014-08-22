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
if ( $action == 'edit' && empty( $id ) )
	trigger_error( 'Trying to edit unspecified user', E_USER_ERROR );

switch( $action ) {
	case 'edit' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			validate_user_data();
		}

		$user = KYSS_User::get_user_by('id', $id);

		$anagrafica = isset( $user->anagrafica ) ? unserialize( $user->anagrafica ) : '';
		if ( ! empty( $anagrafica ) ) {
			extract( $anagrafica );
		}
		break;
	case 'add' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			global $user;

			$name = $_POST['nome'];
			$surname = $_POST['cognome'];
			$password = $_POST['password'];
			$data = array();
			foreach ( $_POST as $key => $value ) {
				switch ( $key ) {
					case 'email':
						$data['email'] = $_POST['email'];
						break;
					case 'telefono':
						$data['telefono'] = $_POST['telefono'];
						break;
					case 'gruppo':
						$data['gruppo'] = $_POST['gruppo'];
						break;
					case 'anagrafica':
						$data['anagrafica'] = serialize( $_POST['anagrafica'] ); 
						break;
				}
			}
			
			$user->create( $name, $surname, $pass, $data );
		} else {
			$user = new KYSS_User();
		}
		break;
}
?>

<?php if ( $action == 'edit' ) : ?>
	<h1 class="page-title">Modifica utente <small><?php echo $user->nome . ' ' . $user->cognome; ?></small></h1>
<?php elseif ( $action == 'add' ) : ?>
	<h1 class="page-title">Nuovo utente</h1>
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
<form id="<?php echo $action; ?>-user" method="post" action="users.php?<?php echo $form_action; ?>">
	<div class="row">
		<div class="medium-6 columns">
			<label for="nome">Nome</label>
			<input id="nome" name="nome" type="text"<?php echo isset( $user->nome ) ? get_value_html( $user->nome ) : '' ?> required>
		</div>
		<div class="medium-6 columns">
			<label for="cognome">Cognome</label>
			<input id="cognome" name="cognome" type="text"<?php echo isset( $user->cognome ) ? get_value_html( $user->cognome ) : '' ?> required>
		</div>
	</div>
	<div class="row">
		<div class="medium-12 columns">
			<label for="anagrafica[CF]">Codice Fiscale</label>
			<input id="anagrafica[CF]" name="anagrafica[CF]" type="text"<?php echo isset( $CF ) ? get_value_html( $CF ) : ''; ?>>
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="email">Email</label>
			<input id="email" name="email" type="email"<?php echo isset( $user->email ) ? get_value_html( $user->email ) : '' ?>>
		</div>
		<div class="medium-6 columns">
			<label for="telefono">Telefono</label>
			<input id="telefono" name="telefono" type="tel"<?php echo isset( $user->telefono ) ? get_value_html( $user->telefono ) : '' ?>>
		</div>
	</div>
	<?php // Add group ?>
	<div class="row">
		<div class="medium-6 columns">
			<label for="password">Password</label>
			<input id="password" name="password" type="password">
		</div>
		<div class="medium-6 columns">
			<label for="pass-confirm">Conferma password</label>
			<input id="pass-confirm" name="pass-confirm" type="password">
		</div>
	</div>
	<div class="row">
		<div class="medium-4 columns">
			<label for="anagrafica[nato_a]">Nato a</label>
			<input id="anagrafica[nato_a]" name="anagrafica[nato_a]" type="text"<?php echo isset( $anagrafica['nato_a'] ) ? get_value_html( $anagrafica['nato_a'] ) : ''; ?>>
		</div>
		<div class="medium-4 columns">
			<label for="anagrafica[nato_il]">Nato il</label>
			<input id="anagrafica[nato_il]" name="anagrafica[nato_il]" type="date"<?php echo isset( $anagrafica['nato_il'] ) ? get_value_html( $anagrafica['nato_il'] ) : ''; ?>>
		</div>
		<div class="medium-4 columns">
			<label for="anagrafica[cittadinanza]">Cittadinanza</label>
			<input id="anagrafica[cittadinanza]" name="anagrafica[cittadinanza]" type="text"<?php echo isset( $anagrafica['cittadinanza'] ) ? get_value_html( $anagrafica['cittadinanza'] ) : ''; ?>>
		</div>
	</div>
	<fieldset>
		<legend>Residenza</legend>
		<div class="row">
			<div class="medium-6 columns">
				<label for="anagrafica[residenza][via]">Via</label>
				<input id="anagrafica[residenza][via]" name="anagrafica[residenza][via]" type="text"<?php echo isset( $anagrafica['residenza']['via'] ) ? get_value_html( $anagrafica['residenza']['via'] ) : ''; ?>>
			</div>
			<div class="medium-6 columns">
				<label for="anagrafica[residenza][city]">Citt&agrave;</label>
				<input id="anagrafica[residenza][city]" name="anagrafica[residenza][city]" type="text"<?php echo isset( $anagrafica['residenza']['city'] ) ? get_value_html( $anagrafica['residenza']['city'] ) : ''; ?>>
			</div>
		</div>
		<div class="row">
			<div class="medium-6 columns">
				<label for="anagrafica[residenza][provincia]">Provincia</label>
				<input id="anagrafica[residenza][provincia]" name="anagrafica[residenza][provincia]" type="text"<?php echo isset( $anagrafica['residenza']['provincia'] ) ? get_value_html( $anagrafica['residenza']['provincia'] ) : ''; ?>>
			</div>
			<div class="medium-6 columns">
				<label for="anagrafica[residenza][CAP]">CAP</label>
				<input id="anagrafica[residenza][CAP]" name="anagrafica[residenza][CAP]" type="text"<?php echo isset( $anagrafica['residenza']['CAP'] ) ? get_value_html( $anagrafica['residenza']['CAP'] ) : ''; ?>>
			</div>
		</div>
	</fieldset>
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

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );
	if ( isset( $_POST['password'] ) ) {
		if ( ! isset( $_POST['pass-confirm'] ) ) {
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
		if ( $key == 'anagrafica' ) {
			$valid[$key] = serialize( $value );
		} elseif ( ( isset( $user->{$key} ) && $user->{$key} == $value ) || empty( $value ) ) {
			unset( $valid[$key] );
		} else {
			$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
		}
	}

	//var_dump( $valid );

	KYSS_User::update( $id, $valid );
}