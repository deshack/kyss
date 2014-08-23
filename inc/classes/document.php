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
 * @since 
 * @package  KYSS
 * @subpackage  Practice
 */
class KYSS_Practice {
	/**
	 * The Practice's protocol number.
	 *
	 * @since 
	 * @access public
	 * @var  
	 */
	public $protocollo;

	/**
	 * THe Practice's user ID.
	 *
	 * @since 
	 * @access public
	 * @var  
	 */
	public $utente;

	/**
	 * The Practice's type.
	 *
	 * @since 
	 * @access public
	 * @var  
	 */
	public $tipo;

	/**
	 * The practice's date.
	 *
	 * @since 
	 * @access public
	 * @var  
	 */
	public $data;

	/**
	 * The Practice's date of receipt.
	 *
	 * @since 
	 * @access public
	 * @var  
	 */
	public $ricezione;

	/**
	 * The Practice's status.
	 *
	 * @since 
	 * @access public
	 * @var  bool
	 */
	public $approvata;

	/**
	 * The Practice's note.
	 *
	 * @since 
	 * @access public
	 * @var  string
	 */
	public $note;

	/**
	 * Constructor.
	 *
	 * @since  
	 * @access public
	 *
	 * @param  
	 * @return KYSS_Practice
	 */
	function __construct() {
		
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
