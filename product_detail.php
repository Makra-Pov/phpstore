<?php

// Enable error reporting for debugging

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($product_id && $quantity) {
            $cartData = [
                'user_id' => 2,
                'product_id' => $product_id,
                'quantity' => $quantity
            ];

            try {
                $result = Connection::insert('cart', $cartData);
                error_log("Insert result: " . var_export($result, true));
                if ($result) {
                    $success_message = "Product added to cart successfully!";
                } else {
                    $error_message = "Failed to add product to cart";
                }
            } catch (PDOException $e) {
                $error_message = "Error adding to cart: " . $e->getMessage();
                error_log("Database error: " . $e->getMessage());
            }
        } else {
            $error_message = "Invalid product ID or quantity.";
        }
    
}

// Security and data fetching
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: 404.php");
    exit;
}

$product = Connection::getOne(
    "products",
    ["id = :id"],
    [":id" => (int)$_GET['id']]
);

if (!$product) {
    header("Location: 404.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Detail</title>
    <!-- Add your CSS and JS includes here -->
</head>

<body>
    <?php 
    if (isset($success_message)) {
        echo "<div class='success-message'>" . htmlspecialchars($success_message) . "</div>";
    }
    if (isset($error_message)) {
        echo "<div class='error-message'>" . htmlspecialchars($error_message) . "</div>";
    }
    ?>

    <!-- Breadcrumb -->
    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="index.html" class="stext-109 cl8 hov-cl1 trans-04">
                Home
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>
            <a href="product.html" class="stext-109 cl8 hov-cl1 trans-04">
                <?php echo htmlspecialchars("Product detail") ?>
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>
            <span class="stext-109 cl4">
                <?php echo htmlspecialchars($product['name']); ?>
            </span>
        </div>
    </div>

    <!-- Product Detail -->
    <section class="sec-product-detail bg0 p-t-65 p-b-60">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-7 p-b-30">
                    <div class="p-l-25 p-r-30 p-lr-0-lg">
                        <div class="wrap-slick3 flex-sb flex-w">
                            <div class="wrap-slick3-dots"></div>
                            <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>
                            <div class="slick3 gallery-lb">
                                <?php
                                $images = [
                                    $product['image_url'] ?? '',
                                    $product['image_url'] ?? '',
                                    $product['image_url'] ?? ''
                                ];
                                foreach ($images as $image):
                                    if (!empty($image)):
                                ?>
                                <div class="item-slick3" data-thumb="<?php echo htmlspecialchars($image); ?>">
                                    <div class="wrap-pic-w pos-relative">
                                        <img src="<?php echo htmlspecialchars($image); ?>" alt="IMG-PRODUCT">
                                        <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                            href="<?php echo htmlspecialchars($image); ?>">
                                            <i class="fa fa-expand"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-5 p-b-30">
                    <div class="p-r-50 p-t-5 p-lr-0-lg">
                        <h4 class="mtext-105 cl2 js-name-detail p-b-14">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h4>
                        <span class="mtext-106 cl2">
                            $<?php echo number_format($product['price'], 2); ?>
                        </span>
                        <p class="stext-102 cl3 p-t-23">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>

                        <div class="p-t-33">
                            <div class="flex-w flex-r-m p-b-10">
                                <div class="size-203 flex-c-m respon6">Size</div>
                                <div class="size-204 respon6-next">
                                    <div class="rs1-select2 bor8 bg0">
                                        <select class="js-select2" name="size">
                                            <option>Choose an option</option>
                                            <?php
                                            $sizes = !empty($product['sizes']) ? explode(',', $product['sizes']) : ['S','M','L','XL'];
                                            foreach ($sizes as $size):
                                            ?>
                                            <option><?php echo htmlspecialchars(trim($size)); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="dropDownSelect2"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-w flex-r-m p-b-10">
                                <div class="size-203 flex-c-m respon6">Color</div>
                                <div class="size-204 respon6-next">
                                    <div class="rs1-select2 bor8 bg0">
                                        <select class="js-select2" name="color">
                                            <option>Choose an option</option>
                                            <?php
                                            $colors = !empty($product['colors']) ? explode(',', $product['colors']) : ['Red','Blue','White','Grey'];
                                            foreach ($colors as $color):
                                            ?>
                                            <option><?php echo htmlspecialchars(trim($color)); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="dropDownSelect2"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-w flex-r-m p-b-10">
                                <form method="POST" action="">
                                    <div class="size-204 flex-w flex-m respon6-next">
                                        <div class="wrap-num-product flex-w m-r-20 m-tb-10">
                                            <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-minus"></i>
                                            </div>
                                            <input class="mtext-104 cl3 txt-center num-product" type="number"
                                                name="quantity" value="1" min="1">
                                            <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
                                                <i class="fs-16 zmdi zmdi-plus"></i>
                                            </div>
                                        </div>
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="add_to_cart" value="1">
                                        <input type="hidden" name="csrf_token"
                                            value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <button type="submit"
                                            class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04 js-addcart-detail">
                                            Add to cart
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="flex-w flex-m p-l-100 p-t-40 respon7">
                            <div class="flex-m bor9 p-r-10 m-r-11">
                                <a href="#"
                                    class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 js-addwish-detail tooltip100"
                                    data-tooltip="Add to Wishlist">
                                    <i class="zmdi zmdi-favorite"></i>
                                </a>
                            </div>
                            <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                data-tooltip="Facebook">
                                <i class="fa fa-facebook"></i>
                            </a>
                            <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                data-tooltip="Twitter">
                                <i class="fa fa-twitter"></i>
                            </a>
                            <a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 m-r-8 tooltip100"
                                data-tooltip="Google Plus">
                                <i class="fa fa-google-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bor10 m-t-50 p-t-43 p-b-40">
                <div class="tab01">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item p-b-10">
                            <a class="nav-link active" data-toggle="tab" href="#description" role="tab">Description</a>
                        </li>
                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#information" role="tab">Additional
                                information</a>
                        </li>
                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#reviews" role="tab">Reviews (1)</a>
                        </li>
                    </ul>

                    <div class="tab-content p-t-43">
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <div class="how-pos2 p-lr-15-md">
                                <p class="stext-102 cl6">
                                    <?php echo htmlspecialchars($product['description']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="information" role="tabpanel">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <ul class="p-lr-28 p-lr-15-sm">
                                        <?php if (!empty($product['weight'])): ?>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">Weight</span>
                                            <span
                                                class="stext-102 cl6 size-206"><?php echo htmlspecialchars($product['weight']); ?>
                                                kg</span>
                                        </li>
                                        <?php endif; ?>
                                        <?php if (!empty($product['dimensions'])): ?>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">Dimensions</span>
                                            <span
                                                class="stext-102 cl6 size-206"><?php echo htmlspecialchars($product['dimensions']); ?></span>
                                        </li>
                                        <?php endif; ?>
                                        <?php if (!empty($product['materials'])): ?>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">Materials</span>
                                            <span
                                                class="stext-102 cl6 size-206"><?php echo htmlspecialchars($product['materials']); ?></span>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Reviews tab remains static -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
<?php ob_end_flush(); ?>