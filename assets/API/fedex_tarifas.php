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

// Obtenemos los datos enviados por el frontend (código postal de origen, destino y peso)
$origenPostal = isset($_GET['origenPostal']) ? $_GET['origenPostal'] : '';
$destinoPostal = isset($_GET['destinoPostal']) ? $_GET['destinoPostal'] : '';
$peso = isset($_GET['peso']) ? $_GET['peso'] : '';

// Validaciones simples
if (empty($origenPostal) || empty($destinoPostal) || empty($peso)) {
    echo json_encode(['error' => 'Faltan parámetros requeridos']);
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

// Crear el payload para la solicitud de tarifas
$rate_request_data = [
    "accountNumber" => [
        "value" => "740561073"
    ],
    "rateRequestControlParameters" => [
        "returnTransitTimes" => true
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
        "shippingChargesPayment" => [
            "paymentType" => "SENDER",
            "payor" => [
                "accountNumber" => "740561073", 
                "countryCode" => "US"           
            ]
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

// Enviar la solicitud de tarifas a la API de FedEx
$rate_url = "https://apis-sandbox.fedex.com/rate/v1/rates/quote";
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

// Verificar si hubo error en la solicitud de tarifas
if (curl_errno($ch)) {
    echo json_encode(["error" => "Error en la solicitud de tarifas: " . curl_error($ch)]);
    exit();
}

curl_close($ch);

// Guardar la respuesta en un archivo de log
$log_file = 'response_log.txt';
$log_message = "Fecha: " . date('Y-m-d H:i:s') . "\n";
$log_message .= "Respuesta de la API: " . $rate_response . "\n\n";

// Abrir archivo de log y escribir la respuesta
file_put_contents($log_file, $log_message, FILE_APPEND);

// Devolver la respuesta de la API de tarifas
echo $rate_response;

?>
