<?php
/**
 * KYSS User API.
 *
 * @package  KYSS
 * @subpackage User
 */

/**
 * KYSS User class.
 *
 * @since  0.7.0
 * @package  KYSS
 * @subpackage  User
 */
class KYSS_User {
	/**
	 * The user's group(s).
	 *
	 * @since  0.7.0
	 * @access private
	 * @var  array
	 */
	public $groups = array();

	/**
	 * The user's office.
	 *
	 * @since  0.8.0
	 * @access private
	 * @var  string
	 */
	public $carica;

	/**
	 * Constructor.
	 *
	 * Retrieves the userdata and passes it to {@link KYSS_User::init()}.
	 * If $id is an expression that evaluates to false, the user data will be retrieved
	 * by email.
	 *
	 * @since  0.7.0
	 * @access public
	 *
	 * @param  int|string|stdClass|KYSS_User $id User's ID, a KYSS_User object, or a user
	 * object from the DB.
	 * @param  string $email Optional. User's email.
	 */
	function __construct() {
		if ( isset( $this->gruppo ) )
			$this->groups = explode( ',', $this->gruppo );
	}

	/**
	 * Get user by id or email.
	 *
	 * @todo  Raise KYSS_Error on failure.
	 *
	 * @since  0.7.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param string $field The field to query against. Accepts <id>, <email>.
	 * @param  string|int $value The field value.
	 * @return  object|bool Raw user data. False on failure.
	 */
	public static function get_user_by( $field, $value ) {
		global $kyssdb;

		switch ( $field ) {
			case 'id':
				$db_field = 'ID';
				// Make sure the value is numeric to avoid casting objects,
				// for example to int 1.
				if ( ! is_numeric( $value ) )
					return false;
				$value = intval( $value );
				if ( $value < 1 )
					return false;
				break;
			case 'email':
				$db_field = 'email';
				$value = trim( $value );
				if ( ! $value )
					return false;
				break;
			default:
				return false;
		}

		if ( !$user = $kyssdb->query(
			"SELECT * FROM {$kyssdb->utenti} WHERE {$db_field} = {$value}"
		) )
			return false;

		if ( $user->num_rows == 0 ) {
			trigger_error( "Unknown user {$field}: {$value}", E_USER_WARNING );
			return false; // TODO: Return KYSS_Error instead
		} else {
			$user = $user->fetch_object( 'KYSS_User' );
		}

		return $user;
	}

	/**
	 * Retrieve users list.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 * 
	 * @return  array|false Array of user objects. False on failure.
	 */
	public static function get_users_list() {
		global $kyssdb;

		if ( !$user = $kyssdb->query(
			"SELECT * FROM {$kyssdb->utenti} ORDER BY cognome ASC"
		) )
			return false;

		$users = array();

		for ( $i = 0; $i < $user->num_rows; $i++ ) {
			array_push( $users, $user->fetch_object( 'KYSS_User' ) );
		}

		return $users;
	}

	/**
	 * Insert new user into the database.
	 *
	 * The `$data` array can contain the following fields:
	 * - 'email' - A string containing the user's email address.
	 * - 'telefono' - A string containing the user's phone number.
	 * - 'gruppo' - An array of strings containing the user's group slugs.
	 * - 'anagrafica' - An associative array of the user's anagraphic data.
	 * It can contain the following fields:
	 * - 'CF' - The user's tax code.
	 * - 'nato_a' - The user's birthplace.
	 * - 'nato_il' - The user's birthday.
	 * - 'cittadinanza' - The user's nationality.
	 * - 'residenza' => array(
	 * 	'via' => A string with the user's street,
	 * 	'city' => A string with the user's city,
	 * 	'provincia' => A two-letters string with the user's district,
	 * 	'CAP' => A five-digits string with the user's ZIP code );
	 *
	 * @since  0.9.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param string $name User's name.
	 * @param string $surname User's surname.
	 * @param string $pass User's password in plain text.
	 * @param array $data Optional. User's data. See above.
	 * @return int|KYSS_Error The newly created user's ID or a KYSS_Error object
	 * if the user could not be created.
	 */
	public static function create( $name, $surname, $pass, $data = array() ) {
		global $kyssdb;

		// Hash the password.
		if ( ! empty( $pass ) )
			$pass = KYSS_Pass::hash( $pass );

		// If email is given, check that it is unique.
		if ( isset( $data['email'] ) && self::email_exists( $data['email'] ) )
			return new KYSS_Error( 'existing_user_email', "Spiacenti, questo indirizzo email &egrave; gi&agrave; in uso." );

		$columns = array( 'nome', 'cognome' );
		$values = array( "'{$name}'", "'{$surname}'" );

		if ( ! empty( $pass ) && ! is_null( $pass ) ) {
			array_push( $columns, 'password' );
			array_push( $values, "'{$pass}'" );
		}
		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( $key == 'anagrafica' )
					$value = serialize( $value );
				if ( empty( $value ) )
					continue;
				else
					$value = "'{$value}'";
				array_push( $columns, $key );
				array_push( $values, $value );
			}
			
