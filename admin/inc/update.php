<?php
/**
 * KYSS Update library.
 *
 * @package  KYSS
 * @subpackage  Update
 * @since  0.14.0
 */

/**
 * KYSS Update class.
 *
 * @package  KYSS
 * @subpackage  Update
 * @version  1.0.0
 * @since  0.14.0
 */
class Update {
	/**
	 * Whether we are in a test environment or not.
	 *
	 * @since  0.14.0
	 * @access private
	 * @var bool
	 */
	private $test = true;

	/**
	 * Whether logging is enabled or not.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var bool
	 */
	private $log;

	/**
	 * Log file path.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string
	 */
	protected $log_file = ABSPATH . 'log/update.log';

	/**
	 * The last error as a KYSS_Error object.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var KYSS_Error
	 */
	private $error;

	/**
	 * Latest version number.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string
	 */
	protected $latest;

	/**
	 * Latest old version number.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string
	 */
	protected $latest_old;

	/**
	 * Latest version url.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string
	 */
	protected $latest_url;

	/**
	 * Latest old version URL.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string
	 */
	protected $latest_old_url;

	/**
	 * Whether there are new updates.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var bool
	 */
	private $has_updates = false;

	/**
	 * Version manifest filename on the server.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string
	 */
	protected $manifest = 'update.ini';

	/**
	 * URI to check for updates.
	 *
	 * @todo  Define UPDATE_URI;
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string
	 */
	protected $url = UPDATE_URI;

	/**
	 * Temporary download directory path.
	 *
	 * @todo  Define TEMP_DIR.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var string
	 */
	protected $temp = TEMP_DIR;

	/**
	 * Permissions for new folders.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var int
	 */
	protected $permissions = 0755;

	/**
	 * Constructor.
	 *
	 * Sets up logging.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  bool $log Optional. Whether to activate logging or not. Default false.
	 */
	public function __construct( $log = false ) {
		$this->log = $log;
	}

	/**
	 * Log a message if logging is enabled.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param  string|KYSS_Error $message Message to log.
	 * @return  bool|KYSS_Error
	 */
	private function log( $message ) {
		if ( ! $this->log )
			return false;

		$this->error = $message;
		$log = fopen( $this->log_file, 'a' );

		if ( ! $log )
			return new KYSS_Error( 'log_open_failed', 'Impossibile scrivere il file di log.' );

		if ( is_kyss_error( $message ) )
			$message = $message->get_error_message();
		$message = date( '[Y-m-d H:i:s]' ) . $message . "\n";
		fputs( $log, $message );
		fclose( $log );
		return true;
	}

	/**
	 * Retrieve last error.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return  null|KYSS_Error
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Check if we found available updates.
	 *
	 * @since  0.14.0
	 * @access public
	 *
	 * @return  bool
	 */
	public function has_updates() {
		return $this->has_updates;
	}

	/**
	 * Retrieve updates version.
	 *
	 * @since  0.14.0
	 * @access public
	 *
	 * @return  string
	 */
	public function get_updates() {
		$output = '';
		if ( ! is_null( $this->latest_old ) )
			$output .= "Old series: " . $this->latest_old . "\n";
		if ( ! is_null( $this->latest ) )
			$output .= "Current series: " . $this->latest;
		return $output;
	}

