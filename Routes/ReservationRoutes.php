<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/ReservationRepository.php';
require_once __DIR__ . '/../services/ReservationService.php';
require_once __DIR__ . '/../controllers/ReservationController.php';

use function Config\getDatabase;
use Repositories\ReservationRepository;
use Services\ReservationService;
use Controllers\ReservationController;

function handleReservationRoutes(string $uri, string $method) {

    $db = getDatabase();
    $repository = new ReservationRepository($db);
    $service = new ReservationService($repository);
    $controller = new ReservationController($service);

    if ($uri === "/reservations") {
        if ($method === "GET") {
            $controller->getAll();
            return;
        }
        if ($method === "POST") {
            $controller->create();
            return;
        }
    }

    if (preg_match('#^/reservations/([a-f0-9]{24})$#', $uri, $matches)) {
        $id = $matches[1];

        if ($method === "GET") {
            $controller->getById($id);
            return;
        }
        if ($method === "DELETE") {
            $controller->delete($id);
            return;
        }
    }

    http_response_code(404);
    echo json_encode(["error" => "Route réservations introuvable"]);
}
