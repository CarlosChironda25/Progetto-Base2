<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../../ESQLDB2.php';
require_once  '../ControllerMongoDBLogger.php';
global $conn;
$logger=new ControllerMongoDBLogger();
if (!isset($_SESSION['user'] ) || $_SESSION['tipoUtente'] != 'Studente') {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}


//$idQuesito = $_GET['idQuesito'];
$titoloTest = trim($_GET['titoloTest']);
$tipo = $_GET['tipo'];  // 'chiuso' o 'codice'
$emailStudente = $_SESSION['user'];
$message = '';
$idQuesito = isset($_GET['idQuesito']) ? intval($_GET['idQuesito']) : null;





if (is_null($idQuesito) || is_null($titoloTest)) {
    echo "Errore: ID o titolo del test non specificato.";
    exit();
    $logger->logEvent($emailStudente, '"Errore: ID o titolo del test non specificato." : ', ['mail_Utente' => $_SESSION['user'] ]);

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

/* Verifica stato del test (completato o risposta visualizzabile)
$queryCompletamento = "
        SELECT COUNT(*) AS completato
        FROM ESQLDB2.Completamento, ESQLDB2.Test
        WHERE (TitoloTest = Titolo AND Stato = 'concluso') 
    ";
$stmt = $conn->prepare($queryCompletamento);
$stmt->bind_param("s", $titoloTest);
$stmt->execute();
$resultCompletamento = $stmt->get_result();
$completato = $resultCompletamento->fetch_assoc()['completato'];
$stmt->close();

$queryVisualizzabile = "
        SELECT COUNT(*) AS visualizzabile
        FROM ESQLDB2.Test
        WHERE Titolo = ? AND VisualizzazioneRisposta = 1
    ";
$stmt = $conn->prepare($queryVisualizzabile);
$stmt->bind_param("s", $titoloTest);
$stmt->execute();
$resultVisualizzabile = $stmt->get_result();
$visualizzabile = $resultVisualizzabile->fetch_assoc()['visualizzabile'];
$stmt->close();
// || $visualizzabile>0
//if ($completato > 0 ) {
   // $message = 'Il test è stato completato o le risposte sono già visualizzabili. Non è possibile risposndere a questo test';
   // $logger->logEvent($emailStudente, 'Invio risposta bloccato: test completato o risposte visibili.', []);
//} else {
*/


    if ($tipo === 'chiuso') {
        $numeroOpzione = $_POST['numeroOpzione'];

        $stmt = $conn->prepare("CALL InserisciRispostaChiusa(?, CURRENT_TIMESTAMP, NULL, ?, ?, ?)");
        $stmt->bind_param("siis", $emailStudente, $numeroOpzione, $idQuesito, $titoloTest);

    } else {
        $testoRisposta = $_POST['testoRisposta'];
        $stmt = $conn->prepare("CALL InserisciRispostaCodice(?, ?, ?, ?)");
        $stmt->bind_param("siss", $emailStudente, $idQuesito, $titoloTest, $testoRisposta);
        // INSERT INTO RispostaCodice (EmailStudente, Esito, Data, TestoRisposta, IdQuesito, TitoloTest)

    }

    if ($stmt->execute()) {
        $message = "Risposta inviata con successo!";
        $logger->logEvent($emailStudente, 'Risposta inviata con successo : ', ['mail_Utente' => $_SESSION['user'] ]);

    } else {
        $logger->logEvent($emailStudente, 'Errore nell\'invio della risposta:: ', ['mail_Utente' => $_SESSION['user'] ]);
        $message = "Errore nell'invio della risposta: " . $stmt->error;

    }

    $stmt->close();//}
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Rispondi al Quesito</title>
</head>
<body>
<div class="container">
    <br>
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2 class="text-center mb-4">Rispondi al Quesito</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($tipo === 'chiuso'): ?>
        <form method="POST">
            <div class="mb-3">
                <label for="numeroOpzione" class="form-label">Seleziona Opzione</label>
                <select name="numeroOpzione" class="form-select" required>
                    <?php
                    $opzioni = $conn->query("SELECT Numerazione, Testo FROM Opzione WHERE IdQuesito = $idQuesito AND TitoloTest = '$titoloTest'");
                    while ($row = $opzioni->fetch_assoc()):
                        ?>
                        <option value="<?php echo $row['Numerazione']; ?>"><?php echo htmlspecialchars($row['Testo']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Invia Risposta</button>
        </form>
    <?php else: ?>
        <form method="POST">
            <div class="mb-3">
                <label for="testoRisposta" class="form-label">Risposta</label>
                <textarea name="testoRisposta" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Invia Risposta</button>
        </form>
    <?php endif; ?>
</div>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
