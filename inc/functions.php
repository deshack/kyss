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
 * only when the execution should not continue any further. It is not
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
		// Strip filenames and options from the url.
		// NOTE: this regex targets only filenames with alphanumerical characters
		// and '-', '_'.
		$url = preg_replace('/(\/admin|inc)?\/[a-zA-Z0-9-_]+.php(.*)$/', '', $url );
	}

	return rtrim($url, '/');
}

/**
 * Retrieve the assets directory URL.
 *
 * If the asset type is not recognized, it returns the main asset directory.
 * E.g: `$type = 'css' => $url = 'http://www.example.com/assets/css/'`
 * `$type = 'foo' => $url = 'http://www.example.com/assets/'`
 *
 * @since  0.9.0
 * @see  kyss_guess_url()
 *
 * @param  string $type Optional. Asset type. Default <css>. Accepts <css>,<js>,<img>.
 * @return  string Asset directory URL.
 */
function get_asset_url( $type = 'css' ) {
	$accepted = array( 'css', 'js', 'img' );

	$url = kyss_guess_url() . '/assets/';

	if ( ! in_array( $type, $accepted ) ) {
		trigger_error( sprintf('Unrecognized type %1$s in %2$s. Available types are: <strong>css</strong>, <strong>js</strong>, <strong>img</strong>.',
			$type,
			__FUNCTION__
		) );
		return $url;
	}

	$url .= $type;

	return trailingslashit( $url );
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

/**
 * Walks the array while sanitizing the contents.
 *
 * @since  0.6.0
 *
 * @param  array $array Array to walk while sanitizing contents.
 * @return  array Sanitized $array.
 */
function add_magic_quotes( $array ) {
	foreach ( (array) $array as $k => $v ) {
		if ( is_array( $v ) )
			$array[$k] = add_magic_quotes( $v );
		else
			$array[$k] = addcslashes( $v, "\\'" );
	}
	return $array;
}

/**
 * Get functions that have been called until now.
 *
 * Returns a comma separated string of functions that have been called to get
 * to the current point in the code.
 *
 * @since  0.6.0
 *
 * @param  string $ignore_class A class to ignore all function calls within - 
 *                              useful when you want to just give info about the
 *                              callee.
 * @param  int $skip_frames A number of stack frames to skip - useful for unwinding
 *                          back to the source of the issue.
 * @param  bool $pretty Whether or not you want a comma separated string or raw array returned.
 * @return  string|array Either a string containing a reversed comma separated trace or
 *                              an array of individual calls.
 */
function debug_backtrace_summary( $ignore_class = null, $skip_frames = 0, $pretty = true ) {
	if ( version_compare( PHP_VERSION, '5.2.5', '>=' ) )
		$trace = debug_backtrace( false );
	else
		$trace = debug_backtrace();

	$caller = array();
	$check_class = ! is_null( $ignore_class );
	$skip_frames++; // skip this function

	foreach ( $trace as $call ) {
		if ( $skip_frames > 0 ) {
			$skip_frames--;
		} elseif ( isset( $call['class'] ) ) {
			if ( $check_class && $ignore_class == $call['class'] )
				continue; // Filter out class

			$caller[] = "{$call['class']}{$call['type']}{$call['function']}";
		} else {
			if ( in_array( $call['function'], array( 'include', 'include_once', 'require', 'require_once' ) ) ) {
				$caller[] = $call['function'] . "('" . str_replace( array( ABSPATH ), '', $call['args'][0] ) . "')";
			} else {
				$caller[] = $call['function'];
			}
		}
	}
	if ( $pretty )
		return join( ', ', array_reverse( $caller ) );
	else
		return $caller;
}

/**
 * Get stylesheet tag.
 *
 * @global  kyss_version
 *
 * @param string $name Stylesheet name.
 * @param bool $echo Optional. True to echo the result, false to return. Default <false>.
 * @return  string The HTML string to be used in page `<head>`.
 */
function kyss_css( $name, $echo = false ) {
	global $kyss_version;

	$name = trim($name);
	$output = sprintf( '<link rel="stylesheet" id="%1$s" href="%2$s" type="text/css" media="all">',
		$name,
		clean_url( get_asset_url() . $name . '.css?' . $kyss_version )
	);

	if ( $echo ) {
		echo $output;
		return;
	}
	return $output;
}

/**
 * Redirect to another page.
 *
 * @since  0.6.0
 *
 * @param  string $location The path to redirect to.
 * @param  int $status Status code to use.
 * @return  bool False if $location is not provided, true otherwise.
 */
function kyss_redirect($location, $status = 302) {
	if ( ! $location )
		return false;

	header("Location: $location", true, $status);

	return true;
}

/**
 * Serialize data, if needed.
 *
 * @since  0.9.0
 *
 * @param  mixed $data Data that might be serialized.
 * @return mixed A scalar data.
 */
function kyss_serialize( $data ) {
	if ( is_array( $data ) || is_object( $data ) )
		return serialize( $data );

	return $data;
}

/**
 * Unserialize value only if it was serialized.
 *
 * If `$data` is not serialized, `unserialize()` returns false
 * and issues an E_NOTICE. To prevent this to be triggered, we
 * suppress it with the
 * {@link(@ operator, http://www.php.net/manual/en/language.operators.errorcontrol.php)}.
 *
 * @since  0.9.0
 *
 * @param  string $data Maybe unserialized original data.
 * @return mixed Unserialized data can be any type.
 */
function kyss_unserialize( $data ) {
	$unserialized = @unserialize( $data );

	if ( false === $unserialized )
		return $data;
	return $unserialized;
}

/**
 * Chech server's PHP version.
 *
 * @since  0.9.0
 */
function check_php_version() {
	global $php_required_version;
	
	if( version_compare( PHP_VERSION, $php_required_version, '>=') ) {
		return;
	} else {
		// Server's PHP version is lower than the required one
		$die = '<h1>Failed Dependency</h1>';
		$die .= '<p>KYSS requires PHP <b>' . $php_required_version . "</b> to works, while server's PHP version is <b>" . PHP_VERSION . "</b></p>";
		$die .= '<p>Upgrade PHP and refresh this page to continue.</p>';

		kyss_die( $die, 'KYSS Error' );
	}
}