<?php
/**
 * Deprecated functions from past KYSS versions.
 *
 * You shouldn't use these functions and look for the alternatives instead.
 * The functions will be removed in a later version.
 *
 * @package  KYSS
 * @subpackage  Deprecated
 * @since  0.15.0
 */

//--------------------------------------------------
// Library to handle deprecations.
//--------------------------------------------------

/**
 * Mark function as deprecated and inform when it has been used.
 *
 * There is a hook `deprecated_function_run` that will be called that can be used
 * to get the backtrace up to what file and function called the deprecated function.
 *
 * The current behavior is to trigger a user error if debug is active.
 *
 * This function is to be used in every function that is deprecated.
 *
 * @since  0.15.0
 * @access private
 *
 * @global  hook
 * 
 * @param  string $function The function that was called.
 * @param  string $version The version of KYSS that deprecated the function.
 * @param  string $replacement Optional. The function that should have been called.
 */
function _deprecated_function( $function, $version, $replacement = null ) {
	global $hook;

	/**
	 * Fires when deprecated function is called.
	 *
	 * @since  0.15.0
	 *
	 * @param  string $function The function that was called.
	 * @param  string $version The version of KYSS that deprecated the function.
	 * @param  string $replacement The function that should have been called.
	 */
	$hook->run( 'deprecated_function', $function, $version, $replacement );

	/**
	 * Decide whether to trigger an exception for deprecated functions.
	 *
	 * This doesn't take into account the application environment, but the
	 * environment setup filters this value.
	 *
	 * @since  0.15.0
	 *
	 * @param  bool $trigger Whether to trigger the error for deprecated functions.
	 * Default false.
	 * @return bool
	 */
	if ( $hook->run( 'deprecated_function_trigger_exception', true ) ) {
		$message = sprintf( '%1$s is <strong>deprecated</strong> since version %2$s!', $function, $version );
		if ( ! is_null( $replacement ) )
			$message .= sprintf( ' Use %s instead', $replacement );
		else
			$message .= ' No alternative available.';

		throw new \KYSS\Exceptions\DeprecatedException( $message );
	}
}


//--------------------------------------------------
// Deprecated functions
//--------------------------------------------------

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
	_deprecated_function( __FUNCTION__, '0.15.0', 'trailingslash()' );

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
	_deprecated_function( __FUNCTION__, '0.15.0', 'untrailingslash()' );
	
	return rtrim( $string, '/\\' );
}