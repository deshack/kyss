<?php
/**
 * KYSS DB Class
 *
 * @package KYSS
 * @subpackage Database
 * @since  0.1.0
 */

class kyssdb {

	/**
	 * Whether to show SQL/DB errors.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var  bool
	 */
	private $show_errors = false;

	/**
	 * Whether to suppress errors during the DB bootstrapping.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var  bool
	 */
	private $suppress_errors = false;

	/**
	 * Last error during query.
	 *
	 * @since  0.6.0
	 * @access public
	 * @var  string
	 */
	public $last_error = '';

	/**
	 * Amount of queries made.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var  int
	 */
	private $num_queries = 0;

	/**
	 * Count rows returned by previous query.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var  int
	 */
	private $num_rows = 0;

	/**
	 * Count of affected rows by previous query.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var  int
	 */
	private $rows_affected = 0;

	/**
	 * The number of times to retry reconnecting before dying.
	 *
	 * @since  0.1.0
	 * @access protected
	 * @see kyssdb::check_connection()
	 * @var  int
	 */
	protected $reconnect_retries = 5;

	/**
	 * Database table columns charset.
	 *
	 * @since  0.6.0
	 * @access public
	 * @var  string
	 */
	public $charset;

	/**
	 * Database table columns collate.
	 *
	 * @since  0.6.0
	 * @access public
	 * @var  string
	 */
	public $collate;

	/**
	 * A textual description of the last query/get_row/get_var call.
	 *
	 * @since  0.6.0
	 * @access public
	 * @var  string
	 */
	public $func_call;

	/**
	 * The ID generated for an AUTO_INCREMENT column by the previous query (usually INSERT).
	 *
	 * @since  0.6.0
	 * @access public
	 * @var  int
	 */
	public $insert_id = 0;

	/**
	 * Last query made.
	 *
	 * @since 0.6.0
	 * @access private
	 * @var  array
	 */
	private $last_query;

	/**
	 * Results of the last query made.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var  array|null
	 */
	private $last_result;

	/**
	 * MySQL result, which is either a resource or boolean.
	 *
	 * @since  0.6.0
	 * @access protected
	 * @var  mixed
	 */
	protected $result;

	/**
	 * Saved info on the table column.
	 *
	 * @since  0.6.0
	 * @access protected
	 * @var  array
	 */
	protected $col_info;

	/**
	 * Saved queries that were executed.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var  array
	 */
	private $queries;

	/**
	 * List of KYSS tables.
	 *
	 * @todo  Populate array.
	 *
	 * @since  x.x.x
	 * @access private
	 * @see kyssdb::tables()
	 * @var  array
	 */
	//private $tables = array();

	/**
	 * KYSS Users table.
	 *
	 * @todo  Add similar properties for all db tables.
	 *
	 * @since  x.x.x
	 * @access public
	 * @var  string
	 */
	//public $users;

	/**
	 * Database Username
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var  string
	 */
	protected $dbuser;

	/**
	 * Database Password
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var  string
	 */
	protected $dbpassword;

	/**
	 * Database Name
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string
	 */
	protected $dbname;

	/**
	 * Database Host
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var string
	 */
	protected $dbhost;

	/**
	 * Database Handle
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var  string
	 */
	protected $dbh;

	/**
	 * Whether we've managed to successfully connect at some point.
	 *
	 * @since  0.1.0
	 * @access private
	 * @var  bool
	 */
	private $has_connected = false;

	/**
	 * Whether the database query are ready to start executing.
	 *
	 * @since  0.6.0
	 * @access private
	 * @var  bool
	 */
	private $ready = false;

	/**
	 * Connect to the database server and select a database.
	 *
	 * PHP5 style constructor for compatibility with PHP5.
	 * Does the actual setting up of the class properties and
	 * connection to the database.
	 *
	 * @since  0.1.0
	 *
	 * @param  string $dbhost MySQL database host.
	 * @param  string $dbname MySQL database name.
	 * @param  string $dbuser MySQL database user.
	 * @param  string $dbpassword MySQL database password.
	 * @param  bool   $create Whether to create a new database or not. Default <false>
	 */
	function __construct( $dbhost, $dbname, $dbuser, $dbpassword, $create = false ) {
		register_shutdown_function( array( $this, '__destruct' ) );

		$this->init_charset();

		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;

		$this->db_connect( $create );
	}

	/**
	 * PHP5 style destructor.
	 *
	 * Will run when database object is destroyed.
	 *
	 * @see  kyssdb::__construct()
	 * @since  0.1.0
	 * @return  bool true
	 */
	function __destruct() {
		return true;
	}

