<?php
$url = 'https://httpbin.org/get';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if ($response === false) {
    echo 'Error en la solicitud: ' . curl_error($ch);
} else {
    echo 'Respuesta: ' . $response;
}

curl_close($ch);
?>
