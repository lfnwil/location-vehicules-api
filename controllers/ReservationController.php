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
            echo json_encode(['error' => 'Reservation non trouvée']);
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
            $res = $this->service->getReservationById($id);
            echo json_encode(['message' => 'Reservation ajoutée', 'reservation' => $this->formatReservation((array)$res)]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update(string $id): void {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        try {
            $success = $this->service->updateReservation($id, $data);
            if ($success) {
                $this->service->updateReservationStatus($id);
                $res = $this->service->getReservationById($id);
                echo json_encode(['message' => 'Reservation mise à jour', 'reservation' => $this->formatReservation((array)$res)]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Reservation non trouvée ou aucune modification']);
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete(string $id): void {
        header('Content-Type: application/json');
        try {
            $success = $this->service->deleteReservation($id);
            if ($success) {
                echo json_encode(['message' => 'Reservation supprimée']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Reservation non trouvée']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
