<?php 
require "./configs/dbconfig.php";

// Handle Create Review
if (isset($_POST['create'])) {
    $user_id = $_POST['user_id'];
    $book_id = $_POST['book_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, book_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $book_id, $rating, $comment]);

    header("Location: reviews.php");
    exit();
}

// Handle Update Review
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $pdo->prepare("UPDATE reviews SET rating=?, comment=? WHERE id=?");
    $stmt->execute([$rating, $comment, $id]);

    header("Location: reviews.php");
    exit();
}

// Handle Delete Review
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id=?");
    $stmt->execute([$id]);

    header("Location: index.php?route=booksReview");
    exit();
}

// Fetch Reviews with User & Book Details
$reviews = $pdo->query("
    SELECT r.*, u.name AS user_name, b.title AS book_title 
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN books b ON r.book_id = b.id
    ORDER BY r.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Users & Books for Dropdowns
$users = $pdo->query("SELECT id, name FROM users")->fetchAll(PDO::FETCH_ASSOC);
$books = $pdo->query("SELECT id, title FROM books")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Book Reviews";
include "./layouts/head.php";
?>

<h2 class="mb-4 text-center">⭐ Book Reviews Management</h2>

<!-- Create Review Button -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createReviewModal">➕ Add Review</button>

<!-- Create Review Modal -->
<div class="modal fade" id="createReviewModal" tabindex="-1" aria-labelledby="createReviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createReviewLabel">Add New Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">User:</label>
                        <select name="user_id" class="form-select" required>
                            <option value="" disabled selected>-- Select User --</option>
                            <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Book:</label>
                        <select name="book_id" class="form-select" required>
                            <option value="" disabled selected>-- Select Book --</option>
                            <?php foreach ($books as $book): ?>
                            <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rating:</label>
                        <select name="rating" class="form-select" required>
                            <option value="" disabled selected>-- Select Rating --</option>
                            <option value="1">⭐ 1</option>
                            <option value="2">⭐⭐ 2</option>
                            <option value="3">⭐⭐⭐ 3</option>
                            <option value="4">⭐⭐⭐⭐ 4</option>
                            <option value="5">⭐⭐⭐⭐⭐ 5</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment:</label>
                        <textarea name="comment" class="form-control" required></textarea>
                    </div>
                    <button type="submit" name="create" class="btn btn-primary">Add Review</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reviews Table -->
<table class="table table-striped">
    <thead class="table">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Book</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reviews as $review): ?>
        <tr>
            <td><?= $review['id'] ?></td>
            <td><?= htmlspecialchars($review['user_name']) ?></td>
            <td><?= htmlspecialchars($review['book_title']) ?></td>
            <td><?= str_repeat("⭐", $review['rating']) ?> (<?= $review['rating'] ?>/5)</td>
            <td><?= nl2br(htmlspecialchars($review['comment'])) ?></td>
            <td><?= $review['created_at'] ?></td>
            <td>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                    data-bs-target="#editReviewModal<?= $review['id'] ?>">✏️ Edit</button>
                <a href="reviews.php?delete=<?= $review['id'] ?>" class="btn btn-danger btn-sm"
                    onclick="return confirm('Delete this review?')">🗑️ Delete</a>
            </td>
        </tr>

        <!-- Edit Review Modal -->
        <div class="modal fade" id="editReviewModal<?= $review['id'] ?>" tabindex="-1" aria-labelledby="editReviewLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editReviewLabel">Edit Review #<?= $review['id'] ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $review['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Rating:</label>
                                <select name="rating" class="form-select" required>
                                    <option value="1" <?= ($review['rating'] == 1) ? 'selected' : '' ?>>⭐ 1</option>
                                    <option value="2" <?= ($review['rating'] == 2) ? 'selected' : '' ?>>⭐⭐ 2</option>
                                    <option value="3" <?= ($review['rating'] == 3) ? 'selected' : '' ?>>⭐⭐⭐ 3</option>
                                    <option value="4" <?= ($review['rating'] == 4) ? 'selected' : '' ?>>⭐⭐⭐⭐ 4</option>
                                    <option value="5" <?= ($review['rating'] == 5) ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ 5</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Comment:</label>
                                <textarea name="comment" class="form-control"
                                    required><?= htmlspecialchars($review['comment']) ?></textarea>
                            </div>
                            <button type="submit" name="update" class="btn btn-primary">Update Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php endforeach; ?>
    </tbody>
</table>

<?php include "./layouts/footer.php"; ?>