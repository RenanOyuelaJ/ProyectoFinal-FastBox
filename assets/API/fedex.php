<?php
// Parámetros necesarios para la solicitud
$client_id = "l7449b0fc299e84c87b6e05ad0a7203255"; // Reemplaza con tu client_id
$client_secret = "b2bf9b99deb645e888e5c9c6e0d66657"; // Reemplaza con tu client_secret

// URL de la API de FedEx para obtener el token
$url = 'https://apis-sandbox.fedex.com/oauth/token';

// Datos a enviar en el cuerpo de la solicitud
$data = [
    'grant_type' => 'client_credentials',
    'client_id' => $client_id,
    'client_secret' => $client_secret,
];

// Inicializamos cURL
$ch = curl_init();

// Configuración de la solicitud cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
]);

// Ejecutamos la solicitud
$response = curl_exec($ch);

// Verificamos si hubo errores
if (curl_errno($ch)) {
    echo 'Error en cURL: ' . curl_error($ch);
    exit;
}

// Decodificamos la respuesta del token
$token_data = json_decode($response, true);
$access_token = $token_data['access_token']; // Obtenemos el token

// Realizamos la solicitud de rastreo a la API de FedEx
$tracking_number = $_POST['trackingNumber']; // Obtenemos el número de rastreo desde el cuerpo de la solicitud

// URL de la API de FedEx para rastreo
$tracking_url = 'https://apis-sandbox.fedex.com/track/v1/trackingnumbers';

// Datos a enviar en la solicitud de rastreo
$tracking_data = [
    'trackingInfo' => [
        [
            'trackingNumberInfo' => ['trackingNumber' => $tracking_number]
        ]
    ]
];

// Inicializamos cURL para la solicitud de rastreo
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tracking_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tracking_data));

// Ejecutamos la solicitud de rastreo
$tracking_response = curl_exec($ch);

// Verificamos si hubo errores en la solicitud de rastreo
if (curl_errno($ch)) {
    echo 'Error en cURL: ' . curl_error($ch);
    exit;
}

// Cerramos la sesión cURL
curl_close($ch);

// Devolvemos la respuesta como JSON
echo $tracking_response;
?>
