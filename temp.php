<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Invalid CSRF token.";
    } else {
        // Validate product ID and quantity
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($product_id && $quantity) {
            $cartData = [
                'product_id' => $product_id,
                'quantity' => $quantity
            ];

            try {
                $result = Connection::insert('cart', $cartData);
                if ($result) {
                    $success_message = "Product added to cart successfully!";
                } else {
                    $error_message = "Failed to add product to cart";
                }
            } catch (PDOException $e) {
                $error_message = "Error adding to cart: " . $e->getMessage();
                error_log("Database error: " . $e->getMessage()); // Log the error
            }
        } else {
            $error_message = "Invalid product ID or quantity.";
        }
    }
}

// Security and data fetching
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: 404.php");
    exit;
}

$product = Connection::getOne(
    "products",
    ["id = :id"],
    [":id" => (int)$_GET['id']]
);

if (!$product) {
    header("Location: 404.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Detail</title>
    <!-- Add your CSS and JS includes here -->
</head>

<body>
    <?php 
    if (isset($success_message)) {
        echo "<div class='success-message'>" . htmlspecialchars($success_message) . "</div>";
    }
    if (isset($error_message)) {
        echo "<div class='error-message'>" . htmlspecialchars($error_message) . "</div>";
    }
    ?>

    <!-- Add to Cart Form -->
    <form method="POST" action="">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="add_to_cart" value="1">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" value="1" min="1">
        <button type="submit">Add to Cart</button>
    </form>
</body>

</html>