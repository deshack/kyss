<?php
/**
 * KYSS Subscription API
 *
 * @package  KYSS
 * @subpackage  Subscription
 */

/**
 * KYSS Subscription class
 *
 * @since
 * @package  KYSS
 * @subpackage  Subscription
 */
class KYSS_Subscription {
	/**
	 * Retrieve subscriptions list.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  string $filed Optional. If is an empty string returns all the subscription. Default empty.
	 * @param  int $value The field value.
	 * @return array|false Array of KYSS_Subscription object. False on failure.
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

	/**
	 * Insert new subscription into the database.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Associative array of column names and values.
	 * @return bool True on success, false on failure.
	 */
	public static function create( $data ) {
		global $kyssdb;

		if ( empty( $data ) )
			return new KYSS_Error( 'empty_subscription_data', 'Subscription data cannot be empty!' );

		foreach ($data as $key => $value) {
			if ( empty( $value ) )
				return new KYSS_Error( 'empty_subscription_data_field', 'Subscription data filed cannot be empty!' );
		}

		$columns = array();
		$values = array();

		foreach ($data as $key => $value) {
			array_push( $columns, $key );
			array_push( $values, "'{$value}'");
		}

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->iscritto} ({$columns}) VALUES ({$values})";
		if ( ! $result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}
		return true;
	}

	/**
	 * Delete subscription in the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  array $data
	 * @return bool
	 */
	public static function delete( $data ) {
		global $kyssdb;

		$wheres = array();
		foreach ( $data as $key => $value ) {
			$wheres[] = " `{$key}`={$value} ";
		}

		$wheres = join( ' AND ', $wheres );

		$query = "DELETE FROM {$kyssdb->iscritto} WHERE {$wheres}";
		$result = $kyssdb->query( $query );
		if ( ! $result )
			return false;
		return true;
	}
}