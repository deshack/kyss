<?php
/**
 * KYSS Event API
 *
 * @package  KYSS
 * @subpackage  Events
 */

/**
 * KYSS Event class
 *
 * @since
 * @package  KYSS
 * @subpackage  Events
 */
class KYSS_Event {
	/**
	 * Retrieve event by ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $field The field to query against. Accepts <id>.
	 * @param  int $value The field value.
	 * @return  object|bool KYSS_Event object. False on failure.
	 */
	public static function get_event_by( $field, $value ) {
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
			default:
				return false;
		}

		if ( ! $event = $kyssdb->query(
			"SELECT * 
			FROM {$kyssdb->eventi} 
			WHERE {$db_field} = '{$value}'"
		) )
			return false;

		if ( $event->num_rows == 0 )
			return new KYSS_Error( 'event_not_found', 'Event not found.', array( $field => $value ) );

		$event = $event->fetch_object( 'KYSS_Event' );

		return $event;
	}

	/**
	 * Retrieve events list.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  bool $all Optional. If true returns all events; else returns all events 
	 * except meetings and courses. Default false.
	 * @return array|false Array of KYSS_Event objects. False on failure.
	 */
	public static function get_events_list( $all = false ) {
		global $kyssdb;

		$query = "SELECT {$kyssdb->eventi}.* FROM {$kyssdb->eventi}";
		
		if ( ! $all ) 
			$query .= " WHERE {$kyssdb->eventi}.ID NOT IN (
						SELECT {$kyssdb->riunioni}.ID FROM {$kyssdb->riunioni}
						UNION 
						SELECT {$kyssdb->corsi}.ID FROM {$kyssdb->corsi})";

		if ( ! $event = $kyssdb->query( $query ) )
			return false;

		$events = array();

		for ( $i = 0; $i < $event->num_rows; $i++ )
			array_push( $events, $event->fetch_object( 'KYSS_Event' ) );

		return $events;
	}

	/**
	 * Insert new event into the database.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Associative array of column names and values.
	 * @return int|bool The newly created event's ID or false on failure.
	 */
	public static function create( $data ) {
		global $kyssdb;

		if ( empty( $data ) )
			return new KYSS_Error( 'empty_meeting_data', 'Meeting data cannot be empty!' );

		if ( ! isset( $data['data_inizio'] ) )
			$data['data_inizio'] = date( 'Y-m-d' );

		$columns = array();
		$values = array();

		foreach ( $data as $key => $value ) {
			array_push( $columns, $key );
			array_push( $values, "'{$value}'" );
		}

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->eventi} ({$columns}) VALUES ({$values})";
		if ( ! $result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}

		return $kyssdb->insert_id;
	}

	/**
	 * Update event in the db.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  array $data Event's data.
	 * @return bool Whether the update succeeded or not.
	 */
	public function update( $data ) {
		global $kyssdb;

		if ( empty( $data) )
			return new KYSS_Error( 'invalid_data', 'I dati che hai inserito non sono validi.' );

		$result = $kyssdb->update( $kyssdb->eventi, $data, array( 'ID' => $this->ID ) );

		if ( ! $result )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );
		return $this;
	}

	/**
	 * Search event in the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $query Search query.
	 * @return array Array of KYSS_Event objects.
	 */
	public static function search( $query = '' ) {
		global $kyssdb;

		if ( empty( $query ) )
			return self::get_events_list();

		$query = $kyssdb->real_escape_string( $query );

		$sql = "SELECT e.* FROM {$kyssdb->eventi} e WHERE e.nome LIKE '%{$query}%'
			AND e.ID NOT IN (
				SELECT r.ID FROM {$kyssdb->riunioni} r
				UNION
				SELECT c.ID FROM {$kyssdb->corsi} c
			)";

		if ( ! $result = $kyssdb->query( $sql ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );

		if ( $result->num_rows === 0 )
			return false;

		$events = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$events[] = $result->fetch_object( 'KYSS_Event' );
		return $events;
	}

	/**
	 * Retrieve upcoming events from the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @return  array.
	 */
	public static function get_upcoming() {
		global $kyssdb;

		$query = "SELECT e.* FROM {$kyssdb->eventi} e
			WHERE (e.ID NOT IN (
				SELECT r.ID FROM {$kyssdb->riunioni} r
				UNION
				SELECT c.ID FROM {$kyssdb->corsi} c
				) AND (
					MONTH(e.data_inizio) >= MONTH(CURRENT_DATE())
					AND
					YEAR(e.data_inizio) = YEAR(CURRENT_DATE())
					AND
					MONTH(e.data_inizio) < MONTH(DATE_ADD(
						CURRENT_DATE(), INTERVAL 1 MONTH
					)) AND
					YEAR(e.data_inizio) <= YEAR(DATE_ADD(
						CURRENT_DATE(), INTERVAL 1 MONTH
					))
				) OR (
					e.data_inizio < CURRENT_DATE()
					AND
					e.data_fine > CURRENT_DATE()
			))";

		if ( ! $result = $kyssdb->query( $query ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error, array( 'query' => $query ) );

		if ( 0 === $result->num_rows )
			return false;

		$events = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$events[] = $result->fetch_object( 'KYSS_Event' );
		return $events;
	}
}

/**
 * KYSS Meeting class
 *
 * @since 0.11.0
 * @package  KYSS
 * @subpackage  Events 
 */
class KYSS_Meeting extends KYSS_Event {
	/**
	 * Holds the list of KYSS_Event column names.
	 *
	 * @since  0.12.0
	 * @access private
	 * @static
	 * @var  array
	 */
	private static $event_data = array(
		'nome',
		'data_inizio',
		'data_fine',
		'luogo'
	);

	/**
	 * Retrieve meeting by ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  int $id The meeting ID.
	 * @return  KYSS_Meeting|bool KYSS_Meeting object. False on failure.
	 */
	public static function get_meeting_by_id( $id ) {
		global $kyssdb;

		if ( ! is_numeric( $id ) )
			return false;
		$id = intval( $id );

		if ( $id < 1 )
			return false;

		if ( ! $meeting = $kyssdb->query(
			"SELECT * 
			FROM {$kyssdb->riunioni} INNER JOIN {$kyssdb->eventi} 
				ON {$kyssdb->riunioni}.ID = {$kyssdb->eventi}.ID
			WHERE {$kyssdb->eventi}.ID = {$id}"
		) )
			return false;

		if ( $meeting->num_rows == 0 )
			return new KYSS_Error( 'meeting_not_found', 'Meeting not found', array( 'ID' => $id ) );

		$meeting = $meeting->fetch_object( 'KYSS_Meeting' );

		return $meeting;
	}
	
	/**
	 * Retrieve meetings list.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  bool $event Optional. If true returns all the fields of the event meeting; 
	 * else only the fields of Meetings. Default true.
	 * @return array|false Array of KYSS_Meeting objects. False on failure.
	 */
	public static function get_list( $event = true ) {
		global $kyssdb;

		$query = "SELECT * FROM {$kyssdb->riunioni}";

		if ( $event )
			$query .= " JOIN {$kyssdb->eventi} ON ({$kyssdb->riunioni}.ID = {$kyssdb->eventi}.ID)";
		if ( ! $meeting = $kyssdb->query( $query ) )
			return false;
		$meetings = array();

		for ( $i = 0; $i < $meeting->num_rows; $i++ )
			array_push( $meetings, $meeting->fetch_object( 'KYSS_Meeting' ) );

		return $meetings;
	}

	/**
	 * Insert new meeting into the database.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Meeting data.
	 * @return  int|bool|KYSS_Error
	 */
	public static function create( $data ) {
		global $kyssdb;

		// Put all $data items representing $kyssdb->eventi columns
		// into the $event array.
		$event = array();
		foreach ( array_keys( $data ) as $key ) {
			if ( in_array( $key, self::$event_data ) ) {
				$event[$key] = $data[$key];
				unset( $data[$key] );
			}
		}

		$data['ID'] = parent::create( $event );

		$columns = array();
		$values = array();

		foreach ( $data as $key => $value ) {
			array_push( $columns, $key );
			if ( is_int( $value ) || $value === 'NULL' )
				array_push( $values, $value );
			else
				array_push( $values, "'{$value}'" );
		}

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->riunioni} ({$columns}) VALUES ({$values})";
		if ( ! $result = $kyssdb->query( $query ) ) {
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );
		}

		return $data['ID'];
	}

	/**
	 * Update meeting in the db.
	 *
	 * @since  0.11.0
	 * @access public
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Meeting data.
	 * @return  bool Whether the update succeeded or not.
	 */
	public function update( $data ) {
		global $kyssdb;

		if ( empty( $data ) )
			return new KYSS_Error( 'invalid_data', 'I dati che hai inserito non sono validi.' );

		// Put all $data items representing $kyssdb->eventi columns
		// into the $event array.
		$event = array();
		foreach ( array_keys( $data ) as $key ) {
			if ( in_array( $key, self::$event_data ) ) {
				$event[$key] = $data[$key];
				unset( $data[$key] );
			}
		}

		parent::update( $event );

		$result = $kyssdb->update( $kyssdb->riunioni, $data, array( 'ID' => $this->ID ) );

		if ( ! $result )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );
		return $this;
	}

	/**
	 * Search meeting in the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $query Search query.
	 * @return  array Array of KYSS_Meeting objects.
	 */
	public static function search( $query = '' ) {
		global $kyssdb;

		if ( empty( $query ) )
			return self::get_list();

		$query = $kyssdb->real_escape_string( $query );

		$sql = "SELECT * FROM {$kyssdb->riunioni} r
			JOIN {$kyssdb->eventi} e ON (r.ID = e.ID)
			WHERE e.nome LIKE '%{$query}%'";

		if ( ! $result = $kyssdb->query( $sql ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );

		if ( $result->num_rows === 0 )
			return false;

		$meetings = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$meetings[] = $result->fetch_object( 'KYSS_Meeting' );
		return $meetings;
	}

	/**
	 * Retrieve upcoming meetings from the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 * @global  current_user
	 *
	 * @return  array.
	 */
	public static function get_upcoming() {
		global $kyssdb, $current_user;

		if ( $current_user->gruppo == 'collaboratori' )
			return false;
		elseif ( $current_user->is_in_group('cd') )
			$all_meetings = true;
		else
			$all_meetings = false; // Can view only AdA meetings.

		$type = ($all_meetings ? '' : "(r.tipo='AdA') AND ");

		$query = "SELECT * FROM {$kyssdb->riunioni} r
			NATURAL JOIN {$kyssdb->eventi} e
			WHERE {$type}(
				MONTH(e.data_inizio) >= MONTH(CURRENT_DATE())
				AND
				YEAR(e.data_inizio) = YEAR(CURRENT_DATE())
				AND
				MONTH(e.data_inizio) < MONTH(DATE_ADD(
					CURRENT_DATE(), INTERVAL 1 MONTH
				)) AND
				YEAR(e.data_inizio) <= YEAR(DATE_ADD(
					CURRENT_DATE(), INTERVAL 1 MONTH
				))
			)";

		if ( ! $result = $kyssdb->query( $query ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error, array( 'query' => $query ) );

		if ( 0 === $result->num_rows )
			return false;

		$meetings = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$meetings[] = $result->fetch_object( 'KYSS_Meeting' );
		return $meetings;
	}
}

/**
 * KYSS Course class
 *
 * @since 0.11.0
 * @package  KYSS
 * @subpackage Events
 */
class KYSS_Course extends KYSS_Event {
	/**
	 * Holds the list of KYSS_Event column names.
	 *
	 * @since  0.12.0
	 * @access private
	 * @static
	 * @var  array
	 */
	private static $event_data = array(
		'nome',
		'data_inizio',
		'data_fine',
		'luogo'
	);
	
	/**
	 * Retrieve course by ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  int $id The course ID.
	 * @return  KYSS_Course|bool KYSS_Course object. False on failure.
	 */
	public static function get_course_by_id( $id ) {
		global $kyssdb;

		if ( ! is_numeric( $id ) )
			return false;
		$id = intval( $id );

		if ( $id < 1 )
			return false;

		if ( ! $course = $kyssdb->query(
			"SELECT * 
			FROM {$kyssdb->corsi} INNER JOIN {$kyssdb->eventi}
				ON {$kyssdb->corsi}.ID = {$kyssdb->eventi}.ID
			WHERE {$kyssdb->eventi}.ID = {$id}"
		) )
			return false;

		if ( $course->num_rows == 0 )
			return new KYSS_Error( 'course_not_found', 'Course not found.', array( 'ID' => $id ) );

		$course = $course->fetch_object( 'KYSS_Course' );

		return $course;
	}
	
	/**
	 * Retrieve course list.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  bool $event Optional. If true returns the fields of the event course; 
	 * else only the fields of Courses. Default true.
	 * @return array|false Array of KYSS_Course object. False on failure.
	 */
	public static function get_list( $event = true ) {
		global $kyssdb;

		$query = "SELECT * FROM {$kyssdb->corsi}";

		if ( $event )
			$query .= " JOIN {$kyssdb->eventi} ON ({$kyssdb->corsi}.ID = {$kyssdb->eventi}.ID)";
		if ( ! $course = $kyssdb->query( $query ) )
			return false;
		$courses = array();

		for ( $i = 0; $i < $course->num_rows; $i++ )
			array_push( $courses, $course->fetch_object( 'KYSS_Course' ) );

		return $courses;
	}

	/**
	 * Insert new course into the database.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Course data.
	 * @return int|bool|KYSS_Error
	 */
	public static function create( $data ) {
		global $kyssdb;

		// Put all $data items representing $kyssdb->eventi columns
		// into the $event array.
		$event = array();
		foreach ( array_keys( $data ) as $key ) {
			if ( in_array( $key, self::$event_data ) ) {
				$event[$key] = $data[$key];
				unset( $data[$key] );
			}
		}

		$data['ID'] = parent::create( $event );

		$columns = array();
		$values = array();

		foreach ( $data as $key => $value ) {
			array_push( $columns, $key );
			if ( is_int( $value ) )
				array_push( $values, $value );
			else
				array_push( $values, "'{$value}'" );
		}

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->corsi} ({$columns}) VALUES ({$values})";
		if ( !$result = $kyssdb->query( $query ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );

		return $data['ID'];
	}

	/**
	 * Update course in the db.
	 *
	 * @since  0.11.0
	 * @access public
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Course data.
	 * @return bool Whether the update succeeded or not.
	 */
	public function update( $data ) {
		global $kyssdb;

		if ( empty( $data ) )
			return new KYSS_Error( 'invalid_data', 'I dati che hai inserito non sono validi.' );

		// Put all $data items representing $kyssdb->eventi columns
		// into the $event array.
		$event = array();
		foreach ( array_keys( $data ) as $key ) {
			if ( in_array( $key, self::$event_data ) ) {
				$event[$key] = $data[$key];
				unset( $data[$key] );
			}
		}

		parent::update( $event );
		$result = $kyssdb->update( $kyssdb->corsi, $data, array( 'ID' => $this->ID ) );

		if ( ! $result )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );
		return $this;
	}

	/**
	 * Search course in the db.
	 *
	 * @since 0.13.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  string $query Search query.
	 * @return  array Array of KYSS_Course objects.
	 */
	public static function search( $query = '' ) {
		global $kyssdb;

		if ( empty( $query ) )
			return self::get_list();

		$query = $kyssdb->real_escape_string( $query );

		$sql = "SELECT * FROM {$kyssdb->corsi} c
			JOIN {$kyssdb->eventi} e ON (c.ID = e.ID)
			WHERE e.nome LIKE '%{$query}%' OR c.livello='{$query}'";

		if ( ! $result = $kyssdb->query( $sql ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );

		if ( $result->num_rows === 0 )
			return false;

		$courses = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$courses[] = $result->fetch_object( 'KYSS_Course' );
		return $courses;
	}

	/**
	 * Retrieve upcoming courses from the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @return  array.
	 */
	public static function get_upcoming() {
		global $kyssdb;

		$query = "SELECT * FROM {$kyssdb->corsi} c
			NATURAL JOIN {$kyssdb->eventi} e
			WHERE (
				MONTH(e.data_inizio) >= MONTH(CURRENT_DATE())
				AND
				YEAR(e.data_inizio) = YEAR(CURRENT_DATE())
				AND
				MONTH(e.data_inizio) < MONTH(DATE_ADD(
					CURRENT_DATE(), INTERVAL 1 MONTH
				)) AND
				YEAR(e.data_inizio) <= YEAR(DATE_ADD(
					CURRENT_DATE(), INTERVAL 1 MONTH
				))
			) OR (
				e.data_inizio < CURRENT_DATE()
				AND
				e.data_fine > CURRENT_DATE()
			)";

		if ( ! $result = $kyssdb->query( $query ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error, array( 'query' => $query ) );

		if ( 0 === $result->num_rows )
			return false;

		$courses = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$courses[] = $result->fetch_object( 'KYSS_Course' );
		return $courses;
	}
}

/**
 * KYSS Talk class
 *
 * @since 0.11.0
 * @package  KYSS
 * @subpackage  Events
 */
class KYSS_Talk {
	/**
	 * Retrieve talk by ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  int $id The talk id.
	 * @return KYSS_Talk|bool KYSS_Course object. False on failure.
	 */
	public static function get_talk_by_id( $id ) {
		global $kyssdb;

		if ( ! is_numeric( $id ) )
			return false;
		$id = intval( $id );

		if ( $id < 1 )
			return false;

		if ( ! $talk = $kyssdb->query(
			"SELECT * 
			FROM {$kyssdb->talk} 
			WHERE ID = {$id}"
		) )
			return false;

		if ( $talk->num_rows == 0 )
			return new KYSS_Error( 'talk_not_found', 'Talk not found.', array( 'ID' => $id ) );

		$talk = $talk->fetch_object( 'KYSS_Talk' );

		return $talk;
	}

	/**
	 * Retrieve talks list.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @param  int $event_id The event ID.
	 * @global kyssdb
	 *
	 * @param  int $event_id Optional. Event ID.
	 * @param  string $order Optional. Results order. Accepts 'ASC', 'DESC'. Default 'DESC'.
	 * @return array|false Array of KYSS_Talk objects. False on failure.
	 */
	public static function get_list( $event_id = 0, $order = 'DESC' ) {
		global $kyssdb;

		$query = "SELECT * FROM {$kyssdb->talk}";

		if ( $event_id )
			$query .= " WHERE `evento`={$event_id}";

		$query .= " ORDER BY `data` {$order}";
		
		if ( ! $talk = $kyssdb->query( $query ) )
			return false;

		$talks = array();

		for ( $i = 0; $i < $talk->num_rows; $i++ )
			array_push( $talks, $talk->fetch_object( 'KYSS_Talk' ) );

		return $talks;
	}

	/**
	 * Insert new talk into the database.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Talk data.
	 * @return int|bool|KYSS_Error
	 */
	public static function create( $data ) {
		global $kyssdb;

		if ( ! isset( $data['titolo'] ) )
			return new KYSS_Error( 'talk_title_missing', 'Per creare un nuovo talk &egrave; necessario un titolo.' );

		$columns = array();
		$values = array();

		foreach ( $data as $key => $value ) {
			array_push( $columns, $key );
			if ( is_int( $value ) )
				array_push( $values, $value );
			else
				array_push( $values, "'{$value}'" );
		}

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->talk} ({$columns}) VALUES ({$values})";
		if ( ! $result = $kyssdb->query( $query ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );

		return $kyssdb->insert_id;
	}

	/**
	 * Update talk in the db.
	 *
	 * @since  0.11.0
	 * @access public
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Talk data.
	 * @return  bool Whether the update succeeded or not.
	 */
	public function update( $data ) {
		global $kyssdb;

		if ( empty( $data) )
			return false;

		$result = $kyssdb->update( $kyssdb->talk, $data, array( 'ID' => $this->ID ) );

		if ( ! $result )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );

		return $this;
	}

	/**
	 * Search talks in the db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global kyssdb
	 *
	 * @param  string $query Search query.
	 * @return array
	 */
	public static function search( $query = '' ) {
		global $kyssdb;

		if ( empty( $query ) )
			return self::get_list();

		$query = $kyssdb->real_escape_string( $query );

		$sql = "SELECT * FROM {$kyssdb->talk} WHERE ";
		$fields = array( 'titolo', 'argomenti' );
		$search = array();
		foreach ( $fields as $field )
			$search[] = "`{$field}` LIKE '%{$query}%'";
		$search = join( ' OR ', $search );
		$sql .= $search;

		if ( ! $result = $kyssdb->query( $sql ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );

		if ( 0 === $result->num_rows )
			return false;

		$talks = array();
		for ( $i = 0; $i < $result->num_rows; $i++ )
			$talks[] = $result->fetch_object( 'KYSS_Talk' );
		return $talks;
	}
}


/**
 * KYSS Lesson class
 *
 * @since 0.11.0
 * @package  KYSS
 * @subpackage  Events
 */
class KYSS_Lesson {
	/**
	 * Retrieve lessons list.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @param  int $course The course ID.
	 * @global kyssdb
	 *
	 * @return array|false Array of KYSS_Lesson objects. False on failure.
	 */
	public static function get_list( $course = '' ) {
		global $kyssdb;

		$query = "SELECT * FROM {$kyssdb->lezioni}";

		if ( is_numeric( $course ) )
			$query .= " WHERE corso = {$course}";

		if ( ! $lesson = $kyssdb->query( $query ) )
			return false;

		$lessons = array();

		for ( $i = 0; $i < $lesson->num_rows; $i++ )
			array_push( $lessons, $lesson->fetch_object( 'KYSS_Lesson' ) );

		return $lessons;
	}

	/**
	 * Retrieve lesson from the db.
	 *
	 * @since 0.13.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  int $course Course ID.
	 * @param  string $date Datetime string.
	 * @return  KYSS_Lesson
	 */
	public static function get_lesson( $course, $date ) {
		global $kyssdb;

		$query = "SELECT * FROM {$kyssdb->lezioni} WHERE `corso`={$course} AND `data`='{$date}'";

		if ( ! $lesson = $kyssdb->query( $query ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error, array( 'query' => $query ) );

		if ( $lesson->num_rows === 0 )
			return false;

		$lesson = $lesson->fetch_object( 'KYSS_Lesson' );

		return $lesson;
	}

	/**
	 * Insert new lesson into the database.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Lesson data.
	 * @return  int|bool|KYSS_Error
	 */
	public static function create( $data ) {
		global $kyssdb;

		if ( ! isset( $data['corso'] ) )
			return new KYSS_Error( 'lesson_without_course', 'La lezione deve essere associata ad un corso.' );

		$columns = array();
		$values = array();

		foreach ( $data as $key => $value ) {
			array_push( $columns, $key );
			if ( is_int( $value ) )
				array_push( $values, $value );
			else
				array_push( $values, "'{$value}'" );
		}

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->lezioni} ({$columns}) VALUES ({$values})";
		if ( !$result = $kyssdb->query( $query ) )
			return new KYSS_Error( $kyssdb->errno, $kyssdb->error );
		return true;
	}

	/**
	 * Update lesson in the db.
	 *
	 * @since  0.11.0
	 * @access public
	 *
	 * @global kyssdb
	 *
	 * @param  int $course The course ID.
	 * @param  datetime $datetime The lesson date and time.
	 * @param  array $data Lesson data.
	 * @return bool Whether the update succeeded or not.
	 */
	public function update( $data ) {
		global $kyssdb;

		if ( empty( $data ) )
			return false;

		$result = $kyssdb->update( $kyssdb->lezioni, $data, array( 'corso' => $this->corso, 'data' => $this->data ) );

		if ( $result )
			return $this;
		return false;
	}

	/**
	 * Remove lesson from db.
	 *
	 * @since  0.13.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  int $corso Course ID.
	 * @param  string $data Lesson date.
	 * @return  bool
	 */
	public static function delete( $corso, $data ) {
		global $kyssdb;

		$query = "DELETE FROM {$kyssdb->lezioni} WHERE `corso`={$corso} AND `data`='{$data}'";
		$result = $kyssdb->query( $query );
		if ( ! $result )
			return false;
		return true;
	}
}