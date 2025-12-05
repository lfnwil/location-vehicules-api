<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

use Models\User;

$db = \Config\getDatabase();
$collection = $db->users;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // IMPORTANT : récupérer JSON Postman
    $data = json_decode(file_get_contents("php://input"), true);

    $name = $data["name"] ?? "";
    $email = $data["email"] ?? "";
    $password = $data["password"] ?? "";

    // Vérification des champs obligatoires
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(["message" => "Tous les champs sont obligatoires (name, email, password)"]);
        exit;
    }

    // Vérifie si l'utilisateur existe déjà
    if ($collection->findOne(["email" => $email])) {
        echo json_encode(["message" => "Email deja utilise"]);
        exit;
    }

    // Hash du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Création d'un User
    $user = new User([
        "name" => $name,
        "email" => $email,
        "password" => $hashedPassword
    ]);

    // Enregistrement en base
    $collection->insertOne([
        "name" => $user->name,
        "email" => $user->email,
        "password" => $user->password,
        "role" => $user->role,
        "created_at" => $user->created_at
    ]);

    echo json_encode(["message" => "Inscription reussie !"]);
}
?>
