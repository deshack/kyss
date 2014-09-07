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
	 * Retrieve Movement by id.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  string $id Movement ID.
	 * @return KYSS_Movement|bool KYSS_Movement object or false on failure.
	 */
	public static function get( $id ) {
		global $kyssdb;

		if ( ! $movement = $kyssdb->query(
			"SELECT * 
			FROM {$kyssdb->movimenti} 
			WHERE ID = '{$id}'"
		) )
			return false;

		if ( $movement->num_rows == 0 )
			return new KYSS_Error( 'movement_not_found', 'Movimento non trovato', array( 'ID' => $id ) );
		$movement = $movement->fetch_object( 'KYSS_Movement' );

		return $movement;
	}

	/**
	 * Retrieve movements list.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  string $filed Optional. If is an empty string returns all the movement. Default empty.
	 * @param  int $value The field value.
	 * @return array|false Array of KYSS_Movement object. False on failure.
	 */
	public static function get_list( $field = '', $value = 0) {
		global $kyssdb;

		$query = "SELECT * FROM {$kyssdb->movimenti} ";

		if ( ! empty( $field ) )
			$query .= "WHERE {$field} = {$value}";

		if ( ! $movement = $kyssdb->query( $query ) )
			return false;

		$movements = array();

		for ( $i = 0; $i < $movement->num_rows; $i++ )
			array_push( $movements, $movement->fetch_object( 'KYSS_Movement' ) );

		return $movements;
	}

	/**
	 * Insert new Movement into the database.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  array $data Associative array of column names and values.
	 * @return int The new created movement's ID or false on failure.
	 */
	public static function create( $data ) {
		global $kyssdb;

		if ( empty( $data ) )
			return new KYSS_Error( 'empty_movement_data', 'Movements data cannot be empty!' );

		$columns = array();
		$values = array();

		foreach ( $data as $key => $value ) {
			array_push( $columns, $key );
			if ( $value != 'NULL')
				array_push( $values, "'{$value}'" );
			else
				array_push( $values, "$value" );
		}

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->movimenti} ({$columns}) VALUES ({$values})";
		if ( ! $result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}

		return $kyssdb->insert_id;
	}

	/**
	 * Update Movement in the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  array $data Movement's data.
	 * @return bool Whether the update succeeded or not.
	 */
	public static function update( $id, $data ) {
		global $kyssdb;

		if ( empty( $data) )
			return false;

		$result = $kyssdb->update( $kyssdb->movimenti, $data, array( 'ID' => $id ) );

		if ( $result )
			return true;
		return false;
	}
}