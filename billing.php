<?php
session_start();

// include configuration file
include 'config.php';

//retrievce total
if (isset($_POST['grandTotal'])) {
    // sanitize input
    $grandTotal = filter_var($_POST['grandTotal'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $_SESSION['grandTotal'] = $grandTotal;
} elseif (isset($_SESSION['grandTotal'])) {
    $grandTotal = $_SESSION['grandTotal'];
} else {
    // if no total load back to cart
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
    <!-- Include Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
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
        
        #google-pay-button-container {
            display: none;
            margin-top: 20px;
        }
        
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
        /* stripe card element */
        #card-element {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        #stripe-payment-form-container button {
            width: 100%;
            padding: 15px;
            background-color: #6772e5;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            cursor: pointer;
        }
        #stripe-payment-form-container button:hover {
            background-color: #5469d4;
        }
        #card-errors {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="billing-container">
    <h2>Billing Information</h2>
    <form id="billing-form" method="post">
        
    <!-- paypal fields -->
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
        


        <!-- buyer details -->
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
            <label>
                <input type="radio" name="payment-method" value="square" id="square-option" required>
                <img src="assets/img/square.png" alt="Square" style="height:24px; vertical-align: middle;">
                Square
            </label>
            <label>
                <input type="radio" name="payment-method" value="stripe" id="stripe-option" required>
                <img src="assets/img/stripe.png" alt="Stripe" style="height:24px; vertical-align: middle;">
                Stripe
            </label>
        </div>

        <!-- container for square payment form -->
        <div id="square-payment-form-container">
            <div id="card-container"></div>
            <button id="sq-creditcard" class="button-credit-card">Pay with Card</button>
            <div id="payment-status-container"></div>
        </div>

        <!-- container for google pay button -->
        <div id="google-pay-button-container"></div>

        <!-- container for stripe payment form -->
        <div id="stripe-payment-form-container">
            <div id="card-element"></div>
            <div id="card-errors" role="alert"></div>
            <button id="stripe-submit-button">Pay with Stripe</button>
        </div>

        <button type="submit" id="proceed-button">Proceed to Payment</button>
    </form>
</div>


