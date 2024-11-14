<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $conn;
session_start();
include '../../ESQLDB2.php';
require_once  '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();
if (!isset($_SESSION['user']) ) {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}
$message = '';

// Recupera gli ID dei quesiti e i titoli dei test per il form
$quesitoOptions = $conn->query("SELECT ID FROM Quesito");
$testOptions = $conn->query("SELECT Titolo FROM Test");

// Aggiunta di un'opzione tramite procedura
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $conn;

    // Parametri dal form
    $idQuesito = $_POST['idQuesito'];
    $titoloTest = $_POST['titoloTest'];
    $opzioneCorretta = isset($_POST['opzioneCorretta']) ? 1 : 0;
    $testo = $_POST['testo'];

    // Chiamata alla procedura di aggiunta opzione
    $stmt = $conn->prepare("CALL AggiungiOpzione(?, ?, ?, ?)");
    $stmt->bind_param("isis", $idQuesito, $titoloTest, $opzioneCorretta, $testo);

    if ($stmt->execute()) {
        $message = "Opzione aggiunta con successo!";
        $logger->logEvent($_SESSION['user'], 'Opzione aggiunta con successo! : ', ['mail_Utente' => $_SESSION['user'] ]);

    } else {
        $logger->logEvent($_SESSION['user'], 'Errore nell\'aggiunta dell\'opzione: : ', ['mail_Utente' => $_SESSION['user'] ]);
        $message = "Errore nell'aggiunta dell'opzione: " . $stmt->error;
    }

    $stmt->close();
}


?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Aggiungi Opzione al Quesito</title>
</head>
<body>
<div class="container">
    <br>
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2>Aggiungi Opzione di Risposta a un Quesito</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="idQuesito" class="form-label">ID Quesito</label>
            <select name="idQuesito" class="form-select" required>
                <?php while ($row = $quesitoOptions->fetch_assoc()): ?>
                    <option value="<?php echo $row['ID']; ?>"><?php echo $row['ID']; ?></option>
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
            <label for="testo" class="form-label">Testo dell'Opzione</label>
            <textarea name="testo" class="form-control" required></textarea>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="opzioneCorretta" class="form-check-input" id="opzioneCorretta">
            <label for="opzioneCorretta" class="form-check-label">È la risposta corretta?</label>
        </div>
        <button type="submit" class="btn btn-primary">Aggiungi Opzione</button>
        <button type="submit" name="azione" value="crea_riferimento" class="btn btn-secondary">Crea soluzione codice</button>

    </form>
</div>
<?php include '../../includes/footer.php'; ?>

<?php
// Redireziona alla pagina di riferimento se si sceglie "Crea e Vai a Riferimento"
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['azione'] == 'crea_riferimento') {
    header("Location: AggiungeSoluzione.php");
}
?>
</body>
</html>
