<?php
// Include configuration file and Stripe PHP library
require_once 'config.php';
require_once 'vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Get the payment intent ID from the session
session_start();
$payment_intent_id = $_SESSION['payment_intent_id'] ?? null;

if ($payment_intent_id) {
    try {
        // Retrieve the payment intent
        $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);

        // Get payment details
        $amount = $payment_intent->amount / 100; // Convert from cents to dollars
        $currency = strtoupper($payment_intent->currency);
        $payment_status = $payment_intent->status;
        $txn_id = $payment_intent->id;

        // You might want to get item details from your database based on the payment intent
        $item_name = "Your Product"; // Replace with actual item name

        // Clear the payment intent ID from the session
        unset($_SESSION['payment_intent_id']);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle any errors
        $error = $e->getMessage();
    }
} else {
    $error = "No payment information found.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Confirmation</title>
</head>
<body>
    <?php if (isset($error)): ?>
        <h1>Payment Error</h1>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php else: ?>
        <h1>Payment Successful</h1>
        <p>Thank you for your purchase!</p>
        <h3>Payment Details:</h3>
        <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($txn_id); ?></p>
        <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($payment_status); ?></p>
        <p><strong>Amount Paid:</strong> <?php echo htmlspecialchars($amount . ' ' . $currency); ?></p>
        <p><strong>Item Purchased:</strong> <?php echo htmlspecialchars($item_name); ?></p>
    <?php endif; ?>
</body>
</html>