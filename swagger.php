<?php
require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/VehicleController.php';
require_once __DIR__ . '/controllers/ReservationController.php';

use OpenApi\Generator;

header('Content-Type: application/json');

$openapi = Generator::scan([
    __DIR__ . '/config'
]);

echo $openapi->toJson();
