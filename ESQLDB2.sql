DROP DATABASE IF EXISTS ESQLDB2;
CREATE DATABASE dbESQL;
USE ESQLDB2;


-- Tabella Studente
CREATE TABLE Studente (
                          Email VARCHAR(255) PRIMARY KEY,
                          Nome VARCHAR(100) NOT NULL,
                          Cognome VARCHAR(100) NOT NULL,
                          Password varchar(255) not null ,
                          Codice CHAR(16) UNIQUE NOT NULL,
                          Telefono VARCHAR(20),
                          AnnoImmatricolazione INT

);
-- Tabella Docente
CREATE TABLE Docente (
                         Email VARCHAR(255) PRIMARY KEY,
                         Nome VARCHAR(100) NOT NULL,
                         Cognome VARCHAR(100) NOT NULL,
                         Telefono VARCHAR(20),
                         Password varchar(255) not null ,
                         NomeDipartimento VARCHAR(100),
                         NomeCorso VARCHAR(100)
);
-- Tabella Tabella_Esercizio
CREATE TABLE Tabella_Esercizio (
                                   Nome VARCHAR(100) PRIMARY KEY,
                                   Data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                   NumeroRighe INT DEFAULT 0,
                                   EmailDocente VARCHAR(255),
                                   FOREIGN KEY (EmailDocente) REFERENCES Docente(Email) ON DELETE CASCADE
);




-- Tabella Attributi
CREATE TABLE Attributi (
                           Nome VARCHAR(100),
                           NomeTabella VARCHAR(100),
                           Tipo VARCHAR(50),
                           Primario BOOLEAN,
                           PRIMARY KEY (Nome, NomeTabella),
                           FOREIGN KEY (NomeTabella) REFERENCES Tabella_Esercizio(Nome) ON DELETE CASCADE
);
-- Tabella Integrità_Referenziale
CREATE TABLE Integrita_Referenziale (
                                        Id INT AUTO_INCREMENT PRIMARY KEY,
                                        NomeAttributo1 VARCHAR(100),
                                        NomeAttributo2 VARCHAR(100),
                                        Tabella1 VARCHAR(100),
                                        Tabella2 VARCHAR(100),
                                        FOREIGN KEY (NomeAttributo1, Tabella1) REFERENCES Attributi(Nome, NomeTabella) ON DELETE CASCADE,
                                        FOREIGN KEY (NomeAttributo2, Tabella2) REFERENCES Attributi(Nome, NomeTabella) ON DELETE CASCADE
);
CREATE TABLE RIGA (
                      NomeTabella VARCHAR(100),
                      Valori TEXT,
                      PRIMARY KEY(NomeTabella, Valori(255)),
                      FOREIGN KEY (NomeTabella) REFERENCES Tabella_Esercizio(Nome) ON DELETE CASCADE
);
-- Tabella TestValutaRisposta.html
CREATE TABLE Test (
                      Titolo VARCHAR(255) PRIMARY KEY,
                      Foto MEDIUMBLOB,
                      Data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                      VisualizzazioneRisposta BOOLEAN DEFAULT FALSE,
                      EmailDocente VARCHAR(255),
                      FOREIGN KEY (EmailDocente) REFERENCES Docente(Email) ON DELETE CASCADE
);

-- Tabella Quesito
CREATE TABLE Quesito (
                         Id INT AUTO_INCREMENT PRIMARY KEY,
                         TitoloTest VARCHAR(255),
                         NumRisposta INT,
                         Difficoltà ENUM('Basso', 'Medio', 'Alto'),
                         Descrizione TEXT,
                         TipoQuesito ENUM('RispostaChiusa', 'Codice'),
                         FOREIGN KEY (TitoloTest) REFERENCES Test(Titolo) ON DELETE CASCADE
);

-- Tabella Riferimento
CREATE TABLE Riferimento (
                             NomeTabellaEsercizio VARCHAR(100),
                             IdQuesito INT,
                             TitoloTest VARCHAR(255),
                             PRIMARY KEY (NomeTabellaEsercizio, IdQuesito, TitoloTest),
                             FOREIGN KEY (NomeTabellaEsercizio) REFERENCES Tabella_Esercizio(Nome) ON DELETE CASCADE,
                             FOREIGN KEY (IdQuesito) REFERENCES Quesito(Id) ON DELETE CASCADE,
                             FOREIGN KEY (TitoloTest) REFERENCES Test(Titolo) ON DELETE CASCADE
);


