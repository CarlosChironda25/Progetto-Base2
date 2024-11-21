<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Determina se l'utente è studente o docente
$isDocente = ($_SESSION['tipoUtente'] === 'Docente');
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../includes/header.php'; ?>
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            display: flex;
            background-color: #f4f7fa;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            min-width: 250px;
            max-width: 250px;
            background-color: #343a40;
            color: #fff;
            padding-top: 1.5rem;
            position: fixed;
            height: 100vh;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 0.8rem;
            display: block;
            border-radius: 0.3rem;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #495057;
            color: #fff;
        }
        .sidebar h4 {
            text-align: center;
            padding-bottom: 1rem;
            color: #fff;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            width: 100%;
        }
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        .card h5 {
            font-weight: bold;
        }
        .btn-logout {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            font-weight: bold;
        }
    </style>
</head>
<body>
<!-- Barra Laterale -->
<div class="sidebar">
    <h4>Benvenuto, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h4>
    <a href="#sezione-docente" class="<?php echo $isDocente ? 'active' : ''; ?>"><i class="fas fa-chalkboard-teacher"></i> Sezione Docente</a>
    <a href="#sezione-studente" class="<?php echo !$isDocente ? 'active' : ''; ?>"><i class="fas fa-user-graduate"></i> Sezione Studente</a>
    <a href="#statistiche"><i class="fas fa-chart-line"></i> Statistiche</a>
    <a href="logout.php" class="btn btn-danger btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Contenuto Principale -->
<div class="main-content">
    <h2 class="text-center mb-4">Dashboard</h2>



    <!-- Sezione Docente -->
    <?php if ($isDocente): ?>
        <div id="sezione-docente" class="card">
            <div class="card-body">
                <h5 class="card-title">Opzioni Docente</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="Docente/CreaTabella.php"><i class="fas fa-table"></i> Crea Tabella di Esercizio</a></li>
                    <li class="list-group-item"><a href="Docente/CreaTest.php"><i class="fas fa-file-alt"></i> Crea Test</a></li>
                    <li class="list-group-item"><a href="Docente/AggiungeQuesito.php"><i class="fas fa-question-circle"></i> Crea Quesito</a></li>
                    <li class="list-group-item"><a href="Docente/AbilitazioneRisposte.php"><i class="fas fa-eye-slash"></i> Abilita/Disabilita Visualizzazione</a></li>
                    <li class="list-group-item"><a href="Docente/OpzioneQuesito.php"><i class="fas fa-list-ul"></i> Aggiungere Opzioni al Quesito</a></li>
                    <li class="list-group-item"><a href="Docente/AggiungeSoluzione.php"><i class="fas fa-list-ul"></i> Aggiungere Soluzione al Quesito</a></li>

                    <!-- <li class="list-group-item"><a href="Docente/aggiungi_riferimento.php"><i class="fas fa-link"></i> Aggiunge Riferimento Test</a></li>-->
                    <li class="list-group-item"><a href="Docente/Inserisci_riga.html"><i class="fas fa-eye"></i> Aggiunge Riga </a></li>
                    <li class="list-group-item"><a href="Docente/InviaMessaggio.php"><i class="fas fa-envelope"></i> Gestione Messaggio</a></li>

                </ul>
            </div>
        </div>
    <?php endif; ?>

    <!-- Sezione Studente -->
    <?php if (!$isDocente): ?>
        <div id="sezione-studente" class="card">
            <div class="card-body">
                <h5 class="card-title">Opzioni Studente</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="Studenti/VisualizzaQuesiti.php"><i class="fas fa-pencil-alt"></i> Inserisci Risposta</a></li>
                    <li class="list-group-item"><a href="Studenti/VisualizzaEsitoChiuso.php"><i class="fas fa-check-circle"></i> Visualizza Esito</a></li>
                    <li class="list-group-item"><a href="Studenti/EsitoCodice.php"><i class="fas fa-check-circle"></i> Visualizza Esito codice</a></li>
                    <li class="list-group-item"><a href="Studenti/VisualizzaSoluzioneChiusa.php"><i class="fas fa-check-circle"></i> Visualizza Soluzione Chiusa</a></li>
                    <li class="list-group-item"><a href="Studenti/VisualizzaSoluzione.php"><i class="fas fa-check-circle"></i> Visualizza Soluzione</a></li>
                    <li class="list-group-item"><a href="Studenti/InviaMessaggio.php"><i class="fas fa-envelope"></i> Gestione Messaggio</a></li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    <!-- Opzioni Comuni -->
    <div id="opzioni-comuni" class="card">
        <div class="card-body">
            <h5 class="card-title">Opzioni Comuni</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="Messaggio.php"><i class="fas fa-envelope"></i> Visualizza Messaggi</a></li>
                <li class="list-group-item"><a href="VisualizzaTabella.html"><i class="fas fa-table"></i> Visualizza Tabelle</a></li>
            </ul>
        </div>
    </div>

    <!-- Statistiche -->
    <div id="statistiche" class="card">
        <div class="card-body">
            <h5 class="card-title">Statistiche</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="classifica_test_completati.php"><i class="fas fa-trophy"></i> Classifica Test Completati</a></li>
                <li class="list-group-item"><a href="classifica_risposte_corrette.php"><i class="fas fa-star"></i> Classifica Risposte Corrette</a></li>
                <li class="list-group-item"><a href="classifica_quesiti.php"><i class="fas fa-question"></i> Classifica Quesiti Risposti</a></li>
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
