<?php
// Avvio della sessione e configurazione
global $conn;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../../ESQLDB2.php'; // Connessione al database

// Controlla se l'email è presente in sessione
if (!isset($_SESSION['user'] ) || $_SESSION['tipoUtente'] != 'Studente') {
    header("Location: ../index.php");
    exit();
}


$emailSessione = $_SESSION['user'];
// Esegui la procedura per calcolare gli esiti
$queryCalcolaEsiti = "CALL CalcolaEsitiRisposte()";
$conn->query($queryCalcolaEsiti);

// Query preparata per recuperare i dati aggiornati da RispostaCodice
$queryVisualizzaEsiti = "
    SELECT r.IdQuesito, r.TitoloTest, r.TestoRisposta, r.Esito, 
           s.Sketch, t.VisualizzazioneRisposta, s.NomeTabellaOutput
    FROM RispostaCodice r
    JOIN Soluzione s ON r.IdQuesito = s.IdQuesito AND r.TitoloTest = s.TitoloTest
    JOIN Test t ON r.TitoloTest = t.Titolo
    WHERE r.EmailStudente = ?;
";
$stmt = $conn->prepare($queryVisualizzaEsiti);
$stmt->bind_param("s", $emailSessione);
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
            <th>Codice Inviato</th>
            <th>Codice Corretto</th>
            <th>Esito</th>
            <th>Nome Tabella Output</th>
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
                    <td><?php echo htmlspecialchars($row['IdQuesito']); ?></td>
                    <td><pre><?php echo htmlspecialchars($row['TestoRisposta']); ?></pre></td>
                    <td>
                        <?php if ($row['VisualizzazioneRisposta'] == 1): ?>
                            <pre><?php echo htmlspecialchars($row['Sketch']); ?></pre>
                        <?php else: ?>
                            Soluzione non ancora disponibile
                        <?php endif; ?>
                    </td>
                    <td><?php echo $row['Esito'] ? 'Corretto' : 'Errato'; ?></td>
                    <td><?php echo htmlspecialchars($row['NomeTabellaOutput']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">Nessun risultato trovato.</td>
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
