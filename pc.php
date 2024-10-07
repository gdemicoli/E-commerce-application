<?php
// Start the session
session_start();

// Include configuration file
include 'config.php';

// Retrieve the grand total from session or POST data
if (isset($_POST['grandTotal'])) {
    // Sanitize the input to prevent security issues
    $grandTotal = filter_var($_POST['grandTotal'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $_SESSION['grandTotal'] = $grandTotal;
} elseif (isset($_SESSION['grandTotal'])) {
    $grandTotal = $_SESSION['grandTotal'];
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
    <!-- Include the Square Web Payments SDK -->
    <script type="text/javascript" src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
    <style>
        /* Your existing styles */
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
        .billing-container .payment-methods {
            margin-bottom: 20px;
        }
        .billing-container .payment-methods label {
            font-weight: normal;
            margin-right: 20px;
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
        Container for Google Pay button
        #google-pay-button-container {
            display: none;
            margin-top: 20px;
        }
        /* Square Payment Form Styles */
        #square-payment-form-container {
            display: none;
            margin-top: 20px;
        }
        #form-container {
            margin-bottom: 15px;
        }
        .sq-input {
            display: block;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 16px;
        }
        .button-credit-card {
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            font-size: 1.1em;
            cursor: pointer;
            width: 100%;
        }
        .button-credit-card:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="billing-container">
    <h2>Billing Information</h2>
    <form id="billing-form" method="post">
        
    <!-- Hidden fields for PayPal -->
    <input type="hidden" name="business" value="<?php echo PAYPAL_ID; ?>" id="paypal-business">
        <input type="hidden" name="cmd" value="_xclick" id="paypal-cmd">
        <input type="hidden" name="item_name" value="Shopping Cart Purchase" id="paypal-item-name">
        <input type="hidden" name="item_number" value="1" id="paypal-item-number">
        <input type="hidden" name="amount" value="<?php echo $grandTotal; ?>" id="paypal-amount">
        <input type="hidden" name="currency_code" value="<?php echo PAYPAL_CURRENCY; ?>" id="paypal-currency-code">
        <input type="hidden" name="return" value="<?php echo PAYPAL_RETURN_URL; ?>" id="paypal-return">
        <input type="hidden" name="cancel_return" value="<?php echo PAYPAL_CANCEL_URL; ?>" id="paypal-cancel-return">
        <input type="hidden" name="notify_url" value="<?php echo PAYPAL_NOTIFY_URL; ?>" id="paypal-notify-url">
        <input type="hidden" id="amount" value="<?php echo intval($grandTotal * 100); ?>">
        


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
            <option value="AU">Australia</option>
            <!-- Add more countries as needed -->
        </select>

        <label for="state">State/Province *</label>
        <input type="text" id="state" name="state" required>

        <label for="zip">Zip/Postal Code *</label>
        <input type="text" id="zip" name="zip" required>

        <h3>Select Payment Method *</h3>
        <div class="payment-methods">
            <!-- Existing payment method options -->
            <label>
                <input type="radio" name="payment-method" value="google-pay" id="google-pay-option" required>
                <img src="assets/img/google-pay.png" alt="Google Pay" style="height:24px; vertical-align: middle;">
                Google Pay
            </label>
            <label>
                <input type="radio" name="payment-method" value="paypal" id="paypal-option" required>
                <img src="assets/img/paypal.png" alt="PayPal" style="height:24px; vertical-align: middle;">
                PayPal
            </label>
            <label>
                <input type="radio" name="payment-method" value="square" id="square-option" required>
                <img src="assets/img/square.png" alt="Square" style="height:24px; vertical-align: middle;">
                Square
            </label>
        </div>

        <!-- Container for Square Payment Form -->
        <div id="square-payment-form-container">
            <div id="card-container"></div>
            <button id="sq-creditcard" class="button-credit-card">Pay with Card</button>
            <div id="payment-status-container"></div>
        </div>

        <!-- Container for Google Pay button -->
        <div id="google-pay-button-container"></div>

        <button type="submit" id="proceed-button">Proceed to Payment</button>
    </form>
</div>

<!-- Combined JavaScript Code -->
<script type="text/javascript">
    // Declare paymentsClient globally
    let paymentsClient = null;

    // Ensure that onGooglePayLoaded() is defined in the global scope
    window.onGooglePayLoaded = function() {
        const paymentsClient = getGooglePaymentsClient();
        paymentsClient.isReadyToPay(getGoogleIsReadyToPayRequest())
            .then(function(response) {
                if (response.result) {
                    addGooglePayButton();
                } else {
                    console.log('Google Pay not available');
                }
            })
            .catch(function(err) {
                console.error('Error determining readiness to use Google Pay:', err);
            });
    };

    /**
     * Initialize the Google Pay API client
     */
    function getGooglePaymentsClient() {
        if (paymentsClient === null) {
            paymentsClient = new google.payments.api.PaymentsClient({
                environment: 'TEST',
                paymentDataCallbacks: {
                    onPaymentAuthorized: onPaymentAuthorized
                }
            });
        }
        return paymentsClient;
    }

    /**
     * Define the version of the Google Pay API referenced when creating your
     * configuration
     */
    const baseRequest = {
      apiVersion: 2,
      apiVersionMinor: 0
    };

    /**
     * Identify your gateway and your site's gateway merchant identifier
     *
     * For testing purposes, we'll use 'example' as the gateway.
     * In a real environment, replace this with your actual gateway and merchant ID.
     */
    const tokenizationSpecification = {
      type: 'PAYMENT_GATEWAY',
      parameters: {
        'gateway': 'example',
        'gatewayMerchantId': 'exampleGatewayMerchantId'
      }
    };

    /**
     * Card networks and authentication methods supported by your site and your gateway
     */
    const allowedCardNetworks = ["AMEX", "DISCOVER", "INTERAC", "JCB", "MASTERCARD", "VISA"];
    const allowedCardAuthMethods = ["PAN_ONLY", "CRYPTOGRAM_3DS"];

    /**
     * Describe your allowed payment methods
     */
    const baseCardPaymentMethod = {
      type: 'CARD',
      parameters: {
        allowedAuthMethods: allowedCardAuthMethods,
        allowedCardNetworks: allowedCardNetworks
      }
    };

    const cardPaymentMethod = Object.assign(
      {tokenizationSpecification: tokenizationSpecification},
      baseCardPaymentMethod
    );

    /**
     * Determine readiness to pay with the Google Pay API
     */
    function getGoogleIsReadyToPayRequest() {
      return Object.assign(
        {},
        baseRequest,
        {
          allowedPaymentMethods: [baseCardPaymentMethod]
        }
      );
    }

    /**
     * Add a Google Pay payment button
     */
    function addGooglePayButton() {
      const paymentsClient = getGooglePaymentsClient();
      const button =
          paymentsClient.createButton({
            onClick: onGooglePaymentButtonClicked,
            allowedPaymentMethods: [baseCardPaymentMethod]
          });
      document.getElementById('google-pay-button-container').appendChild(button);
    }

    /**
     * Create a PaymentDataRequest object
     */
    function getGooglePaymentDataRequest() {
      const paymentDataRequest = Object.assign({}, baseRequest);
      paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
      paymentDataRequest.transactionInfo = getGoogleTransactionInfo();
      paymentDataRequest.merchantInfo = {
        merchantName: 'Example Merchant'
      };
      paymentDataRequest.callbackIntents = ["PAYMENT_AUTHORIZATION"];
      return paymentDataRequest;
    }

    /**
     * Provide Google Pay API with a payment amount, currency, and amount status
     */
    function getGoogleTransactionInfo() {
      // Retrieve the total price from PHP variable
      let totalPrice = '<?php echo $grandTotal; ?>';
      if (!totalPrice) {
          totalPrice = '0.00';
      }
      // Remove any currency symbols or commas
      totalPrice = totalPrice.replace(/[^0-9.]/g, '');

      return {
        countryCode: 'AU',
        currencyCode: "AUD",
        totalPriceStatus: "FINAL",
        totalPrice: totalPrice,
        totalPriceLabel: "Total"
      };
    }

    /**
     * Show Google Pay payment sheet when Google Pay payment button is clicked
     */
    function onGooglePaymentButtonClicked() {
        const paymentDataRequest = getGooglePaymentDataRequest();

        const paymentsClient = getGooglePaymentsClient();
        paymentsClient.loadPaymentData(paymentDataRequest)
            .then(function(paymentData) {
                // The paymentData contains the payment info
                // The onPaymentAuthorized callback has already processed the payment
                // Now we can safely redirect
                window.location.href = 'confirmation.php';
            })
            .catch(function(err) {
                console.error(err);
            });
        }

    /**
     * Handles authorize payments callback intents
     */
    function onPaymentAuthorized(paymentData) {
    return new Promise(function(resolve, reject){
        processGooglePayPayment(paymentData)
        .then(function() {
        resolve({transactionState: 'SUCCESS'});
        })
        .catch(function() {
        resolve({
            transactionState: 'ERROR',
            error: {
            intent: 'PAYMENT_AUTHORIZATION',
            message: 'Payment failed',
            reason: 'PAYMENT_DATA_INVALID'
            }
        });
        });
    });
    }

    /**
     * Process payment data returned by the Google Pay API
     */
    function processGooglePayPayment(paymentData) {
    return new Promise(function(resolve, reject) {
        // Include the amount in the data sent to the server
        paymentData.amount = '<?php echo $grandTotal; ?>'; // Embed PHP variable into JavaScript

        // Send the payment data to the server via AJAX
        fetch('process_google_pay_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(paymentData)
        })
        .then(response => response.json())
        .then(data => {
        if (data.success) {
            console.log('Payment processed successfully on the server');
            resolve({});
        } else {
            console.error('Server processing failed:', data.error);
            reject();
        }
        })
        .catch(error => {
        console.error('Error processing payment:', error);
        reject();
        });
    });
    }
    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {

        // Payment method selection handling
        const googlePayOption = document.getElementById('google-pay-option');
        const paypalOption = document.getElementById('paypal-option');
        const squareOption = document.getElementById('square-option');
        const proceedButton = document.getElementById('proceed-button');
        const googlePayButtonContainer = document.getElementById('google-pay-button-container');
        const squarePaymentFormContainer = document.getElementById('square-payment-form-container');
        const billingForm = document.getElementById('billing-form');
        const paymentMethodInputs = document.querySelectorAll('input[name="payment-method"]');

        function handlePaymentMethodChange() {
            if (googlePayOption.checked) {
                proceedButton.style.display = 'none';
                googlePayButtonContainer.style.display = 'block';
                squarePaymentFormContainer.style.display = 'none';
                billingForm.setAttribute('action', '');
                billingForm.setAttribute('method', 'post');
            } else if (paypalOption.checked) {
                proceedButton.style.display = 'block';
                googlePayButtonContainer.style.display = 'none';
                squarePaymentFormContainer.style.display = 'none';
                billingForm.setAttribute('action', '<?php echo PAYPAL_URL; ?>');
                billingForm.setAttribute('method', 'post');
            } else if (squareOption.checked) {
                proceedButton.style.display = 'none';
                googlePayButtonContainer.style.display = 'none';
                squarePaymentFormContainer.style.display = 'block';
                billingForm.setAttribute('action', '');
                billingForm.setAttribute('method', 'post');
            } else {
                proceedButton.style.display = 'block';
                googlePayButtonContainer.style.display = 'none';
                squarePaymentFormContainer.style.display = 'none';
                billingForm.setAttribute('action', 'confirmation.html');
                billingForm.setAttribute('method', 'post');
            }
        }

        paymentMethodInputs.forEach(function(input) {
            input.addEventListener('change', handlePaymentMethodChange);
        });

        handlePaymentMethodChange();

        // Prevent form submission when Square or Google Pay is selected
        billingForm.addEventListener('submit', function(event) {
            if (squareOption.checked || googlePayOption.checked) {
                event.preventDefault();
                // Do nothing as payment is handled via JavaScript
            }
        });

        // Initialize Square Payments
        initializeSquarePayments();
    });

    // Square Web Payments SDK Implementation
    let payments;

    async function initializeSquarePayments() {
        if (!window.Square) {
            throw new Error('Square.js failed to load properly.');
        }

        payments = window.Square.payments('sandbox-sq0idb-N4SKDRdi75Lt-ZljmB56oQ', 'LR0FTXEP3BWQF');

        const card = await payments.card();
        await card.attach('#card-container');

        document.querySelector('#sq-creditcard').addEventListener('click', async function(event) {
            event.preventDefault();
            const statusContainer = document.getElementById('payment-status-container');
            try {
                const result = await card.tokenize();
                if (result.status === 'OK') {
                    processPayment(result.token);
                } else {
                    throw new Error('Card tokenization failed');
                }
            } catch (e) {
                statusContainer.innerText = e.message;
            }
        });
    }

    function processPayment(token) {
    const amountElement = document.getElementById('amount');
    const amount = parseInt(amountElement.value);
    console.log('Amount to be charged:', amount);
    // Send the token and amount to your server for processing
    fetch('process_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            token: token,
            amount: amount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payment Successful!');
            window.location.href = 'confirmation.php';
        } else {
            alert('Payment Failed: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred during payment processing.');
    });
}

</script>

<!-- Include the Google Pay API JavaScript library AFTER your custom JavaScript code -->
<script async
    src="https://pay.google.com/gp/p/js/pay.js"
    onload="onGooglePayLoaded()">
</script>

</body>
</html>
