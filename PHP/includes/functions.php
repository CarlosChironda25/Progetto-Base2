<?php
// includes/functions.php
include_once '../ESQLDB2.php';
require_once '../Web/ControllerMongoDBLogger.php';

// functions.php


function registerUser($email, $password, $nome, $cognome, $telefono,$tipoUtente, $annoImmatricolazione = null, $nomeDipartimento = null, $nomeCorso = null, $codice = null): bool
{
    global $conn; // Assicurati che ci sia una connessione $conn al database
    $logger = new ControllerMongoDBLogger();

    // Hash della password per sicurezza
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Prepara la query per chiamare la procedura `RegistraUtente`
        $stmt = $conn->prepare("CALL RegistraUtente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssssisss",
            $email,
            $nome,
            $cognome,
            $hashedPassword,
            $codice,
            $telefono,
            $annoImmatricolazione,
            $tipoUtente,
            $nomeDipartimento,
            $nomeCorso
        );
        $logger->logEvent($email, 'Account creato con sucesso. info ','');

        $stmt->execute();
        $stmt->close();

        return true;
    } catch (Exception $e) {
        // Gestione dell'errore, per esempio loggare l'errore
        error_log("Errore durante la registrazione: " . $e->getMessage());
        echo "Errore durante la registrazione: " . $e->getMessage(); // Debugging
        return false;    }
}



// Funzione per autenticare l'utente
function loginUser($email, $password) {
    global $conn;
    $logger = new ControllerMongoDBLogger();
    // Prima controlla nella tabella Studente
    $query = "SELECT Email, Password, 'Studente' AS TipoUtente FROM Studente WHERE Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Se non trova lo studente, cerca nella tabella Docente
    if (!$user) {
        $query = "SELECT Email, Password, 'Docente' AS TipoUtente FROM Docente WHERE Email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $logger->logEvent($email, 'Accesso Docente ', ['mail_Utente' => $user['Email']]);
    }

    // Verifica la password e crea la sessione se l'utente esiste
    if ($user && password_verify($password, $user['Password'])) {
        session_start();
        $_SESSION['user'] = $user['Email'];
        $_SESSION['tipoUtente'] = $user['TipoUtente'];
        $logger->logEvent($email, 'Accesso Studente ', ['mail_Utente' => $user['Email']]);

        return true;
    }

    // Se l'utente non viene trovato o la password non è corretta
    return false;
}


?>
