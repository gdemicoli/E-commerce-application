<?php
// Start the session
session_start();

// Include configuration file
include 'config.php';

// Retrieve the grand total from POST data
if (isset($_POST['grandTotal'])) {
    // Sanitize the input to prevent security issues
    $grandTotal = filter_var($_POST['grandTotal'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $_SESSION['grandTotal'] = $grandTotal;
} else {
    // If grandTotal is not set, redirect back to cart
    header('Location: cart.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .billing-container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .billing-container h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .billing-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .billing-container input[type="text"],
        .billing-container input[type="email"],
        .billing-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .billing-container .optional {
            font-weight: normal;
            color: #888;
            font-size: 0.9em;
        }
        .billing-container button {
            width: 100%;
            padding: 15px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            cursor: pointer;
        }
        .billing-container button:hover {
            background-color: #333;
        }
    </style>
</head>
<body>

<div class="billing-container">
    <h2>Billing Information</h2>
    <form action="<?php echo PAYPAL_URL; ?>" method="post">
        <!-- PayPal Business Email -->
        <input type="hidden" name="business" value="<?php echo PAYPAL_ID; ?>">

        <!-- Specify a Buy Now button -->
        <input type="hidden" name="cmd" value="_xclick">

        <!-- Item Details -->
        <input type="hidden" name="item_name" value="Shopping Cart Purchase">
        <input type="hidden" name="item_number" value="1">
        <input type="hidden" name="amount" value="<?php echo $grandTotal; ?>">
        <input type="hidden" name="currency_code" value="<?php echo PAYPAL_CURRENCY; ?>">

        <!-- URLs -->
        <input type="hidden" name="return" value="<?php echo PAYPAL_RETURN_URL; ?>">
        <input type="hidden" name="cancel_return" value="<?php echo PAYPAL_CANCEL_URL; ?>">
        <input type="hidden" name="notify_url" value="<?php echo PAYPAL_NOTIFY_URL; ?>">

        <!-- Buyer Details -->
        <label for="first-name">First Name *</label>
        <input type="text" id="first-name" name="first_name" required>

        <label for="last-name">Last Name *</label>
        <input type="text" id="last-name" name="last_name" required>

        <label for="email">Email <span class="optional">(Optional)</span></label>
        <input type="email" id="email" name="email">

        <label for="address">Address *</label>
        <input type="text" id="address" name="address1" required>

        <label for="country">Country *</label>
        <select id="country" name="country" required>
            <option value="">--Select Country--</option>
            <!-- Add your country options -->
            <option value="US">United States</option>
            <option value="CA">Canada</option>
            <!-- ... -->
        </select>

        <label for="state">State/Province *</label>
        <input type="text" id="state" name="state" required>

        <label for="zip">Zip/Postal Code *</label>
        <input type="text" id="zip" name="zip" required>

        <!-- Payment Method Selection -->
        <h3>Select Payment Method *</h3>
        <div class="payment-methods">
            <label>
                <input type="radio" name="payment-method" value="paypal" checked required>
                <img src="assets/img/paypal.png" alt="PayPal" style="height:24px; vertical-align: middle;">
                PayPal
            </label>
            <!-- Add other payment methods if needed -->
        </div>

        <!-- Submit Button -->
        <button type="submit">Proceed to Payment</button>
    </form>
</div>

</body>
</html>
