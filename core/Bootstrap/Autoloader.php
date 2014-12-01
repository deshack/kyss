<?php
/**
 * Autoload classes from file paths.
 *
 * Follows the {@link(PSR-4, http://www.php-fig.org/psr/psr-4/)} standard.
 *
 * @package  KYSS\Bootstrap
 * @since  0.15.0
 */

namespace KYSS\Bootstrap;

/**
 * KYSS Autoloader class.
 *
 * Allows multiple base directories for a single namespace prefix.
 *
 * @internal Missing examples.
 *
 * @package KYSS\Bootstrap
 * @since  0.15.0
 * @version  1.0.0
 * @link(PSR-4 Class Example, https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md#class-example)
 */
class Autoloader {
	/**
	 * Associative array of prefixes.
	 * The key is a namespace prefix and the value is an array of
	 * base directories for classes in that namespace.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var array
	 */
	protected $prefixes = array();

	/**
	 * Register loader with SPL autoloader stack.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function register() {
		spl_autoload_register( array( $this, 'load_class' ) );
	}

	/**
	 * Add base directory for a namespace prefix.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $prefix The namespace prefix.
	 * @param  string $base_dir Base directory for class files in the namespace.
	 * @param  bool $prepend Optional. If true, prepend the base directory to
	 * the stack instead of appending it. This causes it to be searched first
	 * rather than last. Default false.
	 */
	public function add_namespace( $prefix, $base_dir, $prepend = false ) {
		// Normalize namespace prefix.
		$prefix = trim( $prefix, '\\' ) . '\\';

		// Normalize the base directory with a trailing separator.
		// We cannot use `trailingslash()` here because the functions' file has
		// not been loaded yet.
		$base_dir = rtrim( $base_dir, DIRECTORY_SEPARATOR ) . '/';

		// Initialize the namespace prefix array.
		if ( isset( $this->prefixes[$prefix] ) === false )
			$this->prefixes[$prefix] = array();

		// Retain the base directory for the namespace prefix.
		if ( $prepend )
			array_unshift( $this->prefixes[$prefix], $base_dir );
		else
			array_push( $this->prefixes[$prefix], $base_dir );
	}

	/**
	 * Load class file for given class name.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $class Fully-qualified class name.
	 * @return  mixed Mapped file name on success, or boolean false on failure.
	 */
	public function load_class( $class ) {
		// The current namespace prefix.
		$prefix = $class;

		// Work backwords through the namespace names of the fully-qualified
		// class name to find a mapped file name.
		while ( false !== $pos = strrpos( $prefix, '\\' ) ) {
			// Retain the trailing namespace separator in the prefix.
			$prefix = substr( $class, 0, $pos + 1 );

			// The rest is the relative class name.
			$relative_class = substr( $class, $pos + 1 );

			// Try to load a mapped file for the prefix and relative class.
			$mapped_file = $this->load_mapped_file( $prefix, $relative_class );
			if ( $mapped_file )
				return $mapped_file;

			// Remove the trailing namespace separator for the next iteration
			// of `strrpos()`.
			$prefix = rtrim( $prefix, '\\' );
		}

		// Mapped file not found.
		return false;
	}

	/**
	 * Load mapped file for namespace prefix and relative class.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  string $prefix Namespace prefix.
	 * @param  string $relative_class Relative class name.
	 * @return mixed Boolean false if no mapped file can be loaded, or the
	 * name of the mapped file that was loaded.
	 */
	protected function load_mapped_file( $prefix, $relative_class ) {
		// Are there any base directories for this namespace prefix?
		if ( isset( $this->prefixes[$prefix] ) === false )
			return false;

		// Look through base directories for this namespace prefix.
		foreach ( $this->prefixes[$prefix] as $base_dir ) {
			// Replace the namespace prefix with the base directory,
			// replace namespace separators with directory separators
			// in the relative class name, append with .php.
			$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

			// If the mapped file exists, require it.
			if ( $this->require_file( $file ) )
				return $file;
		}

		// Never found it.
		return false;
	}

	/**
	 * Require file if exists.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param  string $file File to require.
	 * @return  bool True if exists, false otherwise.
	 */
	protected function require_file( $file ) {
		if ( file_exists( $file ) ) {
			require $file;
			return true;
		}
		return false;
	}
}