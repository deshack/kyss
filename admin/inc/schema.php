<?php
/**
 * KYSS Schema API
 *
 * Here we keep the DB structure and option values.
 *
 * @package  KYSS
 * @subpackage DB
 */

/**
 * Retrieve the SQL for creating database tables.
 *
 * @since  0.9.0
 *
 * @global  kyssdb
 *
 * @return string The SQL needed to create the KYSS database tables.
 */
function get_db_schema() {
	global $kyssdb;

	return "CREATE TABLE {$kyssdb->utenti} (
		`ID`			bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
		`nome`			varchar(20) NOT NULL,
		`cognome`		varchar(20) NOT NULL,
		`anagrafica`	text,
		`email`			varchar(30),
		`telefono`		varchar(15),
		`gruppo`		set('collaboratori','ordinari','fondatori','benemeriti','cd','rc','admin') DEFAULT 'ordinari',
		PRIMARY KEY (`ID`)
	) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->cariche} (
		`utente`		bigint(20) UNSIGNED NOT NULL,
		`tipo`			enum('presidente','vicepresidente','segretario','tesoriere','consigliere') NOT NULL,
		`inizio`		date NOT NULL,
		`fine`			date,
		CONSTRAINT ID PRIMARY KEY (`tipo`,`inizio`),
		FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
			ON UPDATE RESTRICT
			ON DELETE RESTRICT,
		UNIQUE(`utente`,`inizio`)
	) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->pratiche} (
		`protocollo`	int(6) UNSIGNED AUTO_INCREMENT NOT NULL,
		`utente`		bigint(20) UNSIGNED NOT NULL,
		`tipo`			enum('adesione','liberatoria') NOT NULL,
		`data`			date NOT NULL,
		`ricezione`		date NOT NULL,
		`approvata`		boolean DEFAULT NULL,
		`note`			varchar(255),
		PRIMARY KEY (`protocollo`),
		FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
		) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->eventi} (
		`ID`			bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
		`nome`			varchar(20),
		`inizio`		date NOT NULL,
		`fine`			date,
		PRIMARY KEY (`ID`)
	) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->talk} (
		`ID`			bigint(20) UNSIGNED NOT NULL,
		`titolo`		varchar(255) NOT NULL,
		`data`			datetime,
		`luogo`			varchar(20),
		`argomenti`		text,
		`evento`		bigint(20) UNSIGNED,
		`relatore`		bigint(20) UNSIGNED,
		PRIMARY KEY (`ID`),
		FOREIGN KEY (`evento`) REFERENCES {$kyssdb->eventi}(`ID`)
			ON UPDATE CASCADE
			ON DELETE CASCADE,
		FOREIGN KEY (`relatore`) REFERENCES {$kyssdb->utenti}(`ID`)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
	) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->riunioni} (
		`ID`			bigint(20) UNSIGNED NOT NULL,
		`tipo`			enum('CD','AdA') NOT NULL,
		`data`			date NOT NULL,
		`inizio`		time,
		`fine`			time,
		`luogo`			varchar(20),
		`presidente`	bigint(20) UNSIGNED,
		`segretario`	bigint(20) UNSIGNED,
		PRIMARY KEY (`ID`),
		FOREIGN KEY (`presidente`) REFERENCES {$kyssdb->utenti}(`ID`)
			ON UPDATE CASCADE
			ON DELETE RESTRICT,
		FOREIGN KEY (`segretario`) REFERENCES {$kyssdb->utenti}(`ID`)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
	) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->corsi} (
		`ID`			bigint(20) UNSIGNED NOT NULL,
		`titolo`		varchar(255) NOT NULL,
		`livello`		enum('B','M','A') NOT NULL,
		`inizio`		date,
		`fine`			date,
		`luogo`			varchar(20),
		PRIMARY KEY (`ID`)
	) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->iscritti} (
		`utente`		bigint(20) UNSIGNED NOT NULL,
		`corso`			bigint(20) UNSIGNED NOT NULL,
		PRIMARY KEY (`utente`,`corso`),
		FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
			ON UPDATE CASCADE
			ON DELETE CASCADE,
		FOREIGN KEY (`corso`) REFERENCES {$kyssdb->corsi}(`ID`)
			ON UPDATE CASCADE
			ON DELETE CASCADE
	) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->verbali} (
		`protocollo`	int(6) UNSIGNED AUTO_INCREMENT NOT NULL,
		`riunion`		bigint(20) UNSIGNED NOT NULL,
		PRIMARY KEY (`protocollo`),
		FOREIGN KEY (`riunione`) REFERENCES {$kyssdb->riunioni}(`ID`)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
	) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->bilanci} (
		`ID`			bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
		`tipo`			enum('mensile','consuntivo','preventivo') NOT NULL,
		`mese`			enum('Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'),
		`anno`			year NOT NULL,
		`cassa`			varchar(10) NOT NULL DEFAULT 0,
		`banca`			varchar(10) NOT NULL,
		`approvato`		boolean DEFAULT FALSE,
		`verbale`		int(6) UNSIGNED,
		PRIMARY KEY (`ID`),
		FOREIGN KEY (`verbale`) REFERENCES {$kyssdb->verbali}(`protocollo`)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
	) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->movimenti} (
		`ID`			bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
		`causale`		varchar(255) NOT NULL,
		`importo`		varchar(10) NOT NULL,
		`utente`		bigint(20) UNSIGNED NOT NULL,
		`bilancio`		bigint(20) UNSIGNED,
		PRIMARY KEY (`ID`),
		FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
			ON UPDATE CASCADE
			ON DELETE RESTRICT,
		FOREIGN KEY (`bilancio`) REFERENCES {$kyssdb->bilanci}(`ID`)
			ON UPDATE CASCADE
			ON DELETE RESTRICT
	) ENGINE = InnoDB;

	CREATE TABLE {$kyssdb->presenti} (
		`utente`		bigint(20) UNSIGNED NOT NULL,
		`riunione`		bigint(20) UNSIGNED NOT NULL,
		PRIMARY KEY (`utente`,`riunione`),
		FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
			ON UPDATE CASCADE
			ON DELETE CASCADE,
		FOREIGN KEY (`riunione`) REFERENCES {$kyssdb->riunioni}(`ID`)
	) ENGINE = InnoDB;"
	
}

/**
 * Create KYSS database tables.
 *
 * @since  0.9.0
 *
 * @global  kyssdb
 */
function populate_db() {
	global $kyssdb;
}

/**
 * Create KYSS options and set default values.
 *
 * @since  0.9.0
 * @global kyssdb
 * @global hook
 */
function populate_options() {
	global $kyssdb, $hook;

	$guessurl = kyss_guess_url();

	/**
	 * Fires before creating KYSS options and populating their default values.
	 *
	 * @since  0.9.0
	 */
	$hook->run( 'populate_options' );

	$options = array(
		'siteurl' => $guessurl,
		'sitename' => 'KYSS',
		'admin_email' => 'you@example.com'
	);

	$insert = '';
	foreach ( $options as $option => $value ) {
		// This is to support future options, which can include arrays.
		if ( is_array( $value ) )
			$value = serialize($value);
		if ( ! empty($insert) )
			$insert .= ', ';
		$insert .= $kyssdb->prepare( "(%s, %s, %s)", $option, $value);
	}

	if ( !empty($insert) )
		// $kyssdb->options is a table name!
		$kyssdb->query("INSERT INTO $kyssdb->options (option_name, option_value) VALUES " . $insert );
}