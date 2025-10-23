<?php
require 'vendor/autoload.php'; 

function getDatabase() {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    return $client->location_vehicules;
}

$db = getDatabase();
$users = $db->users;

$user = [
    "auth0_id" => "auth0|68f9f70f482e15725ff934d9",
    "email" => "client@test.com",
    "role" => "client",
    "created_at" => new MongoDB\BSON\UTCDateTime()
];

$result = $users->insertOne($user);
echo "Utilisateur ajouté avec l'ID : " . $result->getInsertedId();
