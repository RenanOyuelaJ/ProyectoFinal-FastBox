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

// Obtenemos los parámetros de la solicitud
$origenPostal = isset($_GET['origenPostal']) ? $_GET['origenPostal'] : '';
$destinoPostal = isset($_GET['destinoPostal']) ? $_GET['destinoPostal'] : '';
$peso = isset($_GET['peso']) ? $_GET['peso'] : '';

// Verificamos si los datos están completos
if (empty($origenPostal) || empty($destinoPostal) || empty($peso)) {
    echo json_encode(['error' => 'Por favor, complete todos los campos.']);
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

// Verificar si hubo error en la autenticación
if (curl_errno($ch)) {
    echo json_encode(["error" => "Error al obtener el token: " . curl_error($ch)]);
    exit();
}

curl_close($ch);

// Decodificar la respuesta de autenticación
$auth_response_data = json_decode($auth_response, true);
if (!isset($auth_response_data['access_token'])) {
    echo json_encode(["error" => "No se pudo obtener el token"]);
    exit();
}

$access_token = $auth_response_data['access_token'];

// Realizar la solicitud de tarifas
$tracking_url = "https://apis-sandbox.fedex.com/rate/v1/rates/quotes";
$tracking_data = [
    "rateRequest" => [
        "requestedShipment" => [
            "shipper" => [
                "address" => ["postalCode" => $origenPostal]
            ],
            "recipient" => [
                "address" => ["postalCode" => $destinoPostal]
            ],
            "packageCount" => 1,
            "requestedPackageLineItems" => [
                [
                    "weight" => ["units" => "LB", "value" => $peso]
                ]
            ]
        ]
    ]
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

// Verificar si hubo error en la solicitud de tarifas
if (curl_errno($ch)) {
    echo json_encode(["error" => "Error en la solicitud de tarifas: " . curl_error($ch)]);
    exit();
}

curl_close($ch);

// Limpiar posibles caracteres no deseados y asegurar que el log sea en formato texto
$log_data = date('Y-m-d H:i:s') . " - Respuesta de la API:\n" . json_encode(json_decode($tracking_response), JSON_PRETTY_PRINT) . "\n\n";

// Registrar la respuesta en un archivo de log
file_put_contents('log.txt', $log_data, FILE_APPEND);

// Devolver la respuesta de la API
echo $tracking_response;
?>
