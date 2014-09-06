<?php
/**
 * KYSS Document API
 *
 * @package  KYSS
 * @subpackage  Documents
 */

/**
 * KYSS Practice class
 *
 * @since 0.12.0
 * @package  KYSS
 * @subpackage  Documents
 */
class KYSS_Practice {
	/**
	 * Retrieve Practice by protocol.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $prot Practice protocol number.
	 * @return  KYSS_Practice|bool KYSS_Practice object or false on failure.
	 */
	public static function get( $prot ) {
		global $kyssdb;

		if ( ! $practice = $kyssdb->query(
			"SELECT * 
			FROM {$kyssdb->pratiche} 
			WHERE protocollo = '{$prot}'"
		) )
			return false;

		if ( $practice->num_rows == 0 )
			return new KYSS_Error( 'practice_not_found', 'Pratica non trovata', array( 'protocollo' => $prot ) );
		$practice = $practice->fetch_object( 'KYSS_Practice' );

		return $practice;
	}

	/**
	 * Retrieve Practices list.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @return array|false Array of KYSS_Practice objects or false on failure.
	 */
	public static function get_list() {
		global $kyssdb;

		if ( ! $practice = $kyssdb->query(
			"SELECT * FROM {$kyssdb->pratiche}"
		) )
			return false;

		$practices = array();

		for ( $i = 0; $i < $practice->num_rows; $i++ )
			array_push( $practices, $practice->fetch_object( 'KYSS_Practice' ) );

		return $practices;
	}

	/**
	 * Insert new Prectice into the database.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Associative array of column names and values.
	 * @return int Prectice's protocol number.
	 */
	public static function create( $data ) {
		global $kyssdb;

		if ( empty( $data ) )
			return new KYSS_Error( 'empty_practice_data', 'Practices data cannot be empty!' );

		$columns = array();
		$values = array();

		foreach ( $data as $key => $value ) {
			array_push( $columns, $key );
			array_push( $values, "'{$value}'" );
		}

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->pratiche} ({$columns}) VALUES ({$values})";
		if ( ! $result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}

		return $data['protocollo'];
	}

	/**
	 * Update Precticle in the db.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  array $data Practice's data.
	 * @return bool Whether the update succeeded or not.
	 */
	public static function update( $proc, $data ) {
		global $kyssdb;

		if ( empty( $data) )
			return false;

		$result = $kyssdb->update( $kyssdb->pratiche, $data, array( 'protocollo' => $proc ) );

		if ( $result )
			return true;
		return false;
	}
}

/**
 * KYSS Report class
 *
 * @since
 * @package  KYSS
 * @subpackage  Report
 */
class KYSS_Report {
	/**
	 * Retrieve Report by protocol.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $prot Report protocol number.
	 * @return  KYSS_Report|bool KYSS_Report object or false on failure.
	 */
	public static function get( $prot ) {
		global $kyssdb;

		if ( ! $report = $kyssdb->query(
			"SELECT * 
			FROM {$kyssdb->verbali} 
			WHERE protocollo = '{$prot}'"
		) )
			return false;

		if ( $report->num_rows == 0 )
			return new KYSS_Error( 'report_not_found', 'Verbale non trovato', array( 'protocollo' => $prot ) );
		$report = $report->fetch_object( 'KYSS_Report' );

		return $report;
	}

	/**
	 * Retrieve Reports list.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @return array|false Array of KYSS_Report objects or false on failure.
	 */
	public static function get_list() {
		global $kyssdb;

		if ( ! $report = $kyssdb->query(
			"SELECT * FROM {$kyssdb->verbali}"
		) )
			return false;

		$reports = array();

		for ( $i = 0; $i < $report->num_rows; $i++ )
			array_push( $reports, $report->fetch_object( 'KYSS_Report' ) );

		return $reports;
	}

	/**
	 * Insert new Report into the database.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Associative array of column names and values.
	 * @return int Report's protocol number.
	 */
	public static function create( $data ) {
		global $kyssdb;

		if ( empty( $data ) )
			return new KYSS_Error( 'empty_report_data', 'Reports data cannot be empty!' );

		$columns = array();
		$values = array();

		foreach ( $data as $key => $value ) {
			array_push( $columns, $key );
			array_push( $values, "'{$value}'" );
		}

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->verbali} ({$columns}) VALUES ({$values})";
		if ( ! $result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}

		return $data['protocollo'];
	}

	/**
	 * Update Report in the db.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  array $data Report's data.
	 * @return bool Whether the update succeeded or not.
	 */
	public static function update( $proc, $data ) {
		global $kyssdb;

		if ( empty( $data) )
			return false;

		$result = $kyssdb->update( $kyssdb->verbali, $data, array( 'protocollo' => $proc ) );

		if ( $result )
			return true;
		return false;
	}
}

/**
 * KYSS Budget class
 *
 * @since
 * @package  KYSS
 * @subpackage  Budget
 */
class KYSS_Budget {
/**
	 * Retrieve Budget by protocol.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $id Budget ID.
	 * @return  KYSS_Budget|bool KYSS_Budget object or false on failure.
	 */
	public static function get( $id ) {
		global $kyssdb;

		if ( ! $budget = $kyssdb->query(
			"SELECT * 
			FROM {$kyssdb->bilanci} 
			WHERE ID = '{$id}'"
		) )
			return false;

		if ( $budget->num_rows == 0 )
			return new KYSS_Error( 'budget_not_found', 'Bilancio non trovato', array( 'ID' => $id ) );
		$budget = $budget->fetch_object( 'KYSS_Budget' );

		return $budget;
	}

	/**
	 * Retrieve Budget list.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @return array|false Array of KYSS_Budget objects or false on failure.
	 */
	public static function get_list() {
		global $kyssdb;

		if ( ! $budget = $kyssdb->query(
			"SELECT * FROM {$kyssdb->bilanci}"
		) )
			return false;

		$budgets = array();

		for ( $i = 0; $i < $budget->num_rows; $i++ )
			array_push( $budgets, $budget->fetch_object( 'KYSS_Budget' ) );

		return $budgets;
	}

	/**
	 * Insert new Budget into the database.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Associative array of column names and values.
	 * @return int The new created budget's ID or false on failure.
	 */
	public static function create( $data ) {
		global $kyssdb;

		if ( empty( $data ) )
			return new KYSS_Error( 'empty_budget_data', 'Budgets data cannot be empty!' );

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

		$query = "INSERT INTO {$kyssdb->bilanci} ({$columns}) VALUES ({$values})";
		if ( ! $result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}

		return $kyssdb->insert_id;
	}

	/**
	 * Update Budget in the db.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  array $data Budget's data.
	 * @return bool Whether the update succeeded or not.
	 */
	public static function update( $id, $data ) {
		global $kyssdb;

		if ( empty( $data) )
			return false;

		$result = $kyssdb->update( $kyssdb->bilanci, $data, array( 'ID' => $id ) );

		if ( $result )
			return true;
		return false;
	}
}
