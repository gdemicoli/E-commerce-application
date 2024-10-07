<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <style>
        .cart-container {
            width: 80%;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .cart-header, .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .cart-item {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .cart-item img {
            max-width: 100px;
        }
        .cart-item p {
            margin: 0;
        }
        .cart-item .description {
            flex: 2;
            padding: 0 10px;
        }
        .cart-item .price, .cart-item .qty, .cart-item .total {
            flex: 1;
            text-align: center;
        }
        .cart-item .qty input {
            width: 50px;
            text-align: center;
        }
        .update-btn, .remove-btn, .checkout-btn {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-right: 10px;
        }
        .cart-total {
            text-align: right;
            margin-top: 20px;
        }
        .cart-total h2 {
            margin: 0;
        }
    </style>
</head>
<body>

<div class="cart-container">
    <h1>Shopping Cart</h1>
    <div class="cart-header">
        <div>Remove</div>
        <div>Image</div>
        <div class="description">Product Description</div>
        <div class="price">Price</div>
        <div class="qty">Qty</div>
        <div class="total">Total</div>
    </div>

    <div class="cart-item">
        <div><input type="checkbox"></div>
        <div><img src="assets/img/bronton.jpg" alt="Electric Bike Model 1"></div>
        <div class="description">
            <p><strong>[EB1-500W-48V-28MPH] Electric Bike Model 1</strong></p>
            <p>500W Motor, 48V Battery, Range: 50 miles, Top Speed: 28 mph</p>
            <p>Availability: <span style="color:green;">Online</span> <span style="color:blue;">Immediate Pick-up</span></p>
        </div>
        <div class="price">$1,299.00</div>
        <div class="qty"><input type="number" value="1" min="1"></div>
        <div class="total">$1,299.00</div>
    </div>

    <div class="cart-item">
        <div><input type="checkbox"></div>
        <div><img src="assets/img/bronton.jpg" alt="Electric Bike Model 2"></div>
        <div class="description">
            <p><strong>[EB2-750W-52V-32MPH] Electric Bike Model 2</strong></p>
            <p>750W Motor, 52V Battery, Range: 60 miles, Top Speed: 32 mph</p>
            <p>Availability: <span style="color:green;">Online</span> <span style="color:blue;">Immediate Pick-up</span></p>
        </div>
        <div class="price">$1,499.00</div>
        <div class="qty"><input type="number" value="1" min="1"></div>
        <div class="total">$1,499.00</div>
    </div>

    <form id="checkout-form" action="billing.php" method="post">
        <input type="hidden" name="grandTotal" id="grandTotalInput" value="">

        <button type="button" class="update-btn" id="update-btn">UPDATE QTY</button>
        <button type="button" class="remove-btn" id="remove-btn">REMOVE</button>
        <button type="submit" class="checkout-btn" id="checkout-btn">CHECKOUT NOW</button>
    </form>

    <div class="cart-total">
        <h2>Grand Total: $<span id="grand-total">2,798.00</span></h2>
    </div>
</div>

<script>
    const updateButton = document.getElementById('update-btn');
    const removeButton = document.getElementById('remove-btn');
    const checkoutButton = document.getElementById('checkout-btn');
    const finalTotal = document.getElementById('grand-total');
    const grandTotalInput = document.getElementById('grandTotalInput');

    function updateTotals() {
        let cartItems = document.querySelectorAll('.cart-item');
        let finalTotalValue = 0;

        cartItems.forEach(function(item) {
            // Quantity 
            let qtyInput = item.querySelector('.qty input');
            let quantity = parseInt(qtyInput.value);

            // Price
            let priceElement = item.querySelector('.price');
            let priceValue = parseFloat(priceElement.textContent.replace('$','').replace(',',''));

            // Calculate item total
            let itemTotal = priceValue * quantity;

            // Update total for item
            let totalElement = item.querySelector('.total');
            totalElement.textContent = '$' + itemTotal.toFixed(2);

            // Update final total
            finalTotalValue += itemTotal;
        });

        // Update the displayed grand total
        finalTotal.textContent = finalTotalValue.toFixed(2);

        // Set the grand total in the hidden input field
        grandTotalInput.value = finalTotalValue.toFixed(2);
    }

    // Initialize totals on page load
    updateTotals();

    updateButton.addEventListener('click', function() {
        updateTotals();
    });

    checkoutButton.addEventListener('click', function() {
    // Assuming finalTotal.textContent contains the total amount
    localStorage.setItem('grandTotal', finalTotal.textContent);
    // Proceed to billing page
    window.location.href = 'billing.html';
});

    removeButton.addEventListener('click', function() {
        // Retrieve cart items
        let cartItems = document.querySelectorAll('.cart-item');
        cartItems.forEach(function(item) {
            // Checkbox
            let checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox.checked) {
                // Remove item
                item.parentNode.removeChild(item);
            }
        });
        updateTotals();
    });
</script>

</body>
</html>
