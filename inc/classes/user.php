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
	 * User data container.
	 *
	 * @since  0.7.0
	 * @access private
	 * @var  array
	 */
	private $data;

	/**
	 * The user's ID.
	 *
	 * @since  0.7.0
	 * @access public
	 * @var  int
	 */
	public $ID = 0;

	/**
	 * The user's group(s).
	 *
	 * @since  0.7.0
	 * @access private
	 * @var  array
	 */
	private $groups = array();

	/**
	 * The user's office.
	 *
	 * @since  0.8.0
	 * @access private
	 * @var  string
	 */
	private $carica = '';

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
	 * @return KYSS_User
	 */
	function __construct( $id = 0, $email = '' ) {
		if ( is_a( $id, 'KYSS_User' ) ) {
			$this->init( $id->data );
			return;
		} elseif ( is_object( $id ) ) {
			$this->init( $id );
			return;
		}

		if ( ! empty( $id ) && ! is_numeric( $id ) ) {
			$name = $id;
			$id = 0;
		}

		if ( $id )
			$data = self::get_user_by( 'id', $id );
		else
			$data = self::get_user_by( 'email', $email );

		if ( $data )
			$this->init( $data );
	}

	/**
	 * Set up object properties.
	 *
	 * @since  0.7.0
	 * @access private
	 *
	 * @param  object $data User DB row object.
	 */
	private function init( $data ) {
		$data = $data->fetch_array( MYSQLI_ASSOC );
		$this->data = $data;
		$this->ID = (int) $data['ID'];
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

		return $user;
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
		$pass = KYSS_Pass::hash( $pass );

		// If email is given, check that it is unique.
		if ( isset( $data['email'] ) && self::email_exists( $data['email'] ) )
			return new KYSS_Error( 'existing_user_email', "Mi dispiace, questa email &egrave; gi&agrave; in uso!" );

		$columns = array( 'nome', 'cognome', 'password' );
		$values = array( "'{$name}'", "'{$surname}'", "'{$pass}'" );
		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				switch( $key ) {
					case 'email':
						$columns[] = $key;
						$values[] = "'{$value}'";
						break;
					case 'telefono':
						$columns[] = $key;
						$values[] = "'{$value}'";
						break;
					case 'gruppo':
						$columns[] = $key;
						$values[] = "'{$value}'";
						break;
					case 'anagrafica':
						$columns[] = $key;
						$values[] = serialize($value);
						break;
				}
			}
			$columns = join( ',', $columns );
			$values = join( ',', $values );
			$result = $kyssdb->query( "INSERT INTO {$kyssdb->utenti} ({$columns}) VALUES ({$values})" );
		}

		if ( $result )
			return $kyssdb->insert_id;
		else
			trigger_error( $kyssdb->error, E_USER_WARNING ); // TODO: Return KYSS_Error instead.
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

	/**
	 * Set the user role.
	 *
	 * @todo  Write method KYSS_User::set_role()
	 *
	 * @since  x.x.x
	 * @access public
	 *
	 * @param  string $slug The role slug.
	 * @return  null
	 */
	public function set_role( $slug ) {
		$this->role = $slug;
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
		if ( isset( $this->groups[$slug] ) )
			return $this->groups[$slug];
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