	/**
	 * Check for updates.
	 *
	 * Looks at the `Update::$manifest` ini file in the repository, which has to be
	 * formatted as follows:
	 *
	 * ```
	 * [stable]
	 * current=M.m.p
	 * current_url=http://example.com/path/package-M.m.p.zip
	 * old=M.m.p
	 * old_url=http://example.com/path/package-M.m.p.zip
	 *
	 * [beta]
	 * current=M.m.p
	 * current_url=[snip]
	 * old=M.m.p
	 * old_url=[snip]
	 *
	 * [dev]
	 * current=M.m.p
	 * current_url=[snip]
	 * old=M.m.p
	 * old_url=[snip]
	 * ```
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @global  version
	 *
	 * @return  bool Whether or not an update is available.
	 */
	public function check() {
		global $kyss_version;

		$this->log( 'Checking for a new update...' );

		$update_file = trailingslashit( $this->url ) . $this->manifest;

		$update = @file_get_contents( $update_file );

		if ( $update === false ) {
			// Could not read $update_file.
			$error = new KYSS_Error( 'manifest_open_failed', 'Impossibile aprire il file `' . $update_file . '`.' );
			$this->log( $error );
			return $error;
		}

		$versions = parse_ini_string( $update, true );
		if ( is_array( $versions ) ) {
			$update = false;
			switch ( ENVIRONMENT ) {
				case 'development':
					$branch = 'dev';
					break;
				case 'test':
					$branch = 'beta';
					break;
				case 'production':
				default:
					$branch = 'stable';
					break;
			}
			$versions = $versions[$branch];
			if ( isset( $versions['old'] ) && version_compare( $versions['old'], $kyss_version, '>' ) ) {
				$this->latest_old = $versions['old'];
				$this->latest_old_url = $versions['old_url'];
			}
			if ( isset( $versions['current'] ) && version_compare( $versions['current'], $kyss_version, '>' ) ) {
				$this->latest = $versions['current'];
				$this->latest_url = $versions['current_url'];
			}

			if ( ! is_null( $this->latest_old ) ) {
				$this->log( 'Nuova old release trovata: ' . $this->latest_old );
				$update = true;
			}
			if ( ! is_null( $this->latest ) ) {
				$this->log( 'Nuova release trovata: ' . $this->latest );
				$update = true;
			}

			$this->has_updates = $update;
			return $update;
		} else {
			$error = new KYSS_Error( 'manifest_parse_failed', 'Impossibile leggere il file `' . $update_file . '`.' );
			$this->log( $error );
			return $error;
		}
	}

	/**
	 * Download the update.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return  string|KYSS_Error Path to the update package.
	 */
	public function download( $url ) {
		$this->log( 'Downloading update...' );
		$package = @file_get_contents( $url );

		if ( $package === false ) {
			$error = new KYSS_Error( 'download_failed', "Impossibile scaricare l'aggiornamento `$url`." );
			$this->log( $error );
			return $error;
		}

		$path = $this->temp . basename( $this->url );
		$handle = fopen( $path, 'w' );
		if ( ! $handle ) {
			$error = new KYSS_Error( 'file_save_failed', "Impossibile salvare l'aggiornamento `$path`." );
			$this->log( $error );
			return $error;
		}

		if ( ! fwrite( $handle, $package ) ) {
			$error = new KYSS_Error( 'file_write_failed', "Impossibile scrivere il file di aggiornamento `$path`." );
			$this->log( $error );
			return $error;
		}

		fclose( $handle );
		return $path;
	}

	/**
	 * Install the update.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param  string $path Path to the update package.
	 * @return  bool|KYSS_Error
	 */
	public function install( $path ) {
		$zip = zip_open( $path );

		while ( $file = zip_read( $zip ) ) {
			$filename = zip_entry_name( $file );
			$dirname = $this->temp . dirname( $filename );

			$this->log( 'Updating `' . $filename . '`.' );

			if ( ! is_dir( $dirname ) )
				if ( ! mkdir( $dirname, $this->permissions, true ) )
					$this->log( "Impossibile creare la directory `$dirname`." );

			$contents = zip_entry_read( $file, zip_entry_filesize( $file ) );

			// Skip if entry is a directory.
			if ( substr( $filename, -1, 1 ) == '/' )
				continue;

			// Write to file.
			if ( ! is_writable( $this->temp . $filename ) ) {
				$error = new KYSS_Error( 'not_writeable', 'Impossibile creare il file ' . $this->temp . $filename . ', percorso non scrivibile.' );
				$this->log( $error );
				return $error;
			}

			$handle = @fopen( $this->temp . $filename, 'w' );

			if ( ! $handle ) {
				$error = new KYSS_Error( 'fopen_failed', 'Impossibile aggiornare il file ' . $this->temp . $filename );
				$this->log( $error );
				return $error;
			}

			if ( ! fwrite( $handle, $contents ) ) {
				$error = new KYSS_Error( 'write_failed', 'Impossibile salvare il file ' . $this->temp . $filename );
				$this->log( $error );
				return $error;
			}

			fclose( $handle );
		}

		zip_close( $zip );

		if ( ! $this->test ) {
			$this->remove_dir( $this->temp );
			$this->log( 'Directory temporanea rimossa.' );
		}

		$this->log( 'Aggiornamento installato.' );

		return true;
	}
}