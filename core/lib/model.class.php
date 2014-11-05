<?php
/**
 * Base Model.
 *
 * @package  KYSS
 * @subpackage Model
 * @since  0.15.0
 */

/**
 * Base Model class.
 *
 * @package  KYSS
 * @subpackage  Model
 * @since  0.15.0
 * @version  1.0.0
 */
class Model {
	/**
	 * Constructor.
	 *
	 * TODO: Define needed core properties.
	 *
	 * @since 1.0.0
	 * @access public
	 * @final
	 */
	final public function __construct() {
		$this->before();

		$this->after();
	}

	/**
	 * Run actions at the beginning of the constructor.
	 *
	 * Empty by default.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function before() {

	}

	/**
	 * Run actions at the end of the constructor.
	 *
	 * Empty by default.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function after() {
		
	}
}