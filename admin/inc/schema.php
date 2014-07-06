<?php
/**
 * KYSS Schema API
 *
 * Here we keep the DB structure and option values.
 *
 * @package  KYSS
 * @subpackage DB
 */

/**
 * Retrieve the SQL for creating database tables.
 *
 * @since  0.10.0
 *
 * @return string The SQL needed to create the KYSS database tables.
 */
function get_db_schema() {
	global $kyssdb;
}

/**
 * Create KYSS options and set default values.
 *
 * @since  0.9.0
 * @global kyssdb
 */
function populate_options() {
	global $kyssdb;

	$guessurl = kyss_guess_url();

	/**
	 * Fires before creating KYSS options and populating their default values.
	 *
	 * @since  0.9.0
	 */
	$hook->run( 'populate_options' );

	$options = array(
		'siteurl' => $guessurl,
		'sitename' => 'KYSS',
		'admin_email' => 'you@example.com'
	);

	$insert = '';
	foreach ( $options as $option => $value ) {
		if ( is_array( $value ) )
			$value = serialize($value);
		if ( ! empty($insert) )
			$insert .= ', ';
		$insert .= $kyssdb->prepare( "(%s, %s, %s)", $option, $value);
	}

	if ( !empty($insert) )
		// $kyssdb->options is a table name!
		$kyssdb->query("INSERT INTO $kyssdb->options (option_name, option_value) VALUES " . $insert );
}