<?php
$slideShow = true;
$page = "home.php";
$currentPage = "home"; // Default value

if (isset($_GET['p'])) {
    $p = $_GET['p'];
    switch ($p) {
        case'cart_page':
            $page = 'shop.php';
            $currentPage = 'shop.php'; // Set the current page identifier
            $slideShow = false;
            break;
        case 'shop':
            $page = 'products.php';
            $currentPage = 'shop'; // Set the current page identifier
            $slideShow = false;
            break;
        case 'contact':
            $page = 'contact.php';
            $currentPage = 'contact'; // Set the current page identifier
            $slideShow = false;
            break;
        case'product_detail':
            $page = 'product_detail.php';
            $currentPage = 'product_detail'; // Set the current page identifier
            $slideShow = false;
            break;
        
        case'checkout':
            $page = 'checkout.php';
            $currentPage = 'checkout'; // Set the current page identifier
            $slideShow = false;
            break;
        default:
            $page = 'home.php';
            $currentPage = 'home'; // Set the current page identifier
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'includes/lib/connection.php'; ?>
<?php include 'includes/head.php'; ?>


<body class="animsition">
    <?php include 'includes/header.php'; ?>
    <!-- Cart -->
    <?php include 'includes/cart.php'; ?>

    <!-- Slider -->
    <?php if ($slideShow) include 'includes/slidebar.php'; ?>

    <?php include $page; ?>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/foot.php'; ?>
</body>

</html>