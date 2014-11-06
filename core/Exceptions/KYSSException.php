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

namespace KYSS\Exceptions;

/**
 * Generic KYSS Exception class.
 *
 * @package  KYSS
 * @subpackage  Exception
 * @since  0.15.0
 * @version  1.1.0
 */
class KYSSException extends \Exception {
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
	 * Ruby style exception trace.
	 *
	 * @since  1.1.0
	 *
	 * @param  array $seen Array passed to recursive calls to accumulate
	 * trace lines already seen. Leave NULL when calling this method.
	 * @return  string
	 */
	public function getTraceString( $seen = null ) {
		$starter = $seen ? 'Caused by ' : '';
		$result = array();

		if ( is_null( $seen ) )
			$seen = array();

		$trace = $this->getTrace();
		$prev = $this->getPrevious();
		$result[] = sprintf( '%1$s%2$s: %3$s', $starter, __CLASS__, $this->getMessage() );

		$file = $this->getFile();
		$line = $this->getLine();

		while ( count( $trace ) ) {
			$current = "$file:$line";
			$result[] = sprintf( "\t%s in %s%s",
				$current,
				array_key_exists( 'class', $trace[0] ) ? $trace[0]['class'] . '.' : '',
				array_key_exists( 'function', $trace[0] ) ? $trace[0]['function'] . '()' : ''
			);

			if ( is_array( $seen ) )
				$seen[] = $current;
			$file = array_key_exists( 'file', $trace[0] ) ? $trace[0]['file'] : 'Unknown Source';
			$line = array_key_exists( 'line', $trace[0] ) ? $trace[0]['line'] : '';
			array_shift( $trace );
		}

		$result = join( "\n", $result );
		if ( $prev )
			$result .= "\n" . $prev->getTraceString( $seen );

		return $result;
	}

	/**
	 * Die formatting the message with HTML.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function kill( $title = 'Errore di KYSS', $args = array() ) {
		$r = \parse_args( $args, static::$defaults );

		$message = str_replace( "\t", "&nbsp;&nbsp;&nbsp;&nbsp;", str_replace( "\n", '<br />', $this->getTraceString() ) );

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