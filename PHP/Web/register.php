<?php
include '../includes/functions.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $tipoUtente = $_POST['tipoUtente'];
    $telefono = $_POST['telefono'];

    // Variabili opzionali in base al tipo di utente
    $annoImmatricolazione = null;
    $nomeDipartimento = null;
    $nomeCorso = null;
    $codice = null; // Codice aggiunto

    if ($tipoUtente === 'Studente') {
        $annoImmatricolazione = $_POST['annoImmatricolazione'];
        $codice = $_POST['codice']; // Recupera il Codice solo per lo Studente
    } else if ($tipoUtente === 'Docente') {
        $nomeDipartimento = $_POST['nomeDipartimento'];
        $nomeCorso = $_POST['nomeCorso'];
    }

    // Chiamata alla funzione di registrazione
    if (registerUser($email, $password, $nome, $cognome, $telefono, $tipoUtente, $annoImmatricolazione, $nomeDipartimento, $nomeCorso, $codice)) {
        $message = "Registrazione completata con successo!";
        header("Location: index.php");
    } else {
        $message = "Errore durante la registrazione.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../includes/header.php'; ?>
    <title>Registrazione</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ffafbd, #ffc3a0);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-bottom: 1.5rem;
            color: #d9534f; /* Colore rosso per il titolo */
        }
        .form-label {
            color: #6f42c1; /* Colore viola per le etichette */
        }
        .alert {
            border-radius: 0.5rem;
        }
        button {
            background-color: #28a745; /* Colore verde per il bottone */
            border: none;
        }
        button:hover {
            background-color: #218838; /* Colore verde scuro al passaggio del mouse */
        }
    </style>
</head>
<body>
<div class="register-container">
    <h2 class="text-center"><i class="fas fa-user-plus"></i> Registrati</h2>
    <?php if($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="cognome" class="form-label">Cognome</label>
            <input type="text" name="cognome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Telefono</label>
            <input type="number" name="telefono" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="tipoUtente" class="form-label">Tipo Utente</label>
            <select name="tipoUtente" class="form-select" required>
                <option value="Studente">Studente</option>
                <option value="Docente">Docente</option>
            </select>
        </div>
        <div class="mb-3" id="annoImmatricolazioneDiv" style="display:none;">
            <label for="annoImmatricolazione" class="form-label">Anno Immatricolazione</label>
            <input type="number" name="annoImmatricolazione" class="form-control">
        </div>
        <div class="mb-3" id="codiceDiv" style="display:none;">
            <label for="codice" class="form-label">Codice Studente</label>
            <input type="text" name="codice" class="form-control">
        </div>
        <div class="mb-3" id="nomeDipartimentoDiv" style="display:none;">
            <label for="nomeDipartimento" class="form-label">Nome Dipartimento</label>
            <input type="text" name="nomeDipartimento" class="form-control">
        </div>
        <div class="mb-3" id="nomeCorsoDiv" style="display:none;">
            <label for="nomeCorso" class="form-label">Nome Corso</label>
            <input type="text" name="nomeCorso" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Registrati</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tipoUtente = document.querySelector('select[name="tipoUtente"]').value;
            updateFormFields(tipoUtente);
        });

        document.querySelector('select[name="tipoUtente"]').addEventListener('change', function () {
            updateFormFields(this.value);
        });

        function updateFormFields(tipoUtente) {
            const studenteFields = ['annoImmatricolazioneDiv', 'codiceDiv'];
            const docenteFields = ['nomeDipartimentoDiv', 'nomeCorsoDiv'];
            if (tipoUtente === 'Studente') {
                showFields(studenteFields);
                hideFields(docenteFields);
            } else {
                hideFields(studenteFields);
                showFields(docenteFields);
            }
        }

        function showFields(fields) {
            fields.forEach(id => document.getElementById(id).style.display = 'block');
        }

        function hideFields(fields) {
            fields.forEach(id => document.getElementById(id).style.display = 'none');
        }
    </script>
</div>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
