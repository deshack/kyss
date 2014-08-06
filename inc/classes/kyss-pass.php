<?php
/**
 * KYSS password hashing API.
 *
 * @package  KYSS
 * @subpackage  Pass
 * @since  0.9.0
 */

/**
 * KYSS password hashing class.
 *
 * Use this class to strongly encrypt passwords.
 *
 * @package KYSS
 * @subpackage  Pass
 * @version  1.0.0
 * @since  0.9.0
 */
class KYSS_Pass {
	/**
	 * Generate BCrypt hash of the given password.
	 *
	 * Note: `bin2hex()` called with a parameter of 11 characters
	 * generates a string 22 characters long.
	 *
	 * The BCrypt algorithm requires a salt as follows: "$2a$", "$2x$", or
	 * "$2y$", a two digit cost parameter, "$", and 22 characters from the
	 * alphabet "./0-9A-Za-z".
	 *
	 * @since  KYSS_Pass 1.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $password Password in plain text.
	 * @return string Hashed password.
	 */
	public static function hash( $password ) {
		if ( defined( 'CRYPT_BLOWFISH' ) && CRYPT_BLOWFISH ) {
			$salt = '$2y$11$' . bin2hex( openssl_random_pseudo_bytes(11) );
			return crypt( $password, $salt );
		}
	}

	/**
	 * Verify plain text password against hashed one.
	 *
	 * @since  KYSS_Pass 1.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $password Password in plain text.
	 * @param  string $hashed Hashed password.
	 * @return  bool True if the passwords correspond, false otherwise.
	 */
	public static function verify( $password, $hashed ) {
		return crypt( $password, $hashed ) == $hashed;
	}
}