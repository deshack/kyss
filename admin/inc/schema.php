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
 * @return array Numeric array of the SQL queries needed to create
 * the KYSS database tables.
 */
function get_db_schema() {
	global $kyssdb;

	if ( class_exists('KYSS_Groups') )
		$groups = "set('" . join("','", KYSS_Groups::get_slugs() ) . "')";
	else {
		trigger_error( sprintf( "%s needs the KYSS_Groups class", __FUNCTION__ ), E_USER_ERROR );
		die();
	}

	if ( ! isset( $kyssdb ) )
		kyss_die( sprintf('Something is wrong with your application. KYSS could not find a database connection while installing. Please report to %s',
			'deshack@ubuntu.com') );
	$schema = array(
		"CREATE TABLE IF NOT EXISTS {$kyssdb->utenti} (
			`ID`			bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`nome`			varchar(20) NOT NULL,
			`cognome`		varchar(20) NOT NULL,
			`anagrafica`	text,
			`email`			varchar(30),
			`telefono`		varchar(15),
			`gruppo`		{$groups} DEFAULT 'ordinari',
			`password`		char(128),
			PRIMARY KEY (`ID`),
			UNIQUE (`email`)
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->cariche} (
			`carica`		ENUM('presidente','vicepresidente','segretario','tesoriere','consigliere') NOT NULL,
			`inizio`		date NOT NULL,
			`utente`		bigint(20) UNSIGNED NOT NULL,
			`fine`			date,
			CONSTRAINT ID PRIMARY KEY (`carica`,`inizio`),
			FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE RESTRICT ON DELETE RESTRICT,
			UNIQUE (`utente`,`inizio`)
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->pratiche} (
			`protocollo`		int(6) UNSIGNED AUTO_INCREMENT NOT NULL,
			`utente`			bigint(20) UNSIGNED NOT NULL,
			`tipo`				enum('adesione','liberatoria') NOT NULL,
			`data`				date NOT NULL,
			`ricezione`			date NOT NULL,
			`approvata`			boolean DEFAULT NULL,
			`note`				varchar(255),
			PRIMARY KEY (`protocollo`),
			FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT
			) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->eventi} (
			`ID`			bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`nome`			varchar(20),
			`inizio`		date NOT NULL,
			`fine`			date,
			PRIMARY KEY (`ID`)
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->talk} (
			`ID`			bigint(20) UNSIGNED NOT NULL,
			`titolo`		varchar(255) NOT NULL,
			`data`			datetime,
			`luogo`			varchar(20),
			`argomenti`		text,
			`relatore`		bigint(20) UNSIGNED,
			`evento`		bigint(20) UNSIGNED,
			PRIMARY KEY (`ID`),
			FOREIGN KEY (`relatore`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT,
			FOREIGN KEY (`evento`) REFERENCES {$kyssdb->eventi}(`ID`)
				ON UPDATE CASCADE ON DELETE CASCADE
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->riunioni} (
			`ID`			bigint(20) UNSIGNED NOT NULL,
			`tipo`			enum('CD','AdA') NOT NULL,
			`data`			date NOT NULL,
			`inizio`		time,
			`fine`			time,
			`luogo`			varchar(20),
			`presidente`	bigint(20) UNSIGNED,
			`segretario`	bigint(20) UNSIGNED,
			`evento`		bigint(20) UNSIGNED,
			PRIMARY KEY (`ID`),
			FOREIGN KEY (`presidente`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT,
			FOREIGN KEY (`segretario`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT,
			FOREIGN KEY (`evento`) REFERENCES {$kyssdb->eventi}(`ID`)
				ON UPDATE CASCADE ON DELETE CASCADE
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->corsi} (
			`ID`			bigint(20) UNSIGNED NOT NULL,
			`titolo`		varchar(255) NOT NULL,
			`livello`		enum('base','medio','avanzato') NOT NULL,
			`luogo`			varchar(20),
			`lezioni`		int,
			`evento`		bigint(20) UNSIGNED,
			PRIMARY KEY (`ID`),
			FOREIGN KEY (`evento`) REFERENCES {$kyssdb->eventi}(`ID`)
				ON UPDATE CASCADE ON DELETE CASCADE,
			CHECK(`lezioni` > 0)
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->lezioni} (
			`data`				datetime NOT NULL,
			`argomento`			text,
			`corso`				bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY (`data`),
			FOREIGN KEY (`corso`) REFERENCES {$kyssdb->corsi}(`ID`)
				ON UPDATE CASCADE ON DELETE CASCADE
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->iscritto} (
			`utente`		bigint(20) UNSIGNED NOT NULL,
			`corso`			bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY (`utente`,`corso`),
			FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE CASCADE,
			FOREIGN KEY (`corso`) REFERENCES {$kyssdb->corsi}(`ID`)
				ON UPDATE CASCADE ON DELETE CASCADE
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->verbali} (
			`protocollo`	int(6) UNSIGNED AUTO_INCREMENT NOT NULL,
			`riunione`		bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY (`protocollo`),
			FOREIGN KEY (`riunione`) REFERENCES {$kyssdb->riunioni}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->bilanci} (
			`ID`			bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`tipo`			enum('mensile','consuntivo','preventivo') NOT NULL,
			`mese`			enum('Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'),
			`anno`			year NOT NULL,
			`cassa`			varchar(10) NOT NULL DEFAULT 0,
			`banca`			varchar(10) NOT NULL,
			`approvato`		boolean,
			`verbale`		int(6) UNSIGNED,
			PRIMARY KEY (`ID`),
			FOREIGN KEY (`verbale`) REFERENCES {$kyssdb->verbali}(`protocollo`)
				ON UPDATE CASCADE ON DELETE RESTRICT
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->movimenti} (
			`ID`			bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`utente`		bigint(20) UNSIGNED NOT NULL,
			`causale`		varchar(255) NOT NULL,
			`importo`		varchar(10) NOT NULL,
			`data`			date NOT NULL,
			`bilancio`		bigint(20) UNSIGNED,
			`evento`		bigint(20) UNSIGNED,
			PRIMARY KEY (`ID`),
			FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT,
			FOREIGN KEY (`bilancio`) REFERENCES {$kyssdb->bilanci}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT,
			FOREIGN KEY (`evento`) REFERENCES {$kyssdb->eventi}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->options} (
			`ID`			bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`name`			varchar(64) NOT NULL DEFAULT '',
			`value`			longtext NOT NULL,
			PRIMARY KEY (`ID`),
			UNIQUE (`name`)
		) ENGINE = InnoDB;"
	);

	return $schema;
}

/**
 * Create KYSS database tables.
 *
 * @since  0.9.0
 * @see  get_db_schema()
 *
 * @global  kyssdb
 * @global  hook
 */
function populate_db() {
	global $kyssdb, $hook;

	/**
	 * Fires before creating KYSS tables.
	 *
	 * @since  0.9.0
	 */
	$hook->run( 'populate_db' );

	$schema = get_db_schema();

	// Run the SQL
	$i = 0;
	if ( $kyssdb->multi_query( join(';', $schema) ) ) {
		do {
			$kyssdb->next_result();
			$i++;
		} while ( $kyssdb->more_results() );
	}
	
	if ( $kyssdb->errno )
		kyss_die( 'Query #' . ($i + 1) . ':</p><pre>' . $schema[$i] . '</pre><p>' . $kyssdb->error );
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
		$insert .= sprintf( "('%s', '%s')", $kyssdb->real_escape_string($option), $kyssdb->real_escape_string($value) );
	}

	$query = "INSERT INTO {$kyssdb->options} (name, value) VALUES " . $insert;

	if ( ! $kyssdb->query( $query ) ) {
		trigger_error( $kyssdb->error, E_USER_ERROR );
	}
}