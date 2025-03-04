<?php
// Include the database connection
require './configs/connection.php';

$page_name = "Products";

$products = Connection::getAll('products');
$index = 1;


$categories = Connection::getAll('categories');

// Add Product Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $description = htmlspecialchars($_POST['description'] ?? '');
   
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $image = $_FILES['image_url'] ?? null;
    $image_url = " $image ";

    try {
         // Start transaction 
        Connection::connect()->beginTransaction(); // Start transaction

        // Handle file upload (if an image is provided)
        if (!empty($image['name'])) {
            $targetDir = "uploads/storages/images/";
            $imageName = time() . "_" . basename($image['name']);
            $targetFilePath = $targetDir . $imageName;

            // Ensure upload directory exists
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Move the uploaded file
            if (move_uploaded_file($image["tmp_name"], $targetFilePath)) {
                $image_url = $imageName;
            } else {
                throw new Exception("Error uploading file.");
            }
        }

        // Check if it's an ADD or UPDATE request
        if (isset($_POST['add'])) { // Add new product
            $data = [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock' => $stock,
                'image_url' => $image_url
            ];
            Connection::insert('products', $data);
        } elseif (!empty($_POST['id'])) { // Update existing product
            $id = $_POST['id'];

            // Retrieve existing image if no new image is uploaded
            if (!$image_url) {
                $product = Connection::getOne('products', ['id' => $id], ['select' => 'image_url']);
                $image_url = $product['image_url'] ?? '';
            }

            // Update product details
            $data = [
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock' => $stock,
                'image_url' => $image_url
            ];
            Connection::update('products', $data, ['id' => $id]);
        } else {
            throw new Exception("Invalid request.");
        }

        Connection::connect()->commit(); // Commit transaction
        header("Location: index.php?route=product");
        exit();
    } catch (Exception $e) {
        Connection::connect()->rollBack(); // Rollback on error
        die("Error: " . $e->getMessage());
    }
}

// Edit Product Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_FILES['image_url'];

    $image_url = "";  // New image path

    // Check if a new image is uploaded
    if (!empty($image['name'])) {
        $targetDir = "uploads/storages/images/";
        $imageName = time() . "_" . basename($image['name']);
        $targetFilePath = $targetDir . $imageName;

        // Move uploaded file to the destination
        if (move_uploaded_file($image["tmp_name"], $targetFilePath)) {
            $image_url = $imageName;
        } else {
            die("Error uploading file.");
        }
    }

    // Update SQL query
    $data = [
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'stock' => $stock
    ];

    if ($image_url) {
        $data['image_url'] = $image_url;
    }

    Connection::update('products', $data, ['id' => $id]);

    header("Location: index.php?route=product");
    exit();
}

// Delete Product Logic
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        Connection::delete('products', ['id = ?'], [$id]);
        header("Location: index.php?route=product&message=Product deleted successfully");
        exit();
    } catch (Exception $e) {
        die("Error deleting product: " . $e->getMessage());
    }
}

