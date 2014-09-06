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
			`CAP`				char(5),
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

		"CREATE TABLE IF NOT EXISTS {$kyssdb->errori} (
			`Codice`             int UNSIGNED NOT NULL,
			`Descrizione`        varchar(50) NOT NULL,
			PRIMARY KEY (`Codice`, `Descrizione`)
		) ENGINE = InnoDB",

		"INSERT INTO {$kyssdb->errori}(`Codice`, `Descrizione`) VALUES 
			(1, 'Quota non pagata'),
			(2, 'Carica già ricoperta da qualcuno in queso periodo'),
			(3, 'L`utente ricopre un`altra carica in questo periodo'),
			(4, 'Data di inizio carica successiva alla data di fine'),
			(5, 'Data di nascita successiva alla data corrente'),
			(6, 'Data di inizio successiva a data di fine evento'),
			(7, 'Data precedente alla data di inizio evento'),
			(8, 'Data successiva alla dati di fine evento'),
			(9, 'Ora inizio successiva ad ora fine'),
			(10, 'Data della pratica successiva alla data corrente'),
			(11, 'Data di ricezione successiva alla data corrente'),
			(12, 'Data di ricezione precedente alla data della pratica'),
			(13, 'Data lezione precedente alla data di inizio corso'),
			(14, 'Data lezione successiva alla data di fine corso')",

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
 * Retrieve SQL for creating triggers and procedures.
 *
 * @since  0.13.0
 *
 * @global  kyssdb
 *
 * @return  array Array of SQL code strings.
 */
