<?php
global $conn;
session_start();
include '../../ESQLDB2.php';

$message = '';
require_once '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();
if (!isset($_SESSION['user']) ) {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}
// Recupero dei titoli dei test disponibili per il dropdown
$testOptions = $conn->query("SELECT Titolo FROM Test");

// Aggiunta di un quesito tramite procedura
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Parametri dal form
    $titoloTest = $_POST['titoloTest'];
    $descrizione = $_POST['descrizione'];
    $difficolta = $_POST['difficolta'];
    $tipoQuesito = $_POST['tipoQuesito'];

    // Chiamata alla procedura di aggiunta quesito
    $stmt = $conn->prepare("CALL AggiungiQuesito(?, ?, ?, ?, @result)");
    $stmt->bind_param("ssss", $titoloTest, $descrizione, $difficolta, $tipoQuesito);

    if ($stmt->execute()) {
        $result = $conn->query("SELECT @result AS result")->fetch_assoc();
        if ($result['result'] == 1) {
            $message = "Quesito aggiunto con successo!";
            $logger->logEvent($_SESSION['user'], 'Quesito aggiunto con successo! ', ['mail_Utente' => $_SESSION['user'] ]);

        } else {
            $message = "Errore: Test non trovato.";
            $logger->logEvent($_SESSION['user'], 'Errore: Test non trovato! ', ['mail_Utente' => $_SESSION['user'] ]);

        }
    } else {
        $message = "Errore nell'aggiunta del quesito: " . $stmt->error;
        $logger->logEvent($_SESSION['user'], 'Errore nell\'aggiunta del quesito: ', ['mail_Utente' => $_SESSION['user'] ]);

    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Aggiungi Quesito</title>
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
    <h2>Aggiungi Quesito a un Test</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="titoloTest" class="form-label">Titolo Test</label>
            <select name="titoloTest" class="form-select" required>
                <?php while ($row = $testOptions->fetch_assoc()): ?>
                    <option value="<?php echo $row['Titolo']; ?>"><?php echo $row['Titolo']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="descrizione" class="form-label">Descrizione</label>
            <textarea name="descrizione" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="difficolta" class="form-label">Difficoltà</label>
            <select name="difficolta" class="form-select" required>
                <option value="Basso">Basso</option>
                <option value="Medio">Medio</option>
                <option value="Alto">Alto</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="tipoQuesito" class="form-label">Tipo Quesito</label>
            <select name="tipoQuesito" class="form-select" required>
                <option value="RispostaChiusa">Risposta Chiusa</option>
                <option value="Codice">Codice</option>
            </select>
        </div>
        <button type="submit" name="azione" value="crea" class="btn btn-primary">Crea Quesito</button>
        <button type="submit" name="azione" value="crea_riferimento" class="btn btn-secondary">Crea e Vai a Riferimento</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>

<?php
// Redireziona alla pagina di riferimento se si sceglie "Crea e Vai a Riferimento"
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['azione'] == 'crea_riferimento') {
    header("Location: aggiungi_riferimento.php");
}
?>
</body>
</html>
