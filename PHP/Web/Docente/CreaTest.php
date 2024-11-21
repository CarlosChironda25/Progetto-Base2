<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();

if (!isset($_SESSION['user'] ) || $_SESSION['tipoUtente'] != 'Docente') {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}

include '../../ESQLDB2.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $conn;

    $titolo = $_POST['titolo'];
    $emailDocente = $_SESSION['user'];
    $foto = $_FILES['foto']['tmp_name'] ? file_get_contents($_FILES['foto']['tmp_name']) : null;
    // Prepara la chiamata alla procedura con gestione di @result
    if ($foto === null) {
        $stmt = $conn->prepare("CALL CreaTest(?, NULL, ?, @result)");
        $stmt->bind_param("ss", $titolo, $emailDocente);
    } else {
        $stmt = $conn->prepare("CALL CreaTest(?, ?, ?, @result)");
        $stmt->bind_param("sbs", $titolo, $foto,$emailDocente);
        $stmt->send_long_data(2, $foto);
        //        VALUES (titolo, foto, CURRENT_TIMESTAMP, FALSE, emailDocente);
    }

    if ($stmt->execute()) {
        // Controlla il valore di @result
        $output_result = $conn->query("SELECT @result AS result")->fetch_assoc();

        $message = $output_result['result'] == 1 ? "Test creato con successo!" : "Errore: un test con lo stesso titolo esiste già.";
           if( $output_result['result'] == 1 ) {
           $logger->logEvent($emailDocente, 'Test creato con successo da: ', ['mail_Utente' => $_SESSION['user'] ]);
         } else
        $logger->logEvent($emailDocente, 'Errore: un test con lo stesso titolo esiste già, tentativo fatto da: ', ['mail_Utente' => $_SESSION['user'] ]);

    } else {
        $message = "Errore nella creazione del test: " . $stmt->error;
        $logger->logEvent($emailDocente, 'Errore nella creazione del test ', ['mail_Utente' => $_SESSION['user'] ]);

    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Crea Test</title>
</head>
<body>
<div class="container">
    <br>
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2>Crea un Nuovo Test</h2>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titolo" class="form-label">Titolo Test</label>
            <input type="text" name="titolo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="foto" class="form-label">Foto (Opzionale)</label>
            <input type="file" name="foto" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Crea Test</button>
    </form>
</div>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
