<?php
/**
 * Tests for Exception's trace.
 *
 * @todo  Move to PHPUnit.
 *
 * @package  KYSS\Tests
 * @since  0.15.0
 */

namespace KYSS\Tests;

use \KYSS\Exceptions\KYSSException;
/**
 * ExceptionTraceTest class.
 *
 * @package  KYSS\Tests
 * @since  0.15.0
 * @version  1.0.0
 */
class KYSSExceptionTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test single exception.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @expectedException \KYSS\Exceptions\KYSSException
	 * @throws  \KYSS\Exception\KYSSException
	 */
	public function testSingleException() {
		throw new KYSSException( "Testing single exception" );
	}

	/**
	 * Test chain with two exceptions.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @dataProvider getExceptions
	 * @expectedException \KYSS\Exceptions\KYSSException
	 * @throws  \KYSS\Exceptions\KYSSException
	 */
	public function testDoubleExceptions( $e ) {
		throw new KYSSException( "Testing two chained exceptions", 0, $e );
	}

	/**
	 * Return KYSSException.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return  KYSSException
	 */
	public function getExceptions() {
		return array(
			array(
				new KYSSException
			)
		);
	}
}