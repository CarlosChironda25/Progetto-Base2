<?php
session_start();
if (!isset($_SESSION['user'] ) || $_SESSION['tipoUtente'] != 'Studente') {
    header("Location: ../index.php");
    exit();
}
include '../../ESQLDB2.php';

global $conn;
$quesiti = [];

function eseguiProcedura($conn, $procedura) {
    $resultArray = [];
    try {
        $stmt = $conn->prepare("CALL $procedura()");
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $resultArray[] = $row;
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        echo "Errore: " . $e->getMessage();
    }
    return $resultArray;
}

$quesiti['chiuso'] = eseguiProcedura($conn, "VisualizzaQuesitoChiuso");
$quesiti['codice'] = eseguiProcedura($conn, "VisualizzaQuesitoCodice");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Visualizzazione Quesiti</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8fafc;
        }
        .container {
            max-width: 1000px;
            margin-top: 30px;
        }
        .section-title {
            color: #4a4a4a;
            font-weight: bold;
            margin-top: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
        }
        .card {
            border: none;
            margin-bottom: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
        }
        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        }
        .card-body {
            padding: 16px;
        }
        .btn-rispondi {
            background-color: #1d4ed8;
            color: #fff;
            border-radius: 5px;
            font-size: 0.9rem;
            padding: 8px 16px;
        }
        .btn-rispondi:hover {
            background-color: #1e3a8a;
        }
        .description {
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Visualizzazione Quesiti</h2>

    <?php foreach ($quesiti as $tipo => $dati): ?>
        <h4 class="section-title"><?= $tipo === 'chiuso' ? 'Quesiti Chiuso' : 'Quesiti Codice' ?></h4>
        <?php if (!empty($dati)): ?>
            <div class="row">
                <?php foreach ($dati as $tabella): ?>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($tabella['TitoloTest']); ?></h5>
                                <p class="description"><?php echo htmlspecialchars($tabella['Descrizione']); ?></p>
                                <?php
                                echo "<a href=\"Rispondi_quesito.php?idQuesito=" . htmlspecialchars($tabella['Id']) . "&titoloTest=" . htmlspecialchars($tabella['TitoloTest']) . "&tipo=" . htmlspecialchars($tipo) . "\" class=\"btn btn-rispondi float-end\">Rispondi al Quesito</a>";
                                ?>
                                  <?php   echo $tabella['Id']?>

                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted text-center mt-3">Nessun quesito disponibile per questa categoria.</p>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
