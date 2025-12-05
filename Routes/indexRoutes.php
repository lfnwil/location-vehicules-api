<?php
require_once __DIR__ . '/userRoutes.php';
require_once __DIR__ . '/vehicleRoutes.php';
require_once __DIR__ . '/reservationRoutes.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// USERS
if (strpos($uri, '/users') === 0) {
    handleUserRoutes($uri, $method);
    exit;
}

// VEHICULES
if (strpos($uri, '/vehicules') === 0) {
    handleVehicleRoutes($uri, $method);
    exit;
}

// RESERVATIONS
if (strpos($uri, '/reservations') === 0) {
    handleReservationRoutes($uri, $method);
    exit;
}

// Si aucune route capturée :
http_response_code(404);
echo json_encode(["error" => "Endpoint non trouvé"]);
