<?php
// Includi la connessione al database
global $conn;
require_once '../../ESQLDB2.php';

// Ricevi il nome della tabella dalla query string
$nomeTabella = $_GET['nomeTabella'];

$response = ["success" => false, "attributes" => []];

try {
    // Recupera gli attributi definiti per questa tabella di esercizio
    $stmt = $conn->prepare("SELECT Nome FROM Attributi WHERE NomeTabella = ?");
    $stmt->bind_param("s", $nomeTabella);
    $stmt->execute();
    $result = $stmt->get_result();
    $attributes = $result->fetch_all(MYSQLI_ASSOC);  // Ottieni gli attributi della tabella

    if ($attributes) {
        $response["success"] = true;
        $response["attributes"] = $attributes;
    }
} catch (Exception $e) {
    $response["success"] = false;
    $response["error"] = $e->getMessage();
}

// Restituisci la risposta in formato JSON
echo json_encode($response);
?>
