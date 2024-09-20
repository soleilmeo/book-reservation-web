<?php include "views/includes/header.php" ?>

<div class="frontpage-bg position-relative py-5 mb-5" style='height: min(65vh, 350px) !important;background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url("<?php echo ROOT?>assets/picture/banner-deleted.png");'>
    <div class="container text-white">
        <h2 class="mt-3 display-2 display-2-fluid shadow">Your Book has been deleted.</h1>
        <p class="lead shadow">You're welcomed to share another book for us.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="d-flex flex-column justify-content-center align-content-center web-default-bottom-margin">
        <a href="<?php Router::url("book/create")?>" class="btn btn-success btn-lg"><i class="fa fa-plus mr-1"></i> Submit new Book</a>
        <a href="<?php Router::url("")?>" class="btn btn-primary btn-lg"><i class="fa fa-home mr-1"></i> Back to Homepage</a>
    </div>
</div>

<?php include "views/includes/footer.php" ?>