<?php
/**
 * KYSS Roles API
 *
 * @package KYSS
 * @subpackage User
 */

/**
 * KYSS Roles class.
 *
 * @since  0.8.0
 * @package  KYSS
 * @subpackage  User
 */
class KYSS_Roles {
	/**
	 * List of roles.
	 *
	 * @since  0.8.0
	 * @access private
	 * @var  array
	 */
	private $roles;

	/**
	 * List of the role objects.
	 *
	 * @since  0.8.0
	 * @access private
	 * @var  array
	 */
	private $role_obj = array();

	/**
	 * List of role names.
	 *
	 * @since  0.8.0
	 * @access private
	 * @var  array
	 */
	private $role_names = array();

	/**
	 * Constructor.
	 *
	 * Calls the KYSS_Roles::_init() method.
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
	 * @global array user_roles Used to set the `roles` property value.
	 *
	 * @return  null
	 */
	protected function _init() {
		global $user_roles;

		if ( ! empty( $user_roles ) )
			$this->roles = $user_roles;

		if ( empty( $this->roles ) )
			return;

		$this->role_obj = array();
		$this->role_names = array();
		foreach ( array_keys( $this->roles ) as $role ) {
			$this->role_obj[$role] = new KYSS_Role( $role, $this->roles[$role]['permissions'] );
			$this->role_names[$role] = $this->roles[$role]['name'];
		}
	}

	/**
	 * Add role name with permissions to list.
	 *
	 * Updates the list of roles, if the role doesn't already exist.
	 *
	 * The permissions are defined in the following format `array( 'read' => true );`
	 * To explicitly deny a role a permission, set the value for that permission to false.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param string $role Role name.
	 * @param string $display_name Role display name.
	 * @param array $permissions List of role permissions in the above format.
	 * @return  KYSS_Role|null KYSS_Role object if role is added, null if already exists.
	 */
	public function add_role( $role, $display_name, $permissions = array() ) {
		if ( isset( $this->roles[$role] ) )
			return;

		$this->roles[$role] = array(
			'name' => $display_name,
			'permissions' => $permissions
		);
		$this->role_obj[$role] = new KYSS_Role( $role, $permissions );
		$this->role_names[$rolw] = $display_name;
		return $this->role_obj[$role];
	}

	/**
	 * Remove role by name.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param string $role Role name.
	 */
	public function remove_role( $role ) {
		if ( ! isset( $this->role_obj[$role] ) )
			return;

		unset( $this->role_obj[$role] );
		unset( $this->role_names[$role] );
		unset( $this->roles[$role] );
	}

	/**
	 * Add permission to role.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $role Role name.
	 * @param  string $permission Permission name.
	 * @param  bool $grant Optional. Whether role is capable of performing
	 * capability. Default <true>.
	 * @return  null
	 */
	public function add_permission( $role, $permission, $grant = true ) {
		if ( ! isset( $this->roles[$role] ) )
			return;

		$this->roles[$role]['permissions'][$permission] = $grant;
	}

	/**
	 * Remove permission from role.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $role Role name.
	 * @param  string $permission Permission name.
	 * @return  null
	 */
	public function remove_permission( $role, $permission ) {
		if ( ! isset( $this->roles[$role] ) )
			return;

		unset( $this->roles[$role]['permissions'][$permission] );
	}

	/**
	 * Retrieve role object by name.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $role Role name.
	 * @return  KYSS_Role|null KYSS_Role object if found, null if the role
	 * does not exist.
	 */
	public function get_role( $role ) {
		if ( isset( $this->role_obj[$role] ) )
			return $this->role_obj[$role];
		else
			return null;
	}

	/**
	 * Retrieve list of role names.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @return  array List of role names.
	 */
	public function get_names() {
		return $this->role_names;
	}

	/**
	 * Whether role name is currently in the list of available roles.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $role Role name to look up.
	 * @return  bool
	 */
	public function is_role( $role ) {
		return isset( $this->role_names[$role] );
	}
}

/**
 * KYSS Role class.
 *
 * @since  0.8.0
 * @package  KYSS
 * @subpackage  User
 */
class KYSS_Role {
	/**
	 * Role name.
	 *
	 * @since  0.8.0
	 * @access public
	 * @var  string
	 */
	public $name;

	/**
	 * List of permissions the lore contains.
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
	 * and the value a boolean of whether it is granted to the role.
	 *
	 * @since  0.8.0
	 * @access public
	 *
	 * @param  string $role Role name.
	 * @param  array $perms List of permissions.
	 */
	function __construct( $role, $perms ) {
		$this->name = $role;
		$this->permissions = $perms;
	}

	/**
	 * Assign role a permission.
	 *
	 * @since 0.8.0
	 * @access public
	 * @see  KYSS_Roles::add_permission() Method uses implementation for role.
	 * @global  user_roles
	 *
	 * @param  string $perm Permission name.
	 * @param  bool $grant Whether role has permission privilege.
	 */
	public function add_permission( $perm, $grant = true ) {
		global $user_roles;

		if ( ! isset( $user_roles ) )
			$user_roles = new KYSS_Roles;

		$this->permissions[$perm] = $grant;
		$user_roles->add_permission( $this->name, $perm, $grant );
	}

	/**
	 * Remove permission from role.
	 *
	 * This is a container for {@link KYSS_Roles::remove_permission()} to remove
	 * the permission from the role. That is to say that {@link
	 * KYSS_Roles::remove_permission()} implements the functionality, but it also
	 * makes sense to use this class, because you don't need to enter the role name.
	 *
	 * @since  0.8.0
	 * @access public
	 * @global  user_roles
	 *
	 * @param  string $perm Permission name.
	 */
	public function remove_permission( $perm ) {
		global $user_roles;

		if ( ! isset( $user_roles ) )
			$user_roles = new KYSS_Roles;

		unset( $this->permissions[$perm] );
		$user_roles->remove_permission( $this->name, $perm );
	}

	/**
	 * Whether role has permission.
	 *
	 * The permission is passed through the 'role_has_permission' hook.
	 * The first parameter for the hook is the list of permissions the class
	 * has assigned. The second parameter is the capability name to look.
	 * The third and final parameter for the hook is the role name.
	 *
	 * @since  0.8.0
	 * @access public
	 * @uses  run_hook()
	 *
	 * @param  string $perm Permission name.
	 * @return  bool True if role has permission, false otherwise.
	 */
	public function has_permission( $perm ) {
		/**
		 * Filter which permissions a role has.
		 *
		 * @since  0.8.0
		 *
		 * @param  array $permissions Array of role permissions.
		 * @param  string $perm Permission name.
		 * @param  string $name Role name.
		 */
		$permissions = run_hook( 'role_has_permission', $this->permissions, $perm, $this->name );

		if ( !empty( $permissions[$perm] ) )
			return $permissions[$perm];
		else
			return false;
	}
}