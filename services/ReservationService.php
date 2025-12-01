<?php
namespace Services;

use Repositories\ReservationRepository;
use Repositories\UserRepository;
use Repositories\VehicleRepository;

class ReservationService {
    private ReservationRepository $reservationRepo;
    private UserRepository $userRepo;
    private VehicleRepository $vehicleRepo;

    public function __construct(
        ReservationRepository $reservationRepo,
        UserRepository $userRepo,
        VehicleRepository $vehicleRepo
    ) {
        $this->reservationRepo = $reservationRepo;
        $this->userRepo = $userRepo;
        $this->vehicleRepo = $vehicleRepo;
    }

    public function createReservation(array $data): string {
        // Vérifier que l'user et le véhicule existent
        if (!$this->userRepo->getById($data['user_id'])) {
            throw new \Exception("Utilisateur non trouvé");
        }
        if (!$this->vehicleRepo->getById($data['vehicle_id'])) {
            throw new \Exception("Véhicule non trouvé");
        }
        return $this->reservationRepo->create($data);
    }

    public function getAllReservations(): array {
        return $this->reservationRepo->findAll();
    }

    public function getReservationById(string $id): ?array {
        return $this->reservationRepo->getById($id);
    }

    public function updateReservation(string $id, array $data): bool {
        return $this->reservationRepo->update($id, $data);
    }

    public function deleteReservation(string $id): bool {
        return $this->reservationRepo->delete($id);
    }
}
