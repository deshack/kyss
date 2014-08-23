<?php
/**
 * Render KYSS Event add and edit form.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.11.0
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
			$nome = isset( $_POST['nome'] ) ? $_POST['nome'] : '';
			$inizio = $_POST['inizio'];
			$fine = isset( $_POST['fine'] ) ? $_POST['fine'] : '';
			$id = KYSS_Event::create( $nome, $inizio, $fine );
			kyss_redirect( get_site_url( '/events.php' ) );
		}
		break;
}

$event = KYSS_Event::get_event_by( 'id', $id );

?>

<?php if ( $action == 'edit' || ( $action == 'add' && isset( $_GET['save'] ) && $_GET['save'] == 'true' ) ) : ?>
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
<form id="<?php echo $action; ?>-event" method="post" action="events.php?<?php echo $form_action; ?>">
	<div class="row">
		<div class="medium-12 columns">
			<label for="nome">Nome</label>
			<input id="nome" name="nome" type="text"<?php echo isset( $event->nome ) ? get_value_html( $event->nome ) : '' ?>>
		</div>
	</div>
	<div class="row">
		<div class="medium-6 columns">
			<label for="inizio">Inizio</label>
			<input id="inizio" name="inizio" type="date"<?php echo isset( $event->inizio ) ? get_value_html( $event->inizio ) : '' ?> required>
		</div>
		<div class="medium-6 columns">
			<label for="fine">Fine</label>
			<input id="fine" name="fine" type="date"<?php echo isset( $event->fine ) ? get_value_html( $event->fine ) : '' ?>>
		</div>
	</div>
	<div class="row action-buttons text-center">
		<div class="small-6 columns">
			<input type="submit" class="button" name="submit" value="Salva">
		</div>
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'events.php' ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php
/**
 *
 * Validate event input data.
 *
 * @since  
 *
 * @global  id
 * @global  kyssdb
 *
 * @return array Associative array of event data ready to be saved.
 */
function validate_event_data() {
	global $id, $kyssdb;

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );
	$valid = array();
	foreach ($_POST as $key => $value) {
		$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
	}

	KYSS_Event::update( $id, $valid );	
}