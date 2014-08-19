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

if ( $action == 'edit' ) {
	$user = KYSS_User::get_user_by('id', $id);
	//if ( ! $user )
		// Handle error in some way
	if ( $user->num_rows == 0 ) {
		trigger_error( 'Unknown user id ' . $id, E_USER_WARNING );
		// TODO: Maybe redirect to users list.
	} else {
		$user = $user->fetch_object();
	}
}
?>

<h1 class="page-title">Modifica utente <small><?php echo $user->nome . ' ' . $user->cognome; ?></small></h1>

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
<form id="edit-user" method="post" action="users.php?<?php echo $form_action; ?>">
	<div class="row">
		<div class="medium-6 columns">
			<label for="nome">Nome</label>
			<input name="nome" type="text">
		</div>
		<div class="medium-6 columns">
			<label for="cognome">Cognome</label>
			<input name="cognome" type="text">
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="email">Email</label>
			<input name="email" type="email">
		</div>
		<div class="medium-6 columns">
			<label for="tel">Telefono</label>
			<input name="tel" type="tel">
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="pass">Password</label>
			<input name="pass" type="password">
		</div>
		<div class="medium-6 columns">
			<label for="pass_confirm">Conferma password</label>
			<input name="pass_confirm" type="password">
		</div>
	</div>
</form>