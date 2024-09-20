<div class="col-lg-4 my-2 bookcard-container">
    <div class="bookcard d-flex flex-column justify-content-end border rounded-lg shadow mx-1 p-4 text-white" style="background-image:linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.8)),url(<?php Router::url($book["book_banner_img"])?>)">
        <a href="<?php echo ROOT."book/".$book["book_read_id"] ?>" class="position-absolute w-100 h-100" style="z-index:0;"></a>
        <h3 style="pointer-events:none;user-select:none;"><?php echo $book["book_name"] ?></h3>
        <p style="pointer-events:none;user-select:none;">by <?php echo Placeholder::put($book["book_author"], "") ?></p>
        <p style="z-index:1;">submitted by <a href="<?php echo ROOT."user/".$book["user_id"]?>"><?php echo '@'.$book["username"] ?></a></p>
    </div>
</div>