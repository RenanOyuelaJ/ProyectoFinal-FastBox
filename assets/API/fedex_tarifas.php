<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener los datos enviados por el frontend
$origenPostal = isset($_GET['origenPostal']) ? $_GET['origenPostal'] : '';
$destinoPostal = isset($_GET['destinoPostal']) ? $_GET['destinoPostal'] : '';
$peso = isset($_GET['peso']) ? $_GET['peso'] : '';

// Crear el payload para la solicitud de tarifas
$rate_request_data = [
    "accountNumber" => [
        "value" => "740561073"
    ],
    "rateRequestControlParameters" => [
        "returnTransitTimes" => true
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
        "shippingChargesPayment" => [
            "paymentType" => "SENDER",
            "payor" => [
                "accountNumber" => "740561073", 
                "countryCode" => "US"           
            ]
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

// Mostrar el payload en formato JSON
echo json_encode(["payload_enviado" => $rate_request_data], JSON_PRETTY_PRINT);
?>
