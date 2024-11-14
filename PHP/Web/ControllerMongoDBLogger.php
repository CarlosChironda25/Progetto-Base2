<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use MongoDB\Client;

class ControllerMongoDBLogger
{
    private $collection;

    public function __construct() {
        $client = new Client("mongodb://localhost:27017");
        $database = $client->selectDatabase('ESQLDB2');
        $this->collection = $database->selectCollection('log'); // Collezione "log"
    }

    public function logEvent($utente, $azione, $dettagli = []) {
        $log = [
            'utente' => $utente,
            'azione' => $azione,
            'dettagli' => $dettagli,
            'data' => new MongoDB\BSON\UTCDateTime()
        ];
        $this->collection->insertOne($log);
    }
}