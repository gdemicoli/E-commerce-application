<?php
// Start the session (if not already started)
session_start();

// Include configuration file
require_once 'config.php';

// Include Stripe PHP library
require_once 'vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Define return URL
$isLocalhost = ($_SERVER['HTTP_HOST'] === 'localhost'); // Check if on localhost
$return_url = $isLocalhost ? 'http://localhost/E-commerce-application/stripe-confirmation.php' : 'https://localhost/stripe-confirmation.php'; // Adjust for your domain


// Get the token ID from the POST data
$token = $_POST['stripeToken'];

// Get the amount from POST or session, convert to cents
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) * 100 : (isset($_SESSION['cart_total']) ? $_SESSION['cart_total'] * 100 : 0);

// Ensure amount is valid
if ($amount <= 0) {
    echo json_encode(['error' => 'Invalid amount. Please check your cart and try again.']);
    exit;
}

try {
    // Create a PaymentIntent with amount and currency
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'aud',
        'payment_method_data' => [
            'type' => 'card',
            'card' => [
                'token' => $token,
            ],
        ],
        'confirmation_method' => 'manual',
        'confirm' => true,
        'return_url' => $return_url
    ]);

    // Store the PaymentIntent ID in the session
    $_SESSION['payment_intent_id'] = $paymentIntent->id;

    // Check payment status and handle accordingly
    if ($paymentIntent->status == 'requires_action' && 
        $paymentIntent->next_action->type == 'redirect_to_url') {
        // Redirect the customer to complete the payment
        echo json_encode(['requires_action' => true, 'redirect_url' => $paymentIntent->next_action->redirect_to_url->url]);
    } else if ($paymentIntent->status == 'succeeded') {
        // Payment is successful
        echo json_encode(['success' => true, 'redirect_url' => $return_url]);
    } else {
        // Payment failed or is in an unexpected state
        echo json_encode(['error' => 'Payment failed or is in an unexpected state']);
    }
} catch(\Stripe\Exception\CardException $e) {
    // Display error message to the customer
    echo json_encode(['error' => $e->getMessage()]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    // Display error message
    echo json_encode(['error' => $e->getMessage()]);
}

?>