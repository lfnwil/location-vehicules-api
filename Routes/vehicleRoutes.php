<?php
use Controllers\VehicleController;

if (!isset($db)) {
    http_response_code(500);
    echo json_encode(['error' => 'Base de données non initialisée']);
    exit;
}

$controller = new VehicleController($db);

$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$route = $uri[0] ?? '';
$id = $uri[1] ?? null;

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        if ($route === 'vehicules') $controller->create();
        else http_response_code(404);
        break;

    case 'GET':
        if ($route === 'vehicules') {
            if ($id) $controller->getById($id);
            else $controller->getAll();
        } else http_response_code(404);
        break;

    case 'PUT':
        if ($route === 'vehicules' && $id) $controller->update($id);
        else http_response_code(404);
        break;

    case 'DELETE':
        if ($route === 'vehicules' && $id) $controller->delete($id);
        else http_response_code(404);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
        break;
}
