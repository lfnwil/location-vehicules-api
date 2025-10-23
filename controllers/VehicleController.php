<?php
namespace Controllers;

use Services\VehicleService;

class VehicleController {
    private $service;

    public function __construct($db) {
        $this->service = new VehicleService($db);
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        $result = $this->service->createVehicle($data);
        echo json_encode($result);
    }

    public function getAll() {
        $result = $this->service->getAllVehicles();
        echo json_encode($result);
    }

    public function getById(string $id) {
        $result = $this->service->getVehicleById($id);
        if ($result) {
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Véhicule non trouvé']);
        }
    }

    public function update(string $id) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        $message = $this->service->updateVehicle($id, $data);
        echo json_encode(['message' => $message]);
    }

    public function delete(string $id) {
        $message = $this->service->deleteVehicle($id);
        echo json_encode(['message' => $message]);
    }
}
