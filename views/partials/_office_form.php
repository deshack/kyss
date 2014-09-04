<?php
/**
 * Render KYSS Office add and edit form.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

// Prevent accessing this page directly.
if ( ! defined( 'ABSPATH' ) )
	kyss_die( 'You cannot access this page directly!' );

if ( $action == 'edit' && ( empty( $slug ) || empty( $start ) ) {
	$message = 'Stai tentando di modificare una carica, ma non hai specificato quale.';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) :
	switch ( $action ) {
		case 'edit':
			validate_office_data( $slug, $start );
			break;
		case 'add':
			$office = isset( $_POST['carica'] ) ? $_POST['carica'] : null;
			$start = isset( $_POST['inizio'] ) ? $_POST['inizio'] : null;
			$user = isset( $_POST['utente'] ) ? $_POST['utente'] : null;
			$end = isset( $_POST['fine'] ) ? $_POST['fine'] : null;
			$office = KYSS_Office::create( $office, $start, $user, $end );
			kyss_redirect( get_site_url( '/offices.php' );
			break;
	}
endif;

$office = KYSS_Office::get( $slug, $start );
?>

<h1 class="page-title">
<?php if ( $action == 'edit' ) : ?>
	Modifica carica <small><?php echo $office->carica; ?></small>
<?php else : ?>
	Nuova carica
<?php endif; ?>
</h1>

<?php
$form_action = '';
switch ( $action ) {
	case 'edit':
		$form_action = 'action=edit&office=' . $slug . '&start=' . $start . '&save=true';
		break;
	case 'add':
		$form_action = 'action=add&save=true';
		break;
}

$offices_list = KYSS_Office::get_defaults();
$users_list = KYSS_User::get_users_list()
?>

<form id="<?php echo $action; ?>-office" method="post" action="offices.php?<?php echo $form_action; ?>" data-abide>
	<div class="row">
	<?php if ( $action == 'add ' ) : ?>
		<div class="medium-6 columns">
			<label for="carica">Carica</label>
			<select name="carica">
			<?php foreach ( $offices_list as $office ) : ?>
				<option value="<?php echo $office->carica; ?>"><?php echo ucfirst( $office->carica ); ?></option>
			<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>
		<div class="medium-6 columns<?php echo ($action == 'edit') ? 'medium-offset-3 end' : ''; ?>">
			<label for="utente">Utente</label>
			<select name="utente">
			<?php foreach ( $users_list as $user ) : ?>
				<option value="<?php echo $user->ID; ?>"<?php echo isset( $office->utente ) ? selected( $office->utente, $user->ID, false ) : ''; ?>>
					<?php echo $user->nome . ' ' . $user->cognome; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="inizio">Inizio</label>
			<input type="date" id="inizio" name="inizio"<?php echo isset( $office->inizio ) ? get_value_html( $office->inizio ) : ''; ?>>
		</div>
		<div class="medium-6 columns">
			<label for="fine">Fine</label>
			<input type="date" id="fine" name="fine"<?php echo isset( $office->fine ) ? get_value_html( $office->fine ) : ''; ?>>
		</div>
	</div>
</form>

<?php
/**
 * Validate Office input data.
 *
 * @since  0.12.0
 *
 * @global  kyssdb
 */
function validate_office_data( $office, $start ) {
	global $kyssdb;

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );

	$valid = array();
	foreach ( $_POST as $key => $value ) {
		if ( empty( $value ) )
			continue;
		$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
	}

	$office = KYSS_Office::get( $office, $start );
	$result = $office->update( $valid );
	if ( is_kyss_error( $result ) )
		kyss_die( $result );
}