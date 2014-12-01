<?php
/**
 * KYSS Router.
 *
 * @package KYSS
 * @subpackage  Library
 * @since  0.15.0
 */

/**
 * KYSS Router class.
 *
 * @package  KYSS
 * @subpackage Library
 * @since  0.15.0
 * @version  1.0.1
 */
class Router {
	/**
	 * The controller.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string|object
	 */
	protected $controller;

	/**
	 * The action (controller method).
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string
	 */
	protected $action;

	/**
	 * List of parameters.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var  array
	 */
	protected $parameters;

	/**
	 * Constructor.
	 *
	 * Analyzes the URL elements and calls the according controller/method
	 * or the fallback.
	 *
	 * @since  1.0.0
	 * @since  1.0.1 Introduced `$url` parameter.
	 * @access public
	 *
	 * @param  string $url The requested URL.
	 */
	public function __construct( $url ) {
		// Split URL into controller, action and optionally parameters.
		$this->split_url( $url );

		// Instantiate the controller.
		$this->init_controller();

		// Route to the right action.
		$this->route();
	}

	/**
	 * Retrieve and split the URL.
	 *
	 * Splits the URL into controller, action, and parameters.
	 *
	 * @since  1.0.0
	 * @since  1.0.1 Introduced `$url` parameter.
	 * @access private
	 *
	 * @param  string $url The requested URL.
	 */
	private function split_url( $url ) {
		$url = trim( $url, '/' );
		$url = filter_var( $url, FILTER_SANITIZE_URL );
		$url = explode( '/', $url );

		if ( empty( $url ) )
			return;

		// Put URL parts into according properties.
		// `array_shift()` returns NULL if `$url` is empty.
		$this->controller = ucfirst( array_shift( $url ) ) . 'Controller';
		$this->action = array_shift( $url );
		$this->parameters = (!empty( $url ) ? $url : null);

		var_dump($this->controller,$this->action,$this->parameters);
	}

	/**
	 * Instantiate the requested controller.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function init_controller() {
		if ( file_exists( PATH_CONTROLLERS . $this->controller . '.php' ) ) {
			$this->controller = new $this->controller;
		} else {
			// Invalid URL, show home/index without parameters.
			// TODO: Show Error 404 Page instead.
			$this->controller = new DashboardController;
			$this->action = 'index';
			$this->parameters = null;
		}
	}

	/**
	 * Execute the right action.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function route() {
		// Check for method.
		if ( method_exists( $this->controller, $this->action ) ) {
			// Call the method and maybe pass the arguments to it.
			if ( isset( $this->parameters ) )
				call_user_func_array( array( $this->controller, "{$this->action}" ), $this->parameters );
			else
				$this->controller->{$this->action}();
		} else {
			// Fallback: call the index() method of the selected controller.
			$this->controller->index();
		}
	}
}