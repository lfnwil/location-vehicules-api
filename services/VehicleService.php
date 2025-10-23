<?php
namespace Services;

use Repositories\VehicleRepository;

class VehicleService {
    private VehicleRepository $repository;

    public function __construct(VehicleRepository $repository) {
        $this->repository = $repository;
    }

    public function getAllVehicles(): array {
        return $this->repository->getAll();
    }

    public function getVehicleById(string $id): ?array {
        return $this->repository->getById($id);
    }

    public function createVehicle(array $data): string {
        return $this->repository->create($data);
    }

    public function updateVehicle(string $id, array $data): bool {
        return $this->repository->update($id, $data);
    }

    public function deleteVehicle(string $id): bool {
        return $this->repository->delete($id);
    }
}
