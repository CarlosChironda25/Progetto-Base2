<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}
include '../ESQLDB2.php';

global $conn;
$risposteCorrette = [];
require_once 'ControllerMongoDBLogger.php';
  $logger = new ControllerMongoDBLogger();

try {
    $stmt = $conn->prepare("SELECT * FROM Classifica_Risposte_Corrette");
    $logger->logEvent($_SESSION['user'], 'Acesso alle classifiche risposte corrette  fatta da: ', ['mail_Utente' => $_SESSION['user'] ]);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $risposteCorrette[] = $row;
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
    <title>Classifica Risposte Corrette</title>
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
    <h2>Classifica per Percentuale di Risposte Corrette</h2>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Codice Studente</th>
            <th>Percentuale di Risposte Corrette (%)</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($risposteCorrette)): ?>
            <?php foreach ($risposteCorrette as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['Codice']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($row['PercentualeCorrette'], 2)); ?>%</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">Nessun dato disponibile</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
