<?php
/**
 * The main KYSS API
 *
 * @package KYSS
 * @subpackage API
 */

/**
 * Kill KYSS execution and display HTML with error message.
 *
 * This function complements the die() PHP function. The difference is that
 * HTML will be displayed to the user. It is recommended to use this function
 * only when the execution should nnot continue any further. It is not
 * recommended to call this function very often and try to handle as may errors
 * as possible silently.
 *
 * @since  0.1.0
 *
 * @param  string $message Error message.
 * @param  string $title Error title.
 * @param  string|array $args Optional. Arguments to control behavior.
 */
function kyss_die( $message = '', $title = '', $args = array() ) {
	$defaults = array( 'response' => 500 );
	$r = kyss_parse_args($args, $defaults);

	if ( function_exists( 'is_kyss_error' ) && is_kyss_error( $message ) ) {
		if ( empty( $title ) ) {
			$error_data = $message->get_error_data();
			if ( is_array( $error_data ) && isset( $error_data['title'] ) )
				$title = $error_data['title'];
		}
		$errors = $message->get_error_messages();
		switch ( count( $errors ) ) :
		case 0 :
			$message = '';
			break;
		case 1 :
			$message = "<p>{$errors[0]}</p>";
			break;
		default :
			$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
			break;
		endswitch;
	} elseif ( is_string( $message ) ) {
		$message = "<p>$message</p>";
	}

	if ( isset( $r['back_link'] ) && $r['back_link'] ) {
		$message .= "\n<p><a href='javascript:history.back()'>&laquo; Back</a></p>";
	}

	if ( !headers_sent() ) {
		status_header( $r['response'] );
		nocache_headers();
		header( 'Content-Type: text/html; charset=utf-8' );
	}

	if ( empty($title) ) {
		$title = 'KYSS &rsaquo; Error';
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="/assets/css/install.css" />
	<link rel="stylesheet" type="text/css" href="/assets/css/buttons.css" />
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

/**
 * Display KYSS DB error.
 *
 * The KYSS DB error sets the HTTP status header to 500 to try to prevent
 * search engines from caching this message. CUstom DB messages should do the same.
 *
 * @since  0.2.0
 */
function dead_db() {
	status_header( 500 );
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Database Error</title>
</head>
<body>
	<h1>Error establishing a database connection</h1>
</body>
</html>
<?php
	die();
}

/**
 * Merge user defined arguments into defaults array.
 *
 * This function allows an array to be merged into another array.
 *
 * @since  0.1.0
 *
 * @param  array $args Array to merge with $defaults.
 * @param  array $defaults Array that serves as the defaults.
 * @return  array Merged user defined values with defaults.
 */
function kyss_parse_args( $args, $defaults = '' ) {
	if ( is_object( $args ) )
		$r = get_object_vars( $args );
	elseif ( is_array( $args ) )
		$r =& $args;

	if ( is_array( $defaults ) )
		return array_merge( $defaults, $r );

	// If $defaults is not an array, it's either empty or not accepted,
	// so simply return $r.
	return $r;
}

/**
 * Guess the URL for the site.
 *
 * @since  0.3.0
 *
 * @return  string The site URL.
 */
function kyss_guess_url() {
	if ( defined('SITEURL') && '' != SITEURL ) {
		$url = SITEURL;
	} else {
		$schema = is_ssl() ? 'https://' : 'http://';
		$url = $schema . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	return rtrim($url, '/');
}

/**
 * Determine if SSL is used.
 *
 * @since  0.3.0
 *
 * @return  bool True if SSL, false otherwise.
 */
function is_ssl() {
	if ( isset($_SERVER['HTTPS']) ) {
		if ( 'on' == strtolower($_SERVER['HTTPS']) || '1' == $_SERVER['HTTPS'] )
			return true;
	} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	}
	return false;
}

/**
 * Set HTTP status header.
 *
 * @since  0.3.0
 * @see  get_status_header_desc()
 *
 * @param  int $code HTTP status code.
 */
function status_header( $code ) {
	$description = get_status_header_desc( $code );

	if ( empty( $description ) )
		return;

	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
		$protocol = 'HTTP/1.0';
	$status_header = "$protocol $code $description";

	@header( $status_header, true, $code );
}

/**
 * Retrieve the description for the HTTP status.
 *
 * @since  0.3.0
 *
 * @param  int $code HTTP status code.
 * @return  string Empty string if not found, description if found.
 */
function get_status_header_desc( $code ) {
	global $header_to_desc;

	$code = abs( intval( $code ) );

	if ( !isset( $header_to_desc ) ) {
		$header_to_desc = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',

			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',

			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',

			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',
			428 => 'Precondition Required',
			429 => 'Too Many Requests',
			431 => 'Request Header Fields Too Large',

			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended',
			511 => 'Network Authentication Required',
		);
	}

	if ( isset( $header_to_desc[$code] ) )
		return $header_to_desc[$code];
	else
		return '';
}

/**
 * Sets the headers to prevent caching for the different browsers.
 *
 * Different browsers support different nocache headers, so several headers
 * must be sent so that all of them get the point that no caching should occur.
 *
 * @since  0.3.0
 * @see  get_nocache_headers()
 */
function nocache_headers() {
	$headers = get_nocache_headers();

	unset( $headers['Last-Modified'] );

	// In PHP 5.3+, make sure we are not sending a Last-Modified header.
	if ( function_exists( 'header_remove' ) ) {
		@header_remove( 'Last-Modified' );
	} else {
		// In PHP 5.2, send an empty Last-Modified header, but only as a
		// last resort to override a header already sent.
		foreach ( headers_list() as $header ) {
			if ( 0 === stripos( $header, 'Last-Modified' ) ) {
				$headers['Last-Modified'] = '';
				break;
			}
		}
	}

	foreach( $headers as $name => $value )
		@header("{$name}: {$value}");
}

/**
 * Gets the header information to prevent caching.
 *
 * The several different headers cover the different ways cache prevention
 * is handled by different browsers.
 *
 * @since  0.3.0
 *
 * @return  array The associative array of header names and field values.
 */
function get_nocache_headers() {
	$headers = array(
		'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
		'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
		'Pragma' => 'no-cache'
	);

	$headers['Last-Modified'] = false;
	return $headers;
}