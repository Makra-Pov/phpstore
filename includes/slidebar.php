<?php
$slideShow = Connection::getAll('slideshow');
?>
<section class="section-slide">
    <div class="wrap-slick1">
        <div class="slick1">
            <?php foreach ($slideShow as $slide): ?>
            <div class="item-slick1"
                style="background-image: url(<?php echo htmlspecialchars($slide['image_url']); ?>);">
                <div class="container h-full">
                    <div class="flex-col-l-m h-full p-t-100 p-b-30 respon5">
                        <div class="layer-slick1 animated visible-false" data-appear="fadeInDown" data-delay="0">
                            <span class="ltext-101 cl2 respon2">
                                <?php echo htmlspecialchars($slide['title']); ?>
                            </span>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="fadeInUp" data-delay="800">
                            <h2 class="ltext-201 cl2 p-t-19 p-b-43 respon1">
                                <?php echo htmlspecialchars($slide['description']); ?>
                            </h2>
                        </div>

                        <div class="layer-slick1 animated visible-false" data-appear="zoomIn" data-delay="1600">
                            <a href="<?php echo htmlspecialchars($slide['link'] ?? 'index.php?p=shop'); ?>"
                                class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04">
                                Shop Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>