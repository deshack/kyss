<?php
/**
 * The main KYSS logic.
 *
 * @package  KYSS\Library
 * @since  0.15.0
 */

namespace KYSS\Library;

/**
 * The main KYSS class.
 *
 * @package  KYSS\Library
 * @since  0.15.0
 * @version  1.0.1
 * @final
 */
final class KYSS {
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
	 * PHP 5.3.0 added namespace support.
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
	 * @var \KYSS\Library\Router
	 */
	private $router;

	/**
	 * Database handler.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var \PDO
	 */
	private $db;

	/**
	 * Bootstrap the application.
	 *
	 * Analyzes the URL elements and calls the according controller/method
	 * or the fallback.
	 *
	 * @since  1.0.0
	 * @since  1.0.1 Added $router parameter.
	 * @access public
	 *
	 * @param  \KYSS\Library\Router $router
	 */
	public function __construct( Router $router ) {
		try {
			self::check_php_version();
		} catch( DependencyException $e ) {
			$e->kill();
		}

		// Open connection to the database.
		$this->db_connect();

		$this->router = $router;
	}

	/**
	 * Open connection to the database.
	 *
	 * Uses PDO in order to support multiple drivers, not only MySQL.
	 * It also sets fetch mode to "objects".
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function db_connect() {
		$options = array(
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
		);

		// Generate database connection, using the PDO connector.
		$this->db = new PDO( DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, $options );
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
		$message .= '<p>KYSS richiede PHP <b>' . self::PHP_REQUIRED_VERSION . '</b> per funzionare, mentre il server ha PHP ' . PHP_VERSION . '</p>';
		$message .= '<p>Aggiornare PHP e ricaricare la pagina per continuare.</p>';
		throw new DependencyException( $message );
	}
}