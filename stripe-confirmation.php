<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

session_start();
$payment_intent_id = $_SESSION['payment_intent_id'] ?? null;

if ($payment_intent_id) {
    try {
        $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);

        // recieve transaction details
        $amount = $payment_intent->amount / 100; 
        $currency = strtoupper($payment_intent->currency);
        $payment_status = $payment_intent->status;
        $txn_id = $payment_intent->id;

        $item_name = "Your Product"; 

        unset($_SESSION['payment_intent_id']);
    } catch (\Stripe\Exception\ApiErrorException $e) {
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