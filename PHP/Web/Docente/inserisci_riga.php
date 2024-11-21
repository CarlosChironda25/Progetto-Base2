<?php
// Attiva gli errori per il debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
global $conn;
// Includi la connessione al database
require_once '../../ESQLDB2.php';

session_start();
require_once  '../ControllerMongoDBLogger.php';
$logger = new ControllerMongoDBLogger();
// Verifica che l'utente sia autenticato|| $_SESSION['tipoUtente'] != 'Docente'
if (!isset($_SESSION['user'] ) ) {
    header("Location: ../index.php");
    $logger->logEvent($_SESSION['user'], 'Acesso non consetito a  : ', ['mail_Utente' => $_SESSION['user'] ]);

    exit();
}

// Ricevi i parametri dal form
$nomeTabella = $_POST['nomeTabella'];  // Il nome della tabella di esercizio
$data = [];

// Raccogli tutti i dati dal form
foreach ($_POST as $key => $value) {
    if ($key !== "nomeTabella") {  // Escludi il nome della tabella
        $data[$key] = $value;  // Aggiungi i dati degli attributi
    }
}

// Codifica i dati come JSON
$inputRiga = json_encode($data);

try {
    // Verifica che esista un record nella Tabella_Esercizio con il nome della tabella
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Tabella_Esercizio WHERE Nome = ?");
    $stmt->bind_param("s", $nomeTabella);
    $stmt->execute();
    $result = $stmt->get_result();
    $tableExists = $result->fetch_row()[0];

    if ($tableExists == 0) {
        throw new Exception("La tabella di esercizio '$nomeTabella' non esiste.");
    }

    // Verifica vincoli di integrità referenziale (se presenti)
    $stmt = $conn->prepare("SELECT NomeAttributo1, Tabella1, NomeAttributo2, Tabella2 FROM Integrita_Referenziale WHERE Tabella1 = ? OR Tabella2 = ?");
    $stmt->bind_param("ss", $nomeTabella, $nomeTabella);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row['Tabella1'] == $nomeTabella && isset($data[$row['NomeAttributo1']])) {
            $valore1 = $data[$row['NomeAttributo1']];

            // Verifica se il valore è presente in una riga corrispondente nella tabella RIGA
            $stmt_check1 = $conn->prepare("SELECT COUNT(*) FROM RIGA WHERE NomeTabella = ? AND Valori LIKE ?");
            $values1 = "%" . $valore1 . "%";
            $stmt_check1->bind_param("ss", $row['Tabella2'], $values1);
            $stmt_check1->execute();
            $check1_result = $stmt_check1->get_result();

            if ($check1_result->fetch_row()[0] == 0) {
                throw new Exception("Valore '$valore1' non valido per l'attributo '{$row['NomeAttributo1']}' con riferimento alla tabella '{$row['Tabella2']}'");
            }
        }

        if ($row['Tabella2'] == $nomeTabella && isset($data[$row['NomeAttributo2']])) {
            $valore2 = $data[$row['NomeAttributo2']];

            // Verifica se il valore è presente in una riga corrispondente nella tabella RIGA
            $stmt_check2 = $conn->prepare("SELECT COUNT(*) FROM RIGA WHERE NomeTabella = ? AND Valori LIKE ?");
            $values2 = "%" . $valore2 . "%";
            $stmt_check2->bind_param("ss", $row['Tabella1'], $values2);
            $stmt_check2->execute();
            $check2_result = $stmt_check2->get_result();

            if ($check2_result->fetch_row()[0] == 0) {
                throw new Exception("Valore '$valore2' non valido per l'attributo '{$row['NomeAttributo2']}' con riferimento alla tabella '{$row['Tabella1']}'");
            }
        }
    }

    // Inserisci i dati nella tabella RIGA
    $sql = "INSERT INTO RIGA (NomeTabella, Valori) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nomeTabella, $inputRiga);
    $stmt->execute();
// Aggiorna il conteggio delle righe nella tabella Tabella_Esercizio
    $stmt_update = $conn->prepare("UPDATE Tabella_Esercizio SET NumeroRighe = NumeroRighe + 1 WHERE Nome = ?");
    $stmt_update->bind_param("s", $nomeTabella);
    $stmt_update->execute();


   $logger->logEvent($_SESSION['user'],'  Riga inserita con successo, operazione fatta da:',['email'=>$_SESSION['user']] );
    echo "Riga inserita con successo!<br>";

} catch (Exception $e) {
    echo "Errore: " . $e->getMessage();
}
?>
