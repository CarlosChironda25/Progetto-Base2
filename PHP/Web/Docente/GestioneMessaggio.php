
<?php
global $conn;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include '../../ESQLDB2.php';

if (!isset($_SESSION['user']) || $_SESSION['tipoUtente'] != 'Docente') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Messaggi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Stile per il menu e la sezione di contenuto */
        #menu {
            width: 200px;
            float: left;
            margin-right: 20px;
        }
        #content {
            margin-left: 220px;
        }
    </style>
</head>
<body>
<div class="container my-4">

    <br>
    <div class="mb-3">
        <a href="../Dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Indietro
        </a>
    </div>
    <h2>Gestione Messaggi</h2>

    <!-- Menu laterale -->
    <div id="menu" class="list-group">
        <button onclick="loadPage('../Messaggio.php')" class="list-group-item list-group-item-action">Visualizza Messaggi</button>
        <button onclick="loadPage('InviaMessaggio.php')" class="list-group-item list-group-item-action">Invia Messaggio</button>
    </div>

    <!-- Sezione di contenuto dinamico -->
    <div id="content" class="p-4 border border-primary rounded">
        <h4>Benvenuto! Seleziona un'opzione dal menu a sinistra.</h4>
    </div>
</div>

<script>
    // Funzione per caricare dinamicamente le pagine all'interno del contenitore 'content'
    function loadPage(page) {
        // Esegue una richiesta AJAX per caricare il contenuto della pagina specificata
        fetch(page)
            .then(response => response.text())
            .then(html => {
                document.getElementById('content').innerHTML = html;
            })
            .catch(error => {
                console.error('Errore nel caricamento della pagina:', error);
                document.getElementById('content').innerHTML = "<p class='text-danger'>Errore nel caricamento della pagina.</p>";
            });
    }
</script>
</body>
</html>