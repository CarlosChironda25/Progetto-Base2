<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include '../../ESQLDB2.php';
global $conn;

$tabelleEsercizio = [];

try {
    $stmt = $conn->prepare("SELECT Nome FROM Tabella_Esercizio WHERE EmailDocente = ?");
    $stmt->bind_param("s", $_SESSION['user']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $tabelleEsercizio[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    echo "Errore: " . $e->getMessage();
}

?>

    <!DOCTYPE html>
    <html lang="it">
    <head>
        <?php include '../../includes/header.php'; ?>
        <title>Dashboard Docente</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    </head>
    <body>
    <div class="container">
        <h2>Seleziona una Tabella di Esercizio per Inserire una Riga</h2>
        <ul class="list-group">
            <?php foreach ($tabelleEsercizio as $tabella): ?>
                <li class="list-group-item">
                    <a href="inserimento_riga.php?tabella=<?php echo htmlspecialchars($tabella['Nome']); ?>">
                        <?php echo htmlspecialchars($tabella['Nome']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    </body>
    </html>
<?php
