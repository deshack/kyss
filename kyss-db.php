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
	private $show_errors = true;

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
	 * Connect to the database server and select a database.
	 *
	 * PHP5 style constructor for compatibility with PHP5.
	 * Does the actual setting up of the class properties and
	 * connection to the database.
	 *
	 * @since  0.1.0
	 *
	 * @param  string $dbuser MySQL database user.
	 * @param  string $dbpassword MySQL database password.
	 * @param  string $dbname MySQL database name.
	 * @param  string $dbhost MySQL database host.
	 */
	function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {
		register_shutdown_function( array( $this, '__destruct' ) );

		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;

		$this->db_connect();
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
	 * Connect to and select database.
	 *
	 * @since  0.1.0
	 * @todo  Handle errors.
	 *
	 * @return bool True with a successful connection, false on failure.
	 */
	function db_connect() {
		$this->dbh = mysql_connect( $this->dbhost, $this->dbuser, $this->dbpassword );

		if ( $this->dbh ) {
			$this->has_connected = true;
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
	 * @todo  Handle errors.
	 *
	 * @param  string $db MySQL database name.
	 * @param  resource $dbh Optional. Link identifier.
	 * @return null Always null.
	 */
	function select( $db, $dbh = null ) {
		if ( is_null($dbh) )
			$dbh = $this->dbh;

		$success = @mysql_select_db( $db, $dbh );
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