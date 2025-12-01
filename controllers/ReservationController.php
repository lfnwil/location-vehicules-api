<?php
namespace Controllers;

use Services\ReservationService;

class ReservationController {
    private ReservationService $reservationService;

    public function __construct(ReservationService $reservationService) {
        $this->reservationService = $reservationService;
    }

    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
        try {
            $id = $this->reservationService->createReservation($data);
            http_response_code(201);
            echo json_encode(['message' => 'Réservation ajoutée', 'id' => $id]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAll() {
        $reservations = $this->reservationService->getAllReservations();
        echo json_encode($reservations);
    }

    public function getById(string $id) {
        $reservation = $this->reservationService->getReservationById($id);
        if ($reservation) {
            echo json_encode($reservation);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Réservation non trouvée']);
        }
    }

    public function update(string $id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $updated = $this->reservationService->updateReservation($id, $data);
        if ($updated) {
            echo json_encode(['message' => 'Réservation mise à jour']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Réservation non trouvée']);
        }
    }

    public function delete(string $id) {
        $deleted = $this->reservationService->deleteReservation($id);
        if ($deleted) {
            echo json_encode(['message' => 'Réservation supprimée']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Réservation non trouvée']);
        }
    }
}
