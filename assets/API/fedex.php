<?php
// Las credenciales de la API de FedEx
$clientId = 'l7449b0fc299e84c87b6e05ad0a7203255';
$clientSecret = 'b2bf9b99deb645e888e5c9c6e0d66657';

// URL de la API para obtener el token de acceso
$tokenUrl = "https://apis-sandbox.fedex.com/oauth/token";

// Datos para obtener el token (usando client_credentials)
$data = [
    'grant_type' => 'client_credentials',
];

// Configurar la solicitud POST
$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Authorization: Basic " . base64_encode("$clientId:$clientSecret") . "\r\n" .
                    "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($data),
    ],
];

$context = stream_context_create($options);
$response = file_get_contents($tokenUrl, false, $context);

// Si la respuesta no es falsa (es decir, la solicitud fue exitosa)
if ($response !== FALSE) {
    // Decodificar la respuesta JSON
    $responseData = json_decode($response, true);
    // Devolver el token
    echo json_encode(['access_token' => $responseData['access_token']]);
} else {
    // En caso de error, mostrar un mensaje
    echo json_encode(['error' => 'Error al obtener el token']);
}
?>
