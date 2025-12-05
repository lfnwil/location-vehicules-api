<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/ReservationRepository.php';
require_once __DIR__ . '/../repositories/VehicleRepository.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../services/ReservationService.php';
require_once __DIR__ . '/../controllers/ReservationController.php';

use function Config\getDatabase;
use Controllers\ReservationController;
use Services\ReservationService;
use Repositories\ReservationRepository;
use Repositories\VehicleRepository;
use Repositories\UserRepository;

function handleReservationRoutes(string $uri, string $method)
{
    $db = getDatabase();

    $reservationRepository = new ReservationRepository($db);
    $vehicleRepository = new VehicleRepository($db);
    $userRepository = new UserRepository($db);

    $reservationService = new ReservationService($reservationRepository, $vehicleRepository, $userRepository);
    $reservationController = new ReservationController($reservationService);

    if ($uri === '/reservations') {
        if ($method === 'GET') {
            $reservationController->getAll();
            return;
        }
        if ($method === 'POST') {
            $reservationController->create();
            return;
        }
    }

    if (preg_match('#^/reservations/([a-f0-9]{24})$#', $uri, $matches)) {
        $id = $matches[1];

        if ($method === 'GET') {
            $reservationController->getById($id);
            return;
        }
        if ($method === 'PUT') {
            $reservationController->update($id);
            return;
        }
        if ($method === 'DELETE') {
            $reservationController->delete($id);
            return;
        }
    }

    http_response_code(404);
    echo json_encode(["error" => "Endpoint reservations non trouvé"]);
}
