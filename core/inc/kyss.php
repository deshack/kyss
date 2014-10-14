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
 * @version  1.0.0
 */
class KYSS {
	/**
	 * The controller.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var string|object
	 */
	private $controller;

	/**
	 * The action (controller method).
	 *
	 * @since  1.0.0
	 * @access private
	 * @var string
	 */
	private $action;

	/**
	 * List of parameters.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var array
	 */
	private $parameters;

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

		// Create array with URL parts in $url.
		$this->split_url();

		// Check for controller.
		if ( file_exists( ABSPATH . 'core/controller/' . $this->controller . '.php' ) ) {
			// Include the file and create the controller.
			require ABSPATH . 'core/controller/' . $this->controller . '.php';
			$this->controller = new $this->controller();

			// Check for method.
			if ( method_exists( $this->controller, $this->action ) ) {
				// Call the method and pass the arguments to it.
				if ( isset( $this->parameters ) )
					call_user_func_array( array( $this->controller, {$this->action} ), $this->parameters );
				else // Call the method without parameters.
					$this->controller->{$this->action}();
			} else {
				// Fallback: call the index() method of the selected controller.
				$this->controller->index();
			}
		} else {
			// Invalid URL, show home/index.
			// TODO: Show Error 404 Page instead.
			require ABSPATH . 'core/controller/home.php';
			$home = new Home();
			$home->index();
		}
	}

	/**
	 * Retrieve and split the URL.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function split_url() {
		if ( ! isset( $_GET['url'] ) )
			return;

		$url = trim( $_GET['url'], '/' );
		$url = filter_var( $url, FILTER_SANITIZE_URL );
		$url = explode( '/', $url );

		if ( empty( $url ) )
			return;

		// Put URL parts into according properties.
		// `array_shift()` returns NULL if `$url` is empty.
		$this->controller = array_shift( $url );
		$this->action = array_shift( $url );
		$this->parameters = (!empty( $url ) ? $url : null);
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