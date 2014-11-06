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

use \KYSS\Exceptions\DeprecatedException;

//--------------------------------------------------
// Library to handle deprecations.
//--------------------------------------------------

/**
 * Mark function as deprecated and inform when it has been used.
 *
 * There is a hook `deprecated_function_exception` that will be called that can be used
 * to get the backtrace up to what file and function called the deprecated function.
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

	$message = sprintf( '%1$s is <strong>deprecated</strong> since version %2$s!', $function, $version );
	if ( ! is_null( $replacement ) )
		$message .= sprintf( ' Use %s instead.', $replacement );
	else
		$message .= ' No alternative available.';

	/**
	 * Fires when deprecated function is called.
	 *
	 * @since  0.15.0
	 *
	 * @param  DeprecatedException The exception.
	 * @param  string $function The function that was called.
	 * @param  string $version The version of KYSS that deprecated the function.
	 * @param  string $replacement The function that should have been called instead.
	 */
	$hook->run( 'deprecated_function_exception',
		new DeprecatedException( $message ),
		$function,
		$version,
		$replacement
	);
}

/**
 * Mark file as deprecated and inform when it has been used.
 *
 * There is a hook `deprecated_file_exception` that will be called that can be used
 * to get the backtrace up to what file and function included the deprecated file.
 *
 * This function is to be used in every file that is deprecated.
 *
 * @since 0.15.0
 * @access private
 *
 * @global  hook
 *
 * @param  string $file The file that was included.
 * @param  string $version The version of KYSS that deprecated the file.
 * @param  string $replacement Optional. The file that should have been included
 * based on ABSPATH. Default null.
 * @param  string $message Optional. A message regarding the change. Default empty.
 */
function _deprecated_file( $file, $version, $replacement = null, $message = '' ) {
	global $hook;

	$text = sprintf( '%1$s is <strong>deprecated</strong> since version %2$s!', $file, $version );
	if ( ! is_null( $replacement ) )
		$text .= sprintf( ' Use %s instead.', $replacement );
	else
		$text .= ' No alternative available.';
	$text .= " $message";

	/**
	 * Fires when deprecated file is included.
	 *
	 * @since  0.15.0
	 *
	 * @param  DeprecatedException The exception.
	 * @param  string $file The file that was called.
	 * @param  string $version The version of KYSS that deprecated the file.
	 * @param  string $replacement The file that should have been included instead.
	 */
	$hook->run( 'deprecated_file_exception',
		new DeprecatedException( $text ),
		$file,
		$version,
		$replacement
	);
}

/**
 * Mark function argument as deprecated and inform when it has been used.
 *
 * This function is to be used whenever a deprecated function argument is used.
 * Before this function is called, the argument must be checked for whether it was
 * used by comparing it to its default value or evaluating whether it is empty.
 *
 * @example
 * ```
 * if ( ! empty( $deprecated ) )
 * 	_deprecated_argument( __FUNCTION__, '0.15.0' );
 * ```
 *
 * There is a hook `deprecated_argument_exception` that will be called that can be used
 * to get the backtrace up to what file and function used the deprecated argument.
 *
 * @since  0.15.0
 * @access private
 *
 * @global  hook
 *
 * @param  string $function The function that was called.
 * @param  string $version The version of KYSS that deprecated the argument.
 * @param  string $message Optional. A message regarding the change. Default null.
 */
function _deprecated_argument( $function, $version, $message = null ) {
	global $hook;

	$text = sprintf( '%1$s was called with an argument that is <strong>deprecated</strong> since version %2$s!', $function, $version );
	if ( ! is_null( $message ) )
		$text .= " $message";
	else
		$text .= ' No alternative available.';

	/**
	 * Fires when deprecated argument is used.
	 *
	 * @since  0.15.0
	 *
	 * @param  DeprecatedException The exception.
	 * @param  string $function The function that was called.
	 * @param  string $version The version of KYSS that deprecated the argument.
	 */
	$hook->run( 'deprecated_argument_exception',
		new DeprecatedException( $text ),
		$function,
		$version
	);
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