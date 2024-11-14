<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();
if (!isset($_SESSION['user'])  || $_SESSION['tipoUtente'] != 'Studente') {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito all\'utente  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}

include '../../ESQLDB2.php';
$soluzione = null;
$message = '';

// Recupera la lista dei test per il menu a discesa, senza filtro su VisualizzazioneRisposta
global $conn;
$testList = [];
$result = $conn->query("SELECT Titolo FROM Test");
while ($row = $result->fetch_assoc()) {
    $testList[] = $row['Titolo'];
}

// Controlla se è stata inviata una richiesta POST per visualizzare la soluzione
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titoloTest = $_POST['titoloTest'];

    // Chiama la procedura per ottenere la soluzione
    $query = "CALL VisualizzaSoluzione(?, @Titolo, @DataTest, @VisualizzazioneRisposta, @TestoOpzione, @SoluzioneSketch)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $titoloTest);
    $stmt->execute();

    // Ottieni i risultati della procedura
    $result = $conn->query("SELECT @Titolo AS Titolo, @DataTest AS DataTest, @VisualizzazioneRisposta AS VisualizzazioneRisposta, @TestoOpzione AS TestoOpzione, @SoluzioneSketch AS SoluzioneSketch");
    if ($result && $result->num_rows > 0) {
        $soluzione = $result->fetch_assoc();

        // Verifica se la visualizzazione delle risposte è abilitata
        if ($soluzione['VisualizzazioneRisposta'] != 1) {
            $message = "La soluzione del test non è ancora disponibile.";
            $soluzione = null; // Resetta la soluzione se non disponibile
        }
    } else {
        $message = "Soluzione non disponibile o errore nella visualizzazione.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Visualizza Soluzione Test</title>
</head>
<body>
<div class="container">
    <h2>Seleziona un Test per Visualizzare la Soluzione</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="titoloTest" class="form-label">Seleziona il Test:</label>
            <select name="titoloTest" id="titoloTest" class="form-control" required>
                <?php foreach ($testList as $test): ?>
                    <option value="<?= $test ?>"><?= $test ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Visualizza Soluzione</button>
    </form>

    <?php if ($soluzione): ?>
        <hr>
        <h3>Soluzione del Test: <?= $soluzione['Titolo'] ?></h3>
        <p><strong>Data del Test:</strong> <?= $soluzione['DataTest'] ?></p>
        <p><strong>Testo della soluzione:</strong><br><?= nl2br($soluzione['TestoOpzione']) ?></p>
        <p><strong>Sketch della soluzione:</strong><br><?= nl2br($soluzione['SoluzioneSketch']) ?></p>
    <?php endif; ?>
</div>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