<script type="text/javascript">
    // declare paymentsClient globally
    let paymentsClient = null;

    // define google globally
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
     * initalise google api client
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
     define the api version
     */
    const baseRequest = {
      apiVersion: 2,
      apiVersionMinor: 0
    };

    /**
     testing merchant gateways
     */
    const tokenizationSpecification = {
      type: 'PAYMENT_GATEWAY',
      parameters: {
        'gateway': 'example',
        'gatewayMerchantId': 'exampleGatewayMerchantId'
      }
    };

    /**
     accepted card networks and authenication methods
     */
    const allowedCardNetworks = ["AMEX", "DISCOVER", "INTERAC", "JCB", "MASTERCARD", "VISA"];
    const allowedCardAuthMethods = ["PAN_ONLY", "CRYPTOGRAM_3DS"];

    /**
     allowed payment methods
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
     ready the google pay api
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
     intialise the google pay button
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
     intialise a payment request object
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
     tell the google api transacation details
     */
    function getGoogleTransactionInfo() {
      // total price from cart
      let totalPrice = '<?php echo $grandTotal; ?>';
      if (!totalPrice) {
          totalPrice = '0.00';
      }
      //clean the total price
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
     google payment dialouge
     */
    function onGooglePaymentButtonClicked() {
        const paymentDataRequest = getGooglePaymentDataRequest();

        const paymentsClient = getGooglePaymentsClient();
        paymentsClient.loadPaymentData(paymentDataRequest)
            .then(function(paymentData) {
                // the paymentData contains the payment info
                
                //redirect upon confirmation
                window.location.href = 'confirmation.php';
            })
            .catch(function(err) {
                console.error(err);
            });
        }

    /**
     payment authorization
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
     process the api data
     */
    function processGooglePayPayment(paymentData) {
    return new Promise(function(resolve, reject) {
        
        paymentData.amount = '<?php echo $grandTotal; ?>'; 

        // send the payment data to the server via AJAX
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

    
    document.addEventListener('DOMContentLoaded', function() {

        // payment method selection
        const googlePayOption = document.getElementById('google-pay-option');
        const paypalOption = document.getElementById('paypal-option');
        const squareOption = document.getElementById('square-option');
        const stripeOption = document.getElementById('stripe-option');
        const proceedButton = document.getElementById('proceed-button');
        const googlePayButtonContainer = document.getElementById('google-pay-button-container');
        const squarePaymentFormContainer = document.getElementById('square-payment-form-container');
        const stripePaymentFormContainer = document.getElementById('stripe-payment-form-container');
        const billingForm = document.getElementById('billing-form');
        const paymentMethodInputs = document.querySelectorAll('input[name="payment-method"]');

        function handlePaymentMethodChange() {
            if (googlePayOption.checked) {
                proceedButton.style.display = 'none';
                googlePayButtonContainer.style.display = 'block';
                squarePaymentFormContainer.style.display = 'none';
                stripePaymentFormContainer.style.display = 'none';
                billingForm.setAttribute('action', '');
                billingForm.setAttribute('method', 'post');
            } else if (paypalOption.checked) {
                proceedButton.style.display = 'block';
                googlePayButtonContainer.style.display = 'none';
                squarePaymentFormContainer.style.display = 'none';
                stripePaymentFormContainer.style.display = 'none';
                billingForm.setAttribute('action', '<?php echo PAYPAL_URL; ?>');
                billingForm.setAttribute('method', 'post');
            } else if (squareOption.checked) {
                proceedButton.style.display = 'none';
                googlePayButtonContainer.style.display = 'none';
                squarePaymentFormContainer.style.display = 'block';
                stripePaymentFormContainer.style.display = 'none';
                billingForm.setAttribute('action', '');
                billingForm.setAttribute('method', 'post');
            } else if (stripeOption.checked) {
                proceedButton.style.display = 'none';
                googlePayButtonContainer.style.display = 'none';
                squarePaymentFormContainer.style.display = 'none';
                stripePaymentFormContainer.style.display = 'block';
                billingForm.setAttribute('action', '');
                billingForm.setAttribute('method', 'post');
            } else {
                proceedButton.style.display = 'block';
                googlePayButtonContainer.style.display = 'none';
                squarePaymentFormContainer.style.display = 'none';
                stripePaymentFormContainer.style.display = 'none';
                billingForm.setAttribute('action', 'confirmation.html');
                billingForm.setAttribute('method', 'post');
            }
        }

        paymentMethodInputs.forEach(function(input) {
            input.addEventListener('change', handlePaymentMethodChange);
        });

        handlePaymentMethodChange();

        // prevention of form submission when paypal slelcted
        billingForm.addEventListener('submit', function(event) {
            if (squareOption.checked || googlePayOption.checked || stripeOption.checked) {
                event.preventDefault();
                
            }
        });

        // initialize the square payments
        initializeSquarePayments();

        // initialize Stripe
        const stripe = Stripe('pk_test_51Q74weP51D3tHBstDncCKGUcJLTMteb9GK7y9OZmwDxP4aogxW4gH4uO7Ns3W0M0lzrzEGNlx9d2Y5LMSa1eORzc00QMA8I6by'); // Replace with your Stripe publishable key
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element');

        //error handling for debugging
        card.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        //  form submission for stripe
        document.getElementById('stripe-submit-button').addEventListener('click', function(event) {
            event.preventDefault();
            // once pressed disable button
            document.getElementById('stripe-submit-button').disabled = true;
            // create a token
            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    
                    const errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    document.getElementById('stripe-submit-button').disabled = false;
                } else {
                    // send the token to the server
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            const form = document.getElementById('billing-form');

            const formData = new FormData(form);

            formData.append('stripeToken', token.id);

            const amountElement = document.getElementById('amount');
            const amount = amountElement.value;
            formData.append('amount', amount);

            fetch('stripe-payment-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                if (result.error) {
                    const errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error;
                    document.getElementById('stripe-submit-button').disabled = false;
                } else if (result.requires_action) {
                    window.location.href = result.redirect_url;
                } else if (result.success) {
                    window.location.href = result.redirect_url;
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = 'An error occurred during payment processing.';
                document.getElementById('stripe-submit-button').disabled = false;
            });
        }

    });

// square payment sdk
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

<script async
    src="https://pay.google.com/gp/p/js/pay.js"
    onload="onGooglePayLoaded()">
</script>

</body>
</html>
