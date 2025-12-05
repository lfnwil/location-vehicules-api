<?php
namespace Services;

use Repositories\ReservationRepository;
use Repositories\VehicleRepository;
use MongoDB\BSON\UTCDateTime;

class ReservationService {
    private ReservationRepository $reservationRepo;
    private VehicleRepository $vehicleRepo;

    public function __construct(ReservationRepository $reservationRepo, VehicleRepository $vehicleRepo) {
        $this->reservationRepo = $reservationRepo;
        $this->vehicleRepo = $vehicleRepo;
    }

    public function getAllReservations(): array {
        return $this->reservationRepo->getAll();
    }

    public function getReservationById(string $id): ?array {
        return $this->reservationRepo->getById($id);
    }

    public function createReservation(array $data): string {
        $vehicle = $this->vehicleRepo->getById($data['vehicle_id']);
        if (!$vehicle) {
            throw new \Exception("Véhicule non trouvé");
        }

        $dateDebut = new \DateTime($data['date_debut']);
        $dateFin = new \DateTime($data['date_fin']);
        if ($dateFin < $dateDebut) {
            throw new \Exception("La date de fin doit être après la date de début");
        }

        $existingReservations = $this->reservationRepo->getReservationsByVehicle($data['vehicle_id']);
        foreach ($existingReservations as $res) {
            $resDebut = $res['date_debut']->toDateTime();
            $resFin = $res['date_fin']->toDateTime();
            if ($dateDebut <= $resFin && $dateFin >= $resDebut) {
                throw new \Exception("Le véhicule n'est pas disponible pour ces dates");
            }
        }

        $days = $dateFin->diff($dateDebut)->days + 1;
        $data['prix_total'] = $vehicle['prix_journalier'] * $days;

        $data['date_debut'] = new UTCDateTime($dateDebut->getTimestamp() * 1000);
        $data['date_fin'] = new UTCDateTime($dateFin->getTimestamp() * 1000);
        $data['statut'] = 'en_cours';
        $data['created_at'] = new UTCDateTime();

        return $this->reservationRepo->create($data);
    }

    public function updateReservation(string $id, array $data): bool {
        return $this->reservationRepo->update($id, $data);
    }

    public function deleteReservation(string $id): bool {
        return $this->reservationRepo->delete($id);
    }
}