-- Tabella Soluzione
CREATE TABLE Soluzione (
                           Id INT AUTO_INCREMENT PRIMARY KEY,
                           Sketch TEXT,
                           IdQuesito INT,
                           TitoloTest VARCHAR(255),
                           NomeTabellaOutput VARCHAR(100),
                           FOREIGN KEY (IdQuesito) REFERENCES Quesito(Id) ON DELETE CASCADE,
                           FOREIGN KEY (TitoloTest) REFERENCES Test(Titolo) ON DELETE CASCADE
);
-- Tabella Opzione
CREATE TABLE Opzione (
                         Numerazione INT,
                         IdQuesito INT,
                         TitoloTest VARCHAR(255),
                         OpzioneCorretta BOOLEAN,
                         Testo TEXT,
                         PRIMARY KEY (Numerazione, IdQuesito, TitoloTest),
                         FOREIGN KEY (IdQuesito) REFERENCES Quesito(Id) ON DELETE CASCADE,
                         FOREIGN KEY (TitoloTest) REFERENCES Test(Titolo) ON DELETE CASCADE
);
-- Tabella RispostaCodice
CREATE TABLE RispostaCodice (
                                Id INT AUTO_INCREMENT PRIMARY KEY,
                                EmailStudente VARCHAR(255),
                                Esito BOOLEAN,
                                Data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                TestoRisposta TEXT,
                                IdQuesito INT,
                                TitoloTest VARCHAR(255),
                                FOREIGN KEY (EmailStudente) REFERENCES Studente(Email) ON DELETE CASCADE,
                                FOREIGN KEY (IdQuesito) REFERENCES Quesito(Id) ON DELETE CASCADE,
                                FOREIGN KEY (TitoloTest) REFERENCES Test(Titolo) ON DELETE CASCADE
);
-- Tabella RispostaChiusa
CREATE TABLE RispostaChiusa (
                                Id INT AUTO_INCREMENT PRIMARY KEY,
                                EmailStudente VARCHAR(255),
                                Data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                Esito BOOLEAN,
                                NumeroOpzione INT,
                                IdQuesito INT,
                                TitoloTest VARCHAR(255),
                                FOREIGN KEY (EmailStudente) REFERENCES Studente(Email) ON DELETE CASCADE,
                                -- FOREIGN KEY (TitoloTest) REFERENCES TestValutaRisposta.html(Titolo) ON DELETE CASCADE,
                                FOREIGN KEY (NumeroOpzione, IdQuesito, TitoloTest) REFERENCES Opzione(Numerazione, IdQuesito, TitoloTest) ON DELETE CASCADE
);



-- Tabella Messaggio
CREATE TABLE Messaggio (
                           Titolo VARCHAR(255),
                           Data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                           TitoloTest VARCHAR(255),
                           Testo TEXT,
                           EmailStudenteMittente VARCHAR(255),
                           EmailDocenteMittente VARCHAR(255),
                           EmailDocenteDestinatario VARCHAR(255),
                           PRIMARY KEY (Titolo, Data, TitoloTest),
                           FOREIGN KEY (TitoloTest) REFERENCES Test(Titolo) ON DELETE CASCADE,
                           FOREIGN KEY (EmailStudenteMittente) REFERENCES Studente(Email) ON DELETE CASCADE,
                           FOREIGN KEY (EmailDocenteMittente) REFERENCES Docente(Email) ON DELETE CASCADE,
                           FOREIGN KEY (EmailDocenteDestinatario) REFERENCES Docente(Email) ON DELETE CASCADE
);

-- Tabella Destinatario
CREATE TABLE Destinatario (
                              EmailStudente VARCHAR(255) NOT NULL,
                              TitoloMessaggio VARCHAR(255) NOT NULL,
                              TitoloTest VARCHAR(255) NOT NULL,
                              Data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              PRIMARY KEY (EmailStudente, TitoloMessaggio, TitoloTest, Data),
                              FOREIGN KEY (EmailStudente) REFERENCES Studente(Email) ON DELETE CASCADE,
                              FOREIGN KEY (TitoloMessaggio) REFERENCES Messaggio(Titolo) ON DELETE CASCADE,
                              FOREIGN KEY (TitoloTest) REFERENCES Test(Titolo) ON DELETE CASCADE
);

CREATE TABLE Completamento (
                               TitoloTest VARCHAR(255),
                               EmailStudente  VARCHAR(255),
                               DataPrimaRisposta TIMESTAMP,
                               DataUltimaRisposta TIMESTAMP,
                               Stato ENUM('Aperto', 'InCompletamento', 'Concluso') DEFAULT 'Aperto',
                               PRIMARY KEY (TitoloTest, EmailStudente),
                               FOREIGN KEY (TitoloTest) REFERENCES Test(Titolo) ON DELETE CASCADE,
                               FOREIGN KEY (EmailStudente) REFERENCES Studente(Email) ON DELETE CASCADE
);




DELIMITER //

