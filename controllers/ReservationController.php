<?php
namespace Controllers;

use Services\ReservationService;

class ReservationController {
    private ReservationService $service;

    public function __construct(ReservationService $service) {
        $this->service = $service;
    }

    private function formatReservation(array $res): array {
        if (isset($res['_id']) && $res['_id'] instanceof \MongoDB\BSON\ObjectId) {
            $res['_id'] = (string)$res['_id'];
        }
        if (isset($res['created_at'])) {
            $res['created_at'] = $res['created_at']->toDateTime()->format('Y-m-d H:i:s');
        }
        if (isset($res['date_debut'])) {
            $res['date_debut'] = $res['date_debut']->toDateTime()->format('Y-m-d');
        }
        if (isset($res['date_fin'])) {
            $res['date_fin'] = $res['date_fin']->toDateTime()->format('Y-m-d');
        }
        return $res;
    }

    public function getAll(): void {
        header('Content-Type: application/json');
        $reservations = $this->service->getAllReservations();

        $formatted = [];
        foreach ($reservations as $res) {
            $res = is_array($res) ? $res : (array)$res;
            $formatted[] = $this->formatReservation($res);
        }

        echo json_encode($formatted);
    }

    public function getById(string $id): void {
        header('Content-Type: application/json');
        $res = $this->service->getReservationById($id);
        if ($res) {
            echo json_encode($this->formatReservation((array)$res));
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Reservation non trouvé']);
        }
    }

    public function create(): void {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['user_id'], $data['vehicle_id'], $data['date_debut'], $data['date_fin'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        $id = $this->service->createReservation($data);
        $res = $this->service->getReservationById($id);
        echo json_encode(['message' => 'Reservation ajouté', 'reservation' => $this->formatReservation((array)$res)]);
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
            $res = $this->service->getReservationById($id);
            echo json_encode(['message' => 'Reservation mis à jour', 'reservation' => $this->formatReservation((array)$res)]);
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
