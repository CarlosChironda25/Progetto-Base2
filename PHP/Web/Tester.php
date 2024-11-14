<?php
include_once '../ESQLDB2.php';  // Assicurati che questa connessione venga gestita correttamente

global $conn;

class Tester
{
    private $conn;

    // Costruttore che prende la connessione
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function inserisciRigaInTabellaDinamica($nomeTabella, $datiRiga) {
        // Controlla se i dati sono validi
        if (empty($datiRiga) || !is_array($datiRiga)) {
            return "I dati per l'inserimento non sono validi.";
        }

        // Costruisce la parte delle colonne
        $colonne = implode(", ", array_map(function($colonna) { return "`$colonna`"; }, array_keys($datiRiga)));

        // Costruisce la parte dei valori
        $valori = implode(", ", array_map(function($valore) {
            if ($valore === 'DEFAULT') {
                return "DEFAULT";  // Inserisce il valore DEFAULT senza virgolette
            } elseif ($valore === 'NULL') {
                return "NULL";  // Inserisce il valore NULL senza virgolette
            } else {
                return "'" . $this->conn->real_escape_string($valore) . "'";  // Altri valori con virgolette
            }
        }, array_values($datiRiga)));

        // Crea la query di inserimento
        $queryInserimento = "INSERT INTO `$nomeTabella` ($colonne) VALUES ($valori);";

        // Esegui la query di inserimento
        if ($this->conn->query($queryInserimento)) {
            return true;
        } else {
            // In caso di errore, ritorna il messaggio di errore
            return "Errore nell'inserimento: " . $this->conn->error;
        }
    }
}

// Crea un'istanza della classe Tester, passando la connessione
$tester = new Tester($conn);

// Dati da inserire
$datiRiga = [
    'Data' => '22/10/2024',
    'Nome' => 'Base di dati',
];

// Nome della tabella in cui inserire i dati
$nomeTabella = 'Esame';

// Chiamata alla funzione
$result = $tester->inserisciRigaInTabellaDinamica($nomeTabella, $datiRiga);

// Mostra il risultato
echo $result;
?>