	/**
	 * PHP5 style magic getter, used to lazy-load expensive data.
	 *
	 * @since  0.6.0
	 *
	 * @param  string $name The private member to get, and optionally process.
	 * @return  mixed The private member.
	 */
	function __get( $name ) {
		if ( 'col_info' == $name )
			$this->load_col_info();

		return $this->name;
	}

	/**
	 * Returns an array of KYSS tables.
	 *
	 * @since  0.6.0
	 * @access public
	 * @uses  kyssdb::$tables
	 *
	 * @return  array Table names.
	 */
	public function tables() {
		return $tables;
	}

	/**
	 * Connect to and select database.
	 *
	 * @since  0.1.0
	 * @todo  Handle errors.
	 *
	 * @param  bool $create Whether to create a new db or not. Default <false>.
	 * @return bool True with a successful connection, false on failure.
	 */
	function db_connect( $create = false ) {
		$this->dbh = mysql_connect( $this->dbhost, $this->dbuser, $this->dbpassword );

		if ( $this->dbh ) {
			$this->has_connected = true;
			$this->set_charset( $this->dbh );
			$this->ready = true;
			if ( $create )
				if ( ! $this->create( $this->dbname, $this->dbh ) )
					return true;

			$this->select( $this->dbname, $this->dbh );

			return true;
		}

		// If we are here, the connection failed, so we need to handle the exception.
		$this->bail( sprintf( "
<h1>Error establishing a database connection</h1>
<p>This either means that the data provided in the configuration file is incorrect or we can't contact the database server at <code>%s</code>.
This could mean your host's database server is down.</p>
<ul>
	<li>Are you sure you have the correct username and password?</li>
	<li>Are you sure that you have typed the correct hostname?</li>
	<li>Are you sure that the database server is running?</li>
</ul>
<p>If you are unsure what these terms mean, you should probably contact your host.</p>
", htmlspecialchars( $this->dbhost, ENT_QUOTES ) ), 'db_connect_fail' );

		return false;
	}

	/**
	 * Check that the connection to the database is still up. If not, try to reconnect.
	 *
	 * If this function is unable to reconnect, it will forcibly die.
	 *
	 * @since  0.1.0
	 *
	 * @return bool True if the connection is up.
	 */
	function check_connection() {
		if ( @mysql_ping( $this->dbh ) )
			return true;

		// Disable warning, as we don't want to see a multitude of "unable to connect" messages.
		$error_reporting = error_reporting();
		error_reporting( $error_reporting & ~E_WARNING );

		for ( $tries = 1; $tries <= $this->reconnect_retries; $tries++ ) {
			// On the last try, re-enable warnings. We want to see a single instance of the
			// "unable to connect" message on the bail() screen, if it appears.
			if ( $this->reconnect_retries === $tries ) {
				error_reporting( $error_reporting );
			}

			if ( $this->db_connect( false ) ) {
				if ( $error_reporting ) {
					error_reporting( $error_reporting );
				}

				return true;
			}

			sleep(1);
		}

		// We weren't able to reconnect, so we better bail.
		$this->bail( sprintf( "
<h1>Error reconnecting to the database</h1>
<p>This means that we lost contact with the database server at <code>%s</code>. This could mean your host's database server is down.</p>
<ul>
	<li>Are you sure that the database server is running?</li>
	<li>Are you sure that the database server is not under particulary heavy load?</li>
</ul>
<p>If you're unsure what these terms mean you should probably contact your host.</p>
", htmlspecialchars( $this->dbhost, ENT_QUOTES) ), 'db_connect_fail' );

		// Call dead_db() if bail didn't die, because the db is dead (at least temporarily).
		dead_db();
	}

	/**
	 * Select a database using the current database connection.
	 *
	 * The database name will be changed based on the current database connection.
	 * On failure, the execution will bail and display a DB error.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $db MySQL database name.
	 * @param  resource $dbh Optional. Link identifier.
	 * @return null Always null.
	 */
	function select( $db, $dbh = null ) {
		if ( is_null($dbh) )
			$dbh = $this->dbh;

		$success = @mysql_select_db( $db, $dbh );

		if ( ! $success ) {
			$this->ready = false;
			$this->bail( sprintf( '<h1>Can&#8217;t select database</h1>
<p>We were able to connect to the database server (which means your username and password are okay) but not able to select the <code>%1$s</code> database.</p>
<ul>
	<li>Are you sure it exists?</li>
	<li>Does the user <code>%2$s</code> have permission to use the <code>%1$s</code> database?</li>
	<li>On some systems the name of your database is prefixed with your username, so it would be like <code>username_%1$s</code>. Could that be the problem?</li>
</ul>
<p>If you don&#8217;t know how to set up a database you should <strong>contact your host</strong>.</p>', htmlspecialchars( $db, ENT_QUOTES ), htmlspecialchars( $this->dbuser, ENT_QUOTES ) ), 'db_select_fail' );

			return;
		}
	}

	/**
	 * Create database using the current database connection.
	 *
	 * The database name will be changed based on the current database connection.
	 * On failure, the execution will bail and display a DB error.
	 *
	 * @since  0.4.0
	 *
	 * @param  string $db MySQL database name.
	 * @param  resource $dbh Optional. Link identifier.
	 * @return bool True on success, false on failure.
	 */
	function create( $db, $dbh = null ) {
		if ( is_null( $dbh ) )
			$dbh = $this->dbh;

		$success = mysql_create_db( $db, $dbh );

		if ( ! $success ) {
			$this->bail( sprintf( '<h1>Can&#8217;t create database</h1>
<p>We were able to connect to the database server (which means your username and password are okay) but not able to create the <code>%1$s</code> database.</p>
<ul>
	<li>Does the user <code>%2$s</code> have permission to create a new database?</li>
</ul>
<p>If the user <code>%2$s</code> doesn&#8217;t have permission to create a new database, maybe it already has permission to use one. Your host should have given its name.</p>', htmlspecialchars($db, ENT_QUOTES), htmlspecialchars($this->dbuser, ENT_QUOTES) ), 'db_create_fail' );

			return false;
		}

		return true;
	}

	/**
	 * Perform a MySQL database query, using current database connection.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @param  string $query Database query.
	 * @return  int|false Number of rows affected/selected or false on error.
	 */
	public function query( $query ) {
		if ( ! $this->ready )
			return false;

		$return = 0;
		$this->flush();

		// Log how the function was called.
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug.
		$this->last_query = $query;

		$this->_do_query( $query );

		// MySQL server has gone away, try to reconnect.
		$mysql_errno = 0;
		if ( ! empty( $this->dbh ) ) {
			$mysql_errno = mysql_errno( $this->dbh );
		}

		if ( empty( $this->dbh ) || 2006 == $mysql_errno ) {
			if ( $this->check_connection() ) {
				$this->_do_query( $query );
			} else {
				$this->insert_id = 0;
				return false;
			}
		}

		// If there is an error then take note of it.
		$this->last_error = mysql_error( $this->dbh );

		if ( $this->last_error ) {
			// Clear insert_id on a subsequent failed insert.
			if ( $this->insert_id && preg_match( '/^\s*(insert|replace)\s/i', $query ) )
				$this->insert_id = 0;

			$this->print_error();
			return false;
		}

		if ( preg_match( '/^\s*(create|alter|truncate|drop)\s/i', $query ) ) {
			$return = $this->result;
		} else if ( preg_match( '/^\s*(insert|delete|update|replace)\s/i', $query ) ) {
			$this->rows_affected = mysql_affected_rows( $this->dbh );
			$return = $this->rows_affected;
		} else {
			$num_rows = 0;
			while ( $row = @mysql_fetch_object( $this->result ) ) {
				$this->last_result[$num_rows] = $row;
				$num_rows++;
			}

			// Log number of rows the query returned
			// and return number of rows selected.
			$this->num_rows = $num_rows;
			$return = $num_rows;
		}

		return $return;
	}

	/**
	 * Internal function to perform the mysql_query() call.
	 *
	 * @since  0.6.0
	 * @access private
	 * @see  kyssdb::query()
	 *
	 * @param  string $query The query to run.
	 */
	private function _do_query( $query ) {
		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES )
			$this->timer_start();

		$this->result = @mysql_query( $query, $this->dbh );
		$this->num_queries++;

		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES )
			$this->queries[] = array( $query, $this->timer_stop(), $this->get_caller() );
	}

	/**
	 * Insert row into table.
	 *
	 * <code>
	 * kyssdb::insert( 'table', array( 'column' => 'foo', 'field' => 'bar' ) );
	 * kyssdb::insert( 'table', array( 'column' => 'foo', 'field' => 2014 ), array( '%s', '%d' ) );
	 * </code>
	 *
	 * @since  0.6.0
	 * @see kyssdb::prepare()
	 * @access public
	 *
	 * @param  string $table Table name.
	 * @param  array $data Data to insert (in column => value pairs). Both $data columns and $data values
	 *                     should be "raw" (neither should be SQL escaped).
	 * @param  array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string,
	 *                              that format will be used for all of the values in $data. A format is one
	 *                              of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data
	 *                              will be treated as strings.
	 * @return  int|false The number of rows inserted, or false on error.
	 */
	public function insert( $table, $data, $format = null ) {
		return $this->_insert_replace_helper( $table, $data, $format, 'INSERT' );
	}

	/**
	 * Replace row into table.
	 *
	 * <code>
	 * kyssdb::replace( 'table', array( 'column' => 'foo', 'field' => 'bar' ) );
	 * kyssdb::replace( 'table', array( 'column' => 'foo', 'field' => 2014 ), array( '%s', '%d' ) );
	 * </code>
	 *
	 * @since  0.6.0
	 * @see  kyssdb::prepare()
	 * @access public
	 *
	 * @param  string $table Table name.
	 * @param  array $data Data to insert (in column => value pairs). Both $data columns and $data values
	 *                     should be "raw" (neither should be SQL escaped).
	 * @param  array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string,
	 *                              that format will be used for all of the values in $data. A format is one
	 *                              of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data
	 *                              will be treated as strings.
	 * @return  int|false The number of rows inserted, or false on error.
	 */
	public function replace( $table, $data, $format = null ) {
		return $this->_insert_replace_helper( $table, $data, $format, 'REPLACE' );
	}

	/**
	 * Helper function for insert and replace.
	 *
	 * Runs an insert or replace query based on $type argument.
	 *
	 * @since  0.6.0
	 * @see  kyssdb::prepare()
	 * @access private
	 *
	 * @param  string $table Table name.
	 * @param  array $data Data to insert (in column => value pairs). Both $data columns and $data values
	 *                     should be "raw" (neither should be SQL escaped).
	 * @param  array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string,
	 *                              that format will be used for all of the values in $data. A format is one
	 *                              of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data
	 *                              will be treated as strings.
	 * @param  string $type Optional. Type of operation. Default <INSERT>. Accept <INSERT>, <REPLACE>.
	 * @return  int|false The number of rows inserted, or false on error.
	 */
	private function _insert_replace_helper( $table, $data, $format = null, $type = 'INSERT' ) {
		if ( ! in_array( strtoupper( $type ), array( 'REPLACE', 'INSERT' ) ) )
			return false;
		$this->insert_id = 0;
		$formats = $format = (array) $format;
		$fields = array_keys( $data );
		$formatted_fields = array();
		foreach ( $fields as $field ) {
			if ( !empty( $format ) )
				$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
			else
				$form = '%s';
			$formatted_fields[] = $form;
		}
		$sql = "{$type} INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES (" . implode( ",", $formatted_fields ) . ")";
		return $this->query( $this->prepare( $sql, $data ) );
	}

	/**
	 * Update row in table.
	 *
	 * <code>
	 * kyssdb::update( 'table', array( 'column' => 'foo', 'field', 'bar' ), array( 'ID' => 1 ) );
	 * kyssdb::update( 'table', array( 'column' => 'foo', 'field', 2014 ), array( 'ID' => 1 ), array( '%s', '%d' ), array( '%d' ) );
	 * </code>
	 *
	 * @since  0.6.0
	 * @see  kyssdb::prepare()
	 * @access public
	 *
	 * @param  string $table Table name.
	 * @param  string $data Data to update (in column => value pairs). Both $data columns and $data values
	 *                      should be "raw" (neither should be SQL escaped).
	 * @param  array $where A named array of WHERE clauses (in column => value pairs). Multiple clauses will
	 *                      be joined with ANDs. Both $where columns and $where values should be "raw".
	 * @param  array|string $format Optional. An array of formats to be mapped to each of the values in $data.
	 *                              If string, that format will be used for all of the values in $data. A format
	 *                              is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values
	 *                              in $data will be treated as strings.
	 * @param  array|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
	 *                                    If string, that format will be used for all of the items in $where. A format
	 *                                    is one of '%d', '%f', '%s' (integer, float, string). if omitted, all values
	 *                                    in $where will be treated as strings.
	 * @return  int|false The number of rows updated, or false on error.
	 */
	public function update( $table, $data, $where, $format = null, $where_format = null ) {
		if ( ! is_array( $data ) || ! is_array( $where ) )
			return false;

		$formats = $format = (array) $format;
		$bits = $wheres = array();
		foreach ( (array) array_keys( $data ) as $field ) {
			if ( !empty($format) )
				$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
			else
				$form = '%s';
			$bits[] = "`field` = {$form}";
		}

		$where_formats = $where_format = (array) $where_format;
		foreach ( (array) array_keys($where) as $field ) {
			if ( !empty($where_format) )
				$form = ( $form = array_shift( $where_formats ) ) ? $form : $where_format[0];
			else
				$form = '%s';
			$wheres[] = "`$field` = {$form}";
		}

		$sql = "UPDATE `$table` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres );
		return $this->query( $this->prepare( $sql, array_merge( array_values( $data ), array_values( $where ) ) ) );
	}

	/**
	 * Delete row in table.
	 *
	 * <code>
	 * kyssdb::delete( 'table', array( 'ID' => 1 ) );
	 * kyssdb::delete( 'table', array( 'ID' => 1 ), array( '%d' ) );
	 * </code>
	 *
	 * @since  0.6.0
	 * @see  kyssdb::prepare()
	 * @access public
	 *
	 * @param  string $table Table name
	 * @param  array $where A named array of WHERE clauses (in column => value pairs). Multiple clauses will be
	 *                      joined with ANDs. Both $where columns and $where values should be "raw".
	 * @param  array|string $format Optional. An array of formats to be mapped to each of the values in $where.
	 *                              If string, that format will be used for all of the items in $where. A format
	 *                              is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values
	 *                              in $where will be treated as strings.
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function delete( $table, $where, $format = null ) {
		if ( ! is_array( $where ) )
			return false;

		$bits = $wheres = array();

		$formats = $format = (array) $format;

		foreach ( array_keys( $where ) as $field ) {
			if ( !empty($format) )
				$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
			else
				$form = '%s';

			$wheres[] = "$field = $form";
		}

		$sql = "DELETE FROM $table WHERE " . implode( ' AND ', $wheres );
		return $this->query( $this->prepare( $sql, $where ) );
	}

	/**
	 * Retrieve one value from the database.
	 *
	 * Executes a SQL query and returns the value from the SQL result.
	 * If the SQL result contains more than one column and/or more than one row, this function returns
	 * the value in the column and row specified.
	 * If $query is null, this function returns the value in the specified column and row from the previous
	 * SQL result.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @param  string|null $query Optional. SQL query. Default <null>.
	 * @param  int $x Optional. Column of value to return. Indexed from 0.
	 * @param  int $y Optional. Row of value to return. Indexed from 0.
	 * @return  string|null Database query result (as string), or null on failure.
	 */
	public function get_var( $query = null, $x = 0, $y = 0 ) {
		$this->func_call = "\$db->get_var(\"$query\", $x, $y)";
		if ( $query )
			$this->query( $query );

		// Extract var out of cached results based on x,y values.
		if ( !empty( $this->last_result[$y] ) )
			$values = array_values( get_object_vars( $this->last_result[$y] ) );

		// If there is a value return it, else return null
		return ( isset( $values[$x] ) && $values[$x] !== '' ) ? $values[$x] : null;
	}

	/**
	 * Retrieve one row from the database.
	 *
	 * Executes a SQL query and returns the rwo from the SQL result.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @param  string|null $query SQL query.
	 * @param  string $output Optional. One of ARRAY_A | ARRAY_N | OBJECT constants. Return an associative
	 *                        array (column => value, ...), a numerically indexed array (0 => value, ...),
	 *                        or an object (->column = value), respectively.
	 * @param  int $y Optional. Row to return. Indexed from 0.
	 * @return  mixed Database query result in format specified by $output or null on failure.
	 */
	public function get_row( $query = null, $output = OBJECT, $y = 0 ) {
		$this->func_call = "\$db->get_row(\"$query\", $output, $y)";
		if ( $query )
			$this->query( $query );
		else
			return null;

		if ( ! isset( $this->last_result[$y] ) )
			return null;

		if ( $output == OBJECT )
			return $this->last_result[$y] ? $this->last_result[$y] : null;
		elseif ( $output = ARRAY_A )
			return $this->last_result[$y] ? get_object_vars( $this->last_result[$y] ) : null;
		elseif ( $output = ARRAY_N )
			return $this->last_result[$y] ? array_values( get_object_vars( $this->last_result[$y] ) ) : null;
		else
			$this->print_error( " \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N" );
	}

	/**
	 * Retrieve one column from the database.
	 *
	 * Executes a SQL query and returns the column from the SQL result.
	 * If the SQL result contains more than one column, this function returns
	 * the column specified.
	 * If $query is null, this function returns the specified column from the
	 * previous SQL result.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @param  string|null $query Optional. SQL query. Defaults to previous query.
	 * @param  int $x Optional. Column to return. Indexed from 0.
	 * @return  array Database query result. Array indexed from 0 by SQL result row number.
	 */
	public function get_col( $query = null, $x = 0 ) {
		if ( $query )
			$this->query( $query );

		$new_array = array();
		// Extract the column values.
		for ( $i = 0, $j = count( $this->last_result ); $i < $j; $i++ ) {
			$new_array[$i] = $this->get_var( null, $x, $i );
		}
		return $new_array;
	}

	/**
	 * Retrieve entire SQL result set from the database (i.e., many rows).
	 *
	 * Executes a SQL query and returns the entire SQL result.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @param  string $query SQL query.
	 * @param  string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT |
	 *                        OBJECT_K constants. With one of the first three,
	 *                        return an array of rows indexed from 0 by SQL
	 *                        result row number. Each row is an associative
	 *                        array (column => value, ...), a numerically indexed
	 *                        array (0 => value, ...), or an object (->column =
	 *                        value), respectively. With OBJECT_K, return an
	 *                        associative array of row objects keyed by the value
	 *                        of each row's first column's value. Duplicate keys
	 *                        are discarded.
	 * @return  mixed Database query results.
	 */
	public function get_results( $query = null, $output = OBJECT ) {
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		if ( $query )
			$this->query( $query );
		else
			return null;

		$new_array = array();
		if ( $output = OBJECT ) {
			// Return an integer-keyed array of row objects.
			return $this->last_result;
		} elseif ( $output == OBJECT_K ) {
			// Return an array of row objects with keys from column 1
			// (duplicates are discarded).
			foreach ( $this->last_result as $row ) {
				$var_by_ref = get_object_vars( $row );
				$key = array_shift( $var_by_ref );
				if ( ! isset( $new_array[$key] ) )
					$new_array[$key] = $row;
			}
			return $new_array;
		} elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
			// Return an integer-keyed array of...
			if ( $this->last_result ) {
				foreach ( (array) $this->last_result as $row ) {
					if ( $output == ARRAY_N ) {
						// ...integer-keyed row arrays.
						$new_array[] = array_values( get_object_vars( $row ) );
					} else {
						// ...column name-keyed row arrays.
						$new_array[] = get_object_vars( $row );
					}
				}
			}
			return $new_array;
		}
		return null;
	}

	/**
	 * Load the column metadata from the last query.
	 *
	 * @since  0.6.0
	 * @access protected
	 */
	protected function load_col_info() {
		if ( $this->col_info )
			return;

		for ( $i = 0; $i < @mysql_num_fields( $this->result ); $i++ )
			$this->col_info[$i] = @mysql_fetch_field( $this->result, $i );
	}

	/**
	 * Retrieve column metadata from the last query.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @param  string $type Optional. Info type. Default <name>. Accept <name>, <table>, <def>,
	 *                      <max_length>, <not_null>, <primary_key>, <multiple_key>,
	 *                      <unique_key>, <numeric>, <blob>, <type>, <unsigned>,
	 *                      <zerofill>.
	 * @param  int $offset Optional. Column offset. 0: col name. 1: which table the col's in.
	 *                     2: col's max length. 3: if the col is numeric. 4: col's type.
	 * @return  mixed Column results.
	 */
	public function get_col_info( $type = 'name', $offset = -1 ) {
		$this->load_col_info();

		if ( $this->col_info ) {
			if ( $offset == -1 ) {
				$i = 0;
				$new_array = array();
				foreach( (array) $this->col_info as $col ) {
					$new_array[$i] = $col->{$type};
					$i++;
				}
				return $new_array;
			} else {
				return $this->col_info[$col_offset]->{$type};
			}
		}
	}

	/**
	 * Real escape, using mysql_real_escape_string().
	 *
	 * @since  0.6.0
	 * @access private
	 * @see  mysql_real_escape_string()
	 *
	 * @param  string $string String to escape.
	 * @return  string Escaped string.
	 */
	private function _real_escape( $string ) {
		if ( $this->dbh )
			return mysql_real_escape_string( $string, $this->dbh );

		$class = get_class( $this );
		return addslashes( $string );
	}

	/**
	 * Escape data.
	 *
	 * Works on arrays.
	 *
	 * @since  0.6.0
	 * @access private
	 * @uses kyssdb::_real_escape()
	 *
	 * @param  string|array $data Data to escape.
	 * @return  string|array Escaped data.
	 */
	private function _escape( $data ) {
		if ( is_array( $data ) ) {
			foreach ( $data as $k => $v ) {
				if ( is_array($v) )
					$data[$k] = $this->_escape( $v );
				else
					$data[$k] = $this->_real_escape( $v );
			}
		} else {
			$data = $this->_real_escape( $data );
		}

		return $data;
	}

	/**
	 * Escape content by reference for insertion into the database, for security.
	 *
	 * @since  0.6.0
	 * @uses  kyssdb::_real_escape()
	 * @access public
	 *
	 * @param  string $string String to escape.
	 * @return  void
	 */
	public function escape_by_ref( &$string ) {
		if ( ! is_float( $string ) )
			$string = $this->_real_escape( $string );
	}

	/**
	 * Prepare SQL query for safe execution.
	 *
	 * Uses sprintf()-like syntax.
	 *
	 * The following directives can be used in the query format string:
	 * 	- %d (integer)
	 * 	- %f (float)
	 * 	- %s (string)
	 * 	- %% (literal percentage sign - no argument needed)
	 *
	 * All of %d, %f, and %s are to be left unquoted in the query string and they need an argument passed for them.
	 * Literals (%) as parts of the query must be properly written as %%.
	 *
	 * May be called like {@link http://php.net/sprintf sprintf()} or like {@link http://php.net/vsprintf vsprintf()}.
	 *
	 * <code>
	 * kyssdb::prepare( "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d", 'foo', 2014 );
	 * kyssdb::prepare( "SELECT DATE_FORMAT(`field`, '%%c') FROM `table` WHERE `column` = %s", 'foo' );
	 * </code>
	 *
	 * @link http://php.net/sprintf Description of syntax.
	 * @since  0.6.0
	 * @access public
	 *
	 * @param  string $query Query statement with sprintf()-like placeholders.
	 * @param  array|mixed $args The array of variables to substitute into the query's placeholders if being called like
	 *                           {@link http://php.net/vsprintf vsprintf()}, or the first variable to substitute into the
	 *                           query's placeholders if being called like {@link http://php.net/sprintf sprintf()}.
	 * @param  mixed $args,... Further variables to substitute into the query's placeholders if being called like
	 *                         {@link http://php.net/sprintf sprintf()}.
	 * @return  null|false|string Sanitized query string, null if there is no query, false if there is an error and string
	 *                            if there was something to prepare.
	 */
	public function prepare( $query, $args ) {
		if ( is_null( $query ) )
			return;

		if ( strpos( $query, '%') === false ) {
			$this->print_error( sprintf( " \$db->prepare(string query, array args) -- The query argument of %s must heave a placeholder.", 'kyssdb::prepare()' );
			return false;
		}

		$args = func_get_args();
		// Remove the $query argument from the $args array.
		array_shift( $args );
		// If args were passed as an array (as in vsprintf()), move them up.
		if ( isset( $args[0] ) && is_array($args[0]) )
			$args = $args[0];
		// Unquote string placeholders.
		$query = str_replace( "'%s'", '%s', $query );
		$query = str_replace( '"%s"', '%s', $query );
		// Force floats to be locale unaware.
		// NOTE: (?<!%) is a lookbehind assertion meaning "that is not preceded by '%'".
		$query = preg_replace( '|(?<!%)%f|', '%F', $query );
		// Quote the strings, avoiding escaped strings like %%s.
		$query = preg_replace( '|(?<!%)%s|', "'%s'", $query );
		array_walk( $args, array( $this, 'escape_by_ref' ) );
		return @vsprintf( $query, $args );
	}

	/**
	 * Print SQL/DB error.
	 *
	 * @since  0.6.0
	 * @access public
	 * @global  array $EZSQL_ERROR Stores error information of query and error string.
	 *
	 * @param  string $str The error to display.
	 * @return  bool False if the showing of errors is disabled.
	 */
	public function print_error( $str = '' ) {
		global $EZSQL_ERROR;

		if ( ! $str )
			$str = mysql_error( $this->dbh );
		$EZSQL_ERROR[] = array( 'query' => $this->last_query, 'error_str' => $str );

		if ( $this->suppress_errors )
			return false;

		if ( $caller = $this->get_caller() )
			$error_str = sprintf( 'KYSS database error %1$s for query %2$s made by %3$s', $str, $this->last_query, $caller );
		else
			$error_str = sprintf( 'KYSS database error %1$s for query %2$s', $str, $this->last_query );

		error_log( $error_str );

		// Are we showing errors?
		if ( ! $this->show_errors )
			return false;

		// If there is an error then take note of it.
		$str = htmlspecialchars( $str, ENT_QUOTES );
		$query = htmlspecialchars( $this->last_query, ENT_QUOTES );

		print '<div id="error">
		<p class="kyssdberror"><strong>KYSS database error:</strong> [' . $str . ']<br />
		<code>' . $query . '</code></p>
		</div>';
	}

	/**
	 * Enable showing of database errors.
	 *
	 * This function should be used only to enable showing of errors.
	 * kyssdb::hide_errors() should be used instead for hiding of errors.
	 *
	 * @since  0.6.0
	 * @see  kyssdb::hide_errors()
	 * @access public
	 *
	 * @return  book Old value for showing errors.
	 */
	public function show_errors() {
		$errors = $this->show_errors;
		$this->show_errors = true;
		return $errors;
	}

	/**
	 * Disable showing of database errors.
	 *
	 * By default database errors are not shown.
	 *
	 * @since 0.6.0
	 * @see  kyssdb::show_errors()
	 * @access public
	 *
	 * @return  book Whether showing of errors was active.
	 */
	public function hide_errors() {
		$show = $this->show_errors;
		$this->show_errors = false;
		return $show;
	}

	/**
	 * Whether to suppress database errors.
	 *
	 * By default database errors are suppressed, with a simple
	 * call to this function they can be enabled.
	 *
	 * @since  0.6.0
	 * @see  kyssdb::hide_errors()
	 * @access public
	 *
	 * @param  bool $suppress Optional. New value. Default <true>.
	 * @return  bool Old value.
	 */
	public function suppress_errors( $suppress = true ) {
		$errors = $this->suppress_errors;
		$this->suppress_errors = (bool) $suppress;
		return $errors;
	}

	/**
	 * Kill cached query results.
	 *
	 * @since  0.6.0
	 *
	 * @return  void
	 */
	function flush() {
		$this->last_result = array();
		$this->col_info = null;
		$this->last_query = null;
		$this->rows_affected = $this->num_rows = 0;
		$this->last_error = '';

		if ( is_resource( $this->result ) )
			mysql_free_result( $this->result );
	}

	/**
	 * Set $this->charset and $this->collate.
	 *
	 * @since  0.6.0
	 * @access public
	 */
	public function init_charset() {
		if ( defined( 'DB_COLLATE' ) )
			$this->collate = DB_COLLATE;

		if ( defined( 'DB_CHARSET' ) )
			$this->charset = DB_CHARSET;
	}

	/**
	 * Set the connection's character set.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @param  resource $dbh The resource given by mysql_connect.
	 * @param  string $charset Optional. The character set. Default <null>
	 * @param  string $collate Optional. The collation. Default <null>
	 */
	public function set_charset( $dbh, $charset = null, $collate = null ) {
		if ( ! isset( $charset ) )
			$charset = $this->charset;
		if ( ! isset( $collate ) )
			$collate = $this->collate;
		if ( ! empty( $charset ) ) {
			if ( function_exists( 'mysql_set_charset' ) ) {
				mysql_set_charset( $charset, $dbh );
			} else {
				$query = $this->prepare( 'SET NAMES %s', $charset );
				if ( ! empty( $collate ) )
					$query .= $this->prepare( ' COLLATE %s', $collate );
				mysql_query( $query, $dbh );
			}
		}
	}

	/**
	 * The database character collate.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @return  string The database character collate.
	 */
	public function get_charset_collate() {
		$collate = '';

		if ( ! empty( $this->charset ) )
			$collate = "DEFAULT CHARACTER SET $this->charset";
		if ( ! empty( $this->collate ) )
			$collate .= " COLLATE $this->collate";

		return $collate;
	}

	/**
	 * Start the timer, for debugging purposes.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @return  true
	 */
	public function timer_start() {
		$this->time_start = microtime( true );
		return true;
	}

	/**
	 * Stop the debugging timer.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @return  float Total time spent on the query, in seconds.
	 */
	public function timer_stop() {
		return ( microtime( true ) - $this->time_start );
	}

	/**
	 * Retrieve name of function that called kyssdb.
	 *
	 * Searches up the list of functions until it reaches
	 * the one that would most logically had called this method.
	 *
	 * @since  0.6.0
	 * @access public
	 *
	 * @return  string The name of the calling function.
	 */
	public function get_caller() {
		return debug_backtrace_summary( __CLASS__ );
	}

	/**
	 * Wraps errors in a nice header and footer and dies.
	 *
	 * Will not die if kyssdb::$show_errors is false.
	 *
	 * @since  0.1.0
	 *
	 * @param  string $message The Error message.
	 * @param  string $error_code Optional. A Computer readable string to identify the error.
	 * @return false|void
	 */
	function bail( $message, $error_code = '500' ) {
		if ( !$this->show_errors ) {
			if ( class_exists( 'KYSS_Error' ) )
				$this->error = new KYSS_Error( $error_code, $message );
			else
				$this->error = $message;
			return false;
		}
		kyss_die($message);
	}
}