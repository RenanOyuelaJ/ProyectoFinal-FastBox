<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$trackingNumber = isset($_GET['trackingNumber']) ? $_GET['trackingNumber'] : '';

if (empty($trackingNumber)) {
    echo json_encode(["error" => "No se proporcionó número de rastreo"]);
    exit;
}

// Construye la URL del endpoint remoto en InfinityFree
$remoteUrl = "http://fastbox.infinityfreeapp.com/assets/API/fedex_tracking.php?trackingNumber=" . urlencode($trackingNumber);

// Realiza la solicitud GET al backend remoto
$response = file_get_contents($remoteUrl);

// Verifica si hubo error
if ($response === FALSE) {
    echo json_encode(["error" => "No se pudo contactar el backend remoto"]);
    exit;
}

// Devuelve el contenido tal cual al frontend
echo $response;
