<?php
session_start();
//process_payment.php
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}


// include the Square SDK
require 'vendor/autoload.php';

use Square\SquareClient;
use Square\Environment;
use Square\Models\CreatePaymentRequest;
use Square\Models\Money;

$accessToken = 'EAAAl2xdhJqXHHCAmScspahGku0SHunRiqnyp0WNXHjPHTUOPtJL1p7MCgQR4E99';

// create a square client
$client = new SquareClient([
    'accessToken' => $accessToken,
    'environment' => Environment::SANDBOX,
]);

header('Content-Type: application/json');

$token = 'cnon:card-nonce-ok'; // Square's test nonce
$amount = intval($data['amount']);

// create a unique ID for the transaction
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
        // store payment details in session for confrimation page
        $_SESSION['payment_details'] = [
            'transaction_id' => $payment->getId(),
            'payment_status' => $payment->getStatus(),
            'amount' => $payment->getAmountMoney()->getAmount() / 100, 
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
