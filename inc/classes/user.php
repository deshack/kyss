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
			$data = self::get_data_by( 'id', $id );
		else
			$data = self::get_data_by( 'email', $email );

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
		$this->data = $data;
		$this->ID = (int) $data->ID;
	}

	/**
	 * Get only main user fields.
	 *
	 * @todo  Raise KYSS_Error on failure.
	 *
	 * @since  0.7.0
	 * @access public
	 * @static
	 *
	 * @param string $field The field to query against. Accepts <id>, <email>.
	 * @param  string|int $value The field value.
	 * @return  object|bool Raw user object. False on failure.
	 */
	public static function get_data_by( $field, $value ) {
		global $kyssdb;

		if ( 'id' == $field ) {
			// Make sure the value is numeric to avoid casting objects,
			// for example to int 1.
			if ( ! is_numeric( $value ) )
				return false;
			$value = intval( $value );
			if ( $value < 1 )
				return false;
		} else {
			$value = trim( $value );
		}

		if ( ! $value )
			return false;

		switch ( $field ) {
			case 'id':
				$user_id = $value;
				$db_field = 'ID';
				break;
			case 'email':
				$user_id = get_id_by( 'email', $value );
				$db_field = 'user_email';
				break;
			default:
				return false;
		}

		if ( false !== $user_id ) {
			if ( $user = get_user( $user_id ) )
				return $user;
		}

		if ( !$user = $kyssdb->get_row( $kyssdb->prepare(
			"SELECT * FROM $kyssdb->users WHERE $db_field = %s", $value
		) ) )
			return false;

		return $user;
	}

	/**
	 * Magic method for checking the existence of a certain custom field.
	 *
	 * @since  0.7.0
	 * @access public
	 *
	 * @param  string $key Field to check.
	 * @return  bool True if exists, false otherwise.
	 */
	function __isset( $key ) {
		if ( isset( $this->data->$key ) )
			return true;
		return false;
	}
	
	/**
	 * Magic method for accessing custom fields.
	 *
	 * @since  0.7.0
	 * @access public
	 *
	 * @param  string $key The field to access.
	 * @return  mixed|bool Field value. False on failure.
	 */
	function __get( $key ) {
		if ( isset( $this->data->$key ) )
			$value = $this->data->$key;
		if ( !isset( $value ) )
			return false;
		return $value;
	}

	/**
	 * Magic method for setting custom fields.
	 *
	 * @since  0.7.0
	 * @access public
	 *
	 * @param  string $key The field to set.
	 * @param  mixed $value The value to set.
	 * @return  null.
	 */
	function __set( $key, $value ) {
		$this->data->$key = $value;
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
	 * @param  string $group Group name.
	 */
	public function set_group( $group ) {
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
		 * @since  x.x.x
		 *
		 * @param  int $user_id The user ID.
		 * @param  string $group The new group.
		 * @param  array $old_groups An array of the user's previous groups.
		 */
		run_hook( 'set_user_group', $this->ID, $group, $old_groups );
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
 * KYSS Groups class.
 *
 * @since  0.8.0
 * @package  KYSS
 * @subpackage  User
 */
class KYSS_Groups {
	/**
	 * List of groups.
	 *
	 * @since  0.8.0
	 * @access private
	 * @var  array
	 */
	private $groups;

	/**
	 * List of the group objects.
	 *
	 * @since  0.8.0
	 * @access private
	 * @var  array
	 */
	private $group_obj = array();

	/**
	 * List of group names.
	 *
	 * @since  0.8.0
	 * @access private
	 * @var  array
	 */
	private $group_names = array();

	/**
	 * Constructor.
	 *
	 * Calls the KYSS_Groups::_init() method.
	 *
	 * @since  0.8.0
	 * @access public
	 */
	function __construct() {
		$this->_init();
	}

	/**
	 * Set up the object properties.
	 *
	 * @since  0.8.0
	 * @access protected
	 * @global array user_groups Used to set the 'groups' property value.
	 *
	 * @return  null
	 */
	protected function _init() {
		global $user_groups;

		if ( ! empty( $user_groups ) )
			$this->groups = $user_groups;

		if ( empty( $this->groups ) )
			return;

		$this->group_obj = array();
		$this->group_names = array();
		foreach ( array_keys( $this->groups ) as $group ) {
			$this->group_obj[$group] = new KYSS_Group( $group, $this->groups[$groups]['permissions'] );
			$this->group_names[$group] = $this->groups[$group]['name'];
		}
	}

	/**
	 * Add group name with permissions to list.
	 *
	 * Updates the list of groups, if the group doesn't already exist.
	 *
	 * The permissions are defined in the following format `array( 'read' => true );`
	 * To explicitly deny a group a permission, set the value for that permission to false.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param string $group Group name.
	 * @param string $display_name Group display name.
	 * @param array $permissions List of group permissions in the above format.
	 * @return  KYSS_Group|null KYSS_Group object if group is added, null if already exists.
	 */
	public function add_group( $group, $display_name, $permissions = array() ) {
		if ( isset( $this->groups[$group] ) )
			return;

		$this->groups[$group] = array(
			'name' => $display_name,
			'permissions' => $permissions
		);
		$this->group_obj[$group] = new KYSS_Group( $group, $permissions );
		$this->group_names[$group] = $display_name;
		return $this->group_obj[$group];
	}

	/**
	 * Remove group by name.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param string $group Group name.
	 */
	public function remove_group( $group ) {
		if ( ! isset( $this->group_obj[$group] ) )
			return;

		unset( $this->group_obj[$group] );
		unset( $this->group_names[$group] );
		unset( $this->groups[$group] );
	}

	/**
	 * Add permission to group.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $group Group name.
	 * @param  string $permission Permission name.
	 * @param  bool $grant Optional. Whether group is capable of performing
	 * capability. Default <true>.
	 * @return  null
	 */
	public function add_permission( $group, $permission, $grant = true ) {
		if ( ! isset( $this->groups[$group] ) )
			return;

		$this->groups[$group]['permissions'][$permission] = $grant;
	}

	/**
	 * Remove permission from group.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $group Group name.
	 * @param  string $permission Permission name.
	 * @return  null
	 */
	public function remove_permission( $group, $permission ) {
		if ( ! isset( $this->groups[$group] ) )
			return;

		unset( $this->groups[$group]['permissions'][$permission] );
	}

	/**
	 * Retrieve group object by name.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $group Group name.
	 * @return  KYSS_Group|null KYSS_Group object if found, null if the group
	 * does not exist.
	 */
	public function get_group( $group ) {
		if ( isset( $this->group_obj[$group] ) )
			return $this->group_obj[$group];
		else
			return null;
	}

	/**
	 * Retrieve list of group names.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @return  array List of group names.
	 */
	public function get_names() {
		return $this->group_names;
	}

	/**
	 * Whether group name is currently in the list of available groups.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $group Group name to look up.
	 * @return  bool
	 */
	public function is_group( $group ) {
		return isset( $this->group_names[$group] );
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
	 * List of permissions the group contains.
	 *
	 * @since  0.8.0
	 * @access public
	 * @var  array
	 */
	public $permissions;

	/**
	 * Constructor - Set up object properties.
	 *
	 * The list of permission must have the key as the name of the permission
	 * and the value a boolean of whether it is granted to the group.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $group Group name.
	 * @param  array $perms List of permissions.
	 */
	function __construct( $group, $perms ) {
		$this->name = $group;
		$this->permissions = $perms;
	}

	/**
	 * Assign group a permission.
	 *
	 * @since 0.8.0
	 * @access public
	 * @see  KYSS_Groups::add_permission() Method uses implementation for group.
	 * @global  user_groups
	 *
	 * @param  string $perm Permission name.
	 * @param  bool $grant Whether group has permission privilege.
	 */
	public function add_permission( $perm, $grant = true ) {
		global $user_groups;

		if ( ! isset( $user_groups ) )
			$user_groups = new KYSS_Groups;

		$this->permissions[$perm] = $grant;
		$user_groups->add_permission( $this->name, $perm, $grant );
	}

	/**
	 * Remove permission from group.
	 *
	 * This is a container for {@link KYSSGroups::remove_permission()} to remove
	 * the permission from the group. That is to say that {@link
	 * KYSS_Groups::remove_permission()} implements the functionality, but it also
	 * makes sense to use this class, because you don't need to enter the group name.
	 *
	 * @since  0.8.0
	 * @access public
	 * @global  user_groups
	 *
	 * @param  string $perm Permission name.
	 */
	public function remove_permission( $perm ) {
		global $user_groups;

		if ( ! isset( $user_groups ) )
			$user_groups = new KYSS_Groups;

		unset( $this->permissions[$perm] );
		$user_groups->remove_permission( $this->name, $perm );
	}

	/**
	 * Whether group has permission.
	 *
	 * The permission is passed through the 'group_has_permission' hook.
	 * The first parameter for the hook is the list of permissions the class
	 * has assigned. The second parameter is the capability name to look.
	 * The third and final parameter for the hook is the group name.
	 *
	 * @since  0.8.0
	 * @access public
	 * @uses  run_hook()
	 *
	 * @param  string $perm Permission name.
	 * @return  bool True if group has permission, false otherwise.
	 */
	public function has_permission( $perm ) {
		/**
		 * Filter which permissions a group has.
		 *
		 * @since  0.8.0
		 *
		 * @param  array $permissions Array of group permissions.
		 * @param  string $perm Permission name.
		 * @param  string $name Group name.
		 */
		$permissions = run_hook( 'group_has_permission', $this->permissions, $perm, $this->name );

		if ( !empty( $permissions[$perm] ) )
			return $permissions[$perm];
		else
			return false;
	}
}