function get_db_triggers() {
	global $kyssdb;

	$triggers = array(
		"CREATE PROCEDURE controlloIscritti( IN utente INT(20), corso INT(20) )
		BEGIN
			DECLARE quote INT;
			SELECT COUNT(*) INTO quote
			FROM {$kyssdb->movimenti}
			WHERE {$kyssdb->movimenti}.utente = utente AND {$kyssdb->movimenti}.causale = 'quote' AND {$kyssdb->movimenti}.corso = corso;
			IF ( quote = 0 ) THEN
				INSERT INTO Errori VALUES (1, 'Quota non pagata');
			END IF;
		END",

		"CREATE TRIGGER controlloIscrittiIns
		BEFORE INSERT ON {$kyssdb->iscritto}
		FOR EACH ROW BEGIN
			CALL controlloIscritti( NEW.utente, NEW.corso );
		END",

		"CREATE TRIGGER controlloIscrittiUpd
		BEFORE UPDATE ON {$kyssdb->iscritto}
		FOR EACH ROW BEGIN
			CALL controlloIscritti( NEW.utente, NEW.corso );
		END",

		"CREATE PROCEDURE controlloCariche( IN carica VARCHAR(10), utente INT(20), inizio DATE, fine DATE )
		BEGIN
			DECLARE già_assegnata INT;
			DECLARE cariche_attive INT;
			SELECT COUNT(*) INTO già_assegnata
			FROM {$kyssdb->cariche}
			WHERE {$kyssdb->cariche}.carica = carica AND (
				( DATE( {$kyssdb->cariche}.inizio ) < DATE( inizio ) AND DATE( {$kyssdb->cariche}.fine ) > DATE( inizio ) AND DATE( {$kyssdb->cariche}.fine ) < DATE( fine ) )
				OR ( DATE( {$kyssdb->cariche}.inizio ) < DATE( inizio ) AND DATE( {$kyssdb->cariche}.fine ) > DATE( fine ) )
				OR ( DATE( {$kyssdb->cariche}.inizio ) > DATE( inizio ) AND DATE( {$kyssdb->cariche}.inizio ) < DATE( fine ) AND DATE( {$kyssdb->cariche}.fine ) > DATE( fine ) )
				OR ( DATE( {$kyssdb->cariche}.inizio ) > DATE( inizio ) AND DATE( {$kyssdb->cariche}.fine ) < DATE( fine ) )
			);
			IF ( già_assegnata > 0 ) THEN
				INSERT INTO Errori VALUES (2, 'Carica già ricoperta da qualcuno in queso periodo');
			END IF;
			SELECT COUNT(*) INTO cariche_attive
			FROM {$kyssdb->cariche}
			WHERE {$kyssdb->cariche}.utente = utente AND (
				( {$kyssdb->cariche}.fine IS NULL ) 
				OR ( DATE( {$kyssdb->cariche}.inizio ) < DATE( inizio ) AND DATE( {$kyssdb->cariche}.fine ) > DATE( inizio ) AND DATE( {$kyssdb->cariche}.fine ) < DATE( fine ) )
				OR ( DATE( {$kyssdb->cariche}.inizio ) < DATE( inizio ) AND DATE( {$kyssdb->cariche}.fine ) > DATE( fine ) )
				OR ( DATE( {$kyssdb->cariche}.inizio ) > DATE( inizio ) AND DATE( {$kyssdb->cariche}.inizio ) < DATE( fine ) AND DATE( {$kyssdb->cariche}.fine ) > DATE( fine ) )
				OR ( DATE( {$kyssdb->cariche}.inizio ) > DATE( inizio ) AND DATE( {$kyssdb->cariche}.fine ) < DATE( fine ) )
			);
			IF ( cariche_attive > 0 ) THEN
				INSERT INTO Errori VALUES (3, 'L`utente ricopre un`altra carica in questo periodo');
			END IF;
		END",

		"CREATE PROCEDURE controlloDataCarica( IN inizio DATE, fine DATE )
		BEGIN
			IF ( DATE( inizio ) > DATE( fine ) ) THEN
				INSERT INTO Errori VALUES (4, 'Data di inizio carica successiva alla data di fine');
			END IF;
		END",

		"CREATE TRIGGER controlloCaricheIns
		BEFORE INSERT ON {$kyssdb->cariche}
		FOR EACH ROW BEGIN
			IF (NEW.fine IS NULL) THEN
				CALL controlloCariche( NEW.carica, NEW.utente, NEW.inizio, CURRENT_DATE() );
			ELSE
				CALL controlloCariche( NEW.carica, NEW.utente, NEW.inizio, NEW.fine );
				CALL controlloDataCarica( NEW.inizio, NEW.fine );
			END IF;
		END",

		"CREATE TRIGGER controlloCaricheUpd
		BEFORE UPDATE ON {$kyssdb->cariche}
		FOR EACH ROW BEGIN
			IF (NEW.fine IS NULL) THEN
				CALL controlloCariche( NEW.carica, NEW.utente, NEW.inizio, CURRENT_DATE() );
			ELSE
				CALL controlloCariche( NEW.carica, NEW.utente, NEW.inizio, NEW.fine );
				CALL controlloDataCarica( NEW.inizio, NEW.fine );
			END IF;
		END",

		"CREATE PROCEDURE controlloUtenti( IN nato_il DATE )
		BEGIN
			IF ( DATE( nato_il ) > CURRENT_DATE() ) THEN
				INSERT INTO Errori VALUES (5, 'Data di nascita successiva alla data corrente');
			END IF;
		END",

		"CREATE TRIGGER controlloUtentiIns
		BEFORE INSERT ON {$kyssdb->utenti}
		FOR EACH ROW BEGIN
			CALL controlloUtenti( NEW.nato_il );
			SET NEW.codice_fiscale = UPPER( NEW.codice_fiscale );
		END",

		"CREATE TRIGGER controlloUtentiUpd
		BEFORE UPDATE ON {$kyssdb->utenti}
		FOR EACH ROW BEGIN
			CALL controlloUtenti( NEW.nato_il );
			SET NEW.codice_fiscale = UPPER( NEW.codice_fiscale);
		END",

		"CREATE PROCEDURE controlloInizioFineEvento( IN data_inizio DATE, data_fine DATE )
		BEGIN
			IF ( DATE( data_inizio ) > DATE( data_fine ) ) THEN
				INSERT INTO Errori VALUES (6, 'Data di inizio successiva a data di fine evento');
			END IF;
		END",

		"CREATE TRIGGER controlloInizioFineEventoIns
		BEFORE INSERT ON {$kyssdb->eventi}
		FOR EACH ROW BEGIN
			IF ( NEW.data_fine IS NOT NULL ) THEN
				CALL controlloInizioFineEvento( NEW.data_inizio, NEW.data_fine );
			END IF;
		END",

		"CREATE TRIGGER controlloInizioFineEventoUpd
		BEFORE UPDATE ON {$kyssdb->eventi}
		FOR EACH ROW BEGIN
			IF ( NEW.data_fine IS NOT NULL ) THEN
				CALL controlloInizioFineEvento( NEW.data_inizio, NEW.data_fine );
			END IF;
		END",

		"CREATE PROCEDURE controlloDataTalk( IN data DATE, inizio_evento DATE, fine_evento DATE )
		BEGIN
			IF ( DATE( data ) < DATE( inizio_evento ) ) THEN
				INSERT INTO Errori VALUES (7, 'Data precedente alla data di inizio evento');
			END IF;
			IF ( fine_evento IS NOT NULL ) THEN
				IF ( DATE( data ) > DATE( fine_evento ) ) THEN
					INSERT INTO Errori VALUES (8, 'Data successiva alla dati di fine evento');
			   END IF;
			END IF;
		END",

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
			CALL controlloDataTalk( NEW.data, data_inizio, data_fine );
		END",

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
			CALL controlloDataTalk( NEW.data, data_inizio, data_fine );
		END",

		"CREATE PROCEDURE controlloOrarioRiunioni( IN ora_inizio TIME, ora_fine TIME )
		BEGIN
			IF ( TIME( ora_inizio ) > TIME( ora_fine ) ) THEN
				INSERT INTO Errori VALUES (9, 'Ora inizio successiva ad ora fine');
			END IF;
		END",

		"CREATE TRIGGER controlloOrarioRiunioniIns
		BEFORE INSERT ON {$kyssdb->riunioni}
		FOR EACH ROW BEGIN
			IF ( NEW.ora_inizio IS NOT NULL AND NEW.ora_fine IS NOT NULL ) THEN
				CALL controlloOrarioRiunioni( NEW.ora_inizio, NEW.ora_fine );
			END IF;
		END",

		"CREATE TRIGGER controlloOrarioRiunioniUpd
		BEFORE UPDATE ON {$kyssdb->riunioni}
		FOR EACH ROW BEGIN
			IF ( NEW.ora_inizio IS NOT NULL AND NEW.ora_fine IS NOT NULL ) THEN
				CALL controlloOrarioRiunioni( NEW.ora_inizio, NEW.ora_fine );
			END IF;
		END",

		"CREATE PROCEDURE controlloDatePratiche( IN data DATE, ricezione DATE )
		BEGIN
			IF ( DATE( data ) > CURRENT_DATE() ) THEN
				INSERT INTO Errori VALUES (10, 'Data della pratica successiva alla data corrente');
			END IF;
			IF ( DATE( ricezione ) > CURRENT_DATE() ) THEN
				INSERT INTO Errori VALUES (11, 'Data di ricezione successiva alla data corrente');
			END IF;
			IF ( DATE( ricezione ) < DATE( data ) ) THEN
				INSERT INTO Errori VALUES (12, 'Data di ricezione precedente alla data della pratica');
			END IF;
		END",

		"CREATE TRIGGER controlloDatePraticheIns
		BEFORE INSERT ON {$kyssdb->pratiche}
		FOR EACH ROW BEGIN
			CALL controlloDatePratiche( NEW.data, NEW.ricezione );    
		END",

		"CREATE TRIGGER controlloDatePraticheUpd
		BEFORE UPDATE ON {$kyssdb->pratiche}
		FOR EACH ROW BEGIN
			CALL controlloDatePratiche( NEW.data, NEW.ricezione );    
		END",

		"CREATE PROCEDURE controlloDataLezioni( IN data DATE, data_inizio DATE, data_fine DATE )
		BEGIN
			IF ( DATE( data ) < DATE( data_inizio ) ) THEN
				INSERT INTO Errori VALUES (13, 'Data lezione precedente alla data di inizio corso');
			END IF;
			IF ( data_fine IS NOT NULL ) THEN
				IF ( DATE( data ) > DATE( data_fine ) ) THEN
					INSERT INTO Errori VALUES (14, 'Data lezione successiva alla data di fine corso');
				END IF;
			END IF;
		END",

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
			CALL controlloDataLezioni( NEW.data, data_inizio, data_fine );
		END",

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
			CALL controlloDataLezioni( NEW.data, data_inizio, data_fine );
		END"
	);

	return $triggers;
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

	$triggers = get_db_triggers();

	// mysqli::multi_query doesn't support custom delimiters.
	foreach ( $triggers as $trigger ) {
		if ( ! $kyssdb->query( $trigger ) )
			kyss_die( "C'&egrave; stato un errore durante l'installazione: {$kyssdb->error}" );
	}
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