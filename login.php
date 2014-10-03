<?php
/**
 * KYSS Login Page.
 *
 * Handles authentication, registering, resetting passwords, forgot password,
 * and other user handling.
 *
 * @package  KYSS
 * @subpackage  Login
 * @since 0.11.0
 */

namespace login;

/**
 * Make sure that KYSS has loaded before continuing.
 */
require ( dirname( __FILE__ ) . '/load.php' );

$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 0;

switch ( $step ) {
	case 0:
		display_header();
		form();
		display_footer();
		break;
	case 1:
		validate();
		break;
}

/**
 * Display the login page header.
 *
 * @since  0.11.0
 * 
 * @global  hook
 *
 * @param  string $title Optional. KYSS Login Page title to display in the `<title>`
 * element. Default <'Login'>.
 * @param  string $message Optional. Message to display int he header. Default empty.
 * @param  string|KYSS_Error $error Optional. The error to pass. Default empty string.
 */
function display_header( $title = 'Login', $message = '', $error = '' ) {
	global $hook;

	// Don't index any of these forms.
	$hook->add( 'login_head', 'no_robots' );

	// Add the viewport meta tag.
	$hook->add( 'login_head', 'viewport_meta' );

	// Add the charset meta tag.
	$hook->add( 'login_head', 'charset_meta' );

	if ( empty( $error ) )
		$error = new \KYSS_Error();

	// Default error codes.
	$error_codes = array(
		'empty_password',
		'empty_email',
		'invalid_email',
		'incorrect_password'
	);

	/**
	 * Filter the error codes array for the login form.
	 *
	 * @since  0.11.0
	 *
	 * @param  array $error_codes Error codes for the login form.
	 */
	$error_codes = $hook->run( 'login_error_codes', $error_codes );
	?>
<!doctype html>
<!--[if IE 8]>
<html class="ie8" lang="it">
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html lang="it">
<head>
	<title><?php echo $title; ?> &rsaquo; <?php echo get_option( 'sitename' ); ?></title>
	<?php
	kyss_css( 'dashicons', true );
	kyss_css( 'login', true );
	
	/**
	 * Fires in the login page header.
	 *
	 * @since  0.11.0
	 */
	$hook->run( 'login_head' );
	?>
</head>
<body class="login">
<div class="container">
<div class="row">
	<h1 class="page-title text-center">KYSS &rsaquo; Login</h1>
	<?php
	/**
	 * Filter the message to display above the login form.
	 *
	 * @since  0.11.0
	 *
	 * @param  string $message Login message text.
	 */
	$message = $hook->run( 'login_message', $message );
	if ( ! empty( $message ) )
		echo "<p>$message</p>\n";

	if ( $error->get_error_code() ) {
		$errors = '';
		$messages = '';
		foreach ( $error->get_error_codes() as $code ) {
			$severity = $error->get_error_data($code);
			foreach ( $error->get_error_messages($code) as $msg ) {
				if ( 'message' == $severity )
					$messages .= ' ' . $msg . "<br>\n";
				else
					$errors .= ' ' . $msg . "<br>\n";
			}
		}
		if ( ! empty( $errors ) ) {
			/**
			 * Filter the error messages displayed above the login form.
			 *
			 * @since  0.11.0
			 *
			 * @param  string $errors Login error message.
			 */
			echo '<div id="login-error">' . $hook->run( 'login_errors', $errors ) . "</div>\n";
		}
		if ( ! empty( $messages ) ) {
			/**
			 * Filter instructional messages displayed above the login form.
			 *
			 * @since  0.11.0
			 *
			 * @param  string $messages Login messages.
			 */
			echo '<p class="message">' . $hook->run( 'login_messages', $messages ) . "</p>\n";
		}
	}
} // End display_header()

/**
 * Display the login page footer.
 *
 * @since  0.11.0
 * 
 * @global  hook
 *
 * @param  string $input_id Optional. Which input to auto-focus. Default empty.
 */
function display_footer( $login_id = '' ) {
	global $hook;
?>
	</div><!-- .row -->

<?php if ( ! empty( $input_id ) ) : ?>
	<script type="text/javascript">
		try{document.getElementById( '<?php echo $input_id; ?>' ).focus();}catch(e){}
	</script>
<?php endif; ?>

	<?php
	/**
	 * Fires in the login page footer.
	 *
	 * @since  0.11.0
	 */
	$hook->run( 'login_footer' ); ?>
	<div class="clearfix"></div>
</div><!-- .container -->
</body>
</html>
<?php
} // End display_footer()

/**
 * Display the login form.
 *
 * @since  0.11.0
 *
 * @global  hook
 */
function form() {
	global $hook;
?>

<div class="medium-6 large-4 small-centered columns sheet step text-center">
	<form name="loginform" id="loginform" action="<?php echo get_site_url( 'login.php?step=1' ); ?>" method="post">

		<label for="user_login" class="sr-only">E-mail</label>
		<input type="email" name="user_email" id="user_login" value="" size="20" placeholder="E-mail" autofocus>
		<label for="user_pass" class="sr-only">Password</label>
		<input type="password" name="user_pass" id="user_pass" value="" size="20" placeholder="Password">

		<?php
		/**
		 * Fires after the 'Password' field in the login form.
		 *
		 * @since  0.11.0
		 */
		$hook->run( 'login_form' );
		?>
		<?php // TODO: handle checked ?>
		<button type="submit" class="expand">Login</button>
		<p class="checkbox-wrapper"><input name="rememberme" id="rememberme" type="checkbox" value="forever"><label for="rememberme" class="checkbox-label">Ricordami</label></p>
	</form>
</div>
<?php	
}

/**
 * Validate form data.
 *
 * @since  0.11.0
 *
 * @global  kyssdb
 */
function validate() {
	global $kyssdb;

	$user_email = isset( $_POST['user_email'] ) ? trim( unslash( $_POST['user_email'] ) ) : '';
	$user_pass = isset( $_POST['user_pass'] ) ? trim( unslash( $_POST['user_pass'] ) ) : '';
	$remember = isset( $_POST['rememberme'] ) ? $_POST['rememberme'] : false;

	$errors = new \KYSS_Error();
	if ( empty( $user_email ) )
		$errors->add( 'empty_email', 'Devi inserire un indirizzo email per autenticarti.' );
	else if ( empty( $user_pass ) )
		$errors->add( 'empty_password', 'La password non pu&ograve; essere vuota.' );

	// Email is not empty. Use it to try to retrieve a user from the database.
	$user = $kyssdb->query( "SELECT * FROM {$kyssdb->utenti} WHERE email='{$user_email}'" );

	if ( false === $user )
		trigger_error( $kyssdb->error, E_USER_ERROR );
	else if ( 0 === $user->num_rows )
		$errors->add( 'invalid_email', 'Indirizzo email non trovato.' );

	// We found a user.
	$user = $user->fetch_object();

	if ( ! is_null( $user ) ) {
		if ( ! \KYSS_Pass::verify( $user_pass, $user->password ) )
			$errors->add( 'invalid_password', 'Password errata. Riprovare.' );
	}

	$var = $errors->get_error_code();
	
	if ( ! empty( $var ) ) {
		// TODO: handle some errors
		echo "Failure!";
	} else {
		$_SESSION['login'] = $user->ID;
		if ( $remember )
			setcookie('kyss_login', \KYSS_Pass::hash_auth_cookie($user->ID), time() + 15 * DAY_IN_SECONDS );
		kyss_redirect( get_option( 'siteurl' ) . '/' );
	}
}