<?php
namespace Controllers;

use Services\UserService;

class UserController {
    private UserService $service;

    public function __construct(UserService $service) {
        $this->service = $service;
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $this->service->createUser($data);
        echo json_encode(['message' => 'Utilisateur créé', 'id' => $id]);
    }

    public function getAll() {
        echo json_encode($this->service->getAllUsers());
    }

    public function getById(string $id) {
        echo json_encode($this->service->getUserById($id));
    }

    public function update(string $id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $this->service->updateUser($id, $data);
        echo json_encode(['message' => 'Utilisateur mis à jour']);
    }

    public function delete(string $id) {
        $this->service->deleteUser($id);
        echo json_encode(['message' => 'Utilisateur supprimé']);
    }
}
