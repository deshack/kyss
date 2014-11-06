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

/**
 * Include the Autoloader class.
 */
// require_once dirname( dirname( __FILE__ ) ) . 'Bootstrap/Autoloader.php';

class MockAutoloader extends \KYSS\Bootstrap\Autoloader {
	protected $files = array();

	public function set_files( array $files ) {
		$this->files = $files;
	}

	protected function require_file( $file ) {
		return in_array( $file, $this->files );
	}
}

class AutoloaderTest extends \PHPUnit_Framework_TestCase {
	protected $loader;

	protected function setup() {
		$this->loader = new MockAutoloader;

		$this->loader->set_files( array(
			'/vendor/foo.bar/src/ClassName.php',
			'/vendor/foo.bar/src/DoomClassName.php',
			'/vendor/foo.bar/tests/ClassNameTest.php',
			'/vendor/foo.bardoom/src/ClassName.php',
			'/vendor/foo.bar.baz.dib/src/ClassName.php',
			'/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php'
		));

		$this->loader->add_namespace( 'Foo\Bar', '/vendor/foo.bar/src' );
		$this->loader->add_namespace( 'Foo\Bar', '/vendor/foo.bar/tests' );
		$this->loader->add_namespace( 'Foo\BarDoom', '/vendor/foo.bardoom/src' );
		$this->loader->add_namespace( 'Foo\Bar\Baz\Dib', '/vendor/foo.bar.baz.dib/src' );
		$this->loader->add_namespace( 'Foo\Bar\Baz\Dib\Zim\Gir', '/vendor/foo.bar.baz.dib.zim.gir/src' );
	}

	public function testExistingFile() {
		$actual = $this->loader->load_class( 'Foo\Bar\ClassName' );
		$expect = '/vendor/foo.bar/src/ClassName.php';
		$this->assertSame( $expect, $actual );

		$actual = $this->loader->load_class( 'Foo\Bar\ClassNameTest' );
		$expect = '/vendor/foo.bar/tests/ClassNameTest.php';
		$this->assertSame( $expect, $actual );
	}

	public function testMissingFile() {
		$actual = $this->loader->load_class('No_Vendor\No_Package\NoClass' );
		$this->assertFalse( $actual );
	}

	public function testDeepFile() {
		$actual = $this->loader->load_class( 'Foo\Bar\Baz\Dib\Zim\Gir\ClassName' );
		$expect = '/vendor/foo.bar.baz.dib.zim.gir/src/ClassName.php';
		$this->assertSame( $expect, $actual );
	}

	public function testConfusion() {
		$actual = $this->loader->load_class( 'Foo\Bar\DoomClassName' );
		$expect = '/vendor/foo.bar/src/DoomClassName.php';
		$this->assertSame( $expect, $actual );

		$actual = $this->loader->load_class( 'Foo\BarDoom\ClassName' );
		$expect = '/vendor/foo.bardoom/src/ClassName.php';
		$this->assertSame( $expect, $actual );
	}
}