CREATE PROCEDURE RegistraUtente(
    IN email VARCHAR(255),
    IN nome VARCHAR(100),
    IN cognome VARCHAR(100),
    IN password VARCHAR(255), -- Password aggiunta
    IN codice CHAR(16),
    IN telefono VARCHAR(20),
    IN annoImmatricolazione INT,
    IN tipoUtente ENUM('Studente', 'Docente'),
    IN nomeDipartimento VARCHAR(100),
    IN nomeCorso VARCHAR(100)
)
BEGIN
    IF tipoUtente = 'Studente' THEN
        INSERT INTO Studente (Email, Nome, Cognome, Password, Codice, Telefono, AnnoImmatricolazione)
        VALUES (email, nome, cognome, password, codice, telefono, annoImmatricolazione);
    ELSEIF tipoUtente = 'Docente' THEN
        INSERT INTO Docente (Email, Nome, Cognome, Password, Telefono, NomeDipartimento, NomeCorso)
        VALUES (email, nome, cognome, password, telefono, nomeDipartimento, nomeCorso);
    END IF;
END //

DELIMITER ;


-- Procedure per la creazione di una tabella
DELIMITER $$
-- Creazione tabella esercizio

DELIMITER //

CREATE PROCEDURE CreaTabellaEsercizio(
    IN p_Nome VARCHAR(100),
    IN p_EmailDocente VARCHAR(255)
)
BEGIN
    DECLARE numero_righe INT DEFAULT 0;

    -- Controlla se la tabella di esercizio esiste già
    IF EXISTS (SELECT 1 FROM Tabella_Esercizio WHERE Nome = p_Nome) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La tabella di esercizio esiste già.';
    ELSE
        -- Crea una nuova tabella di esercizio
        INSERT INTO Tabella_Esercizio (Nome, EmailDocente, NumeroRighe )
        VALUES (p_Nome, p_EmailDocente, numero_righe);
    END IF;
END //

DELIMITER ;
 -- creazione attributi tabella esercizi
DELIMITER //

CREATE PROCEDURE AggiungiAttributo(
    IN p_NomeAttributo VARCHAR(100),
    IN p_NomeTabella VARCHAR(100),
    IN p_Tipo VARCHAR(50),
    IN p_Primario BOOLEAN
)
BEGIN
    -- Controlla se l'attributo già esiste per la tabella specificata
    IF EXISTS (SELECT 1 FROM Attributi WHERE Nome = p_NomeAttributo AND NomeTabella = p_NomeTabella) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'L\'attributo esiste già nella tabella specificata.';
    ELSE
        -- Aggiungi l'attributo alla tabella
        INSERT INTO Attributi (Nome, NomeTabella, Tipo, Primario)
        VALUES (p_NomeAttributo, p_NomeTabella, p_Tipo, p_Primario);

        -- Se l'attributo è primario, incrementa il numero delle righe della tabella esercizio
        IF p_Primario THEN
            UPDATE Tabella_Esercizio
            SET NumeroRighe = NumeroRighe + 1
            WHERE Nome = p_NomeTabella;
        END IF;
    END IF;
END //

DELIMITER ;


DELIMITER //
-- procedure per l'aggiunta dei vincoli di integrità nella tabella creata
CREATE PROCEDURE AggiungiVincoloIntegrita(
    IN p_NomeAttributo1 VARCHAR(100),
    IN p_NomeAttributo2 VARCHAR(100),
    IN p_Tabella1 VARCHAR(100),
    IN p_Tabella2 VARCHAR(100)
)
BEGIN
    -- Controlla se i vincoli di integrità già esistono
    IF EXISTS (SELECT 1 FROM Integrita_Referenziale
               WHERE NomeAttributo1 = p_NomeAttributo1 AND Tabella1 = p_Tabella1
                 AND NomeAttributo2 = p_NomeAttributo2 AND Tabella2 = p_Tabella2) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Il vincolo di integrità esiste già.';
    ELSE
        -- Aggiungi il vincolo di integrità referenziale
        INSERT INTO Integrita_Referenziale (NomeAttributo1, NomeAttributo2, Tabella1, Tabella2)
        VALUES (p_NomeAttributo1, p_NomeAttributo2, p_Tabella1, p_Tabella2);
    END IF;
END //

DELIMITER ;

DELIMITER //
CREATE PROCEDURE CreaEsercizio (
    IN p_nome VARCHAR(100),
    IN p_emailDocente VARCHAR(255)
)
BEGIN
    DECLARE esiste INT;

    -- Controlla se esiste già un esercizio con lo stesso nome
    SELECT COUNT(*) INTO esiste FROM Tabella_Esercizio WHERE Nome = p_nome;

    IF esiste > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Esercizio già esistente.';
    ELSE
        INSERT INTO Tabella_Esercizio (Nome, EmailDocente)
        VALUES (p_nome, p_emailDocente);
    END IF;
END //

drop procedure CreaTest;

