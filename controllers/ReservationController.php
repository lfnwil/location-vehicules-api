<?php
namespace Controllers;

use Services\ReservationService;

class ReservationController {
    private ReservationService $service;

    public function __construct(ReservationService $service) {
        $this->service = $service;
    }

    public function getAll(): void {
        header('Content-Type: application/json');
        echo json_encode($this->service->getAllReservations());
    }

    public function getById(string $id): void {
        header('Content-Type: application/json');
        $reservation = $this->service->getReservationById($id);
        if ($reservation) {
            echo json_encode($reservation);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Reservation non trouvé']);
        }
    }

    public function create(): void {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['user_id'], $data['vehicle_id'], $data['date_debut'], $data['date_fin'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        try {
            $id = $this->service->createReservation($data);
            echo json_encode(['message' => 'Réservation ajoutée', 'id' => $id]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update(string $id): void {

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        $success = $this->service->updateReservation($id, $data);
        if ($success) {
            echo json_encode(['message' => 'Reservation mis à jour']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Reservation non trouvé ou aucune modification']);
        }
    }

    public function delete(string $id): void {
        
        $success = $this->service->deleteReservation($id);
        if ($success) {
            echo json_encode(['message' => 'Reservation supprimé']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Reservation non trouvé']);
        }
    }
}
