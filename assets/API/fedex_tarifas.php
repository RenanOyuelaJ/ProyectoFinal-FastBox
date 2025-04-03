<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener los datos enviados por el frontend
$origenPostal = isset($_GET['origenPostal']) ? $_GET['origenPostal'] : '';
$destinoPostal = isset($_GET['destinoPostal']) ? $_GET['destinoPostal'] : '';
$peso = isset($_GET['peso']) ? $_GET['peso'] : '';

// Devolver los datos recibidos
echo json_encode([
    "origenPostal" => $origenPostal,
    "destinoPostal" => $destinoPostal,
    "peso" => $peso
]);
?>
