

<?php
global $conn;
include '../../ESQLDB2.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Gestione del form di inserimento
    $nomeTabella = $_POST['nomeTabella'];
    $valori = [];

    // Crea un array dei valori per ciascun attributo
    foreach ($_POST as $key => $value) {
        if ($key !== 'nomeTabella') {
            $valori[$key] = $value;
        }
    }

// Stampa per debug
echo "Nome tabella ricevuto: " . $nomeTabella;




    // Codifica i valori in JSON
    $jsonValori = json_encode($valori);

    // Chiama la procedura per inserire la riga
    $stmt = $conn->prepare("CALL InserisciRigaTabellaEsercizio(?, ?)");
    $stmt->bind_param("ss", $jsonValori, $nomeTabella);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Riga inserita con successo!";
    } else {
        echo "Errore nell'inserimento della riga.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Inserisci Riga in Tabella di Esercizio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container my-5">
    <h2>Inserisci Riga in Tabella di Esercizio</h2>

    <!-- Seleziona la tabella di esercizio -->
    <form method="POST" id="inserisciRigaForm">
        <div class="mb-3">
            <label for="nomeTabella" class="form-label">Seleziona Tabella</label>
            <select name="nomeTabella" id="nomeTabella" class="form-select" onchange="caricaAttributi()">
                <!-- Popola dinamicamente con le tabelle disponibili -->
                <?php
                $stmt = $conn->query("SELECT Nome FROM Tabella_Esercizio");
                while ($row = $stmt->fetch_assoc()) {
                    echo "<option value='{$row['Nome']}'>{$row['Nome']}</option>";
                }
                ?>
            </select>
        </div>

        <!-- Sezione per inserire i valori per ciascun attributo -->
        <div id="attributiContainer"></div>

        <button type="submit" class="btn btn-success mt-3">Inserisci Riga</button>
    </form>
</div>

<script>
    function caricaAttributi() {
        const nomeTabella = document.getElementById('nomeTabella').value;

        // Richiedi via AJAX i campi della tabella
        fetch(`get_attributi.php?nomeTabella=${nomeTabella}`)
            .then(response => response.json())
            .then(attributi => {
                const container = document.getElementById('attributiContainer');
                container.innerHTML = '';
                attributi.forEach(attr => {
                    const div = document.createElement('div');
                    div.className = 'mb-3';
                    div.innerHTML = `
                        <label class="form-label">${attr.Nome}</label>
                        <input type="text" name="${attr.Nome}" class="form-control" required>
                    `;
                    container.appendChild(div);
                });
            })
            .catch(error => {
                console.error('Errore nel caricamento degli attributi:', error);
            });
    }
</script>

</body>
</html>

