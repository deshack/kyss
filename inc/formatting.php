<?php
/**
 * Main KYSS Formatting API.
 *
 * Contains functions for formatting output.
 *
 * @package  KYSS
 * @subpackage  API
 */

/**
 * Deep stripslashes.
 *
 * Goes through an array and removes slashes from the values.
 *
 * If an array is passed, the array_map() function causes a callback to pass the
 * value back to the function. The slashes from this value will be removed.
 *
 * @since  0.3.0
 *
 * @param  mixed $value The value to be stripped.
 * @return  mixed Stripped value.
 */
function stripslashes_deep($value) {
	if ( is_array($value) ) {
		$value = array_map('stripslashes_deep', $value);
	} elseif ( is_object($value) ) {
		$vars = get_object_vars($value);
		foreach ( $vars as $key=>$data) {
			$value->{$key} = stripslashes_deep( $data );
		}
	} elseif ( is_string( $value) ) {
		$value = stripslashes($value);
	}

	return $value;
}

/**
 * Check and clean a URL.
 *
 * Eliminates invalid and dangerous characters.
 * The `clean_url` hook is applied to the returned cleaned URL.
 *
 * @see  sourceforge.net/projects/kses
 *
 * @since  0.9.0
 *
 * @param  string $url The URL to be cleaned.
 */
function clean_url( $url ) {
	$original_url = $url;

	if ( '' == $url )
		return $url;

	// Remove invalid characters.
	$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
	$strip = array('%0d', '%0a', '%0D', '%0A');
	$url = _deep_replace( $strip, $url );
	$url = str_replace(';//', '://', $url);

	// If the URL doesn't appear to contain a scheme, we
	// presume it needs `http://` appended (unless a relative
	// link starting with /, #, or ?, or a php file).
	if ( strpos($url, ':') === false && ! in_array( $url[0], array( '/', '#', '?' ) ) && ! preg_match('/^[a-z0-9-]+?\.php/i', $url) )
		$url = 'http://' . $url;

	// Replace ampersands and single quotes with ASCII code.
	$url = kses::normalize_entities( $url );
	$url = str_replace( '&amp;', '&#038;', $url );
	$url = str_replace( "'", '&#039;', $url );

	/**
	 * Filter a string cleaned and escaped for output as a URL.
	 *
	 * @since  0.9.0
	 *
	 * @param  string $clean_url The cleaned URL to be returned.
	 * @param  string $original_url The URL prior to cleaning.
	 */
	return run_hook( 'clean_url', $clean_url, $original_url );
}

/**
 * Perform a deep string replace operation to ensure the values in $search
 * are no longer present.
 *
 * Repeats the replacement operation until it no longer replaces anything
 * so as to remove "nested" values.
 * E.g. `$needle = '%0%0%0DDD', $haystack = '%0D', $result = ''` rather than
 * the '%0%0DD' that `str_replace()` would return.
 *
 * @since  0.9.0
 * @access private
 *
 * @param  string|array $needle The value being searched for. An array may
 * be used to designate multiple needles.
 * @param  string $haystack The string being searched and replaced on.
 * @return string The string with the replaced values.
 */
function _deep_replace( $needle, $haystack ) {
	$haystack = (string) $haystack;

	$count = 1;
	while ( $count ) {
		$haystack = str_replace( $needle, '', $haystack, $count );
	}

	return $haystack;
}