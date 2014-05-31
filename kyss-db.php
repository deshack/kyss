<?php
/**
 * KYSS DB Class
 *
 * @package KYSS
 * @subpackage Database
 * @since  0.1.0
 */

class KYSSDB {
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
	 * Connect to the database server and select a database.
	 *
	 * PHP5 style constructor for compatibility with PHP5.
	 * Does the actual setting up of the class properties and
	 * connection to the database.
	 *
	 * @since  0.1.0
	 *
	 * @param  string $dbuser MySQL database user
	 * @param  string $dbpassword MySQL database password
	 * @param  string $dbname MySQL database name
	 * @param  string $dbhost MySQL database host
	 */
	function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;

		$this->db_connect();
	}
}