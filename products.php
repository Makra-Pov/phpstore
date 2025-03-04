<?php
$allProduct = Connection::getAll('products');
?>

<!-- Product -->
<section class="bg0 p-t-23 p-b-140">
    <div class="container">
        <div class="p-b-10">
            <h3 class="ltext-103 cl5">
                Product Overview
            </h3>
        </div>

        <div class="flex-w flex-sb-m p-b-52">
            <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1" data-filter="*">
                    All Products
                </button>
            </div>
        </div>

        <div class="row isotope-grid">
            <?php foreach ($allProduct as $product): ?>
            <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item women">
                <div class="block2">
                    <div class="block2-pic hov-img0">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid">
                        <a href="index.php?p=product_detail&id=<?= $product['id'] ?>"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 ">
                            Quick View
                        </a>
                    </div>

                    <div class="block2-txt flex-w flex-t p-t-14">
                        <div class="block2-txt-child1 flex-col-l ">
                            <a href="product-detail.php?id=<?= $product['id'] ?>"
                                class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>

                            <span class="stext-105 cl3">
                                $<?= number_format($product['price'], 2) ?>
                            </span>
                        </div>

                        <div class="block2-txt-child2 flex-r p-t-3">
                            <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
                                <img class="icon-heart1 dis-block trans-04" src="images/icons/icon-heart-01.png"
                                    alt="Add to Wishlist">
                                <img class="icon-heart2 dis-block trans-04 ab-t-l" src="images/icons/icon-heart-02.png"
                                    alt="Added to Wishlist">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>



        </div>
        <div class="flex-c-m flex-w w-full p-t-45">
            <a href="#" class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04">
                Load More
            </a>
        </div>
    </div>
</section>