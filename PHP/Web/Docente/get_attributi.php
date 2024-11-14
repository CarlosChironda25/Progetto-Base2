<?php
global $conn;
include '../../ESQLDB2.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['nomeTabella'])) {
    $nomeTabella = $_GET['nomeTabella'];

    // Prepara la query per ottenere gli attributi della tabella selezionata
    $stmt = $conn->prepare("SELECT Nome FROM Attributi WHERE NomeTabella = ?");
    $stmt->bind_param("s", $nomeTabella);
    $stmt->execute();
    $result = $stmt->get_result();

    $attributi = [];
    while ($row = $result->fetch_assoc()) {
        $attributi[] = $row;
    }

    // Restituisci gli attributi come JSON
    echo json_encode($attributi);
}
