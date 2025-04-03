<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

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

// Verificar si el token existe
if (!isset($auth_response_data['access_token'])) {
    echo json_encode(["error" => "No se pudo obtener el token"]);
    exit();
}

// Devolver el token obtenido
echo json_encode(["access_token" => $auth_response_data['access_token']]);
?>
