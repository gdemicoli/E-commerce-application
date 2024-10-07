<?php
include 'config.php';

// Get transaction information from paypal
if (!empty($_GET['tx'])) {
    $txn_id = $_GET['tx'];
    $payment_status = $_GET['st'];
    $payment_amount = $_GET['amt'];
    $currency = $_GET['cc'];
    $item_number = $_GET['item_number'];
    $item_name = $_GET['item_name'];

   
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
    <p><strong>Transaction ID:</strong> <?php echo $txn_id; ?></p>
    <p><strong>Payment Status:</strong> <?php echo $payment_status; ?></p>
    <p><strong>Amount Paid:</strong> <?php echo $payment_amount . ' ' . $currency; ?></p>
    <p><strong>Item Purchased:</strong> <?php echo $item_name; ?></p>
</body>
</html>
