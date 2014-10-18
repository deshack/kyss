<?php
/**
 * Generic KYSS Exception.
 *
 * Replaces the old KYSS_Error class.
 *
 * @package  KYSS
 * @subpackage  Exception
 * @since  0.15.0
 */

/**
 * Generic KYSS Exception class.
 *
 * @package  KYSS
 * @subpackage  Exception
 * @since  0.15.0
 * @version  1.0.0
 */
class KYSSException extends Exception {
	/**
	 * Default arguments.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @static
	 * @var array
	 */
	protected static $defaults = array(
		'response' => 500,
		'back_link' => true
	);

	/**
	 * Die formatting the message with HTML.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function kill( $title = 'Errore di KYSS', $args = array() ) {
		$r = parse_args( $args, static::$defaults );

		$message = $this->getMessage();

		if ( $r['back_link'] )
			$message .= "\n<p><a href='javascript:history.back()'>&laquo; Indietro</a></p>";

		if ( ! headers_sent() ) {
			status_header( $r['response'] );
			nocache_headers();
			header( 'Content-Type: text/html; charset=utf-8' );
		}
?>
<!DOCTYPE html>
<html lang="it">
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="assets/css/install.css">
</head>
<body id="error-page">
<div class="container">
	<?php echo $message; ?>
</div>
</body>
</html>
<?php
		die();
	}
}