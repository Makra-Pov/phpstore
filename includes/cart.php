<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_from_cart') {
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    if ($productId <= 0) {
        error_log("Invalid product ID: $productId");
        echo "<p style='color:red;'>Invalid product ID.</p>";
        exit;
    }

 
        $deleted = Connection::delete('cart', ['product_id = ?'], [$productId]);
        
      
}

// Fetch and render cart
$cartItems = Connection::getAll('cart');
if (!is_array($cartItems)) {
    error_log("Error fetching cart items: Data is not an array.");
    $cartItems = [];
}

$productIds = array_column($cartItems, 'product_id');
$products = [];
if (!empty($productIds)) {
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $conditions = ["id IN ($placeholders)"];
    $products = Connection::getAll('products', $conditions, $productIds);
}

$productsById = [];
$total = 0;
foreach ($products as $product) {
    $productsById[$product['id']] = $product;
}
?>

<div class="wrap-header-cart js-panel-cart">
    <div class="s-full js-hide-cart"></div>
    <div class="header-cart flex-col-l p-l-65 p-r-25">
        <div class="header-cart-title flex-w flex-sb-m p-b-8">
            <span class="mtext-103 cl2">Your Cart</span>
            <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>
        <div class="header-cart-content flex-w js-pscroll">
            <ul class="header-cart-wrapitem w-full">
                <?php if (!empty($cartItems)): ?>
                <?php foreach ($cartItems as $item): 
                        $product = $productsById[$item['product_id']] ?? null;
                        if (!$product) continue;
                        $itemTotal = $product['price'] * $item['quantity'];
                        $total += $itemTotal;
                    ?>
                <li class="header-cart-item flex-w flex-t m-b-12">
                    <div class="header-cart-item-img">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="IMG">
                    </div>
                    <div class="header-cart-item-txt p-t-8">
                        <a href="#" class="header-cart-item-name m-b-18 hov-cl1 trans-04">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </a>
                        <span class="header-cart-item-info">
                            <?php echo $item['quantity']; ?> x $<?php echo number_format($product['price'], 2); ?>
                        </span>
                        <form method="POST">
                            <input type="hidden" name="action" value="remove_from_cart">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn-remove">Remove</button>
                        </form>
                    </div>
                </li>
                <?php endforeach; ?>
                <?php else: ?>
                <li class="header-cart-item flex-w flex-t m-b-12">
                    <div class="header-cart-item-txt p-t-8 w-full">
                        <span class="header-cart-item-info">Your cart is empty</span>
                    </div>
                </li>
                <?php endif; ?>
            </ul>
            <div class="w-full">
                <div class="header-cart-total w-full p-tb-40">
                    Total: $<?php echo number_format($total, 2); ?>
                </div>
                <div class="header-cart-buttons flex-w w-full">
                    <a href="index.php?p=cart_page"
                        class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                        View Cart
                    </a>
                    <a href="index.php?p=checkout"
                        class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                        Check Out
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>