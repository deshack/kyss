<?php
/**
 * KYSS Event API
 *
 * @package  KYSS
 * @subpackage  Event
 */

/**
 * KYSS Event class
 *
 * @since
 * @package  KYSS
 * @subpackage  Event
 */
class KYSS_Event {
	/**
	 * The event's ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $ID;

	/**
	 * The event's name.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $nome = '';

	/**
	 * The event' start date.
	 *
	 * @since  
	 * @access public
	 * @var  date
	 */
	public $inizio;

	/**
	 * The event's end date.
	 *
	 * @since  
	 * @access public
	 * @var  date
	 */
	public $fine;

	/**
	 * Constructor.
	 *
	 * @since  
	 * @access public
	 *
	 * @param  
	 * @return KYSS_Event
	 */
	function __construct() {

	}
	
}

/**
 * KYSS Course class
 *
 * @since
 * @package  KYSS
 * @subpackage  Course  
 */
class KYSS_Course {
	/**
	 * The course's ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $ID;

	/**
	 * The course's title.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $titolo;

	/**
	 * The course's level
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $livello;

	/**
	 * The course's place.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $luogo;

	/**
	 * The course's lessons number.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $lezioni;

	/**
	 * The course's event ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $evento;

	/**
	 * Constructor.
	 *
	 * @since  
	 * @access public
	 *
	 * @param  
	 * @return KYSS_Talk
	 */
	function __construct() {
		
	}
	
}

/**
 * KYSS Talk class
 *
 * @since
 * @package  KYSS
 * @subpackage  Talk  
 */
class KYSS_Talk {
	/**
	 * The Talk's ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $ID;

	/**
	 * The Talk's title.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $title;

	/**
	 * The Talk's date.
	 *
	 * @since  
	 * @access public
	 * @var  date
	 */
	public $data;

	/**
	 * The Talk's date time.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $ora;

	/**
	 * The Talk's place.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $luogo;

	/**
	 * The Talk's arguments
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $argomenti;

	/**
	 * The Talk's rapporteur ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $relatore;

	/**
	 * The Talk's event ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $evento;

	/**
	 * Constructor.
	 *
	 * @since  
	 * @access public
	 *
	 * @param  
	 * @return KYSS_Talk
	 */
	function __construct() {
		
	}
	
}

/**
 * KYSS Meeting class
 *
 * @since
 * @package  KYSS
 * @subpackage  Meeting 
 */
class KYSS_Meeting {
	/**
	 * The Meeting's ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $ID;

	/**
	 * The Meeting's type.
	 *
	 * @since  
	 * @access public
	 * @var  string 
	 */
	public $tipo;

	/**
	 * The Meeting's start date time.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $inizio;

	/**
	 * The Meeting's end date time.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $fine;

	/**
	 * The Meeting's place.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $luogo;

	/**
	 * The Meeting's president ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $presidente;

	/**
	 * The Meeting's secretary ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $segretario;

	/**
	 * The Meeting's event ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $evento;

	/**
	 * Constructor.
	 *
	 * @since  
	 * @access public
	 *
	 * @param  
	 * @return KYSS_Meeting
	 */
	function __construct() {
		
	}
	
}

/**
 * KYSS Lesson class
 *
 * @since
 * @package  KYSS
 * @subpackage  Lesson
 */
class KYSS_Lesson {
	/**
	 * The Lesson's date.
	 *
	 * @since  
	 * @access public
	 * @var  date
	 */
	public $data;

	/**
	 * The Lesson's start date time.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $ora;

	/**
	 * The Lesson's arguments.
	 *
	 * @since  
	 * @access public
	 * @var  string
	 */
	public $argomenti;

	/**
	 * The Lesson's course ID.
	 *
	 * @since  
	 * @access public
	 * @var  int
	 */
	public $corso;

	/**
	 * Constructor.
	 *
	 * @since  
	 * @access public
	 *
	 * @param  
	 * @return KYSS_Course
	 */
	function __construct() {
		
	}