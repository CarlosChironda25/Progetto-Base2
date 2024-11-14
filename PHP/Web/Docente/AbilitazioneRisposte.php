<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();



include '../../ESQLDB2.php';

require_once '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();

if (!isset($_SESSION['user'])|| $_SESSION['tipoUtente'] != 'Docente') {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'],'accesso non consetito all\'utente', ['mail_Utente' => $_SESSION['user'] ]);
    exit();
}

$message = '';

// Recupera la lista dei test per il menu a discesa
global $conn;
$testList = [];
$result = $conn->query("SELECT Titolo FROM Test");
while ($row = $result->fetch_assoc()) {
    $testList[] = $row['Titolo'];
}

// Controlla se è stata inviata una richiesta POST per aggiornare la visibilità delle risposte
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titoloTest = $_POST['titoloTest'];
    $visualizza = isset($_POST['visualizza']) ? 1 : 0;
    // Prepara la query per aggiornare la visualizzazione delle risposte
    $stmt = $conn->prepare("UPDATE Test SET VisualizzazioneRisposta = ? WHERE Titolo = ?");
    $stmt->bind_param("is", $visualizza, $titoloTest);

    if ($stmt->execute()) {
        $message = "Visualizzazione delle risposte aggiornata correttamente.";
        $logger->logEvent($_SESSION['user'], 'Visualizzazione delle risposte aggiornata correttamente',['mail_Utente' => $_SESSION['user'] ] );

    } else {
        $message = "Errore nell'aggiornamento della visualizzazione: " . $stmt->error;
        $logger->logEvent($_SESSION['user'], 'Errore nell\'aggiornamento della visualizzazione',['mail_Utente' => $_SESSION['user'] ] );

    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Gestione Visualizzazione Risposte</title>
</head>
<body>
<div class="container">
    <!-- Pulsante "Indietro" -->
    <br>
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2>Abilita/Disabilita la Visualizzazione delle Risposte</h2>
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
        <div class="mb-3 form-check">
            <input type="checkbox" name="visualizza" id="visualizza" class="form-check-input">
            <label for="visualizza" class="form-check-label">Visualizza Risposte</label>
        </div>
        <button type="submit" class="btn btn-primary">Aggiorna</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
