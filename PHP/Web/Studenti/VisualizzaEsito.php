<?php
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $conn;
session_start();
include '../../ESQLDB2.php';
require_once  '../ControllerMongoDBLogger.php';
$logger=new ControllerMongoDBLogger();
if (!isset($_SESSION['user']) || $_SESSION['tipoUtente'] != 'Studente') {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}

$emailStudente = $_SESSION['user'];

// Recupera l'elenco dei test a cui lo studente ha risposto
$queryTest = "SELECT DISTINCT TitoloTest FROM RispostaChiusa WHERE EmailStudente = ?";
$stmtTest = $conn->prepare($queryTest);
$stmtTest->bind_param("s", $emailStudente);
$stmtTest->execute();
$resultTest = $stmtTest->get_result();
$testDisponibili = $resultTest->fetch_all(MYSQLI_ASSOC);

// Ottieni il titolo del test selezionato
$titoloTest = $_POST['titoloTest'] ?? $testDisponibili[0]['TitoloTest'] ?? '';

// Chiama la procedura solo se il titolo del test è specificato
if (!empty($titoloTest)) {
    $query = "CALL CalcolaEsitoTest(?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $emailStudente, $titoloTest);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <title>Esito Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container my-5">
    <!-- Pulsante "Indietro" -->
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>

    <div class="card shadow-lg">
        <div class="card-header text-white bg-primary">
            <h3 class="card-title mb-0">Esito Test</h3>
        </div>
        <div class="card-body">
            <h4>Seleziona un Test per Visualizzare l'Esito</h4>

            <!-- Menu a discesa per selezionare il test -->
            <form method="post" action="" class="mb-4">
                <label for="titoloTest" class="form-label">Titolo del Test:</label>
                <select name="titoloTest" id="titoloTest" class="form-select" style="max-width: 300px;" onchange="this.form.submit()">
                    <?php foreach ($testDisponibili as $test): ?>
                        <option value="<?php echo htmlspecialchars($test['TitoloTest']); ?>"
                            <?php echo $test['TitoloTest'] === $titoloTest ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($test['TitoloTest']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if (!empty($titoloTest) && isset($result)): ?>
                <h5 class="text-muted">Esito del Test: <?php echo htmlspecialchars($titoloTest); ?></h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mt-3">
                        <thead class="table-primary">
                        <tr>
                            <th>Id Quesito</th>
                            <th>Opzione Scelta</th>
                            <th>Testo Risposta</th>
                            <th>Esito</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['IdQuesito']); ?></td>
                                <td><?php echo htmlspecialchars($row['OpzioneScelta']); ?></td>
                                <td><?php echo htmlspecialchars($row['TestoRisposta']); ?></td>
                                <td><?php echo htmlspecialchars($row['Esito']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">Seleziona un test per visualizzare i dettagli dell'esito.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
