<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../controllers/UserController.php';

use function Config\getDatabase;
use Controllers\UserController;
use Services\UserService;
use Repositories\UserRepository;

function handleUserRoutes(string $uri, string $method)
{
    $db = getDatabase();
    $userRepository = new UserRepository($db);
    $userService = new UserService($userRepository);
    $userController = new UserController($userService);

    // /users
    if ($uri === '/users') {
        if ($method === 'GET') {
            $userController->getAll();
            return;
        }
        if ($method === 'POST') {
            $userController->create();
            return;
        }
    }

    // /users/{id}
    if (preg_match('#^/users/([a-f0-9]{24})$#', $uri, $matches)) {
        $id = $matches[1];

        if ($method === 'GET') {
            $userController->getById($id);
            return;
        }
        if ($method === 'PUT') {
            $userController->update($id);
            return;
        }
        if ($method === 'DELETE') {
            $userController->delete($id);
            return;
        }
    }

    http_response_code(404);
    echo json_encode(["error" => "Endpoint utilisateurs non trouvé"]);
}
