<?php
/**
 * Render KYSS Budget add and edit form.
 *
 * @package  KYSS
 * @subpackage  Partials
 * @since  0.12.0
 */

// Handle some errors in a dev-friendly way.
if ( ! in_array( $action, array( 'edit', 'add' ) ) )
	trigger_error( 'Unrecognised action' . $action, E_USER_ERROR );

if ( $action == 'edit' && empty( $id ) ) {
	$message = 'Bilancio da modificare non specificato!';
	kyss_die( $message, '', array( 'back_link' => true ) );
}

switch( $action ) {
	case 'edit' :
		if ( isset( $_GET['save'] ) && $_GET['save'] == 'true' ) {
			$budget = validate_budget_data();
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

			$id = KYSS_Budget::create( $data );
			$budget = KYSS_Budget::get( $id );
		}
		break;
}

if ( isset( $budget ) )
	$after_save = $budget;
if ( ! isset( $budget ) || is_kyss_error( $budget ) )
	$budget = KYSS_Budget::get( $id );
$reports = KYSS_Report::get_list();
?>

<?php if ( $action == 'edit' ) : ?>
	<h1 class="page-title">Modifica bilancio 
		<small>
			<?php echo ( isset( $budget->mese ) ? ( $budget->mese . ' ' ) : '' ) . $budget->anno ; ?>
		</small>
	</h1>
<?php elseif ( $action == 'add' ) : ?>
	<h1 class="page-title">Nuovo bilancio</h1>
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

if ( isset( $after_save ) )
	alert_save( $after_save );
?>

<form id="<?php echo $action; ?>-budget" method="post" action="budgets.php?<?php echo $form_action; ?>" data-abide>
	<div class="row">
		<div class="medium-4 columns">
			<label for="tipo">Tipo</label>
			<select name="tipo">
			<?php foreach ( array(
				'mensile' => 'Mensile',
				'consuntivo' => 'Consuntivo',
				'preventivo' => 'Preventivo'
				) as $type => $name ) : ?>
				<option value="<?php echo $type; ?>"<?php echo isset( $budget->tipo ) ? selected( $budget->tipo, $type, false ) : ''; ?>><?php echo $name; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="medium-4 columns">
			<label for="mese">Mese</label>
			<select name="mese">
				<option value="NULL"<?php echo !isset( $budget->mese ) ? selected( true, true, false ) : ''; ?>>
					-
				</option>
			<?php foreach ( array('Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre') as $month ) : ?>
				<option value="<?php echo $month; ?>"<?php echo isset( $budget->mese ) ? selected( $budget->mese, $month, false ) : ''; ?>><?php echo $month; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="medium-4 columns">
			<label for="anno">Anno</label>
			<input id="anno" name="anno" type="text"<?php echo isset( $budget->anno ) ? get_value_html( $budget->anno ) : ''; ?> required>
			<?php field_error(); ?>
		</div>
	</div>
	<fieldset>
		<legend>Fondi</legend>
		<div class="row">
			<div class="medium-6 columns">
				<label for="cassa">Cassa</label>
				<input id="cassa" name="cassa" type="text"<?php echo isset( $budget->cassa ) ? get_value_html( $budget->cassa ) : ''; ?> required>
				<?php field_error(); ?>
			</div>
			<div class="medium-6 columns">
				<label for="banca">Banca</label>
				<input id="banca" name="banca" type="text"<?php echo isset( $budget->banca ) ? get_value_html( $budget->banca ) : ''; ?> required>
				<?php field_error(); ?>
			</div>
		</div>
	</fieldset>
	<div class="row">
		<div class="medium-6 columns">
			<label for="approvato">Stato</label>
			<select name="approvato">
				<option value="NULL"<?php echo isset( $budget->approvato ) ? get_value_html( $budget->approvato ) : ''; ?>>
					-
				</option>
			<?php foreach ( array(
				'1' => 'Approvato',
				'0' => 'Non approvato'
				) as $type => $name ) : ?>
				<option value="<?php echo $type; ?>"<?php echo isset( $budget->approvato ) ? selected( $budget->approvato, $type, false ) : ''; ?>><?php echo $name; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<div class="medium-6 columns">
			<label for="verbale">Verbale</label>
			<select name="verbale">
				<option value="NULL"<?php echo isset( $budget->verbale ) ? get_value_html( $budget->verbale ) : ''; ?>>
					-
				</option>
			<?php foreach ( $reports as $report ) : ?>
				<option value="<?php echo $report->protocollo; ?>"<?php echo isset( $budget->verbale ) ? selected( $budget->verbale, $report->protocollo, false ) : ''; ?>>
					<?php echo 'Verbale ' . $report->protocollo; ?>
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
			<a href="<?php echo get_site_url( 'budgets.php' ); ?>" class="button">Annulla</a>
		</div>
	</div>
</form>

<?php
/**
 *
 * Validate Budget input data.
 *
 * @since  0.12.0
 *
 * @global id
 * @global kyssdb
 *
 * @return array Associative array of budget data ready to be saved.
 */
function validate_budget_data() {
	global $id, $kyssdb;

	if ( isset( $_POST['submit'] ) )
		unset( $_POST['submit'] );

	$valid = array();
	foreach ($_POST as $key => $value) {
		if ( empty( $value ) )
			continue;
		$valid[$key] = $kyssdb->real_escape_string( trim( $value ) );
	}

	$budget = KYSS_Budget::get( $prot );
	return $budget->update( $valid );
}