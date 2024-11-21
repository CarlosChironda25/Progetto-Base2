<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();
include '../../ESQLDB2.php';
if (!isset($_SESSION['user'] ) || $_SESSION['tipoUtente'] != 'Docente') {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}



$message = '';
$attributi = [];
$vincoli = [];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $conn;
    // Raccogli informazioni dalla richiesta POST
    $nomeTabella = $_POST['nomeTabella'];
    $emailDocente = $_SESSION['user'] ; // Recupera l'email del docente dalla sessione

    $conn->begin_transaction();
    // Raccogli attributi
    for ($i = 0; $i < count($_POST['nomiAttributi']); $i++) {
        if (!empty($_POST['nomiAttributi'][$i]) && !empty($_POST['tipiAttributi'][$i])) {
            $attributi[] = [
                'nome' => $_POST['nomiAttributi'][$i],
                'tipo' => $_POST['tipiAttributi'][$i],
                'primario' => isset($_POST['primari'][$i]) ? 1 : 0
            ];
        }
    }

    // Raccogli vincoli
    for ($i = 0; $i < count($_POST['attributoVincolo1']); $i++) {
        if (!empty($_POST['attributoVincolo1'][$i]) && !empty($_POST['attributoVincolo2'][$i])) {
            $vincoli[] = [
                'attributo1' => $_POST['attributoVincolo1'][$i],
                'attributo2' => $_POST['attributoVincolo2'][$i],
                'tabella2' => $_POST['tabellaVincolo'][$i]
            ];
        }
    }

    // Creazione della tabella
    $conn->begin_transaction();
    try {
        // Crea la tabella di esercizio
        $stmt = $conn->prepare("INSERT INTO Tabella_Esercizio (Nome, EmailDocente) VALUES (?, ?)");
        $stmt->bind_param("ss", $nomeTabella, $emailDocente);
        $logger->logEvent($emailDocente, 'Tabella creata con sucesso da: ', ['mail_Utente' => $_SESSION['user'] ]);
        $stmt->execute();
        // Aggiungi attributi
        foreach ($attributi as $attributo) {
            $stmt = $conn->prepare("INSERT INTO Attributi (Nome, NomeTabella, Tipo, Primario) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $attributo['nome'], $nomeTabella, $attributo['tipo'], $attributo['primario']);
            $logger->logEvent($emailDocente, 'Attributi inseriti con sucesso da : ', ['mail_Utente' => $_SESSION['user'] ]);
            $stmt->execute();
        }

        // Aggiungi vincoli di integrità
        foreach ($vincoli as $vincolo) {
            $stmt = $conn->prepare("INSERT INTO Integrita_Referenziale (NomeAttributo1, NomeAttributo2, Tabella1, Tabella2) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $vincolo['attributo1'], $vincolo['attributo2'], $nomeTabella, $vincolo['tabella2']);
            $logger->logEvent($emailDocente, 'Vincoli di integrità inserito con sucesso: ', ['mail_Utente' => $_SESSION['user'] ]);
            $stmt->execute();
        }

        // Commit della transazione
        $conn->commit();
        $message = "Tabella di esercizio creata con successo con attributi e vincoli.";
        $logger-> logEvent($emailDocente,'Tabella di esercizio creata con successo con attributi e vincoli' , ['mail_Utente' => $_SESSION['user'] ]);
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Errore nella creazione della tabella: " . $e->getMessage();
        $logger-> logEvent($emailDocente,'Errore nella creazione della tabella' , ['mail_Utente' => $_SESSION['user'] ]);

    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../../includes/header.php'; ?>
    <title>Crea Tabella di Esercizio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
            <h3 class="card-title mb-0">Crea Tabella di Esercizio</h3>
        </div>
        <div class="card-body">
            <?php if($message): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nomeTabella" class="form-label">Nome Tabella</label>
                    <input type="text" name="nomeTabella" class="form-control" required>
                </div>

                <h4 class="mt-4">Attributi</h4>
                <div id="attributiContainer">
                    <div class="d-flex align-items-center mb-3 attributo">
                        <input type="text" name="nomiAttributi[]" placeholder="Nome Attributo" class="form-control me-2" required>
                        <input type="text" name="tipiAttributi[]" placeholder="Tipo" class="form-control me-2" required>
                        <label class="form-check-label">
                            <input type="checkbox" name="primari[]" value="1" class="form-check-input"> Primario
                        </label>
                    </div>
                </div>
                <button type="button" onclick="aggiungiAttributo()" class="btn btn-outline-primary mb-3">
                    <i class="bi bi-plus-circle"></i> Aggiungi Attributo
                </button>

                <h4 class="mt-4">Vincoli di Integrità</h4>
                <div id="vincoliContainer">
                    <div class="d-flex align-items-center mb-3 vincolo">
                        <input type="text" name="attributoVincolo1[]" placeholder="Attributo 1" class="form-control me-2" >
                        <input type="text" name="tabellaVincolo[]" placeholder="Tabella di Riferimento" class="form-control me-2" >
                        <input type="text" name="attributoVincolo2[]" placeholder="Attributo 2" class="form-control" >
                    </div>
                </div>
                <button type="button" onclick="aggiungiVincolo()" class="btn btn-outline-secondary mb-3">
                    <i class="bi bi-link-45deg"></i> Aggiungi Vincolo
                </button>

                <button type="submit" class="btn btn-success mt-3"><i class="bi bi-save"></i> Crea Tabella</button>
            </form>
        </div>
    </div>
</div>

<script>
    function aggiungiAttributo() {
        const container = document.getElementById('attributiContainer');
        const nuovoAttributo = document.createElement('div');
        nuovoAttributo.className = 'd-flex align-items-center mb-3 attributo';
        nuovoAttributo.innerHTML = `
        <input type="text" name="nomiAttributi[]" placeholder="Nome Attributo" class="form-control me-2" >
        <input type="text" name="tipiAttributi[]" placeholder="Tipo" class="form-control me-2" >
        <label class="form-check-label">
            <input type="checkbox" name="primari[]" value="1" class="form-check-input"> Primario
        </label>
    `;
        container.appendChild(nuovoAttributo);
    }

    function aggiungiVincolo() {
        const container = document.getElementById('vincoliContainer');
        const nuovoVincolo = document.createElement('div');
        nuovoVincolo.className = 'd-flex align-items-center mb-3 vincolo';
        nuovoVincolo.innerHTML = `
        <input type="text" name="attributoVincolo1[]" placeholder="Attributo 1" class="form-control me-2" >
        <input type="text" name="tabellaVincolo[]" placeholder="Tabella di Riferimento" class="form-control me-2" >
        <input type="text" name="attributoVincolo2[]" placeholder="Attributo 2" class="form-control" >
    `;
        container.appendChild(nuovoVincolo);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css">
<?php include '../../includes/footer.php'; ?>
</body>
</html>
