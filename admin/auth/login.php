<?php
// Check if a session is already active before starting a new one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require './configs/connection.php'; // Ensure the class file is included

// Generate CSRF Token if it doesn't exist in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid CSRF token.";
        header("Location: index.php?route=login");
        exit();
    }

    // Retrieve and sanitize user inputs
    $username_or_email = trim($_POST['name']); // Use the correct field name
    $password = $_POST['password'];

    // Define search conditions
    if (filter_var($username_or_email, FILTER_VALIDATE_EMAIL)) {
        $conditions = ['email = :email'];
        $params = ['email' => $username_or_email];
    } else {
        $conditions = ['username = :username'];
        $params = ['username' => $username_or_email];
    }

    // Fetch user using the Connection class method
    $user = Connection::getOne('users', $conditions, $params); 
    
    
    // Check credentials
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_email'] = $user['email']; // Store email as well if needed

        // Redirect to dashboard
        header("Location: index.php?route=dashboard");
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or password! {$user['username']}:{$user['password']}:  $username_or_email : $password";
        header("Location: index.php?route=login");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Admin Panel - Login</title>

    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="https://demos.creative-tim.com/soft-ui-dashboard/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/soft-ui-dashboard/assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- CSS Files -->
    <link id="pagestyle" href="./assets/css/soft-ui-dashboard.css?v=1.1.0" rel="stylesheet" />
</head>

<body>
    <div class="container">
        <div class="row min-vh-100 justify-content-center align-items-center">
            <div class="col-md-4">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Login</h3>

                        <!-- Show error message if login fails -->
                        <?php if (isset($_SESSION['error'])) { ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                        <?php } ?>

                        <form method="POST" action="">
                            <!-- CSRF Token -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <!-- Username field -->
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>

                            <!-- Password field -->
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                            <p class="text-center mt-3">Don't have an account? <a
                                    href="index.php?route=register">Register</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>