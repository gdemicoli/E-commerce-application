<?php
session_start();
//process_payment.php
// Enable error reporting
ini_set('display_errors', 0);
error_reporting(0);

// Set the content type to JSON
header('Content-Type: application/json');

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is received
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}


// Include the Square SDK
require 'vendor/autoload.php';

use Square\SquareClient;
use Square\Environment;
use Square\Models\CreatePaymentRequest;
use Square\Models\Money;

// Set your Square credentials
$accessToken = 'EAAAl2xdhJqXHHCAmScspahGku0SHunRiqnyp0WNXHjPHTUOPtJL1p7MCgQR4E99';

// Create a Square client
$client = new SquareClient([
    'accessToken' => $accessToken,
    'environment' => Environment::SANDBOX,
]);

// Set the content type to JSON
header('Content-Type: application/json');

// For testing purposes, set a fixed amount and token
$token = 'cnon:card-nonce-ok'; // Square's test nonce
$amount = intval($data['amount']);// $1.00 in cents

// Create a unique ID for the transaction
$transactionId = uniqid();

$money = new Money();
$money->setAmount($amount);
$money->setCurrency('AUD');

$create_payment_request = new CreatePaymentRequest(
    $token,
    $transactionId,
    // $money
);
$create_payment_request->setAmountMoney($money);

$payments_api = $client->getPaymentsApi();

try {
    $response = $payments_api->createPayment($create_payment_request);

    if ($response->isSuccess()) {
        $payment = $response->getResult()->getPayment();
        // Store payment details in session
        $_SESSION['payment_details'] = [
            'transaction_id' => $payment->getId(),
            'payment_status' => $payment->getStatus(),
            'amount' => $payment->getAmountMoney()->getAmount() / 100, // Convert cents to dollars
            'currency' => $payment->getAmountMoney()->getCurrency(),
            'created_at' => $payment->getCreatedAt(),
        ];
        echo json_encode(['success' => true]);
    }  else {
        $errors = $response->getErrors();
        echo json_encode(['success' => false, 'error' => $errors[0]->getDetail()]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
