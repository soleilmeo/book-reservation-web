<!-- Main page -->
<?php include "views/includes/header.php" ?>

<div class="frontpage-bg position-relative py-5 mb-5" style='background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("<?php echo ROOT?>assets/picture/placeholderbg.jpg");'>
    <div class="container text-white">
        <h2 class="display-2 display-2-fluid">Welcome to Libraria!</h1>
        <p class="lead">A book reservation platform that nobody uses</p>
    </div>
</div>

<section id="CTA">
    <div class="container d-flex flex-column align-items-center">
        <?php if (Auth::loggedIn()): ?>
        <h2 class="lead web-big-lead">What would you like to do today?</h2>
        <div class="row w-100 mt-3">
            <div class="d-flex col-md-6 justify-content-center mb-2 mb-md-0">
                <a href="#discover" class="btn btn-primary px-4 py-2 w-100"><i class="fa fa-plane"></i> Explore</a>
            </div>
            <div class="d-flex col-md-6 justify-content-center mb-2 mb-md-0">
                <a href="<?php Router::url("book/create") ?>" class="btn btn-success px-4 py-2 w-100"><i class="fa fa-plus"></i> New Submission</a>
            </div>
        </div>
        <?php else: ?>
        <h2 class="lead web-big-lead">A public library for the community. Start reserving your first book today.</h2>
        <div class="row w-100 mt-3">
            <div class="d-flex col-md-6 justify-content-center mb-2 mb-md-0">
                <a href="<?php Router::url("login") ?>" class="btn btn-primary px-4 py-2 w-100"><i class="fa fa-hand-pointer"></i> Join Us</a>
            </div>
            <div class="d-flex col-md-6 justify-content-center">
                <a href="#discover" class="btn btn-success px-4 py-2 mx-auto w-100"><i class="fa fa-plane"></i> Explore</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<section id="BOOKLIST">

<div class="container mb-5">
    <?php if($discoveryCarousel->hasFeaturedPosts()): ?>
    <h1 class="h1-fluid"><i class="fa fa-star"></i> Featured Books</h1>
    <div class="row mt-4">    
    
        <?php $discoveryCarousel->displayFeatured(); ?>

    </div>
    <?php endif; ?>
</div>

<div id="discover" class="container">
    <h1 class="h1-fluid"><i class="fa fa-search"></i> Discover Books</h1>
    <div class="row mt-4">
        <?php 
        $discoveryCarousel->displayDiscovery()
        ?>
        
    </div>
</div>

</section>

<?php include "views/includes/footer.php" ?>