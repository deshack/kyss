<?php
/**
 * KYSS Logout Page.
 * 
 */

require_once( 'load.php' );

/**
 * Logout from application.
 *
 * Destroys session and deletes session and authorization cookie
 *
 */

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

global $hook;
// Don't index any of these forms.
$hook->add( 'login_head', 'no_robots' );
// Add the viewport meta tag.
$hook->add( 'login_head', 'viewport_meta' );
// Add the charset meta tag.
$hook->add( 'login_head', 'charset_meta' );
?>
<!doctype html>
<!--[if IE 8]>
<html class="ie8" lang="it">
<![endif]-->
<!--[if !(IE 8)]><!-->
<html lang = "it">
<head>
	<title>KYSS &rsaquo; Logout</title>
	<?php kyss_css( 'kyss', true ); ?>
</head>
<body>
	<h1 class="page-title text-center">Logout effettuato!</h1>
	<div class="text-center">
		<a href="<?php echo get_site_url( 'login.php' ); ?>" class="button primary">Login</a>
	</div>
</body>
</html>