			$columns = join( ',', $columns );
			$values = join( ',', $values );
		}

		$query = "INSERT INTO {$kyssdb->utenti} ({$columns}) VALUES ({$values})";
		if ( !$result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}

		return $kyssdb->insert_id;
	}

	/**
	 * Update user in the db.
	 *
	 * The `$data` array can contain the following fields:
	 * - 'nome' - The user's first name.
	 * - 'cognome' - The user's surname.
	 * - 'password' - The user's password.
	 * - 'email' - A string containing the user's email address.
	 * - 'telefono' - A string containing the user's phone number.
	 * - 'gruppo' - An array of strings containing the user's group slugs.
	 * - 'anagrafica' - An associative array of the user's anagraphic data.
	 * It can contain the following fields:
	 * - 'CF' - The user's tax code.
	 * - 'nato_a' - The user's birthplace.
	 * - 'nato_il' - The user's birthday.
	 * - 'cittadinanza' - The user's nationality.
	 * - 'residenza' => array(
	 * 	'via' => A string with the user's street,
	 * 	'city' => A string with the user's city,
	 * 	'provincia' => A two-letters string with the user's district,
	 * 	'CAP' => A five-digits string with the user's ZIP code );
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  array $data User's data.
	 * @return  bool True if successful, false otherwise.
	 */
	public function update( $data ) {
		global $kyssdb;

		// Hash the password, if given.
		if ( isset( $data['password'] ) && ! empty( $data['password'] ) )
			$data['password'] = KYSS_Pass::hash( $data['password'] );

		// If email is given, check that it is unique.
		if ( isset( $data['email'] ) && self::email_exists( $data['email'] ) )
			return new KYSS_Error( 'existing_user_email', "Spiacenti, questo indirizzo email &egrave; gi&agrave; in uso." );

		// If $data is empty, return as unsuccessful.
		if ( empty( $data ) )
			return new KYSS_Error( 'invalid_data', 'I dati che hai inserito non sono validi.' );

		foreach ( $data as $key => $value ) {
			if ( $this->{$key} == $value )
				unset( $data[$key] );
		}

		// If $data is empty here, we were trying to update the user with
		// the same data stored in the db, so do nothing and return as successful.
		if ( empty( $data ) )
			return $this;

		if ( isset( $data['carica'] ) ) {
			if ( !isset( $this->carica ) || empty( $this->carica ) )
				$this->set_office( $data['carica'] );
			else
				$this->update_office( $data['carica'] );
			unset( $data['carica'] );
		}
		
		$result = $kyssdb->update( $kyssdb->utenti, $data, array( 'ID' => $this->ID ) );

		if ( ! $result )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );
		return $this;
	}

	/**
	 * Search user(s) in the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $query Query to search for.
	 * @return array|KYSS_User|KYSS_Error|false
	 */
	public static function search( $query = '' ) {
		global $kyssdb;

		if ( empty( $query ) )
			return self::get_users_list();

		$query = $kyssdb->real_escape_string( $query );

		$sql = "SELECT * FROM {$kyssdb->utenti} WHERE ";

		$search = array();
		$fields = array( 'nome', 'cognome', 'email', 'telefono', 'gruppo', 'citta' );
		foreach ( $fields as $field )
			$search[] = "`{$field}` LIKE '%{$query}%'";
		$search = join( ' OR ', $search );
		$sql .= $search;

		if ( ! $result = $kyssdb->query( $sql ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error, array( 'query' => $sql ) );

		if ( 0 === $result->num_rows )
			return false;

		$users = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$users[] = $result->fetch_object( 'KYSS_User' );

		return $users;
	}

	/**
	 * Check if the provided email already exists in the database. Use KYSS_Error object.
	 *
	 * @since  0.9.0
	 * @access private
	 * @static
	 *
	 * @param  string $email The email to check.
	 * @return bool True if email exists, false otherwise.
	 */
	private static function email_exists( $email ) {
		if ( false !== self::get_user_by( 'email', $email ) )
			return true;
		return false;
	}

	/**
	 * Determine whether the user exists in the database.
	 *
	 * @since  0.7.0
	 * @access public
	 *
	 * @return  bool True if user exists in the db, false if not.
	 */
	public function exists() {
		return ! empty( $this->ID );
	}

	/**
	 * Return an array representation of the user data.
	 *
	 * @since  0.7.0
	 * @access public
	 *
	 * @return  array Array representation of user data.
	 */
	public function to_array() {
		return get_object_vars( $this->data );
	}

	/**
	 * Add group to user.
	 *
	 * Updates the user's data option with groups.
	 *
	 * @todo  Write function code.
	 *
	 * @since  x.x.x
	 * @access public
	 *
	 * @param  string $group Group name.
	 */
	public function add_group( $group ) {

	}

	/**
	 * Remove group from user.
	 *
	 * @todo  Complete function code.
	 *
	 * @since  x.x.x
	 * @access public
	 *
	 * @param  string $group Group name.
	 */
	public function remove_group( $group ) {
		if ( ! in_array( $group, $this->groups ) )
			return;

	}

	/**
	 * Set the group of the user.
	 *
	 * This will remove the previous groups of the user and assign the user the
	 * new one. You can set the group to an empty string and it will remove all
	 * of the groups from the user.
	 *
	 * @todo  Complete function code.
	 *
	 * @since  x.x.x
	 * @access public
	 * 
	 * @global  hook
	 *
	 * @param  string $group Group name.
	 */
	public function set_group( $group ) {
		global $hook;

		if ( 1 == count( $this->groups ) && $group == current( $this->groups ) )
			return;

		$old_groups = $this->groups;
		if ( ! empty( $group ) )
			$this->groups = array( $group => true );
		else
			$this->groups = false;

		/**
		 * Fires after the user's group has changed.
		 *
		 * @since  0.9.0
		 *
		 * @param  int $user_id The user ID.
		 * @param  string $group The new group.
		 * @param  array $old_groups An array of the user's previous groups.
		 */
		$hook->run( 'set_user_group', $this->ID, $group, $old_groups );
	}

	/**
	 * Remove all user groups.
	 *
	 * @since 0.7.0
	 * @access public
	 * @see  KYSS_User::set_group()
	 */
	public function empty_groups() {
		$this->set_group( '' );
	}

	/**
	 * Whether user is in group.
	 *
	 * @since  0.7.0
	 * @access public
	 *
	 * @param  string $group Group name.
	 * @return  bool
	 */
	public function is_in_group( $group ) {
		if ( in_array( $group, $this->groups ) )
			return true;
		return false;
	}
}

