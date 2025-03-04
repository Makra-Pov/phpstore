<?php 

session_start();

// Ensure user is logged in
// Allow login page without checking session
if (!isset($_SESSION['user_name']) && $_GET['route'] !== 'login' && $_GET['route'] !== 'register') {
    header("Location: index.php?route=login");
    exit();
}

function route($path) {
    switch ($path) {
        case 'dashboard':
            include 'pages/dashboard.php';
            break;
        case 'product':
            include 'pages/product.php';
            break;
        case 'category':
            include 'pages/category.php';
            break;

        case 'booksReview':
            include 'pages/booksReview.php';
            break;
        case 'orders':
            include 'pages/orders.php';
            break;
        case 'users':
            include 'pages/users.php';
            break;
       
        case 'setting':
            include 'pages/setting.php';
            break;
        case 'login':
            include 'auth/login.php';
            break;
        case 'register':
            include 'auth/register.php';
            break;
        case 'logout':
            include 'auth/logout.php';
            break;
        default:
            include 'pages/404.php';
            break;
    }
}


// Get the route from URL or default to 'dashboard'
$path = isset($_GET['route']) ? $_GET['route'] : 'dashboard';

// Call the route function
route($path);
?>