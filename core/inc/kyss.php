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
	 * @var string
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
	 * This is set as private because we want a singleton instance of
	 * this class.
	 *
	 * @since  0.15.0
	 * @access private
	 */
	private function __construct() {
		// Create array with URL parts in $url.
		$this->splitUrl();

		// Check for controller.
		if ( file_exists( ABSPATH . 'core/controller/' . $this->controller . '.php' ) ) {
			// Include the file and create the controller.
			require ABSPATH . 'core/controller/' . $this->controller . '.php';
			$this->controller = new $this->controller();

			// Check for method.
			if ( method_exists( $this->controller, $this->action ) ) {
				// Call the method and pass the arguments to it.
				// if ( isset( $this->))
			}
		}
	}
}