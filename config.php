<?php
// PayPal configuration

// Seller PayPal account email
define('PAYPAL_ID', 'kingsinghking101@gmail.com');

// Sandbox mode toggle
define('PAYPAL_SANDBOX', TRUE); // TRUE for sandbox testing, FALSE for live transactions

// PayPal return URLs
define('PAYPAL_RETURN_URL', 'http://localhost/E-commerce-application/index.html');
define('PAYPAL_CANCEL_URL', 'http://localhost/E-commerce-application/cancel.php');
define('PAYPAL_NOTIFY_URL', 'http://localhost/E-commerce-application/ipn.php');

// Currency code
define('PAYPAL_CURRENCY', 'AUD');

// PayPal URL based on sandbox or live mode
define('PAYPAL_URL', (PAYPAL_SANDBOX) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr');
?>
