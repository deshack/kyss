<?php
/**
 * Render KYSS Report add and edit form.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

// Handle some errors in a dev-friendly way.
if ( ! in_array( $action, array( 'edit', 'add' ) ) )
	trigger_error( 'Unrecognised action' . $action, E_USER_ERROR );

if ( $action == 'edit' && empty( $prot ) ) {
	$message = 'Verbale da modificare non specificato!';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

switch( $action ) {
	case 'edit' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			$report = validate_report_data();
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

			$prot = KYSS_Report::create( $data );
			$report = KYSS_Report::get( $prot );
		}
		break;
}

if ( isset( $report ) )
	$after_save = $report;
if ( ! isset( $report ) || is_kyss_error( $report ) )
	$report = KYSS_Report::get( $prot );
$meetings = KYSS_Meeting::get_list();
?>

<?php if ( $action == 'edit' ) : ?>
	<h1 class="page-title">Modifica verbale 
		<small>
			<?php echo $report->protocollo; ?>
		</small>
	</h1>
<?php elseif ( $action == 'add' ) : ?>
	<h1 class="page-title">Nuovo verbale</h1>
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

<form id="<?php echo $action; ?>-report" method="post" action="reports.php?<?php echo $form_action; ?>" data-abide>
	<div class="row">
		<?php if ( $action == 'add' ) : ?>
		<div class="medium-6 columns">
			<label for="protocollo">Protocollo</label>
			<input id="protocollo" name="protocollo" type="text"<?php echo isset( $report->protocollo ) ? get_value_html( $report->protocollo ) : ''; ?> required>
			<?php field_error(); ?>
		</div>
		<?php endif; ?>
		<div class="medium-6 columns">
			<label for="riunione">Riunione</label>
			<select name="riunione">
			<?php foreach ($meetings as $meeting ) : ?>
				<option value="<?php echo $meeting->ID; ?>"<?php echo isset( $report->riunione ) ? selected( $report->riunione, $meeting->ID, false ) : ''; ?>>
				<?php echo isset( $meeting->name ) ? get_value_html( $meeting->name ) : 'Riunione del ' . date( 'd/m/Y', strtotime( $meeting->data_inizio ) ); ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="medium-12 columns">
			<label for="contenuto">Contenuto</label>
			<input id="contenuto" name="contenuto" type="text"<?php echo isset( $report->contenuto ) ? get_value_html( $report->contenuto ) : ''; ?>>
		</div>
	</div>
	<div class="row action-buttons text-center">
		<div class="small-6 columns">
			<input type="submit" class="button" name="submit" value="Salva">
		</div>
		<div class="small-6 columns">
			<a href="<?php echo get_site_url( 'reports.php' ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php
/**
 *
 * Validate Report input data.
 *
 * @since  0.12.0
 *
 * @global prot
 * @global kyssdb
 *
 * @return array Associative array of report data ready to be saved.
 */
function validate_report_data() {
	global $prot, $kyssdb;

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );

	$valid = array();
	foreach ($_POST as $key => $value) {
		if ( empty( $value ) )
			continue;
		$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
	}

	$report = KYSS_Report::get( $prot );
	return $report->update( $valid );
}