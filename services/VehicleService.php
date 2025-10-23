<?php
namespace Services;

use Repositories\VehicleRepository;

class VehicleService {
    private $repository;

    public function __construct($db) {
        $this->repository = new VehicleRepository($db);
    }

    public function createVehicle(array $data): array {
        $insertedId = $this->repository->create($data);
        return [
            'message' => 'Véhicule ajouté',
            'id' => (string)$insertedId
        ];
    }

    public function getAllVehicles(): array {
        return $this->repository->getAll();
    }

    public function getVehicleById(string $id): ?array {
        return $this->repository->getById($id);
    }

    public function updateVehicle(string $id, array $data): string {
        $this->repository->update($id, $data);
        return "Véhicule mis à jour avec succès";
    }

    public function deleteVehicle(string $id): string {
        $this->repository->delete($id);
        return "Véhicule supprimé avec succès";
    }
}
