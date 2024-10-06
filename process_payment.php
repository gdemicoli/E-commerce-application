<?php
// process_payment.php

// Include the Square SDK
require 'vendor/autoload.php'; // Assuming you have installed the SDK via Composer

use Square\SquareClient;
use Square\Environment;
use Square\Exceptions\ApiException;

// Set your Square credentials
$accessToken = 'EAAAl1cAm9fWtPbskSU9auwwD_hWbDRVSIPKEy7J2d9DWHh_h2gDfDmKFZnw3KF_'; // Replace with your Sandbox Access Token

// Create a Square client
$client = new SquareClient([
    'accessToken' => $accessToken,
    'environment' => Environment::SANDBOX,
]);

// Get the POST body
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

if (!isset($data['nonce']) || !isset($data['amount'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$nonce = $data['nonce'];
$amount = intval($data['amount']); // Amount in cents

// Create a unique ID for the transaction
$transactionId = uniqid();

$payments_api = $client->getPaymentsApi();

try {
    $response = $payments_api->createPayment([
        'source_id' => $nonce,
        'idempotency_key' => $transactionId,
        'amount_money' => [
            'amount' => $amount,
            'currency' => 'AUD'
        ]
    ]);

    if ($response->isSuccess()) {
        echo json_encode(['success' => true]);
    } else {
        $errors = $response->getErrors();
        echo json_encode(['success' => false, 'error' => $errors[0]->getDetail()]);
    }
} catch (ApiException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
