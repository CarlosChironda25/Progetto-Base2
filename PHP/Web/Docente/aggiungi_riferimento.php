<?php
global $conn;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../../ESQLDB2.php';


require_once '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();

// Controllo accesso utente
if (!isset($_SESSION['user'] ) || $_SESSION['tipoUtente'] != 'Docente') {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}
$message = '';
// Recupera gli ID dei quesiti e i nomi delle tabelle di esercizio
$quesitoOptions = $conn->query("SELECT ID FROM Quesito");
$tabellaOptions = $conn->query("SELECT Nome FROM Tabella_Esercizio");

// Aggiunta di un riferimento tramite procedura
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idQuesito = $_POST['idQuesito'];
    $titoloTest = $_POST['titoloTest'];
    $nomeTabellaEsercizio = $_POST['nomeTabellaEsercizio'];

    // Chiamata alla procedura
    $stmt = $conn->prepare("CALL AggiungiRiferimento(?, ?, ?, @result)");
    $stmt->bind_param("iss", $idQuesito, $titoloTest, $nomeTabellaEsercizio);

    if ($stmt->execute()) {
        $result = $conn->query("SELECT @result AS result")->fetch_assoc();
        if ($result['result'] == 1) {
            $message = '<div class="alert alert-success">Riferimento aggiunto con successo!</div>';
            $logger->logEvent($_SESSION['user'], 'Riferimento creato.', ['utente' => $_SESSION['user']]);
        } else {
            $message = '<div class="alert alert-warning">Errore: Quesito o tabella di esercizio non trovati.</div>';
            $logger->logEvent($_SESSION['user'], 'Errore durante la creazione del riferimento.', ['utente' => $_SESSION['user']]);
        }
    } else {
        $message = '<div class="alert alert-danger">Errore nell\'aggiunta del riferimento: ' . htmlspecialchars($stmt->error) . '</div>';
        $logger->logEvent($_SESSION['user'], 'Errore SQL.', ['utente' => $_SESSION['user'], 'errore' => $stmt->error]);
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Aggiungi Riferimento</title>
</head>
<body>
<div class="container">
    <br>
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2>Aggiungi Riferimento tra Quesito e Tabella di Esercizio</h2>

    <!-- Mostra il messaggio -->
    <?php if ($message): ?>
        <?php echo $message; ?>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="idQuesito" class="form-label">ID Quesito</label>
            <select name="idQuesito" class="form-select" required>
                <?php while ($row = $quesitoOptions->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['ID']); ?>"><?php echo htmlspecialchars($row['ID']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="titoloTest" class="form-label">Titolo Test</label>
            <input type="text" name="titoloTest" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="nomeTabellaEsercizio" class="form-label">Nome Tabella di Esercizio</label>
            <select name="nomeTabellaEsercizio" class="form-select" required>
                <?php while ($row = $tabellaOptions->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['Nome']); ?>"><?php echo htmlspecialchars($row['Nome']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Aggiungi Riferimento</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
