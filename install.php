<?php
/**
 * KYSS Installer
 *
 * @package  KYSS
 * @subpackage  Installer
 */

namespace admin\install;

// Sanity check.
if ( false ) :
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Error: PHP is not running</title>
</head>
<body>
	<h1 id="logo">KYSS</h1>
	<h2>Error: PHP is not running</h2>
	<p>KYSS requires that your web server is running PHP. Your server does not have PHP installed, or PHP is turned off.</p>
</body>
</html>
<?php
endif;

/**
 * We are installing KYSS.
 *
 * This constant is defined in the global namespace.
 *
 * @since  0.6.0
 * @var  bool
 */
define( 'INSTALLING', true );

/**
 * Load KYSS Bootstrap.
 */
require_once( dirname(__FILE__) . '/load.php' );

/**
 * Load KYSS Administration Upgrade API.
 */
require_once( ABSPATH . 'admin/inc/upgrade.php');

/**
 * Load KYSS Options API.
 */
require_once( ABSPATH . 'inc/options.php' );

/**
 * Load kyssdb.
 */
require_once( CLASSES . 'kyss-db.php' );

/**
 * Load KYSS_Pass
 */
require_once( CLASSES . 'kyss-pass.php' );

$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 0;

switch ( $step ) {
	case 0:
		display_header();
		form();
		footer();
		break;
	case 1:
		display_header();
		validate();
		footer();
		break;
}

/**
 * Display install header.
 *
 * @since  0.6.0
 */
function display_header() {
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta charset="UTF-8" />
	<title>KYSS &rsaquo; Installation</title>
	<?php kyss_css( 'install', true ); ?>
</head>
<body>
<div class="container">
	<h1 id="logo">KYSS</h1>
<?php
} // End display_header()

/**
 * Display install footer.
 *
 * @since  0.6.0
 */
function footer() {
?>
</div>
</body>
</html>
<?php
} // End footer()

/**
 * Display install form.
 *
 * @since  0.9.0
 *
 * @param  string|null $error Error message (if any).
 */
function form( $error = null ) {
	global $kyssdb;

	$assoc_name = isset( $_POST['assoc_name'] ) ? trim( unslash( $_POST['assoc_name'] ) ) : '';
	$admin_name = isset( $_POST['admin_name'] ) ? trim( unslash( $_POST['admin_name'] ) ) : '';
	$admin_surname = isset( $_POST['admin_surname'] ) ? trim( unslash( $_POST['admin_surname'] ) ) : '';
	$admin_email = isset( $_POST['admin_email'] ) ? trim( unslash( $_POST['admin_email'] ) ) : '';
	$admin_password = isset( $_POST['admin_password'] ) ? trim( unslash( $_POST['admin_password'] ) ) : '';
?>
	<section>
		<p>Benvenuto nell'installer di KYSS!</p>
		<p>Sono necessarie le seguenti informazioni:</p>
	</section>
<?php
	if ( ! is_null( $error ) ) :
?>
	<div data-alert class="alert-box error"><?php echo $error; ?></p>
<?php
	endif;
?>
	<form id="setup" method="post" action="install.php?step=1">
		<div class="row">
			<div class="medium-12 columns">
				<label for="assoc_name">Nome dell'associazione</label>
				<input name="assoc_name" id="assoc_name" type="text" autofocus>
			</div>
		</div>
		<fieldset>
			<legend>Utente amministratore</legend>
			<div class="row">
				<div class="medium-6 columns">
					<label for="admin_name">Nome</label>
					<input name="admin_name" id="admin_name" type="text">
				</div>
				<div class="medium-6 columns">
					<label for="admin_surname">Cognome</label>
					<input name="admin_surname" id="admin_surname" type="text">
				</div>
			</div>
			<div class="row">
				<div class="medium-12 columns">
					<label for="admin_email">Email</label>
					<input name="admin_email" id="admin_email" type="email">
				</div>
			</div>
			<div class="row">
				<div class="medium-6 columns">
					<label for="admin_password">Password</label>
					<input name="admin_password" id="admin_password" type="password">
				</div>
				<div class="medium-6 columns">
					<label for="admin_password_check">Conferma password</label>
					<input name="admin_password_check" id="admin_password_check" type="password">
				</div>
				<p class="help">Lasciare <strong>vuoto</strong> il campo <em>Password</em> per generare automaticamente una password.</p>
			</div>
		</fieldset>
		<div class="row">
			<div class="small-4 columns">
				<input type="submit" name="submit" value="Installa KYSS" class="button">
			</div>
		</div>
	</form>
<?php
} // End form()

/**
 * Validate form data.
 *
 * @since  0.9.0
 */
function validate() {
	$assoc_name = isset( $_POST['assoc_name'] ) ? trim( unslash( $_POST['assoc_name'] ) ) : '';
	$admin_name = isset( $_POST['admin_name'] ) ? trim( unslash( $_POST['admin_name'] ) ) : '';
	$admin_surname = isset( $_POST['admin_surname'] ) ? trim( unslash( $_POST['admin_surname'] ) ) : '';
	$admin_email = isset( $_POST['admin_email'] ) ? trim( unslash( $_POST['admin_email'] ) ) : '';
	$admin_password = isset( $_POST['admin_password'] ) ? trim( unslash( $_POST['admin_password'] ) ) : '';
	$admin_password_check = isset( $_POST['admin_password_check'] ) ? trim( unslash( $_POST['admin_password_check'] ) ) : '';

	$error = false;
	if ( empty( $admin_name ) ) {
		form( "Devi inserire il nome dell'amministratore." );
		$error = true;
	} elseif ( empty( $admin_surname ) ) {
		form( "Devi inserire il cognome dell'amministratore." );
		$error = true;
	} elseif ( $admin_password != $admin_password_check ) {
		// TODO: perform this check at runtime and disable submit if they don't match.
		form( "Le password non coincidono. Riprovare." );
		$error = true;
	} elseif ( empty( $admin_email ) ) {
		form( "Devi inserire l'email dell'amministratore." );
		$error = true;
	} elseif ( ! is_email( $admin_email ) ) {
		form( "L'indirizzo email inserito non &egrave; valido." );
		$error = true;
	}

	if ( $error === false ) :
		$result = kyss_install( $assoc_name, $admin_name, $admin_surname, $admin_email, slash( $admin_password ) );
		extract( $result, EXTR_SKIP );
?>

<h2>Installazione avvenuta con successo!</h2>

<table>
	<tr>
		<th scope="row">Email</th>
		<td><?php echo $admin_email; ?></td>
	</tr>
	<tr>
		<th scope="row">Password</th>
		<td> <?php
		if ( ! empty( $password ) && empty( $admin_password_check ) )
			echo '<code>' . $password . '</code><br>';
		if ( ! empty ( $password_message ) )
			echo $password_message; ?>
		</td>
	</tr>
</table>

<p class="step"><a href="<?php echo get_option( 'siteurl' ); ?>/login.php" class="button primary">Login</a></p>

<?php
	endif; // $error === false
} // End validate()
