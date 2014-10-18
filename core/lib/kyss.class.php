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
	 * KYSS Version.
	 *
	 * @since  1.0.1
	 * @access public
	 * @var string
	 */
	const VERSION = '0.15.0';

	/**
	 * PHP required version.
	 *
	 * PHP 5.3.0 added the ability of persistent connections to MySQLi and
	 * namespace support.
	 *
	 * @since  1.0.1
	 * @access public
	 * @var string
	 */
	const PHP_REQUIRED_VERSION = '5.3.0';

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
		try {
			self::check_php_version();
		} catch ( KYSSException $e ) {
			$e->kill();
		}

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

	/**
	 * Check server's PHP version.
	 *
	 * @since  1.0.1
	 * @access private
	 * @static
	 */
	private static function check_php_version() {
		if ( version_compare( PHP_VERSION, self::PHP_REQUIRED_VERSION, '>=' ) )
			return;
		$message = '<h2>Failed Dependency</h2>';
		$message .= '<p>KYSS richiede PHP <b>' . self::PHP_REQUIRED_VERSION . '</b> per funzionare, mentre il server ha PHP ' . PHP_VERSION . '.</p>';
		$message .= '<p>Aggiornare PHP e ricaricare la pagina per continuare.</p>';
		throw new KYSSException( $message );
	}
}