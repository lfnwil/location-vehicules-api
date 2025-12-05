<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/VehicleRepository.php';
require_once __DIR__ . '/../services/VehicleService.php';
require_once __DIR__ . '/../controllers/VehicleController.php';

use function Config\getDatabase;
use Controllers\VehicleController;
use Services\VehicleService;
use Repositories\VehicleRepository;

function handleVehicleRoutes(string $uri, string $method)
{
    $db = getDatabase();
    $vehicleRepository = new VehicleRepository($db);
    $vehicleService = new VehicleService($vehicleRepository);
    $vehicleController = new VehicleController($vehicleService);

    // /vehicules
    if ($uri === '/vehicules') {
        if ($method === 'GET') {
            $vehicleController->getAll();
            return;
        }
        if ($method === 'POST') {
            $vehicleController->create();
            return;
        }
    }

    // /Vehicules/{id}
    if (preg_match('#^/vehicules/([a-f0-9]{24})$#', $uri, $matches)) {
        $id = $matches[1];

        if ($method === 'GET') {
            $vehicleController->getById($id);
            return;
        }
        if ($method === 'PUT') {
            $vehicleController->update($id);
            return;
        }
        if ($method === 'DELETE') {
            $vehicleController->delete($id);
            return;
        }
    }

    http_response_code(404);
    echo json_encode(["error" => "Endpoint véhicules non trouvé"]);
}
