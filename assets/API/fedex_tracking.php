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

// Obtenemos el número de rastreo de la URL
$trackingNumber = isset($_GET['trackingNumber']) ? $_GET['trackingNumber'] : '';

// Registro en log para verificar el número de rastreo recibido
file_put_contents("log.txt", "Tracking Number: $trackingNumber\n", FILE_APPEND);

if (empty($trackingNumber)) {
    echo json_encode(['error' => 'No se proporcionó un número de rastreo']);
    exit;
}

if (!preg_match('/^\d{12}$/', $trackingNumber)) {
    echo json_encode(['error' => 'Número de rastreo no válido']);
    exit;
}

// Parámetros de autenticación
$client_id = "l7449b0fc299e84c87b6e05ad0a7203255";
$client_secret = "b2bf9b99deb645e888e5c9c6e0d66657";
$auth_url = 'https://apis-sandbox.fedex.com/oauth/token';

// Obtener el token de FedEx
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

$auth_response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => "Error al obtener el token: " . curl_error($ch)]);
    exit();
}

curl_close($ch);

$auth_response_data = json_decode($auth_response, true);
if (!isset($auth_response_data['access_token'])) {
    echo json_encode(["error" => "No se pudo obtener el token"]);
    exit();
}

$access_token = $auth_response_data['access_token'];

// Realizar la solicitud de rastreo
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

$tracking_response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => "Error en la solicitud de rastreo: " . curl_error($ch)]);
    exit();
}

curl_close($ch);

// Registrar la respuesta en un archivo log
file_put_contents("fedex_response.log", "Response for $trackingNumber:\n" . $tracking_response . "\n\n", FILE_APPEND);

// Devolver la respuesta de FedEx
echo $tracking_response;
?>
