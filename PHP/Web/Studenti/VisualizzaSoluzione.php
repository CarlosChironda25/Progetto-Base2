<?php
// Avvio della sessione e configurazione
global $conn;
session_start();
include '../../ESQLDB2.php'; // Connessione al database

// Esegui la procedura per calcolare gli esiti
$queryCalcolaEsiti = "CALL CalcolaEsitiRisposte()";
$conn->query($queryCalcolaEsiti);

// Recupera i dati aggiornati da RispostaCodice, includendo la visibilità della risposta corretta
$queryVisualizzaEsiti = "
    SELECT r.IdQuesito, r.TitoloTest, r.TestoRisposta, r.Esito, 
           s.Sketch, t.VisualizzazioneRisposta, s.NomeTabellaOutput
    FROM RispostaCodice r
    JOIN Soluzione s ON r.IdQuesito = s.IdQuesito AND r.TitoloTest = s.TitoloTest
    JOIN Test t ON r.TitoloTest = t.Titolo
";
$result = $conn->query($queryVisualizzaEsiti);
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
                <tr>
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
// Chiudi la connessione al database
$conn->close();
?>
