<?php
/**
 * KYSS Movement API
 *
 * @package  KYSS
 * @subpackage  Movement
 */

/**
 * KYSS Movement class
 *
 * @since
 * @package  KYSS
 * @subpackage  Movement
 */
class KYSS_Movement {
	/**
	 * Retrieve movements list.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  string $filed Optional. If is an empty string returns all the subscription. Default empty.
	 * @param  int $value The field value.
	 * @return array|false Array of KYSS_Movement object. False on failure.
	 */
	public static function get_list( $field = '', $value = 0) {
		global $kyssdb;

		switch ( $field ) {
			case 'utente':
				$query = "SELECT corso ";
				break;
			case 'corso':
				$query = "SELECT utente ";
				break;
			case '':
			default:
				$query = "SELECT * ";	
		}

		$query .= "FROM {$kyssdb->iscritto} ";

		if ( ! empty( $filed ) ) {
			if ( ! is_numeric( $value ) )
				return false;
			$value = intval( $value );
			if ( $value < 1 )
				return false;

			$query .= "WHERE {$field} = {$value}";
		}

		if ( ! $subscription = $kyssdb->query( $query ) )
			return false;

		if ( $subscription->num_rows == 0 )
			return new KYSS_Error( 'no_subscrtiption_found', 'No subscription found.', array( $field => $value ) );

		$subscriptions = array();

		for ( $i = 0; $i < $subscription->num_rows; $i++ )
			array_push( $subscriptions, $subscription->fetch_object( 'KYSS_Subscription' ) );

		return $subscriptions;
	}
}