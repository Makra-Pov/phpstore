<?php
define('PAYPAL_CLIENT_ID', 'AeemyGb85Lnyg6rRXkE3jchc8K5w13Toj5keNTkJYcYWr4IQr-OJEs5JRIGIKfylbm1vJwMT7Ly-e3nP');
define('PAYPAL_SECRET', 'EPvBd-hAH8WiBn6DpoFWFVpHKALnCfFAFlfoDR8o8htwhSyxeu0b5MigJkRR5rvPU_hWE1FlW7gOXCUB');
define('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com'); // Change to 'https://api-m.paypal.com' for live

function getPayPalAccessToken() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE_URL . "/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/json",
        "Accept-Language: en_US"
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);

    $jsonResponse = json_decode($response, true);
    return $jsonResponse['access_token'] ?? null;
}

function processPayPalPayment($totalAmount) {
    $accessToken = getPayPalAccessToken();
    if (!$accessToken) {
        return false;
    }

    $orderData = [
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "amount" => [
                    "currency_code" => "USD",
                    "value" => number_format($totalAmount, 2, '.', '')
                ]
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE_URL . "/v2/checkout/orders");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $accessToken
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $jsonResponse = json_decode($response, true);
    return $jsonResponse['id'] ?? false; // Return PayPal Order ID
}
?>