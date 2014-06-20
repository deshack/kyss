<?php

/**
 * Create the config.php file.
 *
 * The permissions for the base directory must allow for writing files in order
 * for the config.php to be created automatically. If the permissions lack, the
 * user is given the content so that he himself creates the file.
 *
 * @package KYSS
 * @subpackage  Setup
 */

/**
 * We are installing.
 *
 * @since  0.3.0
 * @var  bool
 */
define('INSTALLING', true);

// Disable error reporting.
//error_reporting(0);
// Enable error reporting.
error_reporting( E_ALL );
ini_set('display_errors', 1);

// These defines are required to allow us to build paths.
define('ABSPATH', dirname(dirname(__FILE__)) . '/');
define('INC', ABSPATH . 'inc/');

require(INC . 'version.php');
require_once(INC . 'functions.php');
require_once(INC . 'load.php');
require_once(INC . 'formatting.php');
require_once(INC . 'classes/kyss-error.php');

// Check if config.php has been created.
if ( file_exists( ABSPATH . 'config.php' ) )
	kyss_die( '<p>' . sprintf( "The file 'config.php' already exists. If you need to reset any of the configuration items in this file, please delete it first. You may try <a href='%s'>installing now</a>.", 'install.php' ) . '</p>' );

// Start or resume session.
session_start();

$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 0;

switch($step) {
	case 0:
		setup_config_header();
		setup_config_first();
		setup_config_footer();
		break;
	case 1:
		setup_config_header();
		setup_config_second();
		setup_config_footer();
		break;
	case 2:
		setup_config_third();
		break;
	case 3:
		setup_config_header();
		setup_config_create_form();
		setup_config_footer();
		break;
	case 4:
		setup_config_create();
		break;
	case 5:
		setup_config_write();
		setup_config_session_destroy();
		break;
}

/**
 * Display setup page header.
 *
 * @since  0.3.0
 * @global  kyss_version
 *
 * @return  null
 */
function setup_config_header() {
	global $kyss_version;

	header( 'Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>KYSS &rsaquo; Setup Configuration File</title>
	<?php
	/**
	 * RegEx '/[^0-9a-z\.-]/i' means "match all single chaaracters that are NOT in the range
	 * 0-9, a-z (case-insensitive), . (dot), or - (dash)".
	 * Details:
	 * - [] = single character
	 * - ^ = negate expression
	 * - 0-9 = 0123456789
	 * - a-z = from 'a' to 'z'
	 * - \. = dot escaped
	 * - - = dash
	 * - /i = case-insensitive match
	 */
	
	// Passing the version parameter allows a proper caching of the resource.
	$version = preg_replace( '/[^0-9a-z\.-]/i', '', $kyss_version );
?>
	<link rel="stylesheet" href="../assets/css/install.css?ver=<?php echo $version; ?>" type="text/css" />
	<link rel="stylesheet" href="../assets/css/buttons.css?ver=<?php echo $version; ?>" type="text/css" />
	<link rel="stylesheet" href="../assets/css/forms.css?ver=<?php echo $version; ?>" type="text/css" />
</head>
<body>
<div class="container">
	<h1 id="logo">KYSS</h1>
<?php
} // End setup_config_header()

/**
 * Display setup page footer.
 *
 * @since  0.3.0
 * @global  step
 */
function setup_config_footer() {
	global $step;

	if ( $step == 1 ) :
?>
<script type="text/javascript">
var button = document.getElementById('show-button');

button.onclick = function() {
	var pass = document.getElementById('dbpass');

	if(pass.type=='password') {
		pass.type = 'text';
		button.innerHTML = "Nascondi";
	} else {
		pass.type = 'password';
		button.innerHTML = " Mostra ";
	}
};
</script>
</body>
</html>
<?php
	endif;
} // End setup_config_footer()

/**
 * Display information needed.
 *
 * Displays the informations needed in order to create the config.php file.
 *
 * @since  0.3.0
 */
function setup_config_first() {
?>
	<p>Benvenuto su KYSS. Per continuare abbiamo bisogno delle seguenti informazioni sul database:</p>
	<ol>
		<li>Host</li>
		<li>Username</li>
		<li>Password</li>
		<li>Nome</li>
	</ol>
	<p>Se non hai gi&agrave; un database ma l'utente ha i permessi necessari, lo creeremo successivamente.</p>
	<p><a href="setup-config.php?step=1" class="button primary">Comincia</a></p>
<?php
} // End setup_config_first()

/**
 * Database info form.
 *
 * @since  0.3.0
 */
function setup_config_second() {
	$dbhost = isset($_SESSION['dbhost']) ? $_SESSION['dbhost'] : 'localhost';
	$dbuser = isset($_SESSION['dbuser']) ? ' value="' . $_SESSION['dbuser'] . '"' : '';
	$dbname = isset($_SESSION['dbname']) ? ' value="' . $_SESSION['dbname'] . '"' : '';

	echo join(' - ', array( $dbhost, $dbuser, $dbname ) );
?>
<form method="post" action="setup-config.php?step=2">
	<fieldset>
		<legend>Inserisci i dettagli della connessione al database.</legend>

		<label for="dbhost">Database Host</label>
		<input name="dbhost" id="dbhost" type="text" size="25" value="<?php echo $dbhost; ?>" />
		<p class="help">La scelta pi&ugrave; comune &egrave; <em>localhost</em>, ma il tuo hosting potrebbe averti dato un indirizzo diverso.</p>

		<label for="dbuser">Database Username</label>
		<input name="dbuser" id="dbuser" type="text" size="25"<?php echo $dbuser; ?> />

		<label for="dbpass">Database Password</label>
		<div class="input-group">
			<input name="dbpass" id="dbpass" type="password" size="25" autocomplete="off" />
			<span class="addon">
				<button type="button" id="show-button">Mostra</button>
			</span>
		</div>

		<label for="dbname">Nome Database</label>
		<input name="dbname" id="dbname" type="text" size="25"<?php echo $dbname; ?> />
		<p class="help">Lascia <strong>vuoto</strong> questo campo per creare un nuovo database.</p>

		<p><input name="submit" type="submit" value="Conferma" class="button primary" /></p>
	</fieldset>
</form>
<?php
} // End setup_config_second()

/**
 * Check Database Name and redirect to the correct step.
 *
 * Stores posted values in the $_SESSION superglobal array.
 *
 * @since  0.9.0
 */
function setup_config_third() {
	if ( ! isset( $_POST['dbname'] ) || empty( $_POST['dbname'] ) )
		$_POST['dbname'] = '';

	foreach ( array( 'dbhost', 'dbuser', 'dbpass', 'dbname' ) as $key )
		$_SESSION[$key] = trim( stripslashes_deep( $_POST[$key] ) );

	if ( empty( $_SESSION['dbname'] ) || '' == $_SESSION['dbname'] ) {
		header("location:setup-config.php?step=3");
		exit;
	} else {
		header("location:setup-config.php?step=5");
		exit;
	}
}

/**
 * Database creation form.
 *
 * @since  0.9.0
 */
function setup_config_create_form() {
?>
<form method="post" action="setup-config.php?step=4">
	<fieldset>
		<legend>Inserisci il nome del database da creare.</legend>

		<label for="dbname">Nome Database</label>
		<input name="dbname" id="dbname" type="text" size="25" />

		<p><input name="submit" type="submit" value="Crea database" class="button primary" />
		<a href="setup-config.php?step=2" onclick="javascript:history.go(-1);return false;" class="button">Indietro</a></p>
	</fieldset>
</form>
<?php
} // End setup_config_create_form()

/**
 * Create new database.
 *
 * Attempts to create a new database. If the user doesn't have the appropriate permissions,
 * the creation fails and an error will be displayed.
 *
 * @since  0.9.0
 * @global  kyssdb
 */
function setup_config_create() {
	global $kyssdb;

	$tryagain = '</p><p><a href="setup-config.php?step=2" onclick="javascript:history.go(-2);return false;" class="button primary">Riprova</a>';

	$_SESSION['dbname'] = trim( stripslashes_deep( $_POST['dbname'] ) );

	// Test database connection.
	define('DB_HOST', $_SESSION['dbhost']);
	define('DB_USER', $_SESSION['dbuser']);
	define('DB_PASS', $_SESSION['dbpass']);

	load_kyssdb();

	if ( $kyssdb->has_error() )
		kyss_die( $kyssdb->last_error->get_error_message() . $tryagain );

	// Connection successful. Try to create a new database.
	$kyssdb->create( $_SESSION['dbname'] );

	if ( $kyssdb->has_error() )
		if ( is_a( $kyssdb->last_error, 'KYSS_Error' ) )
			kyss_die( $kyssdb->last_error->get_error_message() . $tryagain );
		else
			kyss_die( $kyssdb->last_error . $tryagain );

	// Both database connection and creation have been successful. Go on!
	header("location:setup-config.php?step=5");
	exit;
}

/**
 * Write configuration file.
 *
 * If the KYSS root directory is writable creates the config.php file, otherwise
 * displays the generated content and requests the user to create the file manually.
 *
 * @since  0.9.0
 */
function setup_config_write() {
	$config_file = ABSPATH . 'config.php';

	define('DB_HOST', $_SESSION['dbhost']);
	define('DB_USER', $_SESSION['dbuser']);
	define('DB_PASS', $_SESSION['dbpass']);
	define('DB_NAME', $_SESSION['dbname']);

	// Prepare content for the config file.
	$constants = array(
		'DB_HOST',
		'DB_USER',
		'DB_PASS',
		'DB_NAME'
	);

	$content = "<?php
/**
 * Main KYSS configuration file.
 *
 * Defines constants for the database connection and application ABSPATH.
 * Last calls the settings.php file, which is responsible for loading the application.
 *
 * @package KYSS
 * @subpackage Loader
 */\r\n\r\n";

	foreach ( $constants as $const )
		$content .= "define('" . $const . "', '" . addcslashes( constant( $const ), "\\'" ) . "');\r\n";

	$content .= "\r\n
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/**
 * Sets up KYSS variables and includes needed files.
 */
require_once(ABSPATH . 'settings.php');";

	if ( ! is_writable(ABSPATH) ) :
		setup_config_header();
?>
	<p>Spiacente, non posso creare il file <code>config.php</code></p>
	<p>Puoi crearlo manualmente ed incollare il testo seguente al suo interno.</p>
	<textarea id="config" cols="98" rows="15" class="code" readonly="readonly"><?php
		echo htmlentities($content, ENT_COMPAT, 'UTF-8');
	?></textarea>
	<p>Fatto questo, clicca &#8221;Installa&#8221;.</p>
	<p><a href="install.php" class="button primary">Installa</a></p>
	<script>
(function(){
	var el=document.getElementById('config');
	el.focus();
	el.select();
})();
	</script>
<?php
		setup_config_footer();
	else :
		// We can write into the ABSPATH directory!
		$handle = fopen( $config_file, 'w' );
	fwrite( $handle, $content );
	// Change file permissions for security reasons.
	chmod( $config_file, 0666 );
	setup_config_header();
?>
	<p>Perfetto! Hai superato la parte pi&ugrave; difficile dell'installazione. KYSS ora pu&ograve; comunicare con il tuo database. Se sei pronto&hellip;</p>
	<p><a href="install.php" class="button primary">Installa</a></p>
<?php
		setup_config_footer();
	endif; // !is_writable(ABSPATH)
}

/**
 * Destroy session.
 *
 * Unsets session variables, deletes session cookie and destroys the session.
 *
 * @since  0.9.0
 * 
 * @return null
 */
function setup_config_session_destroy() {
	// Unset session variables.
	$_SESSION = array();
	// Delete session cookie
	$sname = session_name();
	if ( isset( $_COOKIE[$sname] ) ) {
		$par = session_get_cookie_params();
		setcookie($sname, '', time() - 42000, $par['path'], $par['domain'], $par['secure'], $par['httponly']);
	}
	// Destroy session.
	session_destroy();
}