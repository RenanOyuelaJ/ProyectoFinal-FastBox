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
}

// Cerramos la sesión cURL
curl_close($ch);

// Mostramos la respuesta
echo "<pre>";
echo "Respuesta de la API: ";
print_r(json_decode($response, true));
echo "</pre>";
?>