DELIMITER ;
DELIMITER //
CREATE PROCEDURE CreaTest(
    IN titolo VARCHAR(255),
    IN foto MEDIUMBLOB,
    IN emailDocente VARCHAR(255),
    OUT result INT
)
BEGIN
    -- Gestore per errori di duplicazione
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            SET result = 0; -- Indica errore di duplicazione o altro errore SQL
            ROLLBACK;
        END;

    START TRANSACTION;

    -- Inserisci il nuovo test senza controlli
    IF foto IS NULL THEN
        INSERT INTO Test (Titolo, Foto, Data, VisualizzazioneRisposta, EmailDocente)
        VALUES (titolo, NULL, CURRENT_TIMESTAMP, FALSE, emailDocente);
    ELSE
        INSERT INTO Test (Titolo, Foto, Data, VisualizzazioneRisposta, EmailDocente)
        VALUES (titolo, foto, CURRENT_TIMESTAMP, FALSE, emailDocente);
    END IF;

    -- Se l’inserimento è riuscito, imposta il risultato a 1
    SET result = 1;
    COMMIT;
END //
DELIMITER ;


DELIMITER ;



-- Procedura per l'aggiunta di un quesito al test
DELIMITER //
CREATE PROCEDURE AggiungiQuesito(
    IN titoloTest VARCHAR(255),
    IN descrizione TEXT,
    IN difficolta ENUM('Basso', 'Medio', 'Alto'),
    IN tipoQuesito ENUM('RispostaChiusa', 'Codice'),
    OUT result INT
)
BEGIN
    DECLARE testExists INT;
    DECLARE numRisposta INT DEFAULT 0;

    -- Verifica che il test esista
    SELECT COUNT(*) INTO testExists FROM Test WHERE Titolo = titoloTest;

    IF testExists > 0 THEN
        -- Calcola il numero progressivo del quesito
        SET numRisposta = (SELECT IFNULL(MAX(NumRisposta), 0) + 1 FROM Quesito WHERE TitoloTest = titoloTest);

        -- Inserisce il quesito con il numero progressivo calcolato
        INSERT INTO Quesito (TitoloTest, NumRisposta, Difficoltà, Descrizione, TipoQuesito)
        VALUES (titoloTest, numRisposta, difficolta, descrizione, tipoQuesito);

        SET result = 1;  -- Quesito aggiunto con successo
    ELSE
        SET result = 0;  -- TestValutaRisposta.html non trovato
    END IF;
END //

DELIMITER ;

-- Procedura che crea riferimento tra il test ed il quesito

DELIMITER //

CREATE PROCEDURE AggiungiRiferimento(
    IN idQuesito INT,
    IN titoloTest VARCHAR(255),
    IN nomeTabellaEsercizio VARCHAR(100),
    OUT result INT
)
BEGIN
    DECLARE quesitoExists INT;
    DECLARE tabellaExists INT;

    -- Verifica che il quesito e la tabella di esercizio esistano
    SELECT COUNT(*) INTO quesitoExists FROM Quesito WHERE Id = idQuesito AND TitoloTest = titoloTest;
    SELECT COUNT(*) INTO tabellaExists FROM Tabella_Esercizio WHERE Nome = nomeTabellaEsercizio;

    IF quesitoExists > 0 AND tabellaExists > 0 THEN
        -- Inserisce il riferimento
        INSERT INTO Riferimento (NomeTabellaEsercizio, IdQuesito, TitoloTest)
        VALUES (nomeTabellaEsercizio, idQuesito, titoloTest);

        SET result = 1;  -- Riferimento aggiunto con successo
    ELSE
        SET result = 0;  -- Quesito o tabella di esercizio non trovati
    END IF;
END //

DELIMITER ;

-- Procedure per l'inserimento della soluzione per un test
DELIMITER //

CREATE PROCEDURE AggiungiSoluzione(
    IN sketch VARCHAR(255),
    IN idQuesito INT,
    IN titoloTest VARCHAR(255),
    IN nomeTabellaOutput VARCHAR(100)
)
BEGIN
    INSERT INTO Soluzione (Sketch, IdQuesito, TitoloTest, NomeTabellaOutput)
    VALUES (sketch, idQuesito, titoloTest, nomeTabellaOutput);
END //

DELIMITER ;


DELIMITER //
CREATE PROCEDURE InserisciRispostaCodice(
    IN emailStudente VARCHAR(255),
    IN idQuesito INT,
    IN titoloTest VARCHAR(255),
    IN testoRisposta TEXT
)
BEGIN
    -- Inserisce la risposta nel database, con Esito NULL
    INSERT INTO RispostaCodice (EmailStudente, Esito, Data, TestoRisposta, IdQuesito, TitoloTest)
    VALUES (emailStudente, NULL, CURRENT_TIMESTAMP, testoRisposta, idQuesito, titoloTest);
END //
DELIMITER ;
;

-- Procedura per inserire una risposta a quesito chiuso
DELIMITER //

