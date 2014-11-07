<?php
/**
 * Unit test for Autoloader class.
 *
 * Make sure your current working directory is core/ and run this test with:
 * `phpunit --bootstrap Bootstrap/Autoloader.php Tests/AutoloaderTest`
 *
 * @package  KYSS
 * @subpackage  Tests
 * @since  0.15.0
 */

namespace KYSS\Tests;

class MockAutoloader extends \KYSS\Bootstrap\Autoloader {
	protected $files = array();

	public function set_files( array $files ) {
		$this->files = $files;
	}

	protected function require_file( $file ) {
		return in_array( $file, $this->files );
	}
}

/**
 * Test for Autoloader class.
 *
 * @package  KYSS
 * @subpackage  Tests
 * @since  0.15.0
 * @version  1.0.0
 */
class AutoloaderTest extends \PHPUnit_Framework_TestCase {
	protected $loader;
	protected $true_loader;
	private static $abspath;

	/**
	 * Setup the test.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function setUp() {
		$this->loader = new MockAutoloader;
		$this->true_loader = new \KYSS\Bootstrap\Autoloader;

		self::$abspath = dirname( dirname( __FILE__ ) );

		$this->loader->set_files( array(
			'/vendor/foo/src/ClassName.php',
			'/vendor/foo.bar/src/ClassName.php',
			'/vendor/foo.bar/src/DoomClassName.php',
			'/vendor/foo.bar/tests/ClassNameTest.php',
			'/vendor/foo.bardoom/src/ClassName.php',
			'/vendor/foo.bar.baz.dib/src/ClassName.php',
			'/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php'
		));

		$this->loader->add_namespace( 'Foo', '/vendor/foo/src' );
		$this->loader->add_namespace( 'Foo\Bar', '/vendor/foo.bar/src' );
		$this->loader->add_namespace( 'Foo\Bar', '/vendor/foo.bar/tests' );
		$this->loader->add_namespace( 'Foo\BarDoom', '/vendor/foo.bardoom/src' );
		$this->loader->add_namespace( 'Foo\Bar\Baz\Dib', '/vendor/foo.bar.baz.dib/src' );
		$this->loader->add_namespace( 'Foo\Bar\Baz\Dib\Zim\Gir', '/vendor/foo.bar.baz.dib.zim.gir/src' );
		$this->true_loader->add_namespace( 'KYSS', self::$abspath );
	}

	/**
	 * Test single namespace.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function testSingleNamespace() {
		$actual = $this->loader->load_class( 'Foo\ClassName' );
		$expect = '/vendor/foo/src/ClassName.php';
		$this->assertSame( $expect, $actual );
	}

	/**
	 * Test existing file.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function testExistingFile() {
		$actual = $this->loader->load_class( 'Foo\Bar\ClassName' );
		$expect = '/vendor/foo.bar/src/ClassName.php';
		$this->assertSame( $expect, $actual );

		$actual = $this->loader->load_class( 'Foo\Bar\ClassNameTest' );
		$expect = '/vendor/foo.bar/tests/ClassNameTest.php';
		$this->assertSame( $expect, $actual );
	}

	/**
	 * Test missing file.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function testMissingFile() {
		$actual = $this->loader->load_class('No_Vendor\No_Package\NoClass' );
		$this->assertFalse( $actual );
	}

	/**
	 * Test deep file.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function testDeepFile() {
		$actual = $this->loader->load_class( 'Foo\Bar\Baz\Dib\Zim\Gir\ClassName' );
		$expect = '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php';
		$this->assertSame( $expect, $actual );
	}

	/**
	 * Test confusion.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function testConfusion() {
		$actual = $this->loader->load_class( 'Foo\Bar\DoomClassName' );
		$expect = '/vendor/foo.bar/src/DoomClassName.php';
		$this->assertSame( $expect, $actual );

		$actual = $this->loader->load_class( 'Foo\BarDoom\ClassName' );
		$expect = '/vendor/foo.bardoom/src/ClassName.php';
		$this->assertSame( $expect, $actual );
	}

	/**
	 * Test real Autoloader.
	 *
	 * Previous test methods tested a mock autoloader. Here we test the real
	 * Autoloader, trying to load the KYSSException class.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function testRealAutoloader() {
		$actual = $this->true_loader->load_class( 'KYSS\Exceptions\KYSSException' );
		$expect = self::$abspath . '/Exceptions/KYSSException.php';
		$this->assertFileExists( $expect );
		$this->assertSame( $expect, $actual );
	}
}