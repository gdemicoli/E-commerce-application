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
        /* Container for Google Pay button */
        #google-pay-button-container {
            display: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="billing-container">
    <h2>Billing Information</h2>
    <form id="billing-form" method="post">
        <!-- We'll dynamically set the form's action and method via JavaScript -->

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
        </div>

        <!-- Container for Google Pay button -->
        <div id="google-pay-button-container"></div>

        <button type="submit" id="proceed-button">Proceed to Payment</button>
    </form>
</div>

<!-- Include the Google Pay API JavaScript library -->
<script async
    src="https://pay.google.com/gp/p/js/pay.js"
    onload="onGooglePayLoaded()">
</script>

<!-- JavaScript Code -->
<script>
    // Payment method selection handling
    const googlePayOption = document.getElementById('google-pay-option');
    const paypalOption = document.getElementById('paypal-option');
    const proceedButton = document.getElementById('proceed-button');
    const googlePayButtonContainer = document.getElementById('google-pay-button-container');
    const billingForm = document.getElementById('billing-form');

    function handlePaymentMethodChange() {
        if (googlePayOption.checked) {
            proceedButton.style.display = 'none';
            googlePayButtonContainer.style.display = 'block';
            billingForm.setAttribute('action', ''); // No action needed
            billingForm.setAttribute('method', 'post');
        } else if (paypalOption.checked) {
            proceedButton.style.display = 'block';
            googlePayButtonContainer.style.display = 'none';
            billingForm.setAttribute('action', '<?php echo PAYPAL_URL; ?>');
            billingForm.setAttribute('method', 'post');
        } else {
            proceedButton.style.display = 'block';
            googlePayButtonContainer.style.display = 'none';
            billingForm.setAttribute('action', 'confirmation.html');
            billingForm.setAttribute('method', 'post');
        }
    }

    // Attach event listeners to payment method radio buttons
    const paymentMethodInputs = document.querySelectorAll('input[name="payment-method"]');
    paymentMethodInputs.forEach(function(input) {
        input.addEventListener('change', handlePaymentMethodChange);
    });

    // Initialize the payment method display based on the default selection
    document.addEventListener('DOMContentLoaded', function() {
        handlePaymentMethodChange();
    });

    // Google Pay integration code
    /**
     * Your Google Pay API integration code goes here
     * (We'll integrate the code you provided next)
     */
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
     * Initialize the Google Pay API client
     */
    let paymentsClient = null;

    function getGooglePaymentsClient() {
      if ( paymentsClient === null ) {
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

    function onGooglePayLoaded() {
      const paymentsClient = getGooglePaymentsClient();
      paymentsClient.isReadyToPay(getGoogleIsReadyToPayRequest())
          .then(function(response) {
            if (response.result) {
              addGooglePayButton();
            }
          })
          .catch(function(err) {
            console.error(err);
          });
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
      // Retrieve the total price from session storage
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
            // handle the response
            processPayment(paymentData);
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
        // handle the response
        processPayment(paymentData)
        .then(function() {
          resolve({transactionState: 'SUCCESS'});
        })
        .catch(function() {
          resolve({
            transactionState: 'ERROR',
            error: {
              intent: 'PAYMENT_AUTHORIZATION',
              message: 'Insufficient funds',
              reason: 'PAYMENT_DATA_INVALID'
            }
          });
        });
      });
    }

    /**
     * Process payment data returned by the Google Pay API
     */
    function processPayment(paymentData) {
      return new Promise(function(resolve, reject) {
        setTimeout(function() {
          // For testing purposes, we'll consider the payment successful
          // In a real implementation, you'd send the payment data to your server to process the payment
          console.log('Payment successful', paymentData);
          alert('Payment successful!');
          // Redirect to confirmation page or perform other actions
          window.location.href = 'confirmation.html';
          resolve({});
        }, 1000);
      });
    }
</script>

</body>
</html>
