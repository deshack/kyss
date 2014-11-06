<?php
/**
 * Tests for Exception's trace.
 *
 * @todo  Move to PHPUnit.
 *
 * @package  KYSS
 * @subpackage  Tests
 * @since  0.15.0
 */

namespace KYSS\Tests;

define( 'CORE', dirname(dirname(__FILE__) ) );

require CORE . '/lib/functions.php';
require CORE . '/Exceptions/KYSSException.php';

use \KYSS\Exceptions\KYSSException;

/**
 * ExceptionTraceTest class.
 *
 * @package  KYSS
 * @subpackage  Tests
 * @since  0.15.0
 * @version  1.0.0
 */
class ExceptionTraceTest {
	/**
	 * Test single exception.
	 *
	 * @since  1.0.0
	 *
	 * @throws  KYSSException
	 */
	public function singleException() {
		throw new KYSSException( "Testing single exception" );
	}

	/**
	 * Test chain with two exceptions.
	 *
	 * @since  1.0.0
	 *
	 * @throws  KYSSException
	 */
	public function twoExceptions() {
		try {
			$this->singleException();
		} catch ( KYSSException $e ) {
			throw new KYSSException( "Testing two chained exceptions", 0, $e );
		}
	}
}

function singleTest() {
	$test = new \KYSS\Tests\ExceptionTraceTest;
	$test->singleException();
}

function doubleTest() {
	$test = new \KYSS\Tests\ExceptionTraceTest;
	$test->twoExceptions();
}

try {
	doubleTest();
} catch ( \KYSS\Exceptions\KYSSException $ex ) {
	$ex->kill();
}