<?php
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../repositories/VehicleRepository.php';
    require_once __DIR__ . '/../services/VehicleService.php';
    require_once __DIR__ . '/../controllers/VehicleController.php';
    require_once __DIR__ . '/../vendor/autoload.php';

    use function Config\getDatabase;
    use Controllers\VehicleController;
    use Services\VehicleService;
    use Repositories\VehicleRepository;

$db = getDatabase();
$vehicleRepository = new VehicleRepository($db);
$vehicleService = new VehicleService($vehicleRepository);
$vehicleController = new VehicleController($vehicleService);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/vehicules' && $method === 'GET') {
    $vehicleController->getAll();
    exit;
}

if ($uri === '/vehicules' && $method === 'POST') {
    $vehicleController->create();
    exit;
}

if (preg_match('#^/vehicules/([a-f0-9]{24})$#', $uri, $matches)) {
    $id = $matches[1];

    switch ($method) {
        case 'GET':
            $vehicleController->getById($id);
            break;
        case 'PUT':
            $vehicleController->update($id);
            break;
        case 'DELETE':
            $vehicleController->delete($id);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            break;
    }
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint non trouvé']);
