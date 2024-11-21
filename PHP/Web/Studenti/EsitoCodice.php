<?php
// Avvio della sessione e configurazione
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
global $conn;
session_start();
include '../../ESQLDB2.php'; // Connessione al database
if (!isset($_SESSION['user'] ) || $_SESSION['tipoUtente'] != 'Studente') {
    header("Location: ../index.php");
    exit();
}

// Esegui la procedura per calcolare gli esiti
$queryCalcolaEsiti = "CALL CalcolaEsitiRisposte()";
$conn->query($queryCalcolaEsiti);

// Recupera l'email dello studente dalla sessione
$emailStudente = $_SESSION['user'];

// Query preparata per recuperare i dati aggiornati da RispostaCodice
$queryVisualizzaEsiti = "SELECT IdQuesito, TitoloTest, TestoRisposta, Esito FROM ESQLDB2.RispostaCodice WHERE EmailStudente = ?";
$stmt = $conn->prepare($queryVisualizzaEsiti);
$stmt->bind_param("s", $emailStudente); // "s" per stringa
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Esito delle Risposte</title>
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
    <h2 class="mb-4">Esito delle Risposte</h2>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Id Quesito</th>
            <th>Titolo Test</th>
            <th>Testo Risposta</th>
            <th>Esito</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['IdQuesito']); ?></td>
                    <td><?php echo htmlspecialchars($row['TitoloTest']); ?></td>
                    <td><pre><?php echo htmlspecialchars($row['TestoRisposta']); ?></pre></td>
                    <td><?php echo $row['Esito'] ? 'Corretto' : 'Errato'; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">Nessun risultato trovato.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
// Chiudi lo statement e la connessione al database
$stmt->close();
$conn->close();
?>
