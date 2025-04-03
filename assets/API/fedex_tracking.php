<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Manejo de preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Obtener número de rastreo
$trackingNumber = isset($_GET['trackingNumber']) ? $_GET['trackingNumber'] : '';

if (empty($trackingNumber)) {
    http_response_code(400);
    echo json_encode(['error' => 'No se proporcionó un número de rastreo']);
    exit();
}

if (!preg_match('/^\d{12}$/', $trackingNumber)) {
    http_response_code(400);
    echo json_encode(['error' => 'Número de rastreo no válido']);
    exit();
}

// Parámetros de autenticación
$client_id = "l7449b0fc299e84c87b6e05ad0a7203255";
$client_secret = "b2bf9b99deb645e888e5c9c6e0d66657";
$auth_url = 'https://apis-sandbox.fedex.com/oauth/token';

// Obtener token de FedEx
$auth_data = [
    'grant_type' => 'client_credentials',
    'client_id' => $client_id,
    'client_secret' => $client_secret,
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $auth_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($auth_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Evita redirecciones

$auth_response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener el token: " . curl_error($ch)]);
    exit();
}

curl_close($ch);

$auth_response_data = json_decode($auth_response, true);

if (!isset($auth_response_data['access_token'])) {
    http_response_code(500);
    echo json_encode(["error" => "No se pudo obtener el token"]);
    exit();
}

$access_token = $auth_response_data['access_token'];
error_log("Token obtenido: " . $access_token); // Verificar si se obtiene el token

// Solicitud de rastreo
$tracking_url = "https://apis-sandbox.fedex.com/track/v1/trackingnumbers";
$tracking_data = [
    "trackingInfo" => [
        [
            "trackingNumberInfo" => [
                "trackingNumber" => $trackingNumber
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
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Evita redirecciones

$tracking_response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la solicitud de rastreo: " . curl_error($ch)]);
    exit();
}

curl_close($ch);

echo $tracking_response;
?>
