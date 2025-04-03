<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener datos del frontend
$origenPostal = isset($_GET['origenPostal']) ? $_GET['origenPostal'] : '';
$destinoPostal = isset($_GET['destinoPostal']) ? $_GET['destinoPostal'] : '';
$peso = isset($_GET['peso']) ? $_GET['peso'] : '';

// Validaciones
if (empty($origenPostal) || empty($destinoPostal) || empty($peso)) {
    echo json_encode(["error" => "Faltan parámetros requeridos"]);
    exit;
}

// Devolver los datos recibidos
echo json_encode([
    "origenPostal" => $origenPostal,
    "destinoPostal" => $destinoPostal,
    "peso" => $peso
]);
?>
