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
			`ID`				bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`nome`				varchar(20) NOT NULL,
			`cognome`			varchar(20) NOT NULL,
			`codice_fiscale`	varchar(16),
			`nato_a`			varchar(20),
			`nato_il`			date,
			`cittadinanza`		varchar(20),
			`via`				varchar(20),
			`citta`				varchar(20),
			`provincia`			varchar(20),
			`CAP`				int(5),
			`email`				varchar(30),
			`telefono`			varchar(15),
			`gruppo`			{$groups} DEFAULT 'ordinari',
			`password`			char(128),
			PRIMARY KEY (`ID`),
			UNIQUE (`email`)
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->cariche} (
			`carica`			ENUM('presidente', 'vicepresidente', 'segretario', 'tesoriere', 'consigliere') NOT NULL,
			`inizio`			date NOT NULL,
			`utente`			bigint(20) UNSIGNED NOT NULL,
			`fine`				date,
			CONSTRAINT ID PRIMARY KEY (`carica`,`inizio`),
			FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE RESTRICT ON DELETE RESTRICT,
			UNIQUE (`utente`,`inizio`)
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->pratiche} (
			`protocollo`		int(6) UNSIGNED AUTO_INCREMENT NOT NULL,
			`utente`			bigint(20) UNSIGNED NOT NULL,
			`tipo`				enum('adesione', 'liberatoria') NOT NULL,
			`data`				date NOT NULL,
			`ricezione`			date NOT NULL,
			`approvata`			boolean DEFAULT NULL,
			`note`				varchar(255),
			PRIMARY KEY (`protocollo`),
			FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT
			) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->eventi} (
			`ID`				bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`nome`				varchar(20),
			`data_inizio`		date NOT NULL,
			`data_fine`			date,
			`luogo`				varchar(20),
			PRIMARY KEY (`ID`)
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->talk} (
			`ID`				bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`titolo`			varchar(255) NOT NULL,
			`data`				datetime,
			`argomenti`			text NOT NULL,
			`relatore`			bigint(20) UNSIGNED,
			`evento`			bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY (`ID`),
			FOREIGN KEY (`relatore`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT,
			FOREIGN KEY (`evento`) REFERENCES {$kyssdb->eventi}(`ID`)
				ON UPDATE CASCADE ON DELETE CASCADE
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->riunioni} (
			`ID`				bigint(20) UNSIGNED NOT NULL,
			`tipo`				enum('CD', 'AdA') NOT NULL,
			`ora_inizio`		time,
			`ora_fine`			time,
			`presidente`		bigint(20) UNSIGNED,
			`segretario`		bigint(20) UNSIGNED,
			PRIMARY KEY (`ID`),
			FOREIGN KEY (`ID`) REFERENCES {$kyssdb->eventi}(`ID`)
				ON UPDATE CASCADE ON DELETE CASCADE,
			FOREIGN KEY (`presidente`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT,
			FOREIGN KEY (`segretario`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->corsi} (
			`ID`				bigint(20) UNSIGNED NOT NULL,
			`livello`			enum('base', 'medio', 'avanzato') NOT NULL,
			PRIMARY KEY (`ID`),
			FOREIGN KEY (`ID`) REFERENCES {$kyssdb->eventi}(`ID`)
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
			`utente`			bigint(20) UNSIGNED NOT NULL,
			`corso`				bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY (`utente`,`corso`),
			FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE CASCADE,
			FOREIGN KEY (`corso`) REFERENCES {$kyssdb->corsi}(`ID`)
				ON UPDATE CASCADE ON DELETE CASCADE
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->verbali} (
			`protocollo`		int(6) UNSIGNED AUTO_INCREMENT NOT NULL,
			`contenuto`			text,
			`riunione`			bigint(20) UNSIGNED NOT NULL,
			PRIMARY KEY (`protocollo`),
			FOREIGN KEY (`riunione`) REFERENCES {$kyssdb->riunioni}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->bilanci} (
			`ID`				bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`tipo`				enum('mensile', 'consuntivo', 'preventivo') NOT NULL,
			`mese`				enum('Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'),
			`anno`				year NOT NULL,
			`cassa`				varchar(10) NOT NULL,
			`banca`				varchar(10) NOT NULL,
			`approvato`			boolean,
			`verbale`			int(6) UNSIGNED,
			PRIMARY KEY (`ID`),
			FOREIGN KEY (`verbale`) REFERENCES {$kyssdb->verbali}(`protocollo`)
				ON UPDATE CASCADE ON DELETE RESTRICT
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->movimenti} (
			`ID`				bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`utente`			bigint(20) UNSIGNED NOT NULL,
			`causale`			varchar(255) NOT NULL,
			`importo`			varchar(10) NOT NULL,
			`data`				date NOT NULL,
			`bilancio`			bigint(20) UNSIGNED,
			`evento`			bigint(20) UNSIGNED,
			PRIMARY KEY (`ID`),
			FOREIGN KEY (`utente`) REFERENCES {$kyssdb->utenti}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT,
			FOREIGN KEY (`bilancio`) REFERENCES {$kyssdb->bilanci}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT,
			FOREIGN KEY (`evento`) REFERENCES {$kyssdb->eventi}(`ID`)
				ON UPDATE CASCADE ON DELETE RESTRICT
		) ENGINE = InnoDB",

		"CREATE TABLE IF NOT EXISTS {$kyssdb->options} (
			`ID`				bigint(20) UNSIGNED AUTO_INCREMENT NOT NULL,
			`name`				varchar(64) NOT NULL DEFAULT '',
			`value`				longtext NOT NULL,
			PRIMARY KEY (`ID`),
			UNIQUE (`name`)
		) ENGINE = InnoDB"
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

	// $kyssdb->multi_query does not handle custom delimiters

	$kyssdb->query(
		"CREATE PROCEDURE controlloIscritti(IN utente INT(20), corso INT(20))
		BEGIN
			DECLARE quote INT;
			SELECT COUNT(*) INTO quote
			FROM {$kyssdb->movimenti}
			WHERE {$kyssdb->movimenti}.utente = utente AND {$kyssdb->movimenti}.corso = corso;
			IF (quote = 0) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'Quota non pagata';
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloIscrittiIns
		BEFORE INSERT ON {$kyssdb->iscritto}
		FOR EACH ROW BEGIN
			CALL supportoControlloIscritti(NEW.utente, NEW.corso);
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloIscrittiUpd
		BEFORE UPDATE ON {$kyssdb->iscritto}
		FOR EACH ROW BEGIN
			CALL supportoControlloIscritti(NEW.utente, NEW.corso);
		END"
	);

	$kyssdb->query(
		"CREATE PROCEDURE controlloCariche(IN utente INT(20))
		BEGIN
			DECLARE n_cariche INT;
			SELECT COUNT(*) INTO n_cariche
			FROM {$kyssdb->cariche} 
			WHERE {$kyssdb->cariche}.utente = utente AND {$kyssdb->cariche}.fine IS NULL;
			IF (n_cariche > 0) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'L`utente ricopre già una carica';
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloCaricheIns
		BEFORE INSERT ON {$kyssdb->cariche}
		FOR EACH ROW BEGIN
			CALL controlloCariche(NEW.utente);
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloCaricheUpd
		BEFORE UPDATE ON {$kyssdb->cariche}
		FOR EACH ROW BEGIN
			CALL controlloCariche(NEW.utente);
		END"
	);

	$kyssdb->query(
		"CREATE PROCEDURE controlloUtenti(IN nato_il DATE)
		BEGIN
			DECLARE differenza INT;
			SELECT DATEDIFF( nato_il, CURRENT_DATE() ) INTO differenza;
			IF ( differenza > 0 ) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'Data di nascita successiva alla data corrente';
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloUtentiIns
		BEFORE INSERT ON {$kyssdb->utenti}
		FOR EACH ROW BEGIN
			CALL controlloUtenti(NEW.nato_il);
			SET NEW.codice_fiscale = UPPER(NEW.codice_fiscale);
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloUtentiUpd
		BEFORE UPDATE ON {$kyssdb->utenti}
		FOR EACH ROW BEGIN
			CALL controlloUtenti(NEW.nato_il);
			SET NEW.codice_fiscale = UPPER(NEW.codice_fiscale);
		END"
	);

	$kyssdb->query(
		"CREATE PROCEDURE controlloInizioFineEvento(IN data_inizio date, data_fine date)
		BEGIN
			DECLARE differenza INT;
			SELECT DATEDIFF( data_inizio, data_fine ) INTO differenza;
			IF ( differenza > 0 ) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'Data di inizio successiva a data di fine evento';
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloInizioFineEventoIns
		BEFORE INSERT ON {$kyssdb->eventi}
		FOR EACH ROW BEGIN
			IF (NEW.data_fine IS NOT NULL) THEN
				CALL controlloInizioFineEvento(NEW.data_inizio, NEW.data_fine);
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloInizioFineEventoUpd
		BEFORE UPDATE ON {$kyssdb->eventi}
		FOR EACH ROW BEGIN
			IF (NEW.data_fine IS NOT NULL) THEN
				CALL controlloInizioFineEvento(NEW.data_inizio, NEW.data_fine);
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE PROCEDURE controlloDataTalk(IN data date, inizio_evento date, fine_evento DATE)
		BEGIN
			DECLARE differenza INT;
			SELECT DATEDIFF( data, inizio_evento ) INTO differenza;
			IF ( differenza < 0 ) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'Data precedente alla data di inizio evento';
			END IF;
			IF (fine_evento IS NOT NULL) THEN
				SELECT DATEDIFF( data, fine_evento ) INTO differenza;
				IF ( differenza > 0 ) THEN
					SIGNAL SQLSTATE '45000'
				   SET MESSAGE_TEXT = 'Data successiva alla dati di fine evento';
			   END IF;
			END IF;
		END"
	);

	$kyssdb->query(
			"CREATE TRIGGER controlloDataTalkIns
			BEFORE INSERT ON {$kyssdb->talk}
			FOR EACH ROW BEGIN
				DECLARE data_inizio DATE;
				DECLARE data_fine DATE;
				SELECT {$kyssdb->eventi}.data_inizio INTO data_inizio
				FROM {$kyssdb->eventi}
				WHERE {$kyssdb->eventi}.ID = NEW.evento;
				SELECT {$kyssdb->eventi}.data_fine INTO data_fine
				FROM {$kyssdb->eventi}
				WHERE {$kyssdb->eventi}.ID = NEW.evento;
				CALL controlloDataTalk(NEW.data, data_inizio, data_fine);
			END"
	);

	$kyssdb->query(
			"CREATE TRIGGER controlloDataTalkUpd
			BEFORE UPDATE ON {$kyssdb->talk}
			FOR EACH ROW BEGIN
				DECLARE data_inizio DATE;
				DECLARE data_fine DATE;
				SELECT {$kyssdb->eventi}.data_inizio INTO data_inizio
				FROM {$kyssdb->eventi}
				WHERE {$kyssdb->eventi}.ID = NEW.evento;
				SELECT {$kyssdb->eventi}.data_fine INTO data_fine
				FROM {$kyssdb->eventi}
				WHERE {$kyssdb->eventi}.ID = NEW.evento;
				CALL controlloDataTalk(NEW.data, data_inizio, data_fine);
			END"
	);

	$kyssdb->query(
		"CREATE PROCEDURE controlloOrarioRiunioni(IN ora_inizio TIME, ora_fine TIME)
		BEGIN
			DECLARE differenza INT;
			SELECT TIMEDIFF( ora_inizio, ora_fine ) INTO differenza;
			IF (differenza > 0) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'Ora inizio successiva ad ora fine';
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloOrarioRiunioniIns
		BEFORE INSERT ON {$kyssdb->riunioni}
		FOR EACH ROW BEGIN
			IF (NEW.ora_inizio IS NOT NULL AND NEW.ora_fine IS NOT NULL) THEN
				CALL controlloOrarioRiunioni(NEW.ora_inizio, NEW.ora_fine);
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloOrarioRiunioniUpd
		BEFORE UPDATE ON {$kyssdb->riunioni}
		FOR EACH ROW BEGIN
			IF (NEW.ora_inizio IS NOT NULL AND NEW.ora_fine IS NOT NULL) THEN
				CALL controlloOrarioRiunioni(NEW.ora_inizio, NEW.ora_fine);
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE PROCEDURE controlloDatePratiche(IN data date, ricezione date)
		BEGIN
			DECLARE differenza INT;
			SELECT DATEDIFF( data, CURRENT_DATE() ) INTO differenza;
			IF (differenza > 0) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'Data della pratica successiva alla data corrente';
			END IF;
			SELECT DATEDIFF( ricezione, CURRENT_DATE() ) INTO differenza;
			IF (differenza > 0) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'Data di ricezione successiva alla data corrente';
			END IF;
			SELECT DATEDIFF( ricezione, data ) INTO differenza;
			IF (differenza < 0) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'Data di ricezione precedente alla data della pratica';
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloDatePraticheIns
		BEFORE INSERT ON {$kyssdb->pratiche}
		FOR EACH ROW BEGIN
			CALL controlloDatePratiche(NEW.data, NEW.ricezione);    
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloDatePraticheUpd
		BEFORE UPDATE ON {$kyssdb->pratiche}
		FOR EACH ROW BEGIN
			CALL controlloDatePratiche(NEW.data, NEW.ricezione);    
		END"
	);

	$kyssdb->query(
		"CREATE PROCEDURE controlloDataLezioni(IN data DATE, data_inizio DATE, data_fine DATE)
		BEGIN 
			DECLARE differenza INT;
			SELECT DATEDIFF( data, data_inizio ) INTO differenza;
			IF (differenza < 0) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'Data lezione precedente alla data di inizio corso';
			END IF;
			IF (data_fine IS NOT NULL) THEN
				SELECT DATEDIFF( data, data_fine ) INTO differenza;
				IF (differenza > 0) THEN
					SIGNAL SQLSTATE '45000'
					SET MESSAGE_TEXT = 'Data lezione successiva alla data di fine corso';
				END IF;
			END IF;
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloDataLezioniIns
		BEFORE INSERT ON {$kyssdb->lezioni}
		FOR EACH ROW BEGIN
			DECLARE data_inizio DATE;
			DECLARE data_fine DATE;
			SELECT {$kyssdb->eventi}.data_inizio INTO data_inizio
			FROM {$kyssdb->eventi}
			WHERE {$kyssdb->eventi}.ID = NEW.corso;
			SELECT {$kyssdb->eventi}.data_fine INTO data_fine
			FROM {$kyssdb->eventi}
			WHERE {$kyssdb->eventi}.ID = NEW.corso;
			CALL controlloDataLezioni(NEW.data, data_inizio, data_fine);
		END"
	);

	$kyssdb->query(
		"CREATE TRIGGER controlloDataLezioniUpd
		BEFORE UPDATE ON {$kyssdb->lezioni}
		FOR EACH ROW BEGIN
			DECLARE data_inizio DATE;
			DECLARE data_fine DATE;
			SELECT {$kyssdb->eventi}.data_inizio INTO data_inizio
			FROM {$kyssdb->eventi}
			WHERE {$kyssdb->eventi}.ID = NEW.corso;
			SELECT {$kyssdb->eventi}.data_fine INTO data_fine
			FROM {$kyssdb->eventi}
			WHERE {$kyssdb->eventi}.ID = NEW.corso;
			CALL controlloDataLezioni(NEW.data, data_inizio, data_fine);
		END"
	);
	
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