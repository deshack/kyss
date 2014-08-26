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
	public static function update( $id, $data ) {
		global $kyssdb;

		if ( empty( $data) )
			return false;

		$result = $kyssdb->update( $kyssdb->eventi, $data, array( 'ID' => $id ) );

		if ( $result )
			return true;
		return false;
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
		'data_fine'
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
			FROM {$kyssdb->riunioni}
			INNER JOIN {$kyssdb->eventi} ON {$kyssdb->riunioni}.ID = {$kyssdb->eventi}.ID
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
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}

		return $kyssdb->insert_id;
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
	public static function update( $id, $data ) {
		global $kyssdb;

		if ( empty( $data ) )
			return false;

		// Put all $data items representing $kyssdb->eventi columns
		// into the $event array.
		$event = array();
		foreach ( array_keys( $data ) as $key ) {
			if ( in_array( $key, self::$event_data ) ) {
				$event[$key] = $data[$key];
				unset( $data[$key] );
			}
		}

		parent::update( $id, $event );

		$result = $kyssdb->update( $kyssdb->riunioni, $data, array( 'ID' => $id ) );

		if ( $result )
			return true;
		return false;
	}
}

/**
 * KYSS Course class
 *
 * @since 0.11.0
 * @package  KYSS
 * @subpackage Events
 */
class KYSS_Course {
	/**
	 * The course's ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  int
	 */
	public $ID;

	/**
	 * The course's title.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  string
	 */
	public $titolo;

	/**
	 * The course's level
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  string
	 */
	public $livello;

	/**
	 * The course's place.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  string
	 */
	public $luogo;

	/**
	 * The course's lessons number.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  int
	 */
	public $lezioni;

	/**
	 * Constructor.
	 *
	 * @since  0.11.0
	 * @access public
	 *
	 * @param int|string|stdClass|KYSS_Course $id Course ID, or a KYSS_Course object,
	 * or a course object from the DB.
	 */
	public function __construct() {

	}

	/**
	 * Retrieve course by ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  int $id The event id.
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
			FROM {$kyssdb->corsi}
			WHERE ID = {$id}"
		) )
			return false;

		if ( $course->num_rows == 0 )
			return new KYSS_Error( 'course_not_found', 'Course not found.', array( 'ID' => $id ) );

		$course = $course->fetch_object( 'KYSS_Event' );

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
	public static function get_courses_list( $event = true ) {
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
	 * @return int|bool|KYSS_Error The newly created course's ID, KYSS_Error
	 * or false on failure.
	 */
	public static function create( $data ) {
		global $kyssdb;

		if ( ! isset( $data['titolo'] ) )
			return new KYSS_Error( 'course_title_missing', 'Per creare un nuovo corso &egrave; necessario un titolo.' );
		if ( ! isset( $data['livello'] ) )
			$data['livello'] = 'base';

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
		if ( !$result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}

		return $kyssdb->insert_id;
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

		foreach ( $data as $key => $value ) {
			if ( is_int( $value ) )
				continue;
			$data[$key] = "'{$value}'";
		}

		$result = $kyssdb->update( $kyssdb->corsi, $data, array( 'ID' => $this->ID ) );

		if ( $result )
			return true;
		return false;
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
	 * The Talk's ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  int
	 */
	public $ID;

	/**
	 * The Talk's title.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  string
	 */
	public $title;

	/**
	 * The Talk's date.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  date
	 */
	public $data;

	/**
	 * The Talk's date time.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  string
	 */
	public $ora;

	/**
	 * The Talk's place.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  string
	 */
	public $luogo;

	/**
	 * The Talk's arguments
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  string
	 */
	public $argomenti;

	/**
	 * The Talk's rapporteur ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  int
	 */
	public $relatore;

	/**
	 * The Talk's event ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  int
	 */
	public $evento;

	/**
	 * Constructor.
	 *
	 * @since  0.11.0
	 * @access public
	 *
	 * @param  int|string|stdClass|KYSS_Talk $id Talk ID, or a KYSS_Talk object, or
	 * a talk object from the DB.
	 */
	public function __construct() {
	
	}
	
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

		if ( ! $course = $kyssdb->query(
			"SELECT * FROM {$kyssdb->talk} WHERE ID = {$id}"
		) )
			return false;

		if ( $course->num_rows == 0 )
			return new KYSS_Error( 'talk_not_found', 'Talk not found.', array( 'ID' => $id ) );

		$course = $course->fetch_object( 'KYSS_Talk' );

		return $course;
	}

