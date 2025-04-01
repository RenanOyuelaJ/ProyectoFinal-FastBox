<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$clientId = "l7449b0fc299e84c87b6e05ad0a7203255";
$clientSecret = "b2bf9b99deb645e888e5c9c6e0d66657";
$authUrl = "https://apis-sandbox.fedex.com/oauth/token";
$trackUrl = "https://apis-sandbox.fedex.com/track/v1/trackingnumbers";

// Función para obtener el token de autenticación
function obtenerToken($clientId, $clientSecret, $authUrl) {
    $data = http_build_query(['grant_type' => 'client_credentials']);
    
    $options = [
        CURLOPT_URL => $authUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => [
            "Authorization: Basic " . base64_encode("$clientId:$clientSecret"),
            "Content-Type: application/x-www-form-urlencoded"
        ],
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $authResponse = curl_exec($curl);

    // Verifica si hubo un error en la solicitud
    if ($authResponse === false) {
        echo json_encode(["error" => "Error en la solicitud de token: " . curl_error($curl)]);
        curl_close($curl);
        exit;
    }

    // Imprimir la respuesta completa para depuración
    echo json_encode(["response" => $authResponse]);

    // Intenta analizar la respuesta solo si es JSON válido
    $authResponseData = json_decode($authResponse, true);
    if (json_last_error() != JSON_ERROR_NONE) {
        echo json_encode(["error" => "La respuesta no es JSON válida: " . $authResponse]);
        exit;
    }

    curl_close($curl);

    // Verifica si la respuesta contiene el token
    if (isset($authResponseData["access_token"])) {
        return $authResponseData["access_token"];
    } else {
        echo json_encode(["error" => "No se pudo obtener el token: " . json_encode($authResponseData)]);
        exit;
    }
}

// Verifica si hay un número de rastreo en la solicitud GET
if (!isset($_GET['tracking_number'])) {
    echo json_encode(["error" => "Falta el número de rastreo"]);
    exit;
}

$trackingNumber = $_GET['tracking_number'];
$token = obtenerToken($clientId, $clientSecret, $authUrl);

if (!$token) {
    echo json_encode(["error" => "No se pudo obtener el token"]);
    exit;
}

// Realizar la solicitud a FedEx
$trackingData = json_encode([
    "trackingInfo" => [
        [
            "trackingNumberInfo" => ["trackingNumber" => $trackingNumber]
        ]
    ]
]);

$options = [
    CURLOPT_URL => $trackUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $trackingData,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ],
];

$curl = curl_init();
curl_setopt_array($curl, $options);
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($httpCode === 200) {
    echo $response;  // Devolvemos el resultado al frontend (JSON de tracking)
} else {
    echo json_encode(["error" => "Error al obtener datos de rastreo"]);
}
?>
