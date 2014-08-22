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
	 * The event's ID.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  int
	 */
	public $ID;

	/**
	 * The event's name.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  string
	 */
	public $nome;

	/**
	 * The event start date.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  date
	 */
	public $inizio;

	/**
	 * The event's end date.
	 *
	 * @since  0.11.0
	 * @access public
	 * @var  date
	 */
	public $fine;

	/**
	 * Constructor.
	 *
	 * @since  0.11.0
	 * @access public
	 *
	 * @param  int|string|stdClass|KYSS_Event $id Event ID, or a KYSS_Event object, or
	 * an event object from the DB.
	 */
	public function __construct( $id ) {
		if ( is_a( $id, 'KYSS_Event' ) || is_object( $id ) )
			return;

		if ( ! empty( $id ) && ! is_numeric( $id ) ) {
			trigger_error( 'Bad type for $id parameter', E_USER_WARNING );
			return;
		}

		if ( $id )
			$this = self::get_event_by( 'id', $id );
	}
	
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
			"SELECT * FROM {$kyssdb->eventi} WHERE {$db_field} = '{$value}'"
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
	 * @global  kyssdb
	 *
	 * @return  array|false Array of KYSS_Event objects. False on failure.
	 */
	public static function get_events_list() {
		global $kyssdb;

		if ( ! $event = $kyssdb->query(
			"SELECT * FROM {$kyssdb->eventi}"
		) );
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
	 * @param  string $name Optional. Event's name. Default empty.
	 * @param  string $start Optional. Start date string. Default today.
	 * @param  string $end Optional. End date string. Default <null>.
	 * @return int|bool The newly created event's ID or false on failure.
	 */
	public static function create( $name = '', $start = null, $end = null ) {
		global $kyssdb;

		if ( is_null( $start ) )
			$start = date( 'Y-m-d' );

		$columns = array( 'nome', 'inizio', 'fine' );
		$values = array( "'{$name}'", "'{$start}'", "'{$end}'" );

		$columns = join( ',', $columns );
		$values = join( ',', $values );

		$query = "INSERT INTO {$kyssdb->eventi} ({$columns}) VALUES ({$values})";
		if ( !$result = $kyssdb->query( $query ) ) {
			trigger_error( sprintf( "Query %s returned an error: %s", $query, $kyssdb->error ), E_USER_WARNING );
			return false;
		}

		return $kyssdb->insert_id;
	}

	/**
	 * Update event in the db.
	 *
	 * @since 0.11.0
	 * @access public
	 *
	 * @global  kyssdb
	 *
	 * @param  string $name Optional. Event's name. Default empty.
	 * @param  string $start Optional. Start date string. Default today.
	 * @param  string $end Optional. End date string. Default <null>.
	 * @return bool Whether the update succeeded or not.
	 */
	public function update( $name = '', $start = null, $end = null ) {
		global $kyssdb;

		if ( empty( $name ) )
			$name = $this->nome;
		if ( is_null( $start ) )
			$start = $this->inizio;
		if ( is_null( $end ) )
			$end = $this->fine;

		$data = array(
			'nome'		=> $name,
			'inizio'	=> $start,
			'fine'		=> $end
		);

		$result = $kyssdb->update( $kyssdb->utenti, $data, array( 'ID' => $this->ID ) );

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
	 * The course's event ID.
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
	 * @param int|string|stdClass|KYSS_Course $id Course ID, or a KYSS_Course object,
	 * or a course object from the DB.
	 */
	public function __construct( $id ) {
		if ( is_a( $id, 'KYSS_Course' ) || is_object( $id ) )
			return;

		if ( ! empty( $id ) && ! is_numeric( $id ) ) {
			trigger_error( 'Bad type for $id parameter', E_USER_WARNING );
			return;
		}

		if ( $id )
			$this = self::get_course_by_id( $id );
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
			"SELECT * FROM {$kyssdb->corsi} WHERE ID = {$id}"
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
	 * @global  kyssdb
	 *
	 * @return  array|false Array of KYSS_Course object. False on failure.
	 */
	public static function get_courses_list() {
		global $kyssdb;

		if ( ! $course = $kyssdb->query(
			"SELECT * FROM {$kyssdb->corsi}"
		) )
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
	public function __construct( $id ) {
		if ( is_a( $id, 'KYSS_Talk' ) || is_object( $id ) )
			return;

		if ( ! empty( $id ) && ! is_numeric( $id ) ) {
			trigger_error( 'Bad type for $id parameter', E_USER_WARNING );
			return;
		}

		if ( $id )
			$this = self::get_talk_by_id( $id );
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
	public static function get_list() {
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
 * KYSS Meeting class
 *
 * @since 0.11.0
 * @package  KYSS
 * @subpackage  Events 
 */
class KYSS_Meeting {
	/**
	 * The Meeting's ID.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  int
	 */
	public $ID;

	/**
	 * The Meeting's type.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  string 
	 */
	public $tipo;

	/**
	 * The Meeting's start date time.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  string
	 */
	public $inizio;

	/**
	 * The Meeting's end date time.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  string
	 */
	public $fine;

	/**
	 * The Meeting's place.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  string
	 */
	public $luogo;

	/**
	 * The Meeting's president ID.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  int
	 */
	public $presidente;

	/**
	 * The Meeting's secretary ID.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  int
	 */
	public $segretario;

	/**
	 * The Meeting's event ID.
	 *
	 * @since 0.11.0 
	 * @access public
	 * @var  int
	 */
	public $evento;

	/**
	 * Constructor.
	 *
	 * @since 0.11.0 
	 * @access public
	 *
	 * @param  int|string|stdClass|KYSS_Meeting $id Meeting ID, or a KYSS_Meeting object,
	 * or a meeting object from the DB.
	 */
	function __construct( $id ) {
		if ( is_a( $id, 'KYSS_Meeting' ) || is_object( $id ) )
			return;

		if ( ! empty( $id ) && ! is_numeric( $id ) ) {
			trigger_error( 'Bad type for `id` parameter', E_USER_WARNING );
			return;
		}

		if ( $id )
			$this = self::get_meeting_by_id( $id );
	}

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
			"SELECT * FROM {$kyssdb->riunioni} WHERE ID = {$id}"
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
	 * @global  kyssdb
	 *
	 * @return  array|false Array of KYSS_Meeting objects. False on failure.
	 */
	public static function get_list() {
		global $kyssdb;

		if ( ! $meeting = $kyssdb->query(
			"SELECT * FROM {$kyssdb->riunioni}"
		) )
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

		if ( ! isset( $data['titolo'] ) )
			return new KYSS_Error( 'meeting_title_missing', 'Per creare una nuova riunione &egrave; necessario un titolo.' );

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
	public function update( $data ) {
		global $kyssdb;

		foreach ( $data as $key => $value ) {
			if ( is_int( $value ) )
				continue;
			$data[$key] = "'{$value}'";
		}

		$result = $kyssdb->update( $kyssdb->riunioni, $data, array( 'ID', $this->ID ) );

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
	public function __construct( $id ) {
		if ( is_a( $id, 'KYSS_Lesson') || is_object( $id ) )
			return;

		if ( ! empty( $id ) && ! is_numeric( $id ) ) {
			trigger_error( "Bad type for `id` parameter", E_USER_WARNING );
			return;
		}

		if ( $id )
			$this = self::get_lesson_by_id( $id );
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
	public static function get_list() {
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