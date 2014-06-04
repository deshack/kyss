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