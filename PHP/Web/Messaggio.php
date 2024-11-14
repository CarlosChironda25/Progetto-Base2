<?php
global $conn;
session_start();
include '../ESQLDB2.php';

$emailUtente = $_SESSION['user'];
$ruoloUtente = $_SESSION['tipoUtente'];
if ($ruoloUtente === 'Docente') {
    $query = "SELECT Titolo, Data, Testo, EmailStudenteMittente FROM Messaggio WHERE EmailDocenteDestinatario = ?";
} else {
    $query = "SELECT Titolo, Data, Testo, COALESCE(EmailDocenteMittente, EmailDocenteDestinatario) AS EmailMittente FROM Messaggio WHERE (EmailDocenteMittente IS NOT NULL) OR (EmailStudenteMittente = ?)";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $emailUtente);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>Messaggi Ricevuti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"></head>
<body>
<div class="container mt-5">
    <h2>Messaggi Ricevuti</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($row['Titolo']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($row['Testo']); ?></p>
                <p class="card-text"><small class="text-muted">Data: <?php echo $row['Data']; ?></small></p>
                <p class="card-text"><small class="text-muted">Mittente: <?php echo $row[$ruoloUtente === 'Docente' ? 'EmailStudenteMittente' : 'EmailMittente']; ?></small></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"  crossorigin="anonymous"></script></body>
</html>
