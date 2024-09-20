<?php if ($hasBook): ?>

    <div class="col-lg-3 my-2 bookcard-container">
        <div class="bookcard d-flex flex-column justify-content-end border rounded-lg shadow mx-1 p-4 text-white" style="background-image:linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.8)),url(<?php Router::url($book["book_banner_img"])?>)">
            <a href="<?php echo ROOT."book/".$book["book_read_id"] ?>" class="position-absolute w-100 h-100" style="z-index:0;"></a>
            <h4 style="pointer-events:none;user-select:none;"><?php echo $book["book_name"] ?></h4>
            <p style="pointer-events:none;user-select:none;">by <?php echo Placeholder::put($book["book_author"], "") ?></p>
        </div>
    </div>

<?php else: ?>

    <div class="col-lg-12 my-2 text-secondary text-center p-5">
        <?php if ($userid == (new Session())->getSessionUserId()): ?>
            <p>You have not published any books yet.</p><a class="btn btn-success" href="<?php Router::url("book/dashboard") ?>"><i class="fa fa-pen"></i> Go to Bookshelf</a>
        <?php else: ?>
            This user has not published any books yet.
        <?php endif; ?>
    </div>

<?php endif; ?>