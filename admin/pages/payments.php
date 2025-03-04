<?php
require "./configs/dbconfig.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Handle payment processing
if (isset($_POST['pay'])) {
    $order_id = $_POST['order_id'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'];

    // Validate the payment method
    $valid_payment_methods = ['credit_card', 'paypal', 'bank_transfer'];
    if (!in_array($payment_method, $valid_payment_methods)) {
        die("Error: Invalid payment method.");
    }

    // Insert payment into the database
    $stmt = $pdo->prepare("INSERT INTO payments (order_id, payment_method, transaction_id) 
                           VALUES (?, ?, ?)");
    $stmt->execute([$order_id, $payment_method, $transaction_id]);

    // Update order status to 'paid'
    $stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
    $stmt->execute([$order_id]);

    echo "Payment successfully processed! Thank you for your purchase.";
} else {
    if (isset($_GET['order_id'])) {
        $order_id = $_GET['order_id'];
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            die("Error: Order not found.");
        }

        // Display payment form
        ?>
<h2>Complete Payment for Order #<?= $order['id'] ?></h2>
<form method="post" action="payment.php">
    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
    <label for="payment_method">Payment Method:</label>
    <select name="payment_method" id="payment_method" required>
        <option value="credit_card">Credit Card</option>
        <option value="paypal">PayPal</option>
        <option value="bank_transfer">Bank Transfer</option>
    </select>
    <br><br>
    <label for="transaction_id">Transaction ID:</label>
    <input type="text" name="transaction_id" id="transaction_id" required>
    <br><br>
    <button type="submit" name="pay">Pay Now</button>
</form>
<?php
    } else {
        echo "Error: Missing order ID.";
    }
}
?>