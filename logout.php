<?php
/**
 * KYSS Logout Page.
 * 
 */

require ( dirname( __FILE__ ) . '/load.php' );

/**
 * Logout from application.
 *
 * Destroys session and deletes session and authorization cookie
 *
 */

function logout() {
	$sname = session_name();
	session_destroy();
	// Delete session cookie
	if( isset( $_COOKIE[$sname] ) ) {
		setcookie( $sname, '', time() - 3600, '/');
	}
	// Delete authentication cookie
	if( isset( $_COOKIE['kyss_login'] ) ) {
		setcookie( 'kyss_login', '', time() - 3600, '/');
	}
	echo 'Logout effettuato <b>' . $_SESSION['login'] . '</b>!';
}
?>
<!doctype html>
<html lang = "it">
<head>
	<title><?php echo get_option( 'sitename' ); ?> &rsaquo; Logout</title>
	<?php kyss_css( 'kyss', true ); ?>
</head>
<body>
	<h1><?php
		logout();
	?></h1>
	<a href="<?php echo get_site_url( 'login.php' ); ?>" class="button rimary">Login</a>
	</body>
</html>