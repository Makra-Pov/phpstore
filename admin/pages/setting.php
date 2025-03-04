<?php
require "./configs/dbconfig.php";  // Database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$user_id = $_SESSION['user_id']; // Get user ID from session

// Fetch the user's current details from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If the user does not exist in the database
if (!$user) {
    die("Error: User not found.");
}

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    $username = $_POST['name'];
    $email = $_POST['email'];

    // Validate inputs
    if (empty($username) || empty($email)) {
        $error = "Username and Email cannot be empty!";
    } else {
        // Update user profile in the database
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $user_id]);

        // Update session data if email or username is changed
        $_SESSION['name'] = $username;

        $success = "Profile updated successfully!";
    }
}

// Handle Password Change
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All password fields are required!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password and confirmation do not match!";
    } else {
        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect!";
        } else {
            // Hash new password and update
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);

            $success = "Password updated successfully!";
        }
    }
}

$page_title = "Settings";
include "./layouts/head.php";

?>

<div class="container mt-5">
    <h1>Settings</h1>

    <!-- Show success or error message -->
    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Display current profile details -->
    <div class="mb-3">
        <strong>Username:</strong> <?= htmlspecialchars($user['name']) ?>
    </div>
    <div class="mb-3">
        <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?>
    </div>

    <!-- Button to trigger modal for updating profile -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
        Edit Profile
    </button>

    <!-- Button to trigger modal for changing password -->
    <button type="button" class="btn btn-warning mt-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
        Change Password
    </button>

    <!-- Modal for updating profile -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Profile Update Form inside the modal -->
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Username</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary" name="update_profile">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for changing password -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Password Change Form inside the modal -->
                    <form method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                required>
                        </div>

                        <button type="submit" class="btn btn-warning" name="update_password">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout button -->
    <a href="index.php?route=logout" class="btn btn-danger mt-3">Logout</a>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<?php include "./layouts/footer.php"; ?>