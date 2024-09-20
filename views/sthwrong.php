<?php include "views/includes/header.php" ?>

<div class="frontpage-bg position-relative py-5 mb-5" style='background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url("<?php echo ROOT?>assets/picture/placeholderbg.jpg");'>
    <div class="container text-white">
        <h2 class="display-2 display-2-fluid">Something went wrong.</h1>
        <p class="lead">Please try again later.</p>
    </div>
</div>

<div class="container">
    <div class="d-flex justify-content-center align-content-center">
        <?php if(isset($_SERVER["HTTP_REFERER"])): ?>
        <a href="<?php echo $_SERVER["HTTP_REFERER"];?>" class="btn btn-primary btn-lg"><i class="fa fa-arrow-left mr-1"></i> Go back</a>
        <?php endif; ?>
    </div>
</div>

<?php include "views/includes/footer.php" ?>