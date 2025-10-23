<!-- auth0.php -->
<?php
function getAuth0ManagementToken() {    
    $url = "https://dev-t06i8tsz6jobagj3.us.auth0.com/oauth/token";

    $data = [
        "client_id" => "PUTsvCMpfDHnl23mGo04s7vcPAKKwAkQ",
        "client_secret" => "jeJt8-gDHf7tugcY1YCLTp0E34F0SiIjKaj45ry7639ygnPiX3yScPeznf224g54",
        "audience" => "https://dev-t06i8tsz6jobagj3.us.auth0.com/api/v2/",
        "grant_type" => "client_credentials"
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        die('Erreur lors de la récupération du token');
    }

    $response = json_decode($result, true);
    return $response['access_token'];
}