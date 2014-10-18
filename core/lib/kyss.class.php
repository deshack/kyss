<?php
/**
 * The main KYSS logic.
 *
 * @package  KYSS
 * @subpackage  Library
 * @since  0.15.0
 */

/**
 * The main KYSS class.
 *
 * @package  KYSS
 * @subpackage  Library
 * @since  0.15.0
 * @version  1.0.1
 */
class KYSS {
	/**
	 * The router.
	 *
	 * @since  1.0.1
	 * @access private
	 * @var Router
	 */
	private $router;

	/**
	 * Database handler.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var PDO
	 */
	private $db;

	/**
	 * Bootstrap the application.
	 *
	 * Analyze the URL elements and calls the according controller/method
	 * or the fallback.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {
		// Open connection to the database.
		$this->db_connect();

		$this->router = new Router;
	}

	/**
	 * Open a connection to the database.
	 *
	 * Uses PDO in order to support multiple drivers, not only MySQL.
	 * It also sets fetch mode to "objects".
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function db_connect() {
		$options = array( PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING );

		// Generate a database connection, using the PDO connector.
		// TODO: Define `DB_TYPE` constant.
		$this->db = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, $options );
	}
}