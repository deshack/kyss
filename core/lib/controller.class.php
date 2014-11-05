<?php
/**
 * Base Controller.
 *
 * @package  KYSS
 * @subpackage  Controller
 * @since  0.15.0
 */

/**
 * Base Controller class.
 *
 * @package  KYSS
 * @subpackage  Controller
 * @since  0.15.0
 * @version  1.0.0
 */
class Controller {
	/**
	 * Associated model object.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var Model
	 */
	private $model;

	/**
	 * Constructor.
	 *
	 * Sets up the `$model` property (through Dependency Injection).
	 *
	 * @since  1.0.0
	 * @access public
	 * @final
	 *
	 * @param  Model $model Optional. The associated model.
	 */
	final public function __construct( Model $model = null ) {
		$this->before();

		$this->model = $model;

		$this->after();
	}

	/**
	 * Run actions at the beginning of the constructor.
	 *
	 * Empty by default.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function before() {

	}

	/**
	 * Run actions at the end of the constructor.
	 *
	 * Empty by default.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function after() {

	}
}