/**
 * KYSS Office class.
 *
 * Handles all the operations that can be done with offices and office data.
 *
 * @since  0.12.0
 * @package KYSS
 * @subpackage  User
 */
class KYSS_Office {
	/**
	 * List of default offices.
	 *
	 * @since  0.12.0
	 * @access private
	 * @static
	 * @var array
	 */
	private static $defaults = array(
		'presidente',
		'vicepresidente',
		'segretario',
		'tesoriere',
		'consigliere'
	);

	/**
	 * Constructor.
	 *
	 * @since  0.12.0
	 * @access public
	 */
	public function __construct() {
		$this->get_user();
	}

	/**
	 * Convert $this->user from user ID to KYSS_User object.
	 *
	 * @since 0.13.0
	 * @access private
	 */
	private function get_user() {
		if ( isset( $this->utente ) && is_numeric( $this->utente ) )
			$this->utente = KYSS_User::get_user_by( 'id', $this->utente );
	}

	/**
	 * Retrieve a list of default offices.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 * 
	 * @return array List of default offices.
	 */
	public static function get_defaults() {
		return self::$defaults;
	}

	/**
	 * Insert new office in the db.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $office Office slug.
	 * @param  string $start Start date.
	 * @param  int $user User ID.
	 * @param  string $end Optional. End date. Default null.
	 * @return KYSS_Office|KYSS_Error KYSS_Office object.
	 */
	public static function create( $office, $start, $user, $end = null ) {
		global $kyssdb;

		if ( ! in_array( $office, self::$defaults ) )
			return new KYSS_Error( 'invalid_office', 'Nome della carica non valido.' );

		$columns = array( 'carica', 'inizio', 'utente' );
		$values = array( "'{$office}'", "'{$start}'", "{$user}" );
		if ( isset( $end ) ) {
			$columns[] = 'fine';
			$values[] = "'{$end}'";
		}

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->cariche} ({$columns}) VALUES ({$values})";
		if ( ! $id = $kyssdb->query( $query ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );
		return new self( array( 'carica' => $office, 'inizio' => $start, 'utente' => $user, 'fine' => $end ) );
	}

