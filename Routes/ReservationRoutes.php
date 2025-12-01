<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/ReservationRepository.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../repositories/VehicleRepository.php';
require_once __DIR__ . '/../services/ReservationService.php';
require_once __DIR__ . '/../controllers/ReservationController.php';

use function Config\getDatabase;
use Repositories\ReservationRepository;
use Repositories\UserRepository;
use Repositories\VehicleRepository;
use Services\ReservationService;
use Controllers\ReservationController;

function handleReservationRoutes(string $uri, string $method) {
    $db = getDatabase();

    $reservationRepo = new ReservationRepository($db);
    $userRepo = new UserRepository($db);
    $vehicleRepo = new VehicleRepository($db);
    $reservationService = new ReservationService($reservationRepo, $userRepo, $vehicleRepo);
    $reservationController = new ReservationController($reservationService);

    if ($uri === '/reservations' && $method === 'GET') {
        $reservationController->getAll();
        return;
    }

    if ($uri === '/reservations' && $method === 'POST') {
        $reservationController->create();
        return;
    }

    if (preg_match('#^/reservations/([a-f0-9]{24})$#', $uri, $matches)) {
        $id = $matches[1];
        switch ($method) {
            case 'GET':
                $reservationController->getById($id);
                break;
            case 'PUT':
                $reservationController->update($id);
                break;
            case 'DELETE':
                $reservationController->delete($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Méthode non autorisée']);
                break;
        }
        return;
    }

    http_response_code(404);
    echo json_encode(['error' => 'Endpoint non trouvé']);
}
