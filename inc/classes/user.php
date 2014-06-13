<?php
/**
 * KYSS User API.
 *
 * @package  KYSS
 * @subpackage  API
 */

/**
 * KYSS User class.
 *
 * @since  0.7.0
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
	 * The user's roles.
	 *
	 * @since  0.7.0
	 * @access public
	 * @var  array
	 */
	public $roles = array();

	/**
	 * Constructor.
	 *
	 * Retrieves the userdata and passes it to {@link KYSS_User::init()}.
	 *
	 * @since  0.7.0
	 * @access public
	 *
	 * @param  int|string|stdClass|KYSS_User $id User's ID, a KYSS_User object, or a user
	 *                                           object from the DB.
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
	 * Add role to user.
	 *
	 * Updates the user's data option with roles.
	 *
	 * @todo  Write function code.
	 *
	 * @since  x.x.x
	 * @access public
	 *
	 * @param  string $role Role name.
	 */
	public function add_role( $role ) {

	}

	/**
	 * Remove role from user.
	 *
	 * @todo  Complete function code.
	 *
	 * @since  x.x.x
	 * @access public
	 *
	 * @param  string $role Role name.
	 */
	public function remove_role( $role ) {
		if ( ! in_array( $role, $this->roles ) )
			return;

	}

	/**
	 * Set the role of the user.
	 *
	 * This will remove the previous roles of the user and assign the user the
	 * new one. You can set the role to an empty string and it will remove all
	 * of the roles from the user.
	 *
	 * @todo  Complete function code.
	 *
	 * @since  x.x.x
	 * @access public
	 *
	 * @param  string $role Role name.
	 */
	public function set_role( $role ) {
		if ( 1 == count( $this->roles ) && $role == current( $this->roles ) )
			return;

		$old_roles = $this->roles;
		if ( ! empty( $role ) )
			$this->roles = array( $role => true );
		else
			$this->roles = false;

		/**
		 * Fires after the user's role has changed.
		 *
		 * @since  x.x.x
		 *
		 * @param  int $user_id The user ID.
		 * @param  string $role The new role.
		 * @param  array $old_roles An array of the user's previous roles.
		 */
		run_hook( 'set_user_role', $this->ID, $role, $old_roles );
	}

	/**
	 * Remove all user roles.
	 *
	 * @since 0.7.0
	 * @access public
	 * @see  KYSS_User::set_role()
	 */
	public function empty_roles() {
		$this->set_role( '' );
	}

	/**
	 * Whether user has role.
	 *
	 * @since  0.7.0
	 * @access public
	 *
	 * @param  string $role Role name.
	 * @return  bool
	 */
	public function has_role( $role ) {
		if ( in_array( $role, $this->roles ) )
			return true;
		return false;
	}
}