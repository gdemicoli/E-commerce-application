<?php
//paypal configurations
define('PAYPAL_ID', 'kingsinghking101@gmail.com');

// enable sandbox
define('PAYPAL_SANDBOX', TRUE); 

// paypal return links
define('PAYPAL_RETURN_URL', 'http://localhost/E-commerce-application/sucess.php');
define('PAYPAL_CANCEL_URL', 'http://localhost/E-commerce-application/cancel.php');
define('PAYPAL_NOTIFY_URL', 'http://localhost/E-commerce-application/ipn.php');

define('PAYPAL_CURRENCY', 'AUD');

define('PAYPAL_URL', (PAYPAL_SANDBOX) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr');

define('STRIPE_SECRET_KEY', 'sk_test_51Q74weP51D3tHBstGbCMclSFhyNhiY8aEPt6JMUMDXpYpqOBrsXy1am6UozI6ED2L5GOffJENoDwZQPLmdWvVpcl00NZsTIM0h');

?>
