<div class="col-xl-12 col-lg-3 col-sm-6 bookcard-container-mini">
    <div class="bookcard d-flex flex-column justify-content-end border rounded-lg shadow mx-0 px-4 py-2 text-white" style="background-image:linear-gradient(rgba(0, 0, 0, 0), 60%, rgba(0, 0, 0, 0.7)),url(<?php Router::url($book["book_banner_img"])?>);">
    <a href="<?php echo ROOT."book/".$book["book_read_id"] ?>" class="position-absolute w-100 h-100" style="z-index:0;"></a>
    <h4 class="no-interact"><?php echo $book["book_name"] ?></h4>
    <p class="no-interact">by <?php echo Placeholder::put($book["book_author"], "") ?></p>
    <p style="z-index:1;">submitted by <a href="<?php echo ROOT."user/".$book["user_id"]?>"><?php echo '@'.$book["username"] ?></a></p>
    </div>
</div>