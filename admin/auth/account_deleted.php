<?php
// Start the session
session_start();

// If the user is still logged in, log them out first
if (isset($_SESSION['user_name'])) {
    session_unset();
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deleted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container text-center mt-5">
        <h1 class="text-danger">Account Deleted</h1>
        <p>Your account has been deleted. If you believe this is a mistake, please contact support.</p>
        <a href="login.php" class="btn btn-primary">Go to Login Page</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>