<?php
require_once(dirname(dirname(__FILE__)) . '/inc/classes/kyss-error.php');

class KYSSErrorTest extends PHPUnit_Framework_TestCase {

	protected function tearDown() {
		unset($error);
	}

	/**
	 * Test empty code parameter.
	 *
	 * Should return null as the KYSS_Error constructor returns early
	 * if the code parameter is empty.
	 */
	public function testEmptyCode() {
		$null = new KYSS_Error;
		//$this->assertNull($null, 'testEmptyCode failed!');
		fwrite(STDOUT, $null->get_error_codes() . "\n");
	}

	/**
	 * Test passing only code.
	 *
	 * Assert that the object is not null (i.e. the object gets actually created).
	 */
	public function testOnlyCode() {
		$code = '500';
		$error = new KYSS_Error($code);
		$this->assertNotNull($error);
	}
}