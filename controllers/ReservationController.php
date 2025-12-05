<?php
namespace Controllers;

use Services\ReservationService;

class ReservationController {

    private ReservationService $service;

    public function __construct(ReservationService $service) {
        $this->service = $service;
    }

    public function getAll() {
        header("Content-Type: application/json");
        echo json_encode($this->service->getAll());
    }

    public function getById(string $id) {
        header("Content-Type: application/json");
        $reservation = $this->service->getById($id);

        if ($reservation) {
            echo json_encode($reservation);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Réservation introuvable"]);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data ||
            !isset($data["vehicule_id"], $data["user_id"], $data["date_debut"], $data["date_fin"])) {
            http_response_code(400);
            echo json_encode(["error" => "Données incomplètes"]);
            return;
        }

        $response = $this->service->createReservation($data);
        echo json_encode($response);
    }

    public function delete(string $id) {
        $success = $this->service->deleteReservation($id);

        if ($success) {
            echo json_encode(["message" => "Réservation supprimée"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Réservation introuvable"]);
        }
    }
}
