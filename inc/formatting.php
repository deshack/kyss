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
 * Remove slashes from a string or array of strings.
 *
 * This should be used to remove slashes from data passed to core API that
 * expects data to be unslashed.
 *
 * @since  0.9.0
 *
 * @param  string|array $value String or array of strings to unslash.
 * @return  string|array Unslashed $value.
 */
function unslash( $value ) {
	return stripslashes_deep( $value );
}

/**
 * Add slashes to a string or array of strings.
 *
 * This should be used when preparing data for core API that expects slashed daata.
 * This should not be used to escape data going directly into an SQL query.
 *
 * @since  0.9.0
 *
 * @param  string|array $value String or array of strings to slash.
 * @return  string|array Slashed $value.
 */
function slash( $value ) {
	if ( is_array( $value ) ) {
		foreach ( $value as $k => $v ) {
			if ( is_array( $v ) ) {
				$value[$k] = slash( $v );
			} else {
				$value[$k] = addslashes( $v );
			}
		}
	} else {
		$value = addslashes( $value );
	}

	return $value;
}

/**
 * Check and clean a URL.
 *
 * Eliminates invalid and dangerous characters.
 * The `clean_url` hook is applied to the returned cleaned URL.
 *
 * @since  0.9.0
 *
 * @global  hook
 *
 * @param  string $url The URL to be cleaned.
 */
function clean_url( $url ) {
	global $hook;

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
	$url = str_replace('&', '&amp;', $url);
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
	return $hook->run( 'clean_url', $url, $original_url );
	return $url;
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

/**
 * Appends a trailing slash.
 *
 * Will remove trailing forward and backslashes if it exists already before adding
 * a trailing forward slash. This prevents double slashing a string or path.
 *
 * The primary use of this is for paths and thus should be used for paths. It is
 * not restricted to paths and offers no specific path support.
 *
 * @since  0.9.0
 *
 * @param  string $string What to add the trailing slash to.
 * @return string String with trailing slash added.
 */
function trailingslashit( $string ) {
	return untrailingslashit( $string ) . '/';
}

/**
 * Removes trailing forward slashes and backslashes if they exist.
 *
 * The primary use of this is for paths and thus should be used for paths.
 * It is not restricted to paths and offers no specific path support.
 *
 * @since  0.9.0
 *
 * @param  string $string What to remove the trailing slashes from.
 * @return  string String without the trailing slashes.
 */
function untrailingslashit( $string ) {
	return rtrim( $string, '/\\' );
}

/**
 * Check if email is valid.
 *
 * Does not grok i18n domains. Not RFC compliant.
 *
 * @since  0.9.0
 *
 * @global  hook
 *
 * @param  string $email Email address to verify.
 * @return string|bool Either false or the valid email address.
 */
function is_email( $email ) {
	global $hook;

	if ( strlen( $email ) < 3 ) {
		/**
		 * Filter whether an email address is valid.
		 *
		 * This hook is evaluated under several different contexts, such as 'email_too_short',
		 * 'email_no_at', 'local_invalid_chars', 'domain_period_sequence', 'domain_period_limits',
		 * 'domain_no_periods', 'sub_hyphen_limits', 'sub_invalid_chars', or no specific context.
		 *
		 * @since  0.9.0
		 *
		 * @param  bool $is_email Whether the email address has passed the is_email() checks. Default <false>.
		 * @param  string $email The email address being checked.
		 * @param  string $message An explanatory message to the user.
		 * @param  string $context Context under which the email was tested.
		 */
		return $hook->run( 'is_email', false, $email, '', 'email_too_short' );
	}

	// Test for an @ character after the first position.
	if ( strpos( $email, '@', 1 ) === false ) {
		return $hook->run( 'is_email', false, $email, '', 'email_no_at' );
	}

	// Split out the local and domain parts.
	list( $local, $domain ) = explode( '@', $email, 2 );

	// LOCAL PART
	// Test for invalid characters.
	if ( ! preg_match( '/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local ) ) {
		return $hook->run( 'is_email', false, $email, '', 'local_invalid_chars' );
	}

	// DOMAIN PART
	// Test for sequences of periods.
	if ( preg_match( '/\.{2}/', $domain ) ) {
		return $hook->run( 'is_email', false, $email, '', 'domain_period_sequence' );
	}

	// Test for leading and trailing periods and whitespace.
	if ( trim( $domain, " \t\n\r\0\x0B." ) !== $domain ) {
		return $hook->run( 'is_email', false, $email, '', 'domain_period_limits' );
	}

	// Split the domain into subs.
	$subs = explode( '.', $domain );

	// Assume the domain will have at least two subs.
	if ( 2 > count( $subs ) ) {
		return $hook->run( 'is_email', false, $email, '', 'domain_no_periods' );
	}

	// Loop through each sub.
	foreach ( $subs as $sub ) {
		// Test for leading and trailing hyphens and whitespace.
		if ( trim( $sub, " \t\n\r\0\x0B-" ) !== $sub ) {
			return $hook->run( 'is_email', false, $email, '', 'sub_hyphen_limits' );
		}

		// Test for invalid characters.
		if ( ! preg_match( '/^[a-z0-9-]+$/i', $sub ) ) {
			return $hook->run( 'is_email', false, $email, '', 'sub_invalid_chars' );
		}
	}

	// Yeah, the email made it!
	return $hook->run( 'is_email', $email, $email, '', null );
}