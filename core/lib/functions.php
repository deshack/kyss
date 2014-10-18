<?php
/**
 * KYSS core functions.
 *
 * @package  KYSS
 * @subpackage  Library
 * @since  0.15.0
 */

/**
 * Merge user defined arguments into defaults array.
 *
 * This function allows an array to be merged into another array.
 *
 * @since  0.1.0
 *
 * @param array $args Array to merge with $defaults.
 * @param array $defaults Array that serves as the defaults.
 * @return array Merged user defined values with defaults.
 */
function parse_args( $args, $defaults = '' ) {
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
 * Set HTTP status header.
 *
 * @since  0.3.0
 * @see get_status_header_desc()
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
 * Set headers to prevent caching for different browsers.
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