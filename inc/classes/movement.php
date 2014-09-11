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
	 * @param  int $balance Optional. Balance ID.
	 * @param  string $order Optional. Results order. Accepts 'ASC', 'DESC'. Default 'DESC'.
	 * @return array|false Array of KYSS_Movement object. False on failure.
	 */
	public static function get_list( $balance = 0, $order = 'DESC' ) {
		global $kyssdb;

		$query = "SELECT * FROM {$kyssdb->movimenti}";

		if ( $balance )
			$query .= " WHERE `bilancio`={$balance}";

		$query .= " ORDER BY `data` {$order}";

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

	/**
	 * Search movement in the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $query Search query.
	 * @return  array.
	 */
	public static function search( $query = '' ) {
		global $kyssdb;

		if ( empty( $query ) )
			return self::get_list();

		$query = $kyssdb->real_escape_string( $query );

		$sql = "SELECT m.* FROM {$kyssdb->movimenti} m
			LEFT JOIN {$kyssdb->utenti} AS u ON m.utente = u.ID
			LEFT JOIN {$kyssdb->bilanci} AS b ON m.bilancio = b.ID
			LEFT JOIN {$kyssdb->verbali} AS v ON b.verbale = v.protocollo
			WHERE ";

		$fields = array( 'm.causale', 'm.importo', 'u.nome', 'u.cognome', 'u.email', 'u.telefono', 'u.gruppo', 'u.citta', 'b.tipo', 'b.mese', 'b.anno', 'v.contenuto' );
		//$fields = array( 'causale', 'importo' );
		$search = array();
		foreach ( $fields as $field )
			$search[] = "CONVERT({$field} USING utf8) LIKE '%{$query}%'";
		$search = join( ' OR ', $search );
		$sql .= $search;

		if ( ! $result = $kyssdb->query( $sql ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );

		if ( 0 === $result->num_rows )
			return false;

		$movements = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$movements[] = $result->fetch_object( 'KYSS_Movement' );
		return $movements;
	}
}