<?php
session_start();

// get the post data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if ($data) {

    $paymentMethodData = $data['paymentMethodData'];
    $tokenizationData = $paymentMethodData['tokenizationData'];
    $token = $tokenizationData['token'];

    $amount = $data['amount'];

    // store payment details in session for confirmation page
    $_SESSION['payment_details'] = [
        'transaction_id' => 'SIMULATED_TXN_ID', 
        'payment_status' => 'Success',
        'amount' => $amount,
        'currency' => 'AUD',
        'item_name' => 'Shopping Cart Purchase',
    ];

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid payment data']);
}
?>
