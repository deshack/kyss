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
	 * @param  string $name Optional. User's name.
	 * @return KYSS_User
	 */
	function __construct( $id = 0, $name = '' ) {
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
			$data = self::get_data_by( 'login', $name );

		if ( $data )
			$this->init( $data );
	}
}