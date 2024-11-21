<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}
include '../../ESQLDB2.php';
require_once  '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();
if (!isset($_SESSION['user'] ) || $_SESSION['tipoUtente'] != 'Docente') {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}

// Inizializza un array per raccogliere i dati delle tabelle
 global $conn;
$tabelle = [];

try {
    // Prepariamo e chiamiamo la procedura per ottenere le tabelle
    $stmt = $conn->prepare("CALL VisualizzaTabelle()");
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $logger->logEvent($_SESSION['user'], 'Accesso alla visualizzazione delle tabelle fatto da:: ', ['mail_Utente' => $_SESSION['user'] ]);


        // Inseriamo ogni riga di risultato nell'array $tabelle
        while ($row = $result->fetch_assoc()) {
            $tabelle[] = $row;
        }
        $stmt->close();
    }
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage();
    $logger->logEvent($_SESSION['user'], ' errore   : ', ['mail_Utente' => $_SESSION['user'] ]);

}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Visualizzazione Tabelle</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <br>
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2>Tabelle Disponibili</h2>
    <div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Nome</th>
                <th>Data di Creazione</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($tabelle)): ?>
                <?php foreach ($tabelle as $tabella): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tabella['Nome']); ?></td>
                        <td><?php echo htmlspecialchars($tabella['Data']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Nessuna tabella disponibile</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
