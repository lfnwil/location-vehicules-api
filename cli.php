<?php

function request($method, $url, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['status' => $status, 'response' => json_decode($response, true)];
}

function prompt($message) {
    echo $message;
    return trim(fgets(STDIN));
}

$apiBase = "http://localhost:8000"; // remplacer par ton URL API

while (true) {
    echo "\n--- Menu API Location Véhicules ---\n";
    echo "1. Lister tous les véhicules\n";
    echo "2. Lister tous les utilisateurs\n";
    echo "3. Lister toutes les réservations\n";
    echo "4. Créer une réservation\n";
    echo "5. Quitter\n";

    $choice = prompt("Choisir une option: ");

    switch ($choice) {
        case '1':
            $res = request('GET', "$apiBase/vehicules");
            print_r($res['response']);
            break;

        case '2':
            $res = request('GET', "$apiBase/users");
            print_r($res['response']);
            break;

        case '3':
            $res = request('GET', "$apiBase/reservations");
            print_r($res['response']);
            break;

        case '4':
            $userId = prompt("ID de l'utilisateur: ");
            $vehicleId = prompt("ID du véhicule: ");
            $dateDebut = prompt("Date de début (YYYY-MM-DD): ");
            $dateFin = prompt("Date de fin (YYYY-MM-DD): ");

            $data = [
                'user_id' => $userId,
                'vehicle_id' => $vehicleId,
                'date_debut' => $dateDebut,
                'date_fin' => $dateFin
            ];

            $res = request('POST', "$apiBase/reservations", $data);
            print_r($res['response']);
            break;

        case '5':
            exit("Au revoir !\n");

        default:
            echo "Option invalide.\n";
    }
}
