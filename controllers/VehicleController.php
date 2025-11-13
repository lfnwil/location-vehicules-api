<?php
namespace Controllers;
require_once __DIR__ . '/../services/AuthMiddleware.php';

use Services\VehicleService;

class VehicleController {
    private VehicleService $service;

    public function __construct(VehicleService $service) {
        $this->service = $service;
    }

    public function getAll(): void {
        header('Content-Type: application/json');
        echo json_encode($this->service->getAllVehicles());
    }

    public function getById(string $id): void {
        header('Content-Type: application/json');
        $vehicle = $this->service->getVehicleById($id);
        if ($vehicle) {
            echo json_encode($vehicle);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Véhicule non trouvé']);
        }
    }

    public function create(): void {

        checkAdmin($user);

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['type'], $data['marque'], $data['modele'], $data['prix_journalier'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }
        $data['kilometrage'] = $data['kilometrage'] ?? 0;
        $data['disponibilite'] = true;
        $data['created_at'] = new \MongoDB\BSON\UTCDateTime();

        $id = $this->service->createVehicle($data);
        echo json_encode(['message' => 'Véhicule ajouté', 'id' => $id]);
    }

    public function update(string $id): void {

        checkAdmin($user);

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        $success = $this->service->updateVehicle($id, $data);
        if ($success) {
            echo json_encode(['message' => 'Véhicule mis à jour']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Véhicule non trouvé ou aucune modification']);
        }
    }

    public function delete(string $id): void {

        checkAdmin($user);
        
        $success = $this->service->deleteVehicle($id);
        if ($success) {
            echo json_encode(['message' => 'Véhicule supprimé']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Véhicule non trouvé']);
        }
    }
}
