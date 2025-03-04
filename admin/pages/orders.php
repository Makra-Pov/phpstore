<?php
require "./configs/dbconfig.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Handle Create Order
if (isset($_POST['create'])) {
    if (!isset($_POST['books_id']) || empty($_POST['books_id'])) {
        die("Error: No books selected.");
    }
    if (!isset($_POST['quantities']) || empty($_POST['quantities'])) {
        die("Error: Quantities are missing.");
    }

    $books_id = $_POST['books_id'];
    $quantities = $_POST['quantities'];
    $total_price = 0;
    $order_items = [];

    foreach ($books_id as $index => $book_id) {
        $quantity = $quantities[$index];
        $stmt = $pdo->prepare("SELECT price, image_url FROM books WHERE id = ?");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$book) {
            die("Error: Book with ID $book_id not found.");
        }

        $book_price = $book['price'];
        $total_price += $book_price * $quantity;

        $order_items[] = [
            'book_id' => $book_id,
            'quantity' => $quantity,
            'price' => $book_price
        ];
    }

    // Insert order into the database
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
    $stmt->execute([$user_id, $total_price]);
    $order_id = $pdo->lastInsertId();

    // Insert order items into the database
    foreach ($order_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);
    }

    // Redirect to payment page
    header("Location: payment.php?order_id=" . $order_id);
    exit();
}

// Fetch orders for display
$orders = $pdo->query("SELECT o.*, u.name AS user_name FROM orders o 
                        JOIN users u ON o.user_id = u.id ORDER BY o.id DESC")
             ->fetchAll(PDO::FETCH_ASSOC);

foreach ($orders as &$order) {
    $stmt = $pdo->prepare("SELECT oi.*, b.title, b.price, b.image_url FROM order_items oi 
                           JOIN books b ON oi.book_id = b.id WHERE oi.order_id = ?");
    $stmt->execute([$order['id']]);
    $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$page_title = "Orders";
$index = 1;
include "./layouts/head.php";
?>

<h2 class="mb-4 text-center">📦 Order Management</h2>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Status</th>
            <th>Total Price ($)</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $index++ ?></td>
            <td><?= htmlspecialchars($order['user_name']) ?></td>
            <td><?= htmlspecialchars($order['status']) ?></td>
            <td><?= number_format($order['total_price'], 2) ?></td>
            <td><?= $order['created_at'] ?></td>
            <td>
                <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                    data-bs-target="#orderDetailsModal-<?= $order['id'] ?>">View Items</button>
                <a href="index.php?route=orders&delete=<?= $order['id'] ?>" class="btn btn-danger btn-sm"
                    onclick="return confirm('Are you sure you want to delete Order #<?= $order['id'] ?>?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php foreach ($orders as $order): ?>
<div class="modal fade" id="orderDetailsModal-<?= $order['id'] ?>" tabindex="-1" aria-labelledby="orderDetailsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order #<?= $order['id'] ?> - Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <?php foreach ($order['items'] as $item): ?>
                    <li class="list-group-item">
                        <img src="uploads/storages/images/<?= htmlspecialchars($item['image_url']) ?>" width="50"
                            alt="<?= htmlspecialchars($item['title']) ?>">
                        <?= htmlspecialchars($item['title']) ?> (x<?= $item['quantity'] ?>) -
                        $<?= number_format($item['price'], 2) ?> each
                        <strong>Total: $<?= number_format($item['quantity'] * $item['price'], 2) ?></strong>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php include "./layouts/footer.php"; ?>