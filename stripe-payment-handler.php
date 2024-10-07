<?php
session_start();

require_once 'config.php';

require_once 'vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// define return URL
$isLocalhost = ($_SERVER['HTTP_HOST'] === 'localhost'); // check if on localhost
$return_url = $isLocalhost ? 'http://localhost/E-commerce-application/stripe-confirmation.php' : 'https://yourdomain.com/stripe-confirmation.php'; // Adjust for your domain

ini_set('display_errors', 1);
error_reporting(E_ALL);

// recieve token data
$token = $_POST['stripeToken'] ?? null;

//convert amount to cents
$amount = isset($_POST['amount']) ? intval($_POST['amount']) : (isset($_SESSION['cart_total']) ? $_SESSION['cart_total'] * 100 : 0);

// make sure not 0
if ($amount <= 0) {
    echo json_encode(['error' => 'Invalid amount. Please check your cart and try again.']);
    exit;
}

// check for token percence
if (!$token) {
    echo json_encode(['error' => 'Payment token is missing. Please try again.']);
    exit;
}

try {
    
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

    // store the payement id in the session
    $_SESSION['payment_intent_id'] = $paymentIntent->id;

    if ($paymentIntent->status == 'requires_action' && 
        $paymentIntent->next_action->type == 'redirect_to_url') {
        // redirect to complete the payment
        echo json_encode(['requires_action' => true, 'redirect_url' => $paymentIntent->next_action->redirect_to_url->url]);
    } else if ($paymentIntent->status == 'succeeded') {
        echo json_encode(['success' => true, 'redirect_url' => $return_url]);
    } else {
        echo json_encode(['error' => 'Payment failed or is in an unexpected state']);
    }
} catch(\Stripe\Exception\CardException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => 'An unexpected error occurred. Please try again later.']);
}

?>
