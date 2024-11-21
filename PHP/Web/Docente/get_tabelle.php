<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//  la connessione al database
global $conn;
require_once '../../ESQLDB2.php';
session_start();

//|| $_SESSION['tipoUtente'] != 'Docente'
if (!isset($_SESSION['user'] ) ) {
    header("Location: ../index.php");

    exit();
}

// Risultato predefinito
$response = ["tabelle" => []];
header("Content-Type: application/json");

try {
    // Recupera l'elenco delle tabelle da Tabella_Esercizio
    $stmt = $conn->prepare("SELECT Nome FROM ESQLDB2.Tabella_Esercizio");
    $stmt->execute();
    $result = $stmt->get_result();
    $tabelle = $result->fetch_all(MYSQLI_ASSOC);  // Ottieni le tabelle

    if ($tabelle) {
        $response["tabelle"] = $tabelle;
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

// Restituisci la risposta in formato JSON
echo json_encode($response);
?>
