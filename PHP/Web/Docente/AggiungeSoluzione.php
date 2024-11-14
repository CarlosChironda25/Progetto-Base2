<?php
global $conn;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();
include '../../ESQLDB2.php';
if (!isset($_SESSION['user']) ) {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}

$message = '';

// Recupera gli ID dei quesiti e i titoli dei test per il form
$quesitoOptions = $conn->query("SELECT Id FROM Quesito");
$testOptions = $conn->query("SELECT Titolo FROM Test");

// Aggiunta di una soluzione tramite procedura
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Parametri dal form
    $sketch = $_POST['sketch'];
    $idQuesito = $_POST['idQuesito'];
    $titoloTest = $_POST['titoloTest'];
    $nomeTabellaOutput = $_POST['nomeTabellaOutput'];

    // Chiamata alla procedura di aggiunta soluzione
    $stmt = $conn->prepare("CALL AggiungiSoluzione(?, ?, ?, ?)");
    $stmt->bind_param("siss", $sketch, $idQuesito, $titoloTest, $nomeTabellaOutput);

    if ($stmt->execute()) {
        $message = "Soluzione aggiunta con successo!";
        $logger->logEvent($_SESSION['user'], 'Soluzione aggiunta con successo  : ', ['mail_Utente' => $_SESSION['user'] ]);

    } else {
        $message = "Errore nell'aggiunta della soluzione: " . $stmt->error;
        $logger->logEvent($_SESSION['user'], 'Errore nell\'aggiunta della soluzione  : ', ['mail_Utente' => $_SESSION['user'] ]);

    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Aggiungi Soluzione</title>
</head>
<body>
<div class="container">
    <br>
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2>Aggiungi Soluzione al Quesito</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="sketch" class="form-label">Sketch</label>
            <input type="text" name="sketch" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="idQuesito" class="form-label">ID Quesito</label>
            <select name="idQuesito" class="form-select" required>
                <?php while ($row = $quesitoOptions->fetch_assoc()): ?>
                    <option value="<?php echo $row['Id']; ?>"><?php echo $row['Id']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="titoloTest" class="form-label">Titolo Test</label>
            <select name="titoloTest" class="form-select" required>
                <?php while ($row = $testOptions->fetch_assoc()): ?>
                    <option value="<?php echo $row['Titolo']; ?>"><?php echo $row['Titolo']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="nomeTabellaOutput" class="form-label">Nome Tabella Output</label>
            <input type="text" name="nomeTabellaOutput" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Aggiungi Soluzione</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