	/**
	 * Update office in the db.
	 *
	 * Allows you to edit start date, end date and user, but not office slug.
	 *
	 * @since  0.12.0
	 * @access public
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Data to update.
	 * @return KYSS_Office|KYSS_Error
	 */
	public function update( $data ) {
		global $kyssdb;

		$fields = array( 'inizio', 'utente', 'fine' );
		foreach ( $data as $key => $value ) {
			if ( ! in_array( $key, $fields ) || $value == $this->{$key} )
				unset( $data[$key] );
			if ( empty( $value ) )
				$data[$key] = 'NULL';
		}

		if ( empty( $data ) )
			return $this;

		if ( ! $kyssdb->update( $kyssdb->cariche, $data, array( 'carica' => $this->carica, 'inizio' => $this->inizio ) ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );

		// Here $data holds only the new values.
		foreach ( $data as $key => $value )
			$this->{$key} = $value;

		$this->get_user();

		return $this;
	}

	/**
	 * Retrieve office from the db.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param string $office Office slug.
	 * @param string $start Start date.
	 * @return KYSS_Office|KYSS_Error|false
	 */
	public static function get( $office, $start ) {
		global $kyssdb;

		$query = "SELECT * FROM {$kyssdb->cariche} WHERE `carica`='{$office}' AND `inizio`='{$start}'";

		if ( ! $office = $kyssdb->query( $query ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error, array( 'query' => $query ) );

		if ( $office->num_rows === 0 )
			return false;

		$office = $office->fetch_object( 'KYSS_Office' );

		return $office;
	}

	/**
	 * Retrieve list of offices from the db.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 */
	public static function get_list() {
		global $kyssdb;

		$query = "SELECT * FROM {$kyssdb->cariche} ORDER BY inizio DESC";

		if ( ! $result = $kyssdb->query( $query ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error, array( 'query', $query ) );

		$offices = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$offices[] = $result->fetch_object( 'KYSS_Office' );

		return $offices;
	}

	/**
	 * Retrieve list of current offices from the db.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @return  array Array of KYSS_Office objects.
	 */
	public static function get_current_list() {
		$offices = self::get_list();

		$current = array();
		foreach ( $offices as $office )
			if ( ! isset( $office->fine ) || strtotime( $office->fine ) > time() )
				$current[] = $office;

		return $current;
	}

	/**
	 * Retrieve list of past offices from the db.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @return  array Array of KYSS_Office objects.
	 */
	public static function get_past_list() {
		$offices = self::get_list();

		$past = array();
		foreach ( $offices as $office )
			if ( isset( $office->fine ) && strtotime( $office->fine ) < time() )
				$past[] = $office;

		return $past;
	}

	/**
	 * Search offices in the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $query Search query.
	 * @return  array
	 */
	public static function search( $query = '' ) {
		global $kyssdb;

		if ( empty( $query ) )
			return self::get_list();

		$query = $kyssdb->real_escape_string( $query );

		$fields = array( 'c.carica', 'u.nome', 'u.cognome' );
		$search = array();
		foreach ( $fields as $field )
			$search[] = "CONVERT({$field} USING utf8) LIKE '%{$query}%'";
		$search = join( ' OR ', $search );

		$sql = "SELECT * FROM {$kyssdb->cariche} c
			LEFT JOIN {$kyssdb->utenti} u ON c.utente = u.ID
			WHERE {$search}
			ORDER BY c.inizio DESC";

		if ( ! $result = $kyssdb->query( $sql ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );

		if ( 0 === $result->num_rows )
			return false;

		$offices = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$offices[] = $result->fetch_object( 'KYSS_Office' );
		return $offices;
	}
}

/**
 * KYSS Groups collection class.
 *
 * This is a mostly static method used to handle KYSS_Group objects.
 *
 * @since  0.8.0
 * @package KYSS
 * @subpackage  User
 */
class KYSS_Groups {
	/**
	 * List of group objects.
	 *
	 * @since 0.8.0
	 * @access private
	 * @static
	 * @var  array
	 */
	private static $groups;

	/**
	 * List of default groups.
	 *
	 * @since  0.9.0
	 * @access private
	 * @static
	 * @var  array
	 */
	private static $defaults = array(
		'collaboratori' => array(
			'name' => 'Collaboratori',
			'permissions' => array() ),
		'ordinari' => array(
			'name' => 'Soci Ordinari',
			'permissions' => array() ),
		'fondatori' => array(
			'name' => 'Soci Fondatori',
			'permissions' => array() ),
		'benemeriti' => array(
			'name' => 'Soci Benemeriti',
			'permissions' => array() ),
		'cd' => array(
			'name' => 'Consiglio Direttivo',
			'permissions' => array() ),
		'rc' => array(
			'name' => 'Revisori dei Conti',
			'permissions' => array() ),
		'admin' => array(
			'name' => 'Amministratori',
			'permissions' => array() )
	);

	/**
	 * List of valid permissions.
	 *
	 * @since  0.9.0
	 * @access private
	 * @static
	 * @var  array
	 */
	private static $permissions = array();

	/**
	 * Instantiate default groups.
	 *
	 * Triggers E_USER_NOTICE if called more than once.
	 *
	 * @since  0.9.0
	 * @access public
	 * @static
	 */
	public static function populate_defaults() {
		if ( isset( self::$groups ) )
			trigger_error( 'You already instantiated default KYSS_Groups' );

		foreach ( self::$defaults as $slug => $data ) {
			self::$groups[$slug] = new KYSS_Group( $data['name'], $data['permissions'] );
		}
	}

	/**
	 * Retrieve group object by slug.
	 *
	 * @since  0.9.0
	 * @access public
	 * @static
	 *
	 * @param  string $slug Slug of the group to retrieve.
	 * @return KYSS_Group|null Group object. Null if not found.
	 */
	public static function get_group( $slug ) {
		if ( isset( self::$groups[$slug] ) )
			return self::$groups[$slug];
	}

	/**
	 * Retrieve all group slugs.
	 *
	 * @since  0.9.0
	 * @access public
	 * @static
	 *
	 * @return  array List of all group slugs.
	 */
	public static function get_slugs() {
		return array_keys( self::$groups );
	}

	/**
	 * Retrieve list of default groups.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @return  array Associative array of group slugs and names.
	 */
	public static function get_defaults() {
		$list = array();
		foreach ( self::$defaults as $slug => $atts )
			$list[$slug] = $atts['name'];

		return $list;
	}

	/**
	 * Check if group exists.
	 *
	 * @since  0.9.0
	 * @access public
	 * @static
	 *
	 * @param  string $slug Slug of the group to check.
	 * @return bool True if exists, false otherwise.
	 */
	public static function group_exists( $slug ) {
		return isset( $this->groups[$slug] );
	}

	/**
	 * Check if given object is a KYSS_Group.
	 *
	 * @since  0.9.0
	 * @access public
	 * @static
	 *
	 * @param  object|string $obj Object to check or slug.
	 * @return bool True if given object is a KYSS_Group, false otherwise.
	 */
	public static function is_group( $obj ) {
		if ( is_object( $obj ) && is_a( $obj, 'KYSS_Group' ) )
			return true;
		if ( is_string( $obj ) && self::group_exists( $obj ) )
			return true;
		return false;
	}

	/**
	 * Check if given permission exists.
	 *
	 * @since  0.9.0
	 * @access public
	 * @static
	 *
	 * @param  string $perm Permission to check.
	 * @return bool Whether the given string is a valid permission.
	 */
	public static function permission_exists( $perm ) {
		return in_array( $perm, self::$permissions );
	}

	/**
	 * Get list of valid permissions.
	 *
	 * @since  0.9.0
	 * @access public
	 * @static
	 *
	 * @return  array List of valid group permissions.
	 */
	public static function get_permissions() {
		return self::$permissions;
	}
}

/**
 * KYSS Group class.
 *
 * @since  0.8.0
 * @package  KYSS
 * @subpackage  User
 */
class KYSS_Group {
	/**
	 * Group name.
	 *
	 * @since  0.8.0
	 * @access public
	 * @var  string
	 */
	public $name;

	/**
	 * List of permissions the group has.
	 *
	 * @since  0.8.0
	 * @access public
	 * @var  array
	 */
	public $permissions;

	/**
	 * Constructor - Set up object properties.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $group Group name.
	 * @param  array $perms List of permissions.
	 */
	function __construct( $name, $perms ) {
		$this->name = $name;
		$this->permissions = $perms;
	}

	/**
	 * Assign group a permission.
	 *
	 * Triggers E_USER_ERROR if `$perm` is not a valid permission.
	 *
	 * @since 0.8.0
	 * @access public
	 *
	 * @param  string $perm Permission name.
	 */
	public function add_permission( $perm ) {
		if ( ! KYSS_Groups::permission_exists( $perm ) ) {
			trigger_error( sprintf("%s is not a valid permission", $perm), E_USER_ERROR );
			return;
		}

		array_push( $this->permissions, $perm );
	}

	/**
	 * Remove permission from group.
	 *
	 * This method will not do nothing if the group was not granted the permission
	 * before.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $perm Permission name.
	 */
	public function remove_permission( $perm ) {
		if ( ($key = array_search( $perm, $this->permissions ) ) !== false )
			unset( $this->permissions[$key] );
	}

	/**
	 * Whether group has permission.
	 *
	 * The permission is passed through the 'group_has_permission' hook.
	 * The first parameter for the hook is the list of permissions the group
	 * has granted. The second parameter is the permission name to look.
	 * The third and final parameter for the hook is the group slug.
	 *
	 * @since  0.8.0
	 * @access public
	 * @global  hook
	 *
	 * @param  string $perm Permission name.
	 * @return  bool True if group has permission, false otherwise.
	 */
	public function has_permission( $perm ) {
		global $hook;

		/**
		 * Filter which permissions a group has.
		 *
		 * @since  0.8.0
		 *
		 * @param  array $permissions Array of group permissions.
		 * @param  string $perm Permission name.
		 * @param  string $name Group slug.
		 */
		$permissions = $hook->run( 'group_has_permission', $this->permissions, $perm, array_search( $this, KYSS_Groups::$groups ) );

		if ( false !== array_search( $perm, $this->permissions ) )
			return true;
		else
			return false;
	}
}