CREATE PROCEDURE InserisciRispostaChiusa(
    IN emailStudente VARCHAR(255),
    IN dataRisposta TIMESTAMP,
    IN esito BOOLEAN,
    IN numeroOpzione INT,
    IN idQuesito INT,
    IN titoloTest VARCHAR(255)
)
BEGIN
    INSERT INTO RispostaChiusa (EmailStudente, Data, Esito, NumeroOpzione, IdQuesito, TitoloTest)
    VALUES (emailStudente, dataRisposta, esito, numeroOpzione, idQuesito, titoloTest);
END //

DELIMITER ;

DESCRIBE RispostaCodice;


    /* Stored Procedure per Visualizzare i TestValutaRisposta.html Disponibili per un Utente */
DELIMITER //

CREATE PROCEDURE VisualizzaTestDisponibili()
BEGIN
    SELECT Titolo, Data, EmailDocente, VisualizzazioneRisposta
    FROM Test;
END //

DELIMITER //

CREATE PROCEDURE AggiungiOpzione(
    IN idQuesito INT,
    IN titoloTest VARCHAR(255),
    IN opzioneCorretta BOOLEAN,
    IN testo TEXT
)
BEGIN
    -- Inserisce la nuova opzione calcolando la numerazione in modo incrementale
    INSERT INTO Opzione (Numerazione, IdQuesito, TitoloTest, OpzioneCorretta, Testo)
    SELECT IFNULL(MAX(Numerazione), 0) + 1, idQuesito, titoloTest, opzioneCorretta, testo
    FROM Opzione
    WHERE IdQuesito = idQuesito AND TitoloTest = titoloTest;
END //

DELIMITER ;
-- trigger che prende tutti i testi di tipo quesito
DELIMITER //

CREATE PROCEDURE VisualizzaQuesitoChiuso()
BEGIN
    SELECT Quesito.Id, Quesito.TitoloTest, Quesito.Descrizione
    FROM Quesito
    WHERE TipoQuesito = 'RispostaChiusa';
END //

CREATE PROCEDURE VisualizzaQuesitoCodice()
BEGIN
    SELECT Quesito.Id, Quesito.TitoloTest, Quesito.Descrizione
    FROM Quesito
    WHERE TipoQuesito = 'Codice';
END //

DELIMITER ;

-- Procedure che consenti di inserire un messagio tra un studenti e un docente----------
DELIMITER //
CREATE PROCEDURE InviaMessaggioStudente(
    IN titolo VARCHAR(255),
    IN titoloTest VARCHAR(255),
    IN testo TEXT,
    IN emailStudenteMittente VARCHAR(255),
    IN emailDocenteDestinatario VARCHAR(255)
)
BEGIN
    -- Verifica che lo studente e il docente esistano
    IF (SELECT COUNT(*) FROM Studente WHERE Email = emailStudenteMittente) = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Studente non valido.';
    END IF;
    IF (SELECT COUNT(*) FROM Docente WHERE Email = emailDocenteDestinatario) = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Docente non valido.';
    END IF;

    -- Inserisce il messaggio
    INSERT INTO Messaggio (Titolo, Data, TitoloTest, Testo, EmailStudenteMittente, EmailDocenteDestinatario)
    VALUES (titolo, CURRENT_TIMESTAMP, titoloTest, testo, emailStudenteMittente, emailDocenteDestinatario);

    -- Aggiunge il docente come destinatario
    INSERT INTO Destinatario (EmailStudente, TitoloMessaggio, TitoloTest, Data)
    VALUES (emailStudenteMittente, titolo, titoloTest, CURRENT_TIMESTAMP);
END //
DELIMITER ;


DELIMITER //
CREATE PROCEDURE InviaMessaggioDocente(
    IN titolo VARCHAR(255),
    IN titoloTest VARCHAR(255),
    IN testo TEXT,
    IN emailDocenteMittente VARCHAR(255)
)
BEGIN
    -- Verifica che il docente esista
    IF (SELECT COUNT(*) FROM Docente WHERE Email = emailDocenteMittente) = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Docente non valido.';
    END IF;

    -- Inserisce il messaggio
    INSERT INTO Messaggio (Titolo, Data, TitoloTest, Testo, EmailDocenteMittente, EmailDocenteDestinatario)
    VALUES (titolo, CURRENT_TIMESTAMP, titoloTest, testo, emailDocenteMittente, NULL);

    -- Aggiunge ogni studente come destinatario del messaggio
    INSERT INTO Destinatario (EmailStudente, TitoloMessaggio, TitoloTest, Data)
    SELECT Email, titolo, titoloTest, CURRENT_TIMESTAMP FROM Studente;
END //
DELIMITER ;





-- Visualizza risposta studente di un determinato test -----
DELIMITER //
CREATE PROCEDURE VisualizzaRisposteStudente(
    IN emailStudente VARCHAR(255),
    IN titoloTest VARCHAR(255)
)
BEGIN
    SELECT R.Id, R.Data, R.Esito, R.TestoRisposta, Q.TitoloTest, Q.Id
    FROM RispostaCodice R
             JOIN Quesito Q ON R.IdQuesito = Q.Id
    WHERE R.EmailStudente = emailStudente AND Q.TitoloTest = titoloTest;