	/**
	 * Retrieve talks list.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @return  array|false Array of KYSS_Talk objects. False on failure.
	 */
	public static function get_talks_list() {
		global $kyssdb;

		if ( ! $talk = $kyssdb->query(
			"SELECT * FROM {$kyssdb->talk}"
		) )
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
		if ( ! $result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}

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

		foreach ( $data as $key => $value ) {
			if ( is_int( $value ) )
				continue;
			$data[$key] = "'{$value}'";
		}

		$result = $kyssdb->update( $kyssdb->corsi, $data, array( 'ID', $this->ID ) );

		if ( $result )
			return true;
		return false;
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
	 * The Lesson's date.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  date
	 */
	public $data;

	/**
	 * The Lesson's start date time.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  string
	 */
	public $ora;

	/**
	 * The Lesson's arguments.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  string
	 */
	public $argomenti;

	/**
	 * The Lesson's course ID.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  int
	 */
	public $corso;

	/**
	 * Constructor.
	 *
	 * @since 0.11.0 
	 * @access public
	 *
	 * @param  int|string|stdClass|KYSS_Lesson $id Lesson ID, or a KYSS_Lesson object,
	 * or a lesson object from the DB.
	 */
	public function __construct() {
	
	}

	/**
	 * Retrieve lesson by ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  int $id The lesson ID.
	 * @return  KYSS_Lesson|bool False on failure.
	 */
	public static function get_lesson_by_id( $id ) {
		global $kyssdb;

		if ( ! is_numeric( $id ) )
			return false;
		$id = intval( $id );

		if ( $id < 1 )
			return false;

		if ( ! $lesson = $kyssdb->query (
			"SELECT * FROM {$kyssdb->lezioni} WHERE ID = {$id}"
		) )
			return false;

		if ( $lesson->num_rows == 0 )
			return new KYSS_Error( 'lesson_not_found', 'Lesson not found.', array( 'ID' => $id ) );

		$lesson = $lesson->fetch_object( 'KYSS_Lesson' );

		return $lesson;
	}

	/**
	 * Retrieve lessons list.
	 *
	 * @since  0.11.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @return  array|false Array of KYSS_Lesson objects. False on failure.
	 */
	public static function get_lessons_list() {
		global $kyssdb;

		if ( ! $lesson = $kyssdb->query(
			"SELECT * FROM {$kyssdb->lezioni}"
		) )
			return false;

		$lessons = array();

		for ( $i = 0; $i < $lesson->num_rows; $i++ )
			array_push( $lessons, $lesson->fetch_object( 'KYSS_Lesson' ) );

		return $lessons;
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
		if ( !$result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}
		return $kyssdb->insert_id;
	}

	/**
	 * Update lesson in the db.
	 *
	 * @since  0.11.0
	 * @access public
	 *
	 * @global  kyssdb
	 *
	 * @param  array $data Lesson data.
	 * @return  bool Whether the update succeeded or not.
	 */
	public function update( $data ) {
		global $kyssdb;

		foreach ( $data as $key => $value ) {
			if ( is_int( $value ) )
				continue;
			$data[$key] = "'{$value}'";
		}

		$result = $kyssdb->update( $kyssdb->lezioni, $data, array( 'ID' => $this->ID ) );

		if ( $result )
			return true;
		return false;
	}
}