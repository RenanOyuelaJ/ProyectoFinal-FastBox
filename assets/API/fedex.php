<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Manejo de preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Parámetros de autenticación
$client_id = "l7449b0fc299e84c87b6e05ad0a7203255";
$client_secret = "b2bf9b99deb645e888e5c9c6e0d66657";
$auth_url = 'https://apis-sandbox.fedex.com/oauth/token';

// Datos para obtener el token
$auth_data = [
    'grant_type' => 'client_credentials',
    'client_id' => $client_id,
    'client_secret' => $client_secret,
];

// Inicializar cURL para obtener el token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $auth_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

$auth_response = curl_exec($ch);

// Verificar errores
if (curl_errno($ch)) {
    echo json_encode(["error" => "cURL error: " . curl_error($ch)]);
    exit();
}

curl_close($ch);

// Decodificar la respuesta del token
$auth_response_data = json_decode($auth_response, true);

// Validar si se obtuvo un token
if (!isset($auth_response_data['access_token'])) {
    echo json_encode(["error" => "No se pudo obtener el token", "response" => $auth_response_data]);
    exit();
}

// Guardar el token
$access_token = $auth_response_data['access_token'];

// Simulación de rastreo con un número de prueba
$tracking_url = "https://apis-sandbox.fedex.com/track/v1/trackingnumbers";
$tracking_number = "123456789012"; // Número de rastreo de prueba

$tracking_data = [
    "trackingInfo" => [
        [
            "trackingNumberInfo" => [
                "trackingNumber" => $tracking_number
            ]
        ]
    ],
    "includeDetailedScans" => true
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tracking_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tracking_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access_token"
]);

$tracking_response = curl_exec($ch);

// Verificar errores en el rastreo
if (curl_errno($ch)) {
    echo json_encode(["error" => "Error en cURL: " . curl_error($ch)]);
    exit();
}

curl_close($ch);

// Decodificar la respuesta del rastreo
$tracking_response_data = json_decode($tracking_response, true);

// Validar si la respuesta tiene información
if (!$tracking_response_data) {
    echo json_encode(["error" => "No se recibió una respuesta válida de FedEx"]);
    exit();
}

// Devolver los datos obtenidos
echo json_encode([
    "token" => $access_token,
    "tracking_result" => $tracking_response_data
], JSON_PRETTY_PRINT);
?>
