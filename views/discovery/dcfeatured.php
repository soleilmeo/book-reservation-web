<!-- Don't use this -->

<div id="featuredBooks" class="carousel slide rounded overflow-hidden mt-4 shadow" data-ride="carousel">
    <ol class="carousel-indicators">
        <?php
        for ($i = 0; $i < count($featuredPosts); $i++) {
            $featured = $featuredPosts[$i];
        ?>
            <li data-target="#featuredBooks" data-slide-to="<?php echo $i; ?>" class="<?php if ($i === 0) echo "active"; ?>"></li>
        <?php
        }
        ?>
    </ol>
    <div class="carousel-inner w-100 h-100">
        <?php
        for ($i = 0; $i < count($featuredPosts); $i++) {
            $featured = $featuredPosts[$i];
        ?>
            <div class="carousel-item w-100 h-100 overflow-hidden <?php if ($i === 0) echo "active"; ?>">
                <a href="<?php echo ROOT . "book/" . $featured["book_read_id"] ?>" class="d-block w-100 h-100" style="background-image:linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.5)),url(<?php Router::url($featured["book_banner_img"]) ?>);background-size:cover;background-position:center;" data-holder-rendered="true"></a>
                <div class="carousel-caption d-none d-block no-interact">
                    <h4 class="display-4 display-4-fluid font-weight-bold mb-1"><?php echo $featured["book_name"] ?></h4>
                    <p class="lead text-truncate"><?php echo Placeholder::put($featured["book_author"], "") ?></p>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
    <a class="carousel-control-prev" href="#featuredBooks" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#featuredBooks" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>