END //
DELIMITER ;

-- Questa procedura visualizza tutte le opzioni di risposta per un quesito di tipo "RispostaChiusa".---
DELIMITER //
CREATE PROCEDURE VisualizzaOpzioniQuesito(
    IN idQuesito INT,
    IN titoloTest VARCHAR(255)
)
BEGIN
    SELECT Numerazione, Testo, OpzioneCorretta
    FROM Opzione
    WHERE IdQuesito = idQuesito AND TitoloTest = titoloTest;
END //
DELIMITER ;


 -- Cambia stato di un test quando deve essere aggiornato manualmente --
DELIMITER //
CREATE PROCEDURE AggiornaStatoCompletamentoTest(
    IN emailStudente VARCHAR(255),
    IN titoloTest VARCHAR(255),
    IN stato ENUM('InCorso', 'Completato', 'NonIniziato')
)
BEGIN
    UPDATE Completamento
    SET Stato = stato
    WHERE EmailStudente = emailStudente AND TitoloTest = titoloTest;
END //
DELIMITER ;


  -- Procedure per la visualizzazione di tutte le tabelle
/*DELIMITER //
CREATE PROCEDURE VisualizzaTabelle()
BEGIN
    SELECT Nome, Data FROM Tabella_Esercizio;
END //
DELIMITER ;
  drop procedure CalcolaEsitoTest;
DELIMITER //

CREATE PROCEDURE CalcolaEsitoTest(
    IN emailStudente VARCHAR(255),
    IN titoloTest VARCHAR(255)
)
BEGIN

    SELECT
        r.IdQuesito,
        r.NumeroOpzione AS OpzioneScelta,
        o.OpzioneCorretta,
        CASE WHEN o.OpzioneCorretta = 1 THEN 'Corretto' ELSE 'Errato' END AS Esito,
        o.Testo AS TestoRisposta
    FROM RispostaChiusa r
             JOIN Opzione o ON r.NumeroOpzione = o.Numerazione
        AND r.IdQuesito = o.IdQuesito
        AND r.TitoloTest = o.TitoloTest
    WHERE r.EmailStudente = emailStudente
      AND r.TitoloTest = titoloTest;

END //

DELIMITER ;
*/

DELIMITER $$
CREATE PROCEDURE CalcolaEsitoTest(
    IN emailStudente VARCHAR(255),
    IN titoloTest VARCHAR(255)
)
BEGIN
    -- Aggiorna il campo Esito nella tabella RispostaChiusa
    UPDATE RispostaChiusa r
        JOIN Opzione o
        ON r.NumeroOpzione = o.Numerazione
            AND r.IdQuesito = o.IdQuesito
            AND r.TitoloTest = o.TitoloTest
    SET r.Esito = (o.OpzioneCorretta = 1)
    WHERE r.EmailStudente = emailStudente
      AND r.TitoloTest = titoloTest;

    -- Se necessario, seleziona i risultati aggiornati per conferma
    SELECT
        r.IdQuesito,
        r.NumeroOpzione AS OpzioneScelta,
        o.OpzioneCorretta,
        CASE WHEN o.OpzioneCorretta = 1 THEN 'Corretto' ELSE 'Errato' END AS Esito,
        o.Testo AS TestoRisposta
    FROM RispostaChiusa r
             JOIN Opzione o ON r.NumeroOpzione = o.Numerazione
        AND r.IdQuesito = o.IdQuesito
        AND r.TitoloTest = o.TitoloTest
    WHERE r.EmailStudente = emailStudente
      AND r.TitoloTest = titoloTest;
END$$

DELIMITER ;



DELIMITER //

CREATE PROCEDURE CalcolaEsitiRisposte()
BEGIN
    -- Variabili per iterare sui risultati
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id INT;
    DECLARE v_idQuesito INT;
    DECLARE v_titoloTest VARCHAR(255);
    DECLARE v_testoRisposta TEXT;
    DECLARE v_sketch TEXT;
    DECLARE v_esito BOOLEAN;

    -- Cursore per iterare su tutte le righe di RispostaCodice
    DECLARE cur CURSOR FOR
        SELECT Id, IdQuesito, TitoloTest, TestoRisposta
        FROM RispostaCodice;

    -- Gestore di uscita dal ciclo
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Apriamo il cursore
    OPEN cur;

    -- Loop per ciascuna risposta di RispostaCodice
    read_loop: LOOP
        -- Leggiamo i dati dal cursore
        FETCH cur INTO v_id, v_idQuesito, v_titoloTest, v_testoRisposta;

        -- Verifichiamo se abbiamo finito
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Recuperiamo la soluzione corrispondente
        SELECT Sketch INTO v_sketch
        FROM Soluzione
        WHERE IdQuesito = v_idQuesito AND TitoloTest = v_titoloTest
        LIMIT 1;

        -- Confrontiamo la risposta con la soluzione
        SET v_esito = (v_testoRisposta = v_sketch);

        -- Aggiorniamo l'esito nella tabella RispostaCodice
        UPDATE RispostaCodice
        SET Esito = v_esito
        WHERE Id = v_id;
    END LOOP;

    -- Chiudiamo il cursore
    CLOSE cur;
END //

DELIMITER ;










-- ----------------------------------------------------------------- Trigger ------------------------------------------------------------------------------------------
DELIMITER //
-- Trigger per inserimento su RispostaCodice
CREATE TRIGGER Trigger_Completamento_InserisciCodice
    AFTER INSERT ON RispostaCodice
    FOR EACH ROW
BEGIN
    -- Inserisce una nuova riga in Completamento se non esiste già per questo studente e test
    IF (SELECT COUNT(*) FROM Completamento
        WHERE TitoloTest = NEW.TitoloTest AND EmailStudente = NEW.EmailStudente) = 0 THEN
        INSERT INTO Completamento (TitoloTest, EmailStudente, DataPrimaRisposta, DataUltimaRisposta, Stato)
        VALUES (NEW.TitoloTest, NEW.EmailStudente, NEW.Data, NEW.Data, 'Aperto');
    ELSE
        -- Aggiorna DataUltimaRisposta se già esiste
        UPDATE Completamento
        SET DataUltimaRisposta = NEW.Data
        WHERE TitoloTest = NEW.TitoloTest AND EmailStudente = NEW.EmailStudente;
    END IF;
END //

-- Trigger per inserimento su RispostaChiusa
CREATE TRIGGER Trigger_Completamento_InserisciChiusa
    AFTER INSERT ON RispostaChiusa
    FOR EACH ROW
BEGIN
    IF (SELECT COUNT(*) FROM Completamento
        WHERE TitoloTest = NEW.TitoloTest AND EmailStudente = NEW.EmailStudente) = 0 THEN
        INSERT INTO Completamento (TitoloTest, EmailStudente, DataPrimaRisposta, DataUltimaRisposta, Stato)
        VALUES (NEW.TitoloTest, NEW.EmailStudente, NEW.Data, NEW.Data, 'Aperto');
    ELSE
        UPDATE Completamento
        SET DataUltimaRisposta = NEW.Data
        WHERE TitoloTest = NEW.TitoloTest AND EmailStudente = NEW.EmailStudente;
    END IF;
END //

DELIMITER ;







-- Questo trigger imposta lo stato del test su "InCompletamento" per uno studente quando viene inserita una risposta chiusa ----
DELIMITER //
CREATE TRIGGER Trigger_InCompletamento
    AFTER INSERT ON RispostaChiusa
    FOR EACH ROW
BEGIN
    -- Controlla se il test per questo studente è in stato "NonIniziato" nella tabella `Completamento`
    IF EXISTS (
        SELECT 1 FROM Completamento
        WHERE TitoloTest = NEW.TitoloTest
          AND EmailStudente = NEW.EmailStudente
          AND Stato = 'Aperto'
    ) THEN
        -- Aggiorna lo stato e imposta la data della prima risposta
        UPDATE Completamento
        SET Stato = 'InCompletamento',
            DataPrimaRisposta = NEW.Data
        WHERE TitoloTest = NEW.TitoloTest
          AND EmailStudente = NEW.EmailStudente;
    END IF;
END //

DELIMITER ;

-- Questo trigger imposta lo stato del test su "InCompletamento" per uno studente quando viene inserita una risposta codice  ----
DELIMITER //

CREATE TRIGGER Trigger_InCompletamentoCodice
    AFTER INSERT ON RispostaCodice
    FOR EACH ROW
BEGIN
    -- Controlla se il test per questo studente è in stato "NonIniziato" nella tabella `Completamento`
    IF EXISTS (
        SELECT 1 FROM Completamento
        WHERE TitoloTest = NEW.TitoloTest
          AND EmailStudente = NEW.EmailStudente
          AND Stato = 'Aperto'
    ) THEN
        -- Aggiorna lo stato e imposta la data della prima risposta
        UPDATE Completamento
        SET Stato = 'InCompletamento',
            DataPrimaRisposta = NEW.Data
        WHERE TitoloTest = NEW.TitoloTest
          AND EmailStudente = NEW.EmailStudente;
    END IF;
END //

DELIMITER ;

-- trigger per settare lo stato di un test come concluso, qaundo il docente cambia la visualizzazione
DELIMITER //

CREATE TRIGGER Trigger_Concluso_TuttiStudenti
    AFTER UPDATE ON Test
    FOR EACH ROW
BEGIN
    IF NEW.VisualizzazioneRisposta = TRUE THEN
        UPDATE Completamento
        SET Stato = 'Concluso'
            -- DataUltimaRisposta = CURRENT_TIMESTAMP
        WHERE TitoloTest = NEW.Titolo;
    END IF;
