<?php
// Avvio della sessione e configurazione
global $conn;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../../ESQLDB2.php'; // Connessione al database

// Controlla se l'email è presente in sessione
if (!isset($_SESSION['user']) || $_SESSION['tipoUtente'] != 'Studente') {
    header("Location: ../index.php");
    exit();
}

$emailSessione = $_SESSION['user']; // Recupera l'email dello studente loggato

// Recupera l'elenco di tutti i test completati dallo studente
$queryTestCompletati = "SELECT DISTINCT TitoloTest FROM RispostaChiusa WHERE EmailStudente = ?";
$stmtTest = $conn->prepare($queryTestCompletati);
$stmtTest->bind_param("s", $emailSessione);
$stmtTest->execute();
$resultTest = $stmtTest->get_result();

// Itera sui test e calcola gli esiti
if ($resultTest && $resultTest->num_rows > 0) {
    while ($row = $resultTest->fetch_assoc()) {
        $titoloTest = $row['TitoloTest'];

        // Chiama la procedura per calcolare gli esiti per ogni test
        $queryCalcolaEsiti = "CALL CalcolaEsitoTest(?, ?)";
        $stmtCalcolo = $conn->prepare($queryCalcolaEsiti);
        $stmtCalcolo->bind_param("ss", $emailSessione, $titoloTest);
        $stmtCalcolo->execute();
        $stmtCalcolo->close();
    }
}

// Query per visualizzare tutte le risposte aggiornate
$queryVisualizzaEsiti = "
    SELECT r.TitoloTest, r.IdQuesito, r.NumeroOpzione, r.Esito, 
           o.Testo AS TestoOpzione, o.OpzioneCorretta, 
           t.VisualizzazioneRisposta
    FROM RispostaChiusa r
    JOIN Opzione o ON r.NumeroOpzione = o.Numerazione
                  AND r.IdQuesito = o.IdQuesito
                  AND r.TitoloTest = o.TitoloTest
    JOIN Test t ON r.TitoloTest = t.Titolo
    WHERE r.EmailStudente = ?
";
$stmtVisualizza = $conn->prepare($queryVisualizzaEsiti);
$stmtVisualizza->bind_param("s", $emailSessione);
$stmtVisualizza->execute();
$result = $stmtVisualizza->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Esito delle Risposte Chiuse</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container my-5">
    <br>
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2 class="mb-4">Esito delle Risposte Chiuse</h2>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Titolo Test</th>
            <th>Id Quesito</th>
            <th>Opzione Scelta</th>
            <th>Testo Opzione</th>
            <th>Esito</th>
            <th>Soluzione Corretta</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <style>
                    .table-success {
                        background-color: #d4edda !important;
                        color: #155724 !important;
                    }

                    .table-danger {
                        background-color: #f8d7da !important;
                        color: #721c24 !important;
                    }
                </style>

                <tr class="<?php echo $row['Esito'] ? 'table-success' : 'table-danger'; ?>">
                    <td><?php echo htmlspecialchars($row['TitoloTest']); ?></td>
                    <td><?php echo htmlspecialchars($row['IdQuesito']); ?></td>
                    <td><?php echo htmlspecialchars($row['NumeroOpzione']); ?></td>
                    <td><?php echo htmlspecialchars($row['TestoOpzione']); ?></td>
                    <td><?php echo $row['Esito'] ? 'Corretto' : 'Errato'; ?></td>
                    <td>
                        <?php if ($row['VisualizzazioneRisposta']): ?>
                            <pre><?php echo htmlspecialchars($row['TestoOpzione']); ?></pre>
                        <?php else: ?>
                            Soluzione non ancora disponibile
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">Nessun risultato trovato.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
// Chiudi lo statement e la connessione al database
$stmtVisualizza->close();
$conn->close();
?>
