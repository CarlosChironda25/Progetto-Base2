<?php
// Includi la connessione al database
global $conn;
require_once '../ESQLDB2.php';

// Verifica se il nome della tabella è stato inviato tramite POST
if (isset($_POST['nomeTabella'])) {
    $nomeTabella = $_POST['nomeTabella'];  // Recupera il nome della tabella dal POST
} else {
    echo "Errore: nome della tabella non specificato!";
    exit();
}

// Recupera tutte le righe dalla tabella RIGA per il nomeTabella
$stmt = $conn->prepare("SELECT Valori FROM RIGA WHERE NomeTabella = ?");
if (!$stmt) {
    echo "Errore nella preparazione della query: " . $conn->error;
    exit();
}

$stmt->bind_param("s", $nomeTabella);
$stmt->execute();
$result = $stmt->get_result();

// Crea un array per memorizzare tutte le righe decodificate
$valoriTabella = [];
while ($row = $result->fetch_assoc()) {
    $valoriTabella[] = json_decode($row['Valori'], true);  // Decodifica ogni campo Valori come JSON
}

// Recupera gli attributi della tabella di esercizio (NomeAttributo) per la UI
$stmt = $conn->prepare("SELECT Nome FROM Attributi WHERE NomeTabella = ?");
if (!$stmt) {
    echo "Errore nella preparazione della query: " . $conn->error;
    exit();
}

$stmt->bind_param("s", $nomeTabella);
$stmt->execute();
$attributesResult = $stmt->get_result();
$attributes = $attributesResult->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizzazione Tabella di Esercizio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<div class="mb-3">
    <a href="VisualizzaTabella.html" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Indietro
    </a>
</div>
<h1>Visualizzazione della Tabella di Esercizio: <?php echo htmlspecialchars($nomeTabella); ?></h1>

<!-- Tabella che mostra gli attributi e i valori -->
<table>
    <thead>
    <tr>
        <?php
        // Crea le intestazioni della tabella basate sugli attributi
        foreach ($attributes as $attribute) {
            echo "<th>" . htmlspecialchars($attribute['Nome']) . "</th>";
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php
    // Itera su tutte le righe e mostra ciascuna riga nella tabella
    foreach ($valoriTabella as $valoriRiga) {
        echo "<tr>";
        foreach ($attributes as $attribute) {
            $attributeName = $attribute['Nome'];
            // Se il dato è presente per quell'attributo, mostralo, altrimenti metti 'NULL'
            $value = isset($valoriRiga[$attributeName]) ? $valoriRiga[$attributeName] : 'NULL';
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
