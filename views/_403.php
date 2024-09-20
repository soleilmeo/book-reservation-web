<?php
$HEADER_TRANSPARENT = true;
include "views/includes/header.php";
?>

<div class="position-fixed vw-100 vh-100" style="background-image: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.75)), url(<?php Router::url("assets/picture/banner-error.jpg") ?>); background-size: cover; background-position: center; background-repeat: no-repeat; top: 0; z-index:-1;"></div>
<div class="jumbotron bg-transparent text-white text-center">
    <div class="container">
        <?php if (mt_rand(0, 100) === 38): ?>
        <img src="<?php Router::url("assets/ui/kururin-kuru-kuru.gif") ?>" class="rounded-pill shadow no-interact mb-4" style="width: 250px;"></img>
        <?php endif; ?>
        <h1 class="display-2 font-weight-bold">403</h1>
        <p class="lead">Forbidden</p>
        <hr class="my-4">
        <div><a class="btn btn-outline-light mb-2" onclick="history.back()"><i class="fa fa-arrow-left mr-1"></i> Go back</a></div>
        <a class="btn btn-outline-light" href="<?php Router::url() ?>"><i class="fa fa-home mr-1"></i> Go home</a>
    </div>
</div>

<?php
$FOOTER_HIDDEN = true;
include "views/includes/footer.php";
http_response_code(403);
?>