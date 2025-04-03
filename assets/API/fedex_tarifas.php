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

// Obtener datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['origin'], $data['destination'], $data['weight'])) {
    echo json_encode(['error' => 'Faltan datos requeridos (origen, destino, peso)']);
    exit;
}

// Parámetros de autenticación
$client_id = "l7753a7f01f8674b219da9ace51b892791";
$client_secret = "e49a2d14836b418493661e6333b93f7f";
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

// Realizar la solicitud de tarifas
$rates_url = "https://apis-sandbox.fedex.com/rate/v1/rates/quotes";
$rates_data = [
    "accountNumber" => ["value" => "YOUR_ACCOUNT_NUMBER"],
    "requestedShipment" => [
        "shipper" => [
            "address" => ["postalCode" => $data['origin'], "countryCode" => "US"]
        ],
        "recipient" => [
            "address" => ["postalCode" => $data['destination'], "countryCode" => "US"]
        ],
        "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
        "rateRequestTypes" => ["ACCOUNT"],
        "requestedPackageLineItems" => [
            ["weight" => ["units" => "LB", "value" => $data['weight']]]
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $rates_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($rates_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access_token"
]);

$rates_response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => "Error en la solicitud de tarifas: " . curl_error($ch)]);
    exit();
}

curl_close($ch);

echo $rates_response;
?>
