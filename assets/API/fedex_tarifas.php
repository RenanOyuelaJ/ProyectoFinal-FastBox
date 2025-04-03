<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener los datos enviados por el frontend
$origenPostal = isset($_GET['origenPostal']) ? $_GET['origenPostal'] : '';
$destinoPostal = isset($_GET['destinoPostal']) ? $_GET['destinoPostal'] : '';
$peso = isset($_GET['peso']) ? $_GET['peso'] : '';

// Validar los parámetros de entrada
if (empty($origenPostal) || empty($destinoPostal) || empty($peso)) {
    echo json_encode(['error' => 'Faltan parámetros requeridos']);
    exit;
}

// Parámetros de autenticación (usamos el token obtenido previamente)
$access_token = "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzY29wZSI6WyJDWFMtVFAiXSwiUGF5bG9hZCI6eyJjbGllbnRJZGVudGl0eSI6eyJjbGllbnRLZXkiOiJsNzc1M2E3ZjAxZjg2NzRiMjE5ZGE5YWNlNTFiODkyNzkxIn0sImF1dGhlbnRpY2F0aW9uUmVhbG0iOiJDTUFDIiwiYWRkaXRpb25hbElkZW50aXR5Ijp7InRpbWVTdGFtcCI6IjAzLUFwci0yMDI1IDEzOjQwOjU2IEVTVCIsImdyYW50X3R5cGUiOiJjbGllbnRfY3JlZGVudGlhbHMiLCJhcGltb2RlIjoiU2FuZGJveCIsImN4c0lzcyI6Imh0dHBzOi8vY3hzYXV0aHNlcnZlci1zdGFnaW5nLmFwcC5wYWFzLmZlZGV4LmNvbS90b2tlbi9vYXV0aDIifSwicGVyc29uYVR5cGUiOiJEaXJlY3RJbnRlZ3JhdG9yX0IyQiJ9LCJleHAiOjE3NDM3MDkyNTYsImp0aSI6ImFmMzViM2MyLWFhZjAtNGNkYy04NzIwLWZlMzY5MDI0M2FlYyJ9.WqdjB0EWQnooZk81EKi-sYj-bNittUzeh27xAAGKNDL-ZzkQCZ9pXoerE8AP_0Q55PTJFynplAHhToPc9NQ5R5-61XvfrA7ka3dWxMVgVyssER7pP8SQaXis8SZ3Kh7gKtV2CfYHkWYpHykjQDy2v7vH6kM2hqraMb-4-XiMWIOrEWDPLdhyH8m0M9U_ojKWT2P7Zr5eQwjaTxkBY9Ew_Fbsmc2pDaQrDZ9jEkGnCG93sZbmBeU6o6cZb3vAhBYWf7IEtP31jLiAVAFXEvw6BKoj_UkKRxXgZCwdwAYitAl_L4jIeMX-NET1V2FLaz0Ob7u9tbeQgG6PuAoA4jNiRob3qzNk_-Nl-15KuD87B_9wiKGN-9GhNvuQiFpg7s-Zv7_LIEl1arDV7xkIREYu5WRtttpyw0YBzSfav_mKG5nbE7P4G5050DuVvAYEhzl6bdDT4RWQ-KjT2ql8dTGwA5qpBnfvUM-lQ8Xqflnl9lsKqs47850trv6-iJ__IkLCqQr8J1WVIOzeCXsuYjxoKj7uhw8LiXFKA4EboB_ffhDitMEhg1iUL4pdn9WCm6591brOnSfZirhWjuVz1ihzfoofTasTfR-sbTmV8xA8D2Oaz1GIvwIL-W60K0Ayy6FXxzsTiXwVPqKiMZl8Ax02o5mYvmWlO_uLl6KEC7IGhyQ";  // Reemplaza esto con tu token de acceso

// Crear el payload para la solicitud de tarifas
$rate_request_data = [
    "accountNumber" => [
        "value" => "740561073"  // Reemplaza con tu número de cuenta de FedEx
    ],
    "requestedShipment" => [
        "shipper" => [
            "address" => [
                "postalCode" => $origenPostal,
                "countryCode" => "US"
            ]
        ],
        "recipient" => [
            "address" => [
                "postalCode" => $destinoPostal,
                "countryCode" => "US"
            ]
        ],
        "pickupType" => "DROPOFF_AT_FEDEX_LOCATION",
        "rateRequestType" => [
            "ACCOUNT",
            "LIST"
        ],
        "requestedPackageLineItems" => [
            [
                "weight" => [
                    "units" => "LB",
                    "value" => $peso
                ]
            ]
        ]
    ]
];

// Enviar la solicitud de tarifas a la API de FedEx
$rate_url = "https://apis-sandbox.fedex.com/rate/v1/rates/quotes";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $rate_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($rate_request_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access_token"
]);

$rate_response = curl_exec($ch);
curl_close($ch);

// Verificar si hubo error en la solicitud de tarifas
if (curl_errno($ch)) {
    echo json_encode(["error" => "Error en la solicitud de tarifas: " . curl_error($ch)]);
    exit();
}

// Mostrar la respuesta de la API
echo "<pre>";
print_r(json_decode($rate_response, true));  // Mostrar la respuesta de la API en formato legible
echo "</pre>";

?>
