<?php
global $conn;
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../ESQLDB2.php';
require_once '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();
  //|| $_SESSION['role'] != 'studente'
if (!$_SESSION['user'] ) {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titolo = $_POST['titolo'];
    $titoloTest = $_POST['titoloTest'];
    $testo = $_POST['testo'];
    $emailStudente = $_SESSION['user'];
    $emailDocente = $_POST['emailDocente'];

    $stmt = $conn->prepare("CALL InviaMessaggioStudente(?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $titolo, $titoloTest, $testo, $emailStudente, $emailDocente);
    if ($stmt->execute()) {
        $message = "Messaggio inviato al docente!";
        $logger->logEvent($emailDocente, 'Messaggio Inviato  dal studente ', ['mail_Utente' => $_SESSION['user'] ]);

    } else {
        $message = "Errore nell'invio del messaggio: " . $stmt->error;
        $logger->logEvent($emailDocente, 'Errore durante l invio ', ['mail_Utente' => $_SESSION['user'] ]);

    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>Invia Messaggio al Docente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"  crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">
    <br>
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2>Invia Messaggio al Docente</h2>
    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Titolo del messaggio:</label>
            <input type="text" name="titolo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Titolo del test:</label>
            <input type="text" name="titoloTest" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Testo del messaggio:</label>
            <textarea name="testo" class="form-control" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Email del docente:</label>
            <input type="email" name="emailDocente" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Invia Messaggio</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script>
</body>
</html>