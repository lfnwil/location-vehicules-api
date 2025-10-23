<?php
require __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;

function getDatabase() {
    $client = new Client("mongodb://localhost:27017"); 
    return $client->location_vehicules; 
}