END //

DELIMITER ;




-- trigger che mette come concluso tutti i testi che hanno le risposte correte--
DELIMITER //

CREATE TRIGGER Trigger_ConcludiTest
    AFTER INSERT ON RispostaCodice
    FOR EACH ROW
BEGIN
    DECLARE risposte_totali INT;
    DECLARE risposte_date INT;
    DECLARE risposte_corrette INT;

    -- Conta tutti i quesiti nel test corrente
    SELECT COUNT(*) INTO risposte_totali
    FROM Quesito
    WHERE TitoloTest = NEW.TitoloTest;

    -- Conta tutte le risposte fornite dallo studente (sia chiuse che di codice)
    SELECT COUNT(DISTINCT IdQuesito) INTO risposte_date
    FROM (
             SELECT IdQuesito FROM RispostaCodice
             WHERE EmailStudente = NEW.EmailStudente AND TitoloTest = NEW.TitoloTest
             UNION ALL
             SELECT IdQuesito FROM RispostaChiusa
             WHERE EmailStudente = NEW.EmailStudente AND TitoloTest = NEW.TitoloTest
         ) AS RisposteDate;

    -- Conta tutte le risposte corrette dallo studente
    SELECT COUNT(DISTINCT IdQuesito) INTO risposte_corrette
    FROM (
             SELECT IdQuesito FROM RispostaCodice
             WHERE EmailStudente = NEW.EmailStudente AND TitoloTest = NEW.TitoloTest AND Esito = TRUE
             UNION ALL
             SELECT IdQuesito FROM RispostaChiusa
             WHERE EmailStudente = NEW.EmailStudente AND TitoloTest = NEW.TitoloTest AND Esito = TRUE
         ) AS RisposteCorrette;

    -- Concludi il test solo se:
    -- (i) il numero di risposte date equivale al numero di quesiti nel test
    -- (ii) il numero di risposte corrette equivale al numero di quesiti nel test
    IF risposte_totali = risposte_date AND risposte_totali = risposte_corrette THEN
        UPDATE Completamento
        SET Stato = 'Concluso', DataUltimaRisposta = NEW.Data
        WHERE TitoloTest = NEW.TitoloTest AND EmailStudente = NEW.EmailStudente;
    END IF;
END //

DELIMITER ;






-- Trigger per Incrementare numrisposte al Aggiunta di una Nuova Risposta---------------

CREATE TRIGGER IncrementaNumRisposte
    AFTER INSERT ON RispostaChiusa
    FOR EACH ROW
BEGIN
    UPDATE Quesito
    SET NumRisposta = NumRisposta + 1
    WHERE Id = NEW.IdQuesito AND TitoloTest = NEW.TitoloTest;
END;
CREATE TRIGGER IncrementaNumRisposteCodice
    AFTER INSERT ON RispostaCodice
    FOR EACH ROW
BEGIN
    UPDATE Quesito
    SET NumRisposta = NumRisposta + 1
    WHERE Id = NEW.IdQuesito AND TitoloTest = NEW.TitoloTest;
END;


-- --------------------------------------------------Statisctiche----------------
-- Questa vista conta il numero di test completati per ciascuno studente.
CREATE VIEW Classifica_Test_Completati AS
SELECT Codice,
       COUNT(*) AS NumeroTestCompletati
FROM Completamento, Studente
WHERE Stato = 'Concluso' and Email=EmailStudente
GROUP BY Codice
ORDER BY NumeroTestCompletati DESC;


-- Questa vista mostra la percentuale di risposte corrette inserite da ogni studente.
    CREATE VIEW Classifica_Risposte_Corrette AS
SELECT Codice,
     (SUM(IF(Esito = TRUE, 1, 0)) / COUNT(*)) * 100 AS PercentualeCorrette
 FROM (
        SELECT Codice, Esito FROM RispostaChiusa, Studente
                  where EmailStudente= Email
         UNION ALL
        SELECT Codice, Esito FROM RispostaCodice,Studente
                         where Email=EmailStudente
   ) AS Risposte
 GROUP BY Codice
ORDER BY PercentualeCorrette DESC;


-- Questa vista conta il numero di risposte fornite per ciascun quesito, indipendentemente dalla correttezza.

CREATE VIEW Classifica_Quesiti AS
SELECT IdQuesito,
       TitoloTest,
       COUNT(*) AS NumeroRisposte
FROM (
         SELECT IdQuesito, TitoloTest FROM RispostaChiusa
         UNION ALL
         SELECT IdQuesito, TitoloTest FROM RispostaCodice
     ) AS Risposte
GROUP BY IdQuesito, TitoloTest
ORDER BY NumeroRisposte DESC;


DELIMITER //

CREATE PROCEDURE OttieniClassificaTestCompletati()
BEGIN
    SELECT * FROM Classifica_Test_Completati;
END //

DELIMITER ;




