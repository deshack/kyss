<?php
/**
 * KYSS Filesystem API.
 *
 * @package  KYSS
 * @subpackage  Filesystem
 * @since  0.14.0
 */

/**
 * The main filesystem class.
 *
 * @package  kYSS
 * @subpackage  Filesystem
 * @since 0.14.0
 * @version  1.0.1
 */
class Filesystem {
	/**
	 * Determine if a file exists.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path Path to check.
	 * @return  bool
	 */
	public function exists( $path ) {
		return file_exists( $path );
	}

	/**
	 * Get contents of a file.
	 *
	 * @since  1.0.0
	 * @access public
	 * 
	 * @param  string $path Path to the file.
	 * @return string
	 */
	public function get( $path ) {
		if ( $this->isFile( $path ) )
			return file_get_contents( $path );
		return new KYSS_Error( 'file_not_found', "Il file {$path} non esiste." );
	}

	/**
	 * Write contents of file.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path
	 * @param  string $contents
	 * @return int|false Number of bytes written. False on failure.
	 */
	public function put( $path, $contents ) {
		return file_put_contents( $path, $contents );
	}

	/**
	 * Prepend to file.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path
	 * @param  string $data
	 * @return int|false Number of bytes written. False on failure.
	 */
	public function prepend( $path, $data ) {
		if ( $this->exists( $path ) )
			return $this->put( $path, $data . $this->get( $path ) );
		else
			return $this->put( $path, $data );
	}

	/**
	 * Append to file.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $path
	 * @param string $data
	 * @return int Number of bytes written. False on failure.
	 */
	public function append( $path, $data ) {
		return file_put_contents( $path, $data, FILE_APPEND );
	}

	/**
	 * Delete file at given path.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string|array $paths
	 * @return bool Whether the operation succeeded or not.
	 */
	public function delete( $paths ) {
		$paths = is_array( $paths ) ? $paths : func_get_args();

		$success = true;

		foreach ( $paths as $path )
			if ( ! @unlink( $path ) )
				$success = false;

		return $success;
	}

	/**
	 * Remove directory at given path.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @param  string|array $paths
	 * @return  bool Whether the operation succeeded or not.
	 */
	public function delete_dir( $paths ) {
		$paths = is_array( $paths ) ? $paths : func_get_args();

		$dirs = array();
		$files = array();
		foreach ( $paths as $path ) {
			$it = new RecursiveDirectoryIterator( $path, RecursiveDirectoryIterator::SKIP_DOTS );
			$fit = new RecursiveIteratorIterator( $it, RecursiveIteratorIterator::CHILD_FIRST );
			foreach ( $fit as $f ) {
				if ( $f->isDir() )
					$dirs[] = $f->getRealPath();
				else
					$files[] = $f->getRealPath();
			}
			$dirs[] = $path;
		}

		if ( ! empty( $files ) )
			$this->delete( $files );
		
		// Assume that $dirs is not empty, because it has at least $paths.
		$this->_remove_dirs( $dirs );
	}

	/**
	 * Helper function to delete an array of directories.
	 *
	 * @since  0.14.0
	 * @access private
	 *
	 * @param  array $dirs
	 */
	private function _remove_dirs( array $dirs ) {
		foreach ( $dirs as $dir )
			rmdir( $dir );
	}

	/**
	 * Move file to new location.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path
	 * @param  string $target
	 * @return  bool
	 */
	public function move( $path, $target ) {
		return rename( $path, $target );
	}

	/**
	 * Copy file to new location.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path
	 * @param  string $target
	 * @return  bool
	 */
	public function copy( $path, $target ) {
		return copy( $path, $target );
	}

	/**
	 * Extract file extension from file path.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path
	 * @return  string
	 */
	public function extension( $path ) {
		return pathinfo( $path, PATHINFO_EXTENSION );
	}

	/**
	 * Get file type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path
	 * @return  string
	 */
	public function type( $path ) {
		return filetype( $path );
	}

	/**
	 * Get file size.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path
	 * @return  int
	 */
	public function size( $path ) {
		return filesize( $path );
	}

	/**
	 * Get the file's last modification time.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path
	 * @return  int
	 */
	public function last_modified( $path ) {
		return filemtime( $path );
	}

	/**
	 * Determine if the given path is a directory.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $directory
	 * @return  bool
	 */
	public function is_directory( $directory ) {
		return is_dir( $directory );
	}

	/**
	 * Determine if the given path is writable.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path
	 * @return  bool
	 */
	public function is_writable( $path ) {
		return is_writable( $path );
	}

	/**
	 * Determine if the given path is a file.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $file
	 * @return  bool
	 */
	public function is_file( $file ) {
		return is_file( $file );
	}

	/**
	 * Find path names matching a given pattern.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $pattern
	 * @param  int $flags Optional.
	 * @return  array
	 */
	public function glob( $pattern, $flags = 0 ) {
		return glob( $pattern, $flags );
	}

	/**
	 * Get list of files in a directory.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $directory
	 * @return  array
	 */
	public function files( $directory ) {
		$glob = glob( $directory . '/*' );

		if ( $glob === false )
			return array();

		// To get the appropriate files, we'll simply glob the directory and filter
		// out any "files" that are not truly files so we do not end up with any
		// directories in our list, but only true files within the directory.
		return array_filter( $glob, function( $file ) {
			return filetype( $file ) == 'file';
		});
	}

	/**
	 * Create directory.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path
	 * @param int $mode
	 * @param  bool $recursive
	 * @param  bool $force
	 * @return  bool
	 */
	public function make_directory( $path, $mode = 0755, $recursive = false, $force = false ) {
		if ( $force )
			return @mkdir( $path, $mode, $recursive );
		return mkdir( $path, $mode, $recursive );
	}
}