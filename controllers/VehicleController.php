<?php
namespace Controllers;

use Services\VehicleService;

class VehicleController {
    private VehicleService $service;

    public function __construct(VehicleService $service) {
        $this->service = $service;
    }

    private function formatVehicle(array $vehicle): array {
        if (isset($vehicle['_id']) && $vehicle['_id'] instanceof \MongoDB\BSON\ObjectId) {
            $vehicle['_id'] = (string)$vehicle['_id'];
        }
        if (isset($vehicle['created_at'])) {
            $vehicle['created_at'] = $vehicle['created_at']->toDateTime()->format('Y-m-d H:i:s');
        }
        return $vehicle;
    }

    /**
    * @OA\Get(
    *     path="/vehicules",
    *     summary="Récupérer tous les véhicules",
    *     tags={"Véhicules"},
    *     @OA\Response(response=200, description="Liste des véhicules",
    *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Vehicle"))
    *     )
    * )
    */

    public function getAll(): void {
        header('Content-Type: application/json');
        $vehicles = $this->service->getAllVehicles();

        $formatted = [];
        foreach ($vehicles as $v) {
            $v = is_array($v) ? $v : (array)$v;
            $formatted[] = $this->formatVehicle($v);
        }

        echo json_encode($formatted);
    }

   /**
     * @OA\Get(
     *     path="/vehicules/{id}",
     *     summary="Récupérer un véhicule par ID",
     *     tags={"Véhicules"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Véhicule trouvé", @OA\JsonContent(ref="#/components/schemas/Vehicle")),
     *     @OA\Response(response=404, description="Véhicule non trouvé")
     * )
     */

    public function getById(string $id): void {
        header('Content-Type: application/json');
        $vehicle = $this->service->getVehicleById($id);
        if ($vehicle) {
            echo json_encode($this->formatVehicle((array)$vehicle));
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Véhicule non trouvé']);
        }
    }

    /**
     * @OA\Post(
     *     path="/vehicules",
     *     summary="Créer un véhicule",
     *     tags={"Véhicules"},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/Vehicle")),
     *     @OA\Response(response=200, description="Véhicule créé", @OA\JsonContent(ref="#/components/schemas/Vehicle")),
     *     @OA\Response(response=400, description="Données manquantes")
     * )
     */

    public function create(): void {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['type'], $data['marque'], $data['modele'], $data['prix_journalier'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        $data['kilometrage'] = $data['kilometrage'] ?? 0;
        $data['disponibilite'] = true;
        $data['created_at'] = new \MongoDB\BSON\UTCDateTime();

        $id = $this->service->createVehicle($data);
        $vehicle = $this->service->getVehicleById($id);
        echo json_encode(['message' => 'Véhicule ajouté', 'vehicle' => $this->formatVehicle((array)$vehicle)]);
    }

    /**
     * @OA\Put(
     *     path="/vehicules/{id}",
     *     summary="Mettre à jour un véhicule",
     *     tags={"Véhicules"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/Vehicle")),
     *     @OA\Response(response=200, description="Véhicule mis à jour", @OA\JsonContent(ref="#/components/schemas/Vehicle")),
     *     @OA\Response(response=404, description="Véhicule non trouvé")
     * )
     */
    public function update(string $id): void {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        $success = $this->service->updateVehicle($id, $data);
        if ($success) {
            $vehicle = $this->service->getVehicleById($id);
            echo json_encode(['message' => 'Véhicule mis à jour', 'vehicle' => $this->formatVehicle((array)$vehicle)]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Véhicule non trouvé ou aucune modification']);
        }
    }

      /**
     * @OA\Delete(
     *     path="/vehicules/{id}",
     *     summary="Supprimer un véhicule",
     *     tags={"Véhicules"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Véhicule supprimé"),
     *     @OA\Response(response=404, description="Véhicule non trouvé")
     * )
     */

    public function delete(string $id): void {
        $success = $this->service->deleteVehicle($id);
        if ($success) {
            echo json_encode(['message' => 'Véhicule supprimé']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Véhicule non trouvé']);
        }
    }
}
