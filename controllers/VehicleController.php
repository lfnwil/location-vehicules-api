<?php
namespace Controllers;

use Services\VehicleService;

class VehicleController {
    private VehicleService $service;

    public function __construct(VehicleService $service) {
        $this->service = $service;
    }

    private function formatVehicle(array $vehicle): array {
        if (isset($vehicle['_id']) && $vehicle['_id'] instanceof \MongoDB\BSON\ObjectId) {
            $vehicle['_id'] = (string)$vehicle['_id'];
        }
        if (isset($vehicle['created_at'])) {
            $vehicle['created_at'] = $vehicle['created_at']->toDateTime()->format('Y-m-d H:i:s');
        }
        return $vehicle;
    }

    public function getAll(): void {
        header('Content-Type: application/json');
        $vehicles = $this->service->getAllVehicles();

        $formatted = [];
        foreach ($vehicles as $v) {
            $v = is_array($v) ? $v : (array)$v;
            $formatted[] = $this->formatVehicle($v);
        }

        echo json_encode($formatted);
    }

    public function getById(string $id): void {
        header('Content-Type: application/json');
        $vehicle = $this->service->getVehicleById($id);
        if ($vehicle) {
            echo json_encode($this->formatVehicle((array)$vehicle));
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Véhicule non trouvé']);
        }
    }

    public function create(): void {
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
        $vehicle = $this->service->getVehicleById($id);
        echo json_encode(['message' => 'Véhicule ajouté', 'vehicle' => $this->formatVehicle((array)$vehicle)]);
    }

    public function update(string $id): void {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        $success = $this->service->updateVehicle($id, $data);
        if ($success) {
            $vehicle = $this->service->getVehicleById($id);
            echo json_encode(['message' => 'Véhicule mis à jour', 'vehicle' => $this->formatVehicle((array)$vehicle)]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Véhicule non trouvé ou aucune modification']);
        }
    }

    public function delete(string $id): void {
        $success = $this->service->deleteVehicle($id);
        if ($success) {
            echo json_encode(['message' => 'Véhicule supprimé']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Véhicule non trouvé']);
        }
    }
}
