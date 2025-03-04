<?php

$page_title = "Product Categories";
require './configs/connection.php';

// Function to add a new category
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $category_description = $_POST['category_description'];

    // Insert new category into the database
    Connection::insert('category', ['name' => $category_name]);

    // Redirect to avoid form resubmission
    header("Location: index.php?route=category");
    exit;
}

// Function to update a category
if (isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $category_description = $_POST['category_description'];

    // Update category in the database
    $stmt = $pdo->prepare("UPDATE categories SET name = :name, description = :description WHERE id = :id");
    $stmt->execute(['id' => $category_id, 'name' => $category_name, 'description' => $category_description]);

    // Redirect to avoid form resubmission
    header("Location: index.php?route=category");
    exit;
}

// Function to delete a category
if (isset($_GET['delete_id'])) {
    $category_id = $_GET['delete_id'];

    // Delete category from the database
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => $category_id]);

    // Redirect to avoid reloading issues
    header("Location: index.php?route=category");
    exit;
}

// Function to fetch categories
$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


$index = 1;

include "./layouts/head.php"; 
?>

<!-- Categories Section -->
<div class="container my-5">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="text-center fs-3">Manage Categories</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add New
            Categories</button>
    </div>

    <!-- Search Bar -->
    <div class="mb-3">
        <input type="text" id="categorySearch" class="form-control" placeholder="Search Categories">
    </div>

    <!-- Categories Table -->
    <table class="table">
        <thead>
            <tr>
                <th class="text-center">Name</th>
                <th class="text-center">Description</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
            <tr>
                <td class="text-center"><?php echo $index++ ?></td>
                <td class="text-center"><?php echo htmlspecialchars($category['name']); ?>
                </td>
                <td class="text-center"><?php echo htmlspecialchars($category['description']); ?></td>
                <td class="text-center">
                    <!-- Edit and Delete Links -->
                    <button class="btn btn-warning btn-sm text-center" data-bs-toggle="modal"
                        data-bs-target="#editCategoryModal" data-id="<?php echo $category['id']; ?>"
                        data-name="<?php echo htmlspecialchars($category['name']); ?>"
                        data-description="<?php echo htmlspecialchars($category['description']); ?>">
                        Edit
                    </button>
                    <a href="index.php?route=category&delete_id=<?php echo $category['id']; ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?route=category" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_name">Category Name</label>
                        <input type="text" id="category_name" name="category_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="category_description">Category Description</label>
                        <textarea id="category_description" name="category_description" class="form-control"
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?route=category" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="form-group">
                        <label for="edit_category_name">Category Name</label>
                        <input type="text" id="edit_category_name" name="category_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_category_description">Category Description</label>
                        <textarea id="edit_category_description" name="category_description" class="form-control"
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="edit_category" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Edit Category Modal
document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
    button.addEventListener('click', () => {
        // Set the values in the modal from the button's data attributes
        document.getElementById('edit_category_id').value = button.dataset.id;
        document.getElementById('edit_category_name').value = button.dataset.name;
        document.getElementById('edit_category_description').value = button.dataset.description;
    });
});

// Search functionality for categories
document.getElementById('categorySearch').addEventListener('keyup', function() {
    var searchTerm = this.value.toLowerCase();
    var rows = document.querySelectorAll('tbody tr');

    rows.forEach(function(row) {
        var name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
        var description = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

        if (name.includes(searchTerm) || description.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>