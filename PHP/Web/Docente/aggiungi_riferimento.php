<?php
global $conn;
session_start();
include '../../ESQLDB2.php';

$message = '';
require_once '../ControllerMongoDBLogger.php';
$logger=new ControllerMongoDBLogger();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}

// Recupera gli ID dei quesiti e i nomi delle tabelle di esercizio
$quesitoOptions = $conn->query("SELECT ID FROM Quesito");
$tabellaOptions = $conn->query("SELECT Nome FROM Tabella_Esercizio"); // Aggiornamento qui

// Aggiunta di un riferimento tramite procedura
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $conn;

    $idQuesito = $_POST['idQuesito'];
    $titoloTest = $_POST['titoloTest'];
    $nomeTabellaEsercizio = $_POST['nomeTabellaEsercizio'];

    // Chiamata alla procedura di aggiunta riferimento
    $stmt = $conn->prepare("CALL AggiungiRiferimento(?, ?, ?, @result)");
    $stmt->bind_param("iss", $idQuesito, $titoloTest, $nomeTabellaEsercizio);

    if ($stmt->execute()) {
        $result = $conn->query("SELECT @result AS result")->fetch_assoc();
        if ($result['result'] == 1) {
            $message = "Riferimento aggiunto con successo!";
            $logger->logEvent($_SESSION['user'], $message, ['mail_Utente' => $_SESSION['user'] ]);

        } else {
            $message = "Errore: Quesito o tabella di esercizio non trovati.";
            $logger->logEvent($_SESSION['user'], $message, ['mail_Utente' => $_SESSION['user'] ]);

        }
    } else {
        $message = "Errore nell'aggiunta del riferimento: " . $stmt->error;
        $logger->logEvent($_SESSION['user'], $message, ['mail_Utente' => $_SESSION['user'] ]);

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
            <input type="text" name="titoloTest" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="nomeTabellaEsercizio" class="form-label">Nome Tabella di Esercizio</label>
            <select name="nomeTabellaEsercizio" class="form-select" required>
                <?php while ($row = $tabellaOptions->fetch_assoc()): ?>
                    <option value="<?php echo $row['Nome']; ?>"><?php echo $row['Nome']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Aggiungi Riferimento</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
