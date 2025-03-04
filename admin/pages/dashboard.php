<?php

$page_title = "Dashboard";

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_name'])) {
    header("Location: index.php?route=login");
    exit();
}


require './configs/connection.php';// Ensure the database is connected properly

try {
    $totalBooks=Connection::getCount('products');
    
    
    $books =Connection::getAll('products');
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include "./layouts/head.php";
?>

<div class="container mt-4">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h2>
    <a href="index.php?route=logout" class="btn btn-danger">Logout</a>

    <h2 class="mt-4">📊 Dashboard</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Product</h5>
                    <p class="card-text fs-3"><?= $totalBooks ?></p>
                </div>
            </div>
        </div>

    </div>

    <h4 class="mt-4">📚 Latest Product</h4>
    <table class="table table-striped mt-2">
        <thead>
            <tr>
                <th>Name</th>
                <th>Price ($)</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
            <tr>
                <td><?= htmlspecialchars($book['name']) ?></td>
                <td><?= number_format($book['price'], 2) ?></td>
                <td><?= $book['stock'] ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">No books found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "./layouts/footer.php"; ?>