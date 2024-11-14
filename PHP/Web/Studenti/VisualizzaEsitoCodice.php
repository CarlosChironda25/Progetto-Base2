<?php
global $conn;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../../ESQLDB2.php';

if (!isset($_SESSION['user']) || $_SESSION['tipoUtente'] != 'Studente') {
    header("Location: ../index.php");
    exit();
}

$emailStudente = $_SESSION['user'];

// Recupera l'elenco dei test a cui lo studente ha risposto con RispostaCodice
$queryTest = "SELECT DISTINCT TitoloTest FROM RispostaCodice WHERE EmailStudente = ?";
$stmtTest = $conn->prepare($queryTest);
$stmtTest->bind_param("s", $emailStudente);
$stmtTest->execute();
$resultTest = $stmtTest->get_result();
$testDisponibili = $resultTest->fetch_all(MYSQLI_ASSOC);
$stmtTest->close();  // Chiudiamo lo statement per sicurezza

// Ottieni il titolo del test selezionato
$titoloTest = $_POST['titoloTest'] ?? $testDisponibili[0]['TitoloTest'] ?? '';

if (!empty($titoloTest)) {
    // Recupera tutti i quesiti del test selezionato
    $queryQuesiti = "SELECT IdQuesito FROM RispostaCodice WHERE EmailStudente = ? AND TitoloTest = ?";
    $stmtQuesiti = $conn->prepare($queryQuesiti);
    $stmtQuesiti->bind_param("ss", $emailStudente, $titoloTest);
    $stmtQuesiti->execute();
    $resultQuesiti = $stmtQuesiti->get_result();
    $quesiti = $resultQuesiti->fetch_all(MYSQLI_ASSOC);
    $stmtQuesiti->close();  // Chiudiamo lo statement per sicurezza

    // Valuta tutte le risposte dello studente per il test selezionato
    foreach ($quesiti as $quesito) {
        $idQuesito = $quesito['IdQuesito'];
        $queryValuta = "CALL ValutaRisposta(?, ?, ?)";
        $stmtValuta = $conn->prepare($queryValuta);
        $stmtValuta->bind_param("sis", $emailStudente, $idQuesito, $titoloTest);
        $stmtValuta->execute();
        $stmtValuta->close(); // Libera risorse dopo ogni esecuzione
    }

    // Esegui la procedura per ottenere l'esito delle risposte codice aggiornato
    $queryEsito = "CALL CalcolaEsitoTestCodice(?, ?)";
    $stmtEsito = $conn->prepare($queryEsito);
    $stmtEsito->bind_param("ss", $emailStudente, $titoloTest);
    $stmtEsito->execute();
    $result = $stmtEsito->get_result();  // Ottiene il risultato della procedura
    $stmtEsito->close(); // Chiude lo statement dopo l'esecuzione
}
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <title>Esito Test Codice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container my-5">
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>

    <div class="card shadow-lg">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title mb-0">Esito Test Codice</h3>
        </div>
        <div class="card-body">
            <h4>Seleziona un Test per Visualizzare l'Esito del Codice</h4>

            <form method="post" action="" class="mb-4">
                <label for="titoloTest" class="form-label">Titolo del Test:</label>
                <select name="titoloTest" id="titoloTest" class="form-select" style="max-width: 300px;" onchange="this.form.submit()">
                    <?php foreach ($testDisponibili as $test): ?>
                        <option value="<?php echo htmlspecialchars($test['TitoloTest']); ?>"
                            <?php echo $test['TitoloTest'] === $titoloTest ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($test['TitoloTest']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if (!empty($titoloTest) && isset($result)): ?>
                <h5 class="text-muted">Esito del Test: <?php echo htmlspecialchars($titoloTest); ?></h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mt-3">
                        <thead class="table-primary">
                        <tr>
                            <th>Id Quesito</th>
                            <th>Codice Inviato</th>
                            <th>Codice Corretto</th>
                            <th>Esito</th>
                            <th>Nome Tabella Output</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['IdQuesito']); ?></td>
                                <td><pre><?php echo htmlspecialchars($row['CodiceInviato']); ?></pre></td>
                                <td><pre><?php echo htmlspecialchars($row['CodiceCorretto']); ?></pre></td>
                                <td><?php echo htmlspecialchars($row['Esito']); ?></td>
                                <td><?php echo htmlspecialchars($row['NomeTabellaOutput']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">Seleziona un test per visualizzare i dettagli dell'esito.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
