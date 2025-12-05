<?php
namespace Services;

use Repositories\ReservationRepository;
use Repositories\VehicleRepository;
use Repositories\UserRepository;
use MongoDB\BSON\UTCDateTime;

class ReservationService {
    private ReservationRepository $reservationRepo;
    private VehicleRepository $vehicleRepo;
    private UserRepository $userRepo;

    public function __construct(
        ReservationRepository $reservationRepo,
        VehicleRepository $vehicleRepo,
        UserRepository $userRepo
    ) {
        $this->reservationRepo = $reservationRepo;
        $this->vehicleRepo = $vehicleRepo;
        $this->userRepo = $userRepo;
    }

    public function getAllReservations(): array {
        return $this->reservationRepo->getAll();
    }

    public function getReservationById(string $id): ?array {
        return $this->reservationRepo->getById($id);
    }

    public function createReservation(array $data): string {
        $vehicle = $this->vehicleRepo->getById($data['vehicle_id']);
        if (!$vehicle) throw new \Exception("Véhicule non trouvé");

        $user = $this->userRepo->getById($data['user_id']);
        if (!$user) throw new \Exception("Utilisateur non trouvé");

        $dateDebut = new \DateTime($data['date_debut']);
        $dateFin   = new \DateTime($data['date_fin']);
        if ($dateFin < $dateDebut) throw new \Exception("La date de fin doit être après la date de début");

        $existingReservations = $this->reservationRepo->getReservationsByVehicle($data['vehicle_id']);
        foreach ($existingReservations as $res) {
            $resDebut = $res['date_debut'] instanceof UTCDateTime ? $res['date_debut']->toDateTime() : new \DateTime($res['date_debut']);
            $resFin   = $res['date_fin'] instanceof UTCDateTime ? $res['date_fin']->toDateTime() : new \DateTime($res['date_fin']);
            if ($dateDebut <= $resFin && $dateFin >= $resDebut) {
                throw new \Exception("Le véhicule n'est pas disponible pour ces dates");
            }
        }

        $now = new \DateTime();
        if ($dateDebut > $now) $statut = 'confirmée';
        elseif ($dateDebut <= $now && $dateFin >= $now) $statut = 'en_cours';
        else $statut = 'terminée';

        $days = $dateFin->diff($dateDebut)->days + 1;
        $data['duree_jours']  = $days;
        $data['prix_total']   = $vehicle['prix_journalier'] * $days;
        $data['client_name']  = $user['name'];
        $data['vehicle_name'] = $vehicle['marque'] . ' ' . $vehicle['modele'];
        $data['date_debut']   = new UTCDateTime($dateDebut->getTimestamp() * 1000);
        $data['date_fin']     = new UTCDateTime($dateFin->getTimestamp() * 1000);
        $data['statut']       = $statut;
        $data['created_at']   = new UTCDateTime();

        return $this->reservationRepo->create($data);
    }

    public function updateReservation(string $id, array $data): bool {
        $reservation = $this->reservationRepo->getById($id);
        if (!$reservation) throw new \Exception("Réservation non trouvée");

        $dateDebut = isset($data['date_debut']) ? new \DateTime($data['date_debut']) :
                     ($reservation['date_debut'] instanceof UTCDateTime ? $reservation['date_debut']->toDateTime() : new \DateTime($reservation['date_debut']));
        $dateFin   = isset($data['date_fin']) ? new \DateTime($data['date_fin']) :
                     ($reservation['date_fin'] instanceof UTCDateTime ? $reservation['date_fin']->toDateTime() : new \DateTime($reservation['date_fin']));

        if ($dateFin < $dateDebut) throw new \Exception("La date de fin doit être après la date de début");

        $existingReservations = $this->reservationRepo->getReservationsByVehicle($reservation['vehicle_id']);
        foreach ($existingReservations as $res) {
            if ((string)$res['_id'] === $id) continue; 
            $resDebut = $res['date_debut'] instanceof UTCDateTime ? $res['date_debut']->toDateTime() : new \DateTime($res['date_debut']);
            $resFin   = $res['date_fin'] instanceof UTCDateTime ? $res['date_fin']->toDateTime() : new \DateTime($res['date_fin']);
            if ($dateDebut <= $resFin && $dateFin >= $resDebut) {
                throw new \Exception("Le véhicule n'est pas disponible pour ces dates");
            }
        }
        $data['date_debut'] = new UTCDateTime($dateDebut->getTimestamp() * 1000);
        $data['date_fin']   = new UTCDateTime($dateFin->getTimestamp() * 1000);

        $days = $dateFin->diff($dateDebut)->days + 1;
        $data['duree_jours'] = $days;
        $vehicle = $this->vehicleRepo->getById($reservation['vehicle_id']);
        $data['prix_total'] = $vehicle['prix_journalier'] * $days;

        return $this->reservationRepo->update($id, $data);
    }

    public function deleteReservation(string $id): bool {
        return $this->reservationRepo->delete($id);
    }

    public function updateReservationStatus(string $id): bool {
        $reservation = $this->reservationRepo->getById($id);
        if (!$reservation) throw new \Exception("Réservation non trouvée");

        if (in_array($reservation['statut'], ['annulée', 'terminée'])) return false;

        $now = new \DateTime();
        $dateDebut = $reservation['date_debut'] instanceof UTCDateTime ? $reservation['date_debut']->toDateTime() : new \DateTime($reservation['date_debut']);
        $dateFin   = $reservation['date_fin'] instanceof UTCDateTime ? $reservation['date_fin']->toDateTime() : new \DateTime($reservation['date_fin']);

        if ($now < $dateDebut) $reservation['statut'] = 'confirmée';
        elseif ($now >= $dateDebut && $now <= $dateFin) $reservation['statut'] = 'en_cours';
        else $reservation['statut'] = 'terminée';

        return $this->reservationRepo->update($id, ['statut' => $reservation['statut']]);
    }
}
-