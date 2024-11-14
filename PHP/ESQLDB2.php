<?php

$servername = "localhost";
$username = "root";    // Nome utente del database MySQL
$password = "";        // Password dell'utente MySQL (lasciare vuoto se non impostata)
$dbname = "ESQLDB2";   // Nome del database a cui vuoi connetterti

// Creare connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
} else {
   // echo "Connessione al database riuscita!";
}

// Chiudere la connessione quando hai finito
//$conn->close();
?>

