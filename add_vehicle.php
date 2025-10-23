<?php
require 'vendor/autoload.php'; 
use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;

$mongo = new Client("mongodb://localhost:27017");
$db = $mongo->location_vehicules;
$vehicules = $db->vehicules;

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['type'], $data['marque'], $data['modele'], $data['prix_journalier'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}


$vehicule = [
    "type" => $data['type'],
    "marque" => $data['marque'],
    "modele" => $data['modele'],
    "kilometrage" => $data['kilometrage'] ?? 0,
    "prix_journalier" => $data['prix_journalier'],
    "disponibilite" => true,
    "created_at" => new UTCDateTime()
];

$result = $vehicules->insertOne($vehicule);

echo json_encode([
    'message' => 'Véhicule ajouté',
    'id' => (string)$result->getInsertedId()
]);