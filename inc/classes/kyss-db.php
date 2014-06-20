<?php
/**
 * KYSS Database API
 *
 * Provide all necessary methods to connect to the database server
 * an execute queries.
 *
 * @package KYSS
 * @subpackage  DB
 */

/**
 * KYSS DB Class
 *
 * Uses MySQLi PHP extension, because as of PHP5.5 the MySQL extension is deprecated.
 *
 * @package  KYSS
 * @subpackage  DB
 * @since  0.1.0
 * @since  0.9.0 Extend mysqli.
 * @see  http://www.php.net/manual/en/book.mysqli.php mysqli class documentation.
 */
class KYSS_DB extends mysqli {
	/**
	 * Database Host
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var  string
	 */
	protected $dbhost;

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
	 * @since  0.1.0
	 * @access protected
	 * @var  string
	 */
	protected $dbpass;

	/**
	 * Database Name
	 *
	 * @since 0.1.0
	 * @access protected
	 * @var string
	 */
	protected $dbname;

	/**
	 * Database Port
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var  int
	 */
	protected $dbport;

	/**
	 * Database Socket
	 *
	 * @since  0.1.0
	 * @access protected
	 * @var  string
	 */
	protected $dbsocket;

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
	 * Connect to the database server.
	 *
	 * Does the actual setting up of the class properties and
	 * connection to the database.
	 *
	 * @todo  Check if database exists or not.
	 * @todo  Initialize charset.
	 *
	 * @since  0.1.0
	 * @access public
	 *
	 * @param  string $dbhost MySQL database host.
	 * @param  string $dbuser MySQL database user.
	 * @param  string $dbpass MySQL database password.
	 * @param  string $dbname Optional. MySQL database name. Defaults to empty string.
	 * @return  Returns an object which represents the connection to a MySQL server.
	 */
	public function __construct( $dbhost, $dbuser, $dbpass, $dbname = '' ) {
		// Initialize charset
		//$this->init_charset()
		
		$this->dbhost = $dbhost;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->dbname = $dbname;

		$this->get_port_socket();

		parent::__construct( $this->dbhost, $this->dbuser, $this->dbpass, $this->dbname, $this->dbport, $this->dbsocket );

		if ( $this->connect_errno ) {
			$title = '<h1>Error establishing a database connection</h1>';
			$message = $title . '<p>' . $this->connect_error . '</p>';
			//$this->bail()
		}
	}

	/**
	 * Retrieve database port and database socket.
	 *
	 * Retrieves the database port and socket, if present, from the database host.
	 *
	 * @since  0.9.0
	 * @access private
	 *
	 * @return  null
	 */
	private function get_port_socket() {
		$port = null;
		$socket = null;
		$port_or_socket = strstr( $this->dbhost, ':' );

		if ( ! empty( $port_or_socket ) ) {
			// We have detected a port or a socket or both in the host.
			// First, isolate the host.
			$this->dbhost = substr( $this->dbhost, 0, strpos( $this->dbhost, ':' ) );
			$port_or_socket = substr( $port_or_socket, 1 );
			if ( 0 !== strpos( $port_or_socket, '/' ) ) {
				// We have a port.
				$port = intval( $port_or_socket );
				$socket = strstr( $port_or_socket, ':' );
				if ( ! empty( $socket ) )
					// We have a socket too.
					$socket = substr( $socket, 1 );
			} else {
				// We only have a socket.
				$socket = $port_or_socket;
			}
		}

		// Assign detected values if present, defaults otherwise.
		$this->dbport = is_null( $port ) ? ini_get("mysqli.default_port") : $port;
		$this->dbsocket = is_null( $socket ) ? ini_get("mysqli.default_socket") : $socket;
	}

	/**
	 * Create and select database using the current database connection.
	 *
	 * On failure, the execution will bail and display a DB error.
	 *
	 * @since  0.4.0
	 * @access public
	 *
	 * @param  string $db MySQL database name.
	 * @return  bool True on success, false on failure.
	 */
	public function create( $db ) {
		$query = $this->real_escape_string( sprintf( "CREATE DATABASE %s", $db ) );
		$success = $this->query( $query );

		if ( $success ) {
			$this->select_db( $db );
			return true;
		}

		//$this->bail()
		return false;
	}

	/**
	 * Look for errors.
	 *
	 * Checks if the last execution caused an error.
	 *
	 * @since  0.9.0
	 * @access public
	 *
	 * @return  bool True if an error is detected, false otherwise.
	 */
	public function has_error() {
		if ( '00000' == $this->sqlstate )
			return false;
		return true;
	}
}