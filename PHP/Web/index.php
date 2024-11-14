<?php
include '../includes/functions.php';
require_once 'ControllerMongoDBLogger.php';

$logger = new ControllerMongoDBLogger();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (loginUser($email, $password)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Credenziali errate!";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php include '../includes/header.php'; ?>
    <title>Login</title>
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
        .login-container {
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
        .register-link {
            margin-top: 1rem;
            text-align: center;
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
<div class="login-container">
    <h2 class="text-center"><i class="fas fa-user-lock"></i> Login</h2>
    <?php if($message): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="register-link">
        <p>Non sei registrato? <a href="register.php" class="link-primary">Registrati qui</a></p>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
