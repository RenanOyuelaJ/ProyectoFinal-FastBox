<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$clientId = "l7449b0fc299e84c87b6e05ad0a7203255";
$clientSecret = "b2bf9b99deb645e888e5c9c6e0d66657";
$authUrl = "https://apis-sandbox.fedex.com/oauth/token";
$trackUrl = "https://apis-sandbox.fedex.com/track/v1/trackingnumbers";

// Función para obtener el token
function obtenerToken($clientId, $clientSecret, $authUrl) {
    $data = http_build_query(['grant_type' => 'client_credentials']);
    
    $authHeader = "Authorization: Basic " . base64_encode("$clientId:$clientSecret");

    $options = [
        CURLOPT_URL => $authUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => [
            $authHeader,
            "Content-Type: application/x-www-form-urlencoded"
        ],
        CURLOPT_VERBOSE => true // Activa el modo detallado
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $authResponse = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);

    curl_close($curl);

    // Depuración: Muestra la respuesta de FedEx
    if ($authResponse === false) {
        echo json_encode(["error" => "Error en la solicitud de token: " . $curlError]);
        exit;
    }

    echo "Respuesta del servidor de autenticación: " . $authResponse . "\n";

    // Intenta analizar la respuesta
    $authResponseData = json_decode($authResponse, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["error" => "La respuesta del token no es JSON válida: " . $authResponse]);
        exit;
    }

    if (!isset($authResponseData["access_token"])) {
        echo json_encode(["error" => "No se pudo obtener el token. Respuesta: " . json_encode($authResponseData)]);
        exit;
    }

    return $authResponseData["access_token"];
}


// Verificar si hay un número de rastreo en la solicitud
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

// 3Construcción del JSON de tracking (basado en la documentación)
$trackingData = json_encode([
    "trackingInfo" => [
        [
            "trackingNumberInfo" => ["trackingNumber" => $trackingNumber]
        ]
    ]
]);

// Configuración de la solicitud de rastreo
$options = [
    CURLOPT_URL => $trackUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $trackingData,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
        "X-locale: en_US",  // Agregamos esta cabecera
        "Content-Type: application/json"
    ],
];

$curl = curl_init();
curl_setopt_array($curl, $options);
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Evaluamos la respuesta
if ($httpCode === 200) {
    echo $response;
} else {
    echo json_encode(["error" => "Error al obtener datos de rastreo", "http_code" => $httpCode]);
}
?>
