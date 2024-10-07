<?php
session_start();

if (isset($_SESSION['payment_details'])) {
    $paymentDetails = $_SESSION['payment_details'];

} else {
    header('Location: billing.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Successful</title>
</head>
<body>
    <h1>Payment Successful</h1>
    <p>Thank you for your purchase!</p>
    <h3>Payment Details:</h3>
    <p><strong>Transaction ID:</strong> <?php echo $paymentDetails['transaction_id']; ?></p>
    <p><strong>Payment Status:</strong> <?php echo $paymentDetails['payment_status']; ?></p>
    <p><strong>Amount Paid:</strong> <?php echo $paymentDetails['amount'] . ' ' . $paymentDetails['currency']; ?></p>
    <p><strong>Item Purchased:</strong> <?php echo $paymentDetails['item_name']; ?></p>
</body>
</html>
