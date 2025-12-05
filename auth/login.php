<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

$db = \Config\getDatabase();
$collection = $db->users;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Récupérer JSON Postman
    $data = json_decode(file_get_contents("php://input"), true);

    $email = $data["email"] ?? "";
    $password = $data["password"] ?? "";

    // Cherche l'utilisateur par email
    $user = $collection->findOne(["email" => $email]);

    if (!$user) {
        echo json_encode(["message" => "Email introuvable"]);
        exit;
    }

    // Vérifie le mot de passe hashé
    if (!password_verify($password, $user->password)) {
        echo json_encode(["message" => "Mot de passe incorrect"]);
        exit;
    }

    echo json_encode([
        "message" => "Connexion reussie",
        "name" => $user->name
    ]);
}
?>
