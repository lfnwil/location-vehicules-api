<?php
namespace Controllers;

use Services\UserService;

class UserController {
    private UserService $service;

    public function __construct(UserService $service) {
        $this->service = $service;
    }

    private function formatUser(array $user): array {
        if (isset($user['_id']) && $user['_id'] instanceof \MongoDB\BSON\ObjectId) {
            $user['_id'] = (string)$user['_id'];
        }
        if (isset($user['created_at'])) {
            $user['created_at'] = $user['created_at']->toDateTime()->format('Y-m-d H:i:s');
        }
        return $user;
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="Récupérer tous les utilisateurs",
     *     tags={"Utilisateurs"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     )
     * )
     */

    public function getAll(): void {
        header('Content-Type: application/json');
        $users = $this->service->getAllUsers();

        $formatted = [];
        foreach ($users as $user) {
            $user = is_array($user) ? $user : (array)$user;
            $formatted[] = $this->formatUser($user);
        }

        echo json_encode($formatted);
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Récupérer un utilisateur par ID",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Utilisateur trouvé", @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function getById(string $id): void {
        header('Content-Type: application/json');
        $user = $this->service->getUserById($id);
        if ($user) {
            echo json_encode($this->formatUser((array)$user));
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User non trouvé']);
        }
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Créer un utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=200, description="Utilisateur créé", @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=400, description="Champs obligatoires manquants")
     * )
     */

    public function create(): void {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['name'], $data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Champs obligatoires : name, email, password']);
            return;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['role'] = $data['role'] ?? 'user';
        $data['created_at'] = new \MongoDB\BSON\UTCDateTime();

        $id = $this->service->createUser($data);
        $user = $this->service->getUserById($id);
        echo json_encode(['message' => 'User créé', 'user' => $this->formatUser((array)$user)]);
    }

      /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Mettre à jour un utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=200, description="Utilisateur mis à jour", @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */

    public function update(string $id): void {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $success = $this->service->updateUser($id, $data);
        if ($success) {
            $user = $this->service->getUserById($id);
            echo json_encode(['message' => 'User mis à jour', 'user' => $this->formatUser((array)$user)]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User non trouvé']);
        }
    }

     /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Supprimer un utilisateur",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Utilisateur supprimé"),
     *     @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */

    public function delete(string $id): void {
        header('Content-Type: application/json');
        $success = $this->service->deleteUser($id);
        if ($success) {
            echo json_encode(['message' => 'User supprimé']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User non trouvé']);
        }
    }
}
