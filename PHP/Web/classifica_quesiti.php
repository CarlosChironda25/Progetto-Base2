<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}
include '../ESQLDB2.php';
require_once 'ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();

global $conn;
$classificaQuesiti = [];

try {

    $stmt = $conn->prepare("SELECT * FROM Classifica_Quesiti");
    $logger->logEvent($_SESSION['user'], 'Acesso alle classifiche fatta da: ', ['mail_Utente' => $_SESSION['user'] ]);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $classificaQuesiti[] = $row;
        }
        $stmt->close();
    }
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage();
    $logger->logEvent($_SESSION['user'], 'Errore nell\'acesso alle classifiche: ', ['mail_Utente' => $_SESSION['user'] ]);

}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../includes/header.php'; ?>
    <title>Classifica Quesiti</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <br>
    <div class="mb-3">
        <a href="Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2>Classifica dei Quesiti per Numero di Risposte Inserite</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Id Quesito</th>
            <th>Titolo Test</th>
            <th>Numero di Risposte</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($classificaQuesiti)): ?>
            <?php foreach ($classificaQuesiti as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['IdQuesito']); ?></td>
                    <td><?php echo htmlspecialchars($row['TitoloTest']); ?></td>
                    <td><?php echo htmlspecialchars($row['NumeroRisposte']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">Nessun dato disponibile</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
