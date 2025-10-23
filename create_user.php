<!-- create_user.php -->
<?php
require 'auth0.php';
$token = getAuth0ManagementToken();

function createUser($email, $password, $role = 'client') {
    global $token;

    $url = "https://dev-t06i8tsz6jobagj3.us.auth0.com/api/v2/users";

    $data = [
        "email" => $email,
        "password" => $password,
        "connection" => "Username-Password-Authentication",
        "email_verified" => true,
        "app_metadata" => ["role" => $role]
    ];

    $options = [
        'http' => [
            'header'  => "Authorization: Bearer $token\r\nContent-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}

// Exemple de création d'un client
$user = createUser("client@test.com", "MotDePasse123", "client");
print_r($user);