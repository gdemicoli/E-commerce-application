<?php
// Start the session
session_start();

// Get the raw POST data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if ($data) {
    // Extract necessary payment details
    $paymentMethodData = $data['paymentMethodData'];
    $tokenizationData = $paymentMethodData['tokenizationData'];
    $token = $tokenizationData['token'];

    // Get the amount from the data
    $amount = $data['amount'];

    // For this sandbox environment, we'll simulate a successful payment processing
    // In a real scenario, you'd process the payment here using your payment gateway API

    // Store payment details in session
    $_SESSION['payment_details'] = [
        'transaction_id' => 'SIMULATED_TXN_ID', // Replace with actual transaction ID
        'payment_status' => 'Success',
        'amount' => $amount,
        'currency' => 'AUD',
        'item_name' => 'Shopping Cart Purchase',
    ];

    // Return a success response
    echo json_encode(['success' => true]);
} else {
    // Return an error response
    echo json_encode(['success' => false, 'error' => 'Invalid payment data']);
}
?>
