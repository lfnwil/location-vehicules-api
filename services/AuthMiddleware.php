<?php

function checkAdmin($user) {
    if (!$user || !isset($user['role']) || $user['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Accès refusé — réservé aux administrateurs.']);
        exit;
    }
}