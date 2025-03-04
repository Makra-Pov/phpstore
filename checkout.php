<?php

// Fetch cart items
$cartItems = Connection::getAll('cart');

$totalAmount = 0;
$cartDetails = []; // New array to store cart items with product details

if ($cartItems) {
    foreach ($cartItems as $item) {
        // Get product details based on product_id
        $product = Connection::getAll('products', ["id = :id"], [":id" => $item['product_id']]);

        if ($product) {
            $product = $product[0]; // Extract the product details

            // Add product details to cart details array
            $cartDetails[] = [
                'name' => $product['name'],
                'quantity' => $item['quantity'],
                'price' => $product['price'],
            ];

            // Calculate total amount
            $totalAmount += $product['price'] * $item['quantity'];
        }
    }
}

// Format total amount
$totalAmount = number_format($totalAmount, 2);

// PayPal Client ID (Replace with your own)
$paypalClientId = "AQF4-LO7BqizaeGZelaVXdE4jw1Zw2StMNFegLMEONMF4bEAw7nz0ygL1zRyxqQyBHnvsmk2uhojUspS";

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypalClientId ?>&currency=USD"></script>
</head>

<body>

    <form method="POST" action="process_order.php">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1>Checkout</h1>
                    <section>
                        <h2>Billing Details</h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label htmlFor="firstName">First Name *</label>
                                <input type="text" class="form-control" id="firstName" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label htmlFor="lastName">Last Name *</label>
                                <input type="text" class="form-control" id="lastName" required />
                            </div>
                        </div>
                        <div class="mb-3 ">
                            <label htmlFor="company">Company Name (Optional)</label>
                            <input type="text" class="form-control" id="company" />
                        </div>
                        <div class="mb-3 ">
                            <label htmlFor="address">Street Address *</label>
                            <input type="text" class="form-control" id="address"
                                placeholder="House number and street name" required />
                        </div>
                        <div class="mb-3 ">
                            <label htmlFor="city">Town/City *</label>
                            <input type="text" class="form-control" id="city" required />
                        </div>
                        <div class="mb-3 ">
                            <label htmlFor="email">Email Address *</label>
                            <input type="email" class="form-control" id="email" required />
                        </div>
                        <div class="mb-3 ">
                            <label htmlFor="phone">Phone *</label>
                            <input type="tel" class="form-control" id="phone" required />
                        </div>
                        <br>
                </div>
                </section>
                <div class="col-md-6">
                    <div class="row mb-5">
                        <div class="col-md-12">
                            <h2 class="h3 mb-3 text-black">Your Order</h2>
                            <div class="p-3 p-lg-5 border bg-white">
                                <table class="table site-block-order-table mb-5">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cartDetails as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['name']) ?></td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="2"><strong>Order Total</strong></td>
                                            <td><strong>$<?= $totalAmount ?></strong></td>
                                        </tr>
                                    </tbody>

                                </table>

                                <div id="paypal-button-container"></div>
                                <script>
                                paypal.Buttons({
                                    createOrder: function(data, actions) {
                                        return actions.order.create({
                                            purchase_units: [{
                                                amount: {
                                                    value: "<?= $totalAmount ?>"
                                                }
                                            }]
                                        });
                                    },
                                    onApprove: function(data, actions) {
                                        return actions.order.capture().then(function(details) {
                                            alert('Transaction completed by ' + details.payer.name
                                                .given_name);
                                            setTimeout(function() {
                                                window.location.href = 'checkout.php';
                                            }, 20000); // Redirect after 2 seconds
                                        });
                                    }
                                }).render('#paypal-button-container');
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </form>
</body>

</html>