<?php
/**
 * KYSS Document API
 *
 * @package  KYSS
 * @subpackage  Documents
 */

/**
 * KYSS Practice class
 *
 * @since 0.12.0
 * @package  KYSS
 * @subpackage  Documents
 */
class KYSS_Practice {
	/**
	 * The Practice's protocol number.
	 *
	 * @since 0.12.0
	 * @access public
	 * @var  
	 */
	public $protocollo;

	/**
	 * THe Practice's user ID.
	 *
	 * @since 0.12.0
	 * @access public
	 * @var  
	 */
	public $utente;

	/**
	 * The Practice's type.
	 *
	 * @since 0.12.0
	 * @access public
	 * @var  
	 */
	public $tipo;

	/**
	 * The practice's date.
	 *
	 * @since 0.12.0
	 * @access public
	 * @var  
	 */
	public $data;

	/**
	 * The Practice's date of receipt.
	 *
	 * @since 0.12.0
	 * @access public
	 * @var  
	 */
	public $ricezione;

	/**
	 * The Practice's status.
	 *
	 * @since 0.12.0
	 * @access public
	 * @var  bool
	 */
	public $approvata;

	/**
	 * The Practice's note.
	 *
	 * @since 0.12.0
	 * @access public
	 * @var  string
	 */
	public $note;

	/**
	 * Constructor.
	 *
	 * @since 0.12.0 
	 * @access public
	 *
	 * @param  string|stdClass|KYSS_Practice $prot Protocol number, a KYSS_Practice
	 * object or a practice object from the db.
	 */
	public function __construct( $prot ) {
		if ( is_a( $prot, 'KYSS_Practice' ) || is_object( $prot ) )
			return;

		$this = self::get( $prot );
	}

	/**
	 * Retrieve practice by protocol.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @param  string $prot Practice protocol number.
	 * @return  KYSS_Practice|bool KYSS_Practice object or false on failure.
	 */
	public static function get( $prot ) {
		global $kyssdb;

		if ( ! $practice = $kyssdb->query(
			"SELECT * FROM {$kyssdb->pratiche} WHERE protocollo = '{$prot}'"
		) )
			return false;

		if ( $practice->num_rows == 0 )
			return new KYSS_Error( 'practice_not_found', 'Pratica non trovata', array( 'protocollo' => $prot ) );
		$practice = $practice->fetch_object( 'KYSS_Practice' );

		return $practice;
	}

	/**
	 * Retrieve practices list.
	 *
	 * @since  0.12.0
	 * @access public
	 * @static
	 *
	 * @global  kyssdb
	 *
	 * @return array|false Array of KYSS_Practice objects or false on failure.
	 */
	public static function get_list() {
		global $kyssdb;

		if ( ! $practice = $kyssdb->query(
			"SELECT * FROM {$kyssdb->pratiche}"
		) )
			return false;

		$practices = array();

		for ( $i = 0; $i < $practice->num_rows; $i++ )
			array_push( $practices, $practice->fetch_object( 'KYSS_Practice' ) );

		return $practices;
	}
}

/**
 * KYSS Report class
 *
 * @since
 * @package  KYSS
 * @subpackage  Report
 */
class KYSS_Report {
	/**
	 * The Report's protocol number.
	 *
	 * @since 
	 * @access public
	 * @var  int
	 */
	public $protocollo;

	/**
	 * The Report's reuinion ID.
	 *
	 * @since 
	 * @access public
	 * @var  int
	 */
	public $riunione;

	/**
	 * Constructor.
	 *
	 * @since  
	 * @access public
	 *
	 * @param  
	 * @return KYSS_Report
	 */
	function __construct() {
		
	}

/**
 * KYSS Budget class
 *
 * @since
 * @package  KYSS
 * @subpackage  Budget
 */
class KYSS_Budget {
	/**
	 * The Budget's ID.
	 *
	 * @since 
	 * @access public
	 * @var  int
	 */
	public $ID;

	/**
	 * The Budget's type.
	 *
	 * @since 
	 * @access public
	 * @var  string
	 */
	public $tipo;

	/**
	 * The Budget's month.
	 *
	 * @since 
	 * @access public
	 * @var  int
	 */
	public $mese;

	/**
	 * The Budget's year.
	 *
	 * @since 
	 * @access public
	 * @var  int
	 */
	public $anno;

	/**
	 * Amount of the funds in bank
	 *
	 * @since 
	 * @access public
	 * @var  
	 */
	public $cassa;

	/**
	 * Amount of the funds in the checkout.
	 *
	 * @since 
	 * @access public
	 * @var  
	 */
	public $banca;

	/**
	 * The Budeget's status.
	 *
	 * @since 
	 * @access public
	 * @var  
	 */
	public $approvato;

	/**
	 * The Budget's report ID.
	 *
	 * @since 
	 * @access public
	 * @var  
	 */
	public $verbale;

	/**
	 * Constructor.
	 *
	 * @since  
	 * @access public
	 *
	 * @param  
	 * @return KYSS_Budget
	 */
	function __construct() {
		
	}
