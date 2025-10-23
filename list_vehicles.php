<?php
require 'vendor/autoload.php'; 
use MongoDB\Client;

$mongo = new Client("mongodb://localhost:27017");
$db = $mongo->location_vehicules;
$vehicules = $db->vehicules;

$allVehicles = $vehicules->find()->toArray();

foreach ($allVehicles as &$v) {
    $v['_id'] = (string)$v['_id'];
    if ($v['created_at'] instanceof MongoDB\BSON\UTCDateTime) {
    $v['created_at'] = $v['created_at']->toDateTime()->format('Y-m-d H:i:s');
    }
}

echo json_encode($allVehicles);