include "./layouts/head.php"; 
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>pros List</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addproModal">
            <i class="fas fa-plus-circle"></i> Add New pro
        </button>
    </div>
    <!-- Search Bar -->
    <div class="mb-3">
        <input type="text" id="productSearch" class="form-control" placeholder="Search Products">
    </div>


    <!-- Table Wrapper for Responsiveness -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>name</th>
                    <th>Description</th>

                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="6" class="text-center alert">
                        No pros available.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $pro): ?>
                <tr>
                    <td><?php echo $index++; ?></td>
                    <td><img src="uploads/storages/images/<?php echo $pro['image_url']; ?>" class="img-thumbnail"
                            style="max-height: 50px;"></td>
                    <td><?php echo htmlspecialchars($pro['name']); ?></td>
                    <td><?php echo htmlspecialchars(substr($pro['description'], 0, 100)); ?>...</td>

                    <td>$<?php echo htmlspecialchars($pro['price']); ?></td>
                    <td><?php echo htmlspecialchars($pro['stock']); ?></td>
                    <td>
                        <button class="btn btn-info btn-sm view-pro" data-bs-toggle="modal"
                            data-bs-target="#viewproModal" data-id="<?php echo $pro['id']; ?>"
                            data-name="<?php echo htmlspecialchars($pro['name']); ?>"
                            data-description="<?php echo htmlspecialchars($pro['description']); ?>"
                            data-price="<?php echo $pro['price']; ?>" data-stock="<?php echo $pro['stock']; ?>"
                            data-image="<?php echo $pro['image_url']; ?>" <i class="fas fa-eye"></i> View
                        </button>


                        <button class="btn btn-warning btn-sm edit-btn" data-bs-toggle="modal"
                            data-bs-target="#editproModal" data-id="<?php echo $pro['id']; ?>"
                            data-name="<?php echo htmlspecialchars($pro['name']); ?>"
                            data-description="<?php echo htmlspecialchars($pro['description']); ?>"
                            data-price="<?php echo $pro['price']; ?>" data-stock="<?php echo $pro['stock']; ?>"
                            data-image="<?php echo $pro['image_url']; ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>

                        <a href="index.php?route=product&id=<?php echo $pro['id']; ?>" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add pro Modal -->
    <div class="modal fade" id="addproModal" tabindex="-1" aria-labelledby="addproModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-name" id="addproModalLabel">Add New product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="add">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6 mb-3">
                                <label for="proname" class="form-label">product name</label>
                                <input type="text" name="name" id="proname" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="proPrice" class="form-label">Price</label>
                                <input type="number" name="price" id="proPrice" class="form-control" step="0.01"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Right Column -->
                            <div class="col-md-6 mb-3">
                                <label for="proDescription" class="form-label">Description</label>
                                <textarea name="description" id="proDescription" class="form-control" rows="4"
                                    required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="proStock" class="form-label">Stock</label>
                                <input type="number" name="stock" id="proStock" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Category and Image -->
                            <div class="col-md-6 mb-3">
                                <label for="proCategory" class="form-label">Category</label>
                                <select name="category" id="editproCategory" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>

                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="proImage" class="form-label">product Image</label>
                                <input type="file" name="image" id="proImage" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Save product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit pro Modal -->
    <div class="modal fade" id="editproModal" tabindex="-1" aria-labelledby="editproModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-name" id="editproModalLabel">Edit product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="edit">
                        <input type="hidden" name="id" id="editproId">

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editproname" class="form-label">product name</label>
                                    <input type="text" name="name" id="editproname" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editproPrice" class="form-label">Price</label>
                                    <input type="number" name="price" id="editproPrice" class="form-control" step="0.01"
                                        required>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editproDescription" class="form-label">Description</label>
                                    <textarea name="description" id="editproDescription" class="form-control" rows="4"
                                        required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="editproStock" class="form-label">Stock</label>
                                    <input type="number" name="stock" id="editproStock" class="form-control" required>
                                </div>
                            </div>
                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editproImage" class="form-label">product Image</label>
                                    <input type="file" name="image" id="editproImage" class="form-control"
                                        accept="image/*">
                                    <img src="" alt="pro Image" class="img-thumbnail mt-2" id="proImagePreview"
                                        style="max-width: 100%; max-height: 150px;">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="editproCategory" class="form-label">Category</label>
                            <select name="category" id="editproCategory" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <button type="submit" class="btn btn-warning w-100">Update product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- View pro Modal -->
    <div class="modal fade" id="viewproModal" tabindex="-1" aria-labelledby="viewproModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-name" id="viewproModalLabel">product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="proDetailsContent">
                        <!-- pro details will be dynamically injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>


<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function() {
            // Get pro data from button attributes
            let id = this.getAttribute("data-id");
            let name = this.getAttribute("data-name");
            let description = this.getAttribute("data-description");
            let category = this.getAttribute("data-category"); // Corrected typo
            let price = this.getAttribute("data-price");
            let stock = this.getAttribute("data-stock");
            let image = this.getAttribute("data-image");

            // Set modal input values
            document.getElementById("editproId").value = id;
            document.getElementById("editproname").value = name;
            document.getElementById("editproDescription").value = description;
            document.getElementById("editproPrice").value = price;
            document.getElementById("editproStock").value = stock;

            // Set category value in dropdown (assuming it's a select element)
            let categorySelect = document.getElementById("editproCategory");
            for (let option of categorySelect.options) {
                if (option.value === category) {
                    option.selected = true;
                    break;
                }
            }

            // Set image preview
            let imagePreview = document.getElementById("editproImagePreview");
            if (image) {
                imagePreview.src = "uploads/storages/images/" +
                    image;
            } else {
                imagePreview.src = "default-image.jpg";
            }
        });
    });

    // Live Preview of New Image Upload
    document.getElementById("editproImage").addEventListener("change", function(event) {
        let reader = new FileReader();
        reader.onload = function() {
            document.getElementById("editproImagePreview").src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    });
});

// Trigger View pro Modal with the appropriate data
document.querySelectorAll('.view-pro').forEach(button => {
    button.addEventListener('click', function() {
        // Get the data from the button attributes
        const proname = this.dataset.name;
        const proDescription = this.dataset.description;
        const proCategory = this.dataset.category;
        const proPrice = this.dataset.price;
        const proStock = this.dataset.stock;
        const proImage = this.dataset.image;

        // Inject the data into the modal
        const modalContent = `
            <h4 class="">${proname}</h4>
            <p><strong>Category:</strong> ${proCategory}</p>
            <p><strong>Price:</strong> $${proPrice}</p>
            <p><strong>Stock:</strong> ${proStock}</p>
            <p><strong>Description:</strong> ${proDescription}</p>
            <img src="uploads/storages/images/${proImage}" alt="${proname}" class="img-fluid">
        `;

        // Set the content inside the modal
        document.getElementById('proDetailsContent').innerHTML = modalContent;
    });
});


// Search functionality for products
document.getElementById('productSearch').addEventListener('keyup', function() {
    var searchTerm = this.value.toLowerCase();
    var rows = document.querySelectorAll('tbody tr');

    rows.forEach(function(row) {
        var titile = row.querySelector('td:nth-child(3)').textContent
            .toLowerCase();
        var description = row.querySelector('td:nth-child(4)').textContent
            .toLowerCase();

        if (name.includes(searchTerm) || description.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>


<?php include "./layouts/footer.php"; ?>