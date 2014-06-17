<?php
/**
 * Create the config.php file.
 *
 * The permissions for the base directory must allow for writing files in order
 * for the config.php to be create automatically. If the permissions lack, the
 * user is given the content so that he himself creates the file.
 *
 * @package  KYSS
 * @subpackage  Setup
 */

/**
 * We are installing
 *
 * @since  0.3.0
 * @var  bool
 */
define('KYSS_INSTALLING', true);

/**
 * Disable error reporting.
 */
//error_reporting(0);
/**
 * Enable error reporting.
 *
 * Uncomment the following lines to display errors.
 */
error_reporting( E_ALL );
ini_set('display_errors', 'on');

// These defines are required to allow us to build paths.
define('ABSPATH', dirname(dirname(__FILE__)) . '/');
define('INC', 'inc');

require(ABSPATH . INC . '/version.php');
require_once(ABSPATH . INC . '/functions.php');
require_once(ABSPATH . INC . '/load.php');
require_once(ABSPATH . INC . '/formatting.php');
require_once(ABSPATH . INC . '/classes/kyss-error.php');

// Check if config.php has been created
if ( file_exists( ABSPATH . 'config.php' ) )
	kyss_die( '<p>' . sprintf( "The file 'config.php' already exists. If you need to reset any of the configuration items in this file, please delete it first. You may try <a href='%s'>installing now</a>.", 'install.php' ) . '</p>' );

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
}

/**
 * Display setup config.php file header.
 *
 * @since  0.3.0
 * @global  $kyss_version
 *
 * @return  null
 */
function setup_config_header() {
	global $kyss_version, $step;

	header( 'Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
	<title>KYSS &rsaquo; Setup Configuration File</title>
	<?php
	/**
	 * RegEx '/[^0-9a-z\.-]/i' means "match all single characters that are NOT in the range 0-9, a-z (case-insensitive),
	 * . (dot), or - (dash)"
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
	?>
	<link rel="stylesheet" href="../assets/css/install.css?ver=<?php echo preg_replace( '/[^0-9a-z\.-]/i', '', $kyss_version ); ?>" type="text/css" />
	<link rel="stylesheet" href="../assets/css/buttons.css?ver=<?php echo preg_replace( '/[^0-9a-z\.-]/i', '', $kyss_version ); ?>" type="text/css" />
<?php if ($step == 1 || $step == 2) : ?>
	<link rel="stylesheet" href="../assets/css/forms.css?ver=<?php echo preg_replace( '/[^0-9a-z\.-]/i', '', $kyss_version ); ?>" type="text/css" />
<?php endif; ?>
</head>
<body>
<div class="container">
	<h1 id="logo">KYSS</h1>
<?php
} // End setup_config_header()

/**
 * Display setup config.php file footer.
 *
 * @since  0.3.0
 */
function setup_config_footer() {
	global $step;

if ( $step == 1 ) : ?>
<script type="text/javascript">
var button = document.getElementById('show-button');

button.onclick = function() {
	var pass = document.getElementById('dbpass');

	if(pass.type=='password') {
		pass.type = 'text';
		button.innerHTML = "Hide";
	} else {
		pass.type = 'password';
		button.innerHTML = "Show";
	}
};
</script>
<?php endif; ?>
</div>
</body>
</html>
<?php
} // End setup_config_footer()

function setup_config_first() {
?>
	<p>Welcome to KYSS. In order to proceed we need the following informations:</p>
	<ol>
		<li>Database host</li>
		<li>Database name</li>
		<li>Database username</li>
		<li>Database password</li>
	</ol>
	<p><a href="setup-config.php?step=1" class="button primary">Start</a></p>
<?php
} // End setup_config_first()

/**
 * Database info form.
 *
 * @since  0.3.0
 *
 * @return null
 */
function setup_config_second() {
?>

<form method="post" action="setup-config.php?step=2">
	<fieldset>
		<legend>Enter your database connection details below.</legend>

		<label for="dbhost">Database Host</label>
		<input name="dbhost" id="dbhost" type="text" size="25" value="localhost" />
		<p class="help"><em>localhost</em> might probably be your Database Host.</p>

		<label for="dbname">Database Name</label>
		<input name="dbname" id="dbname" type="text" size="25" placeholder="kyss" />

		<label for="dbuser">Database Username</label>
		<input name="dbuser" id="dbuser" type="text" size="25" placeholder="username" />

		<label for="dbpass">Database Password</label>
		<div class="input-group">
			<input name="dbpass" id="dbpass" type="password" size="25" autocomplete="off" />
			<span class="addon">
				<button type="button" id="show-button" onclick="showPassword();">Show</button>
			</span>
		</div>

		<input name="dbcreate" id="dbcreate" type="checkbox" />
		<label for="dbcreate">Create new database?</label>

		<p><input name="submit" type="submit" value="Submit" class="button primary" /></p>
	</fieldset>
</form>
<?php
} // End setup_config_second()

/**
 * Check db info and write config file.
 *
 * Checks the provided database informations and if the KYSS root directory is writable
 * creates the config.php file, otherwise displays the generated content and requests
 * the user to create the file manually.
 * Last asks the user to run the install.
 *
 * @since  0.3.0
 * 
 * @return null
 */
function setup_config_third() {
	global $kyssdb;

	foreach ( array( 'dbhost', 'dbname', 'dbuser', 'dbpass' ) as $key )
		$$key = trim( stripslashes_deep( $_POST[$key] ) );
	$dbcreate = isset($_POST['dbcreate']) ? $_POST['dbcreate'] : false;

	$tryagain = '</p><p><a href="setup-config.php?step=1" onclick="javascript:history.go(-1);return false;" class="button primary">Try again</a>';

	// Test db connection.
	define('DB_HOST', $dbhost);
	define('DB_NAME', $dbname);
	define('DB_USER', $dbuser);
	define('DB_PASS', $dbpass);

	// That's the actual test.
	load_kyssdb();

	// Check for errors in the $kyssdb object.
	if ( !empty( $kyssdb->last_error ) )
		kyss_die( $kyssdb->last_error->get_error_message() . $tryagain );

	$config_file = ABSPATH . 'config.php';

	// Prepare content for the config file.
	$constants = array(
		'DB_HOST',
		'DB_NAME',
		'DB_USER',
		'DB_PASS'
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
	<p>Sorry, but I can&#8217;t write the <code>config.php</code> file.</p>
	<p>You can create it manually and paste the following text into it.</p>
	<textarea id="config" cols="98" rows="15" class="code" readonly="readonly"><?php
		echo htmlentities($content, ENT_COMPAT, 'UTF-8');
	?></textarea>
	<p>After you&#8217;ve done that, click &#8221;Run the install&#8221;.</p>
	<p><a href="install.php" class="button primary">Run the install</a></p>
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
		fclose( $handle );
		// Change file permissions for security reasons.
		chmod( $config_file, 0666 );
		setup_config_header();
?>
	<p>All right, you&#8217;ve made it through the hardest part of the installation. KYSS can now communicate with your database.
		If you are ready, it&#8217;s time to&hellip;</p>
	<p><a href="install.php" class="button primary">Run the install</a></p>
<?php
		setup_config_footer();
	endif; // !is_writable(ABSPATH)
}
