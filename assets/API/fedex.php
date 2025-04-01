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
    'grant_type' => 'client_credentials'
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

// Mostrar el código de estado HTTP y la respuesta
echo "Código de estado HTTP: " . $http_status . "<br>";
echo "Respuesta de la API: <pre>" . htmlspecialchars($response) . "</pre>";

// Intentar decodificar la respuesta para obtener más detalles
$response_data = json_decode($response, true);

// Si hay errores en la respuesta, mostrarlos
if (isset($response_data['errors'])) {
    echo "<strong>Errores:</strong><pre>" . print_r($response_data['errors'], true) . "</pre>";
}

// Si la respuesta contiene un token, mostrarlo
if (isset($response_data['access_token'])) {
    echo "<strong>Token de acceso:</strong> " . $response_data['access_token'];
} else {
    echo "<strong>Detalles adicionales de la respuesta:</strong><pre>" . print_r($response_data, true) . "</pre>";
}
?>
