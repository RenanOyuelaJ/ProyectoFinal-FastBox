<?php
// fedex.php

// Datos de autenticación
$client_id = "l7449b0fc299e84c87b6e05ad0a7203255";
$client_secret = "b2bf9b99deb645e888e5c9c6e0d66657";

// Codificar las credenciales en Base64
$credentials = base64_encode("$client_id:$client_secret");

// URL para obtener el token de acceso
$url = "https://apis-sandbox.fedex.com/oauth/token";

// Configurar los encabezados
$headers = [
    "Content-Type: application/x-www-form-urlencoded",
    "Authorization: Basic $credentials"
];

// Configurar los datos del formulario para obtener el token
$data = [
    'grant_type' => 'client_credentials',
    'scope' => 'tracking'  // Usamos el scope relacionado con el seguimiento
];

// Iniciar cURL
$ch = curl_init();

// Configurar cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

// Ejecutar la solicitud cURL
$response = curl_exec($ch);

// Verificar si hubo un error en la solicitud cURL
if ($response === false) {
    die('Error en la solicitud: ' . curl_error($ch));
}

// Obtener el código de estado HTTP de la respuesta
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Cerrar cURL
curl_close($ch);

// Verificar si el código de estado es 200 (OK)
if ($http_status != 200) {
    echo "Código de estado HTTP: " . $http_status . "<br>";
    echo "Respuesta de la API: <pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}

// Decodificar la respuesta JSON para obtener el token de acceso
$response_data = json_decode($response, true);

// Obtener el token de acceso
$access_token = $response_data['access_token'] ?? null;

if (!$access_token) {
    echo "Error: No se obtuvo el token de acceso.<br>";
    exit;
}

// Ahora que tenemos el token, hacer la solicitud para el seguimiento
// URL de la API para obtener el estado de un paquete
$tracking_url = "https://apis-sandbox.fedex.com/track/v1/trackingnumbers";

// Datos de la solicitud de seguimiento
$tracking_payload = [
    "includeDetailedScans" => true,
    "trackingInfo" => [
        [
            "shipDateBegin" => "2020-03-29",
            "shipDateEnd" => "2020-04-01",
            "trackingNumberInfo" => [
                "trackingNumber" => "123456789012" // Aquí pones el número de seguimiento que deseas rastrear
            ]
        ]
    ]
];

// Configurar los encabezados para la solicitud de seguimiento
$tracking_headers = [
    "Content-Type: application/json",
    "Authorization: Bearer $access_token"
];

// Iniciar cURL nuevamente para la solicitud de seguimiento
$ch_tracking = curl_init();

// Configurar cURL para la solicitud de seguimiento
curl_setopt($ch_tracking, CURLOPT_URL, $tracking_url);
curl_setopt($ch_tracking, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_tracking, CURLOPT_HTTPHEADER, $tracking_headers);
curl_setopt($ch_tracking, CURLOPT_POST, true);
curl_setopt($ch_tracking, CURLOPT_POSTFIELDS, json_encode($tracking_payload));

// Ejecutar la solicitud de seguimiento
$response_tracking = curl_exec($ch_tracking);

// Verificar si hubo un error en la solicitud de seguimiento
if ($response_tracking === false) {
    die('Error en la solicitud de seguimiento: ' . curl_error($ch_tracking));
}

// Obtener el código de estado HTTP de la respuesta de seguimiento
$http_status_tracking = curl_getinfo($ch_tracking, CURLINFO_HTTP_CODE);

// Cerrar la cURL de seguimiento
curl_close($ch_tracking);

// Mostrar el código de estado y la respuesta de seguimiento
echo "Código de estado HTTP de seguimiento: " . $http_status_tracking . "<br>";
echo "Respuesta de la API de seguimiento: <pre>" . htmlspecialchars($response_tracking) . "</pre>";

?>
