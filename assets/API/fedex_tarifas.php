<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Verificar si el método es OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$origenPostal = isset($_GET['origenPostal']) ? $_GET['origenPostal'] : '';
$destinoPostal = isset($_GET['destinoPostal']) ? $_GET['destinoPostal'] : '';
$peso = isset($_GET['peso']) ? $_GET['peso'] : '';

if (empty($origenPostal) || empty($destinoPostal) || empty($peso)) {
    echo json_encode(['error' => 'Faltan parámetros requeridos']);
    exit;
}

// Parámetros de autenticación
$client_id = "l7753a7f01f8674b219da9ace51b892791";
$client_secret = "e49a2d14836b418493661e6333b93f7f";
$auth_url = 'https://apis-sandbox.fedex.com/oauth/token';

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
    error_log("Error al obtener el token: " . curl_error($ch));
    echo json_encode(["error" => "Error al obtener el token"]);
    exit();
}

curl_close($ch);

$auth_response_data = json_decode($auth_response, true);
if (!isset($auth_response_data['access_token'])) {
    error_log("No se pudo obtener el token: " . $auth_response);
    echo json_encode(["error" => "No se pudo obtener el token"]);
    exit();
}

$access_token = $auth_response_data['access_token'];

// Solicitud de tarifas
$rate_request_data = [
    "accountNumber" => [
        "value" => "740561073"
    ],
    "requestedShipment" => [
        "shipper" => [
            "address" => [
                "postalCode" => $origenPostal,
                "countryCode" => "US"
            ]
        ],
        "recipient" => [
            "address" => [
                "postalCode" => $destinoPostal,
                "countryCode" => "US"
            ]
        ],
        "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
        "rateRequestType" => [
            "ACCOUNT",
            "LIST"
        ],
        "requestedPackageLineItems" => [
            [
                "weight" => [
                    "units" => "LB",
                    "value" => $peso
                ]
            ]
        ]
    ]
];

$rate_url = "https://apis-sandbox.fedex.com/rate/v1/rates/quotes";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $rate_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($rate_request_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access_token"
]);

$rate_response = curl_exec($ch);
if (curl_errno($ch)) {
    error_log("Error en la solicitud de tarifas: " . curl_error($ch));
    echo json_encode(["error" => "Error en la solicitud de tarifas"]);
    exit();
}

curl_close($ch);

// Registrar la respuesta de la API de tarifas
error_log("Respuesta de la API de tarifas: " . $rate_response);

// Decodificar y devolver la respuesta
$rate_response_data = json_decode($rate_response, true);
echo json_encode($rate_response_data);
?>
