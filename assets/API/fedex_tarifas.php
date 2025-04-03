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

// Leer el JSON de la solicitud
$input_data = json_decode(file_get_contents("php://input"), true);
if (!$input_data) {
    echo json_encode(["error" => "Datos de entrada no válidos"]);
    exit();
}

// Construcción del payload
$rate_request_data = [
    "accountNumber" => ["value" => "740561073"],
    "rateRequestControlParameters" => ["returnTransitTimes" => true],
    "requestedShipment" => [
        "shipper" => ["address" => ["postalCode" => $input_data['shipperPostal'], "countryCode" => "US"]],
        "recipient" => ["address" => ["postalCode" => $input_data['recipientPostal'], "countryCode" => "US"]],
        "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
        "shippingChargesPayment" => ["paymentType" => "SENDER", "payor" => []],
        "requestedPackageLineItems" => [["weight" => ["units" => "LB", "value" => $input_data['weight']]]]
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
    echo json_encode(["error" => "Error en la solicitud de tarifas: " . curl_error($ch)]);
    exit();
}
curl_close($ch);

echo $rate_response;
?>
