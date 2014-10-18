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
	 *
	 * @param  Model $model Optional. The associated model.
	 */
	public function __construct( Model $model = null ) {
		$this->model = $model;
	}
}