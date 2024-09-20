<?php
$PAGE_TITLE = "Your Bookshelf - ".GlobalConfig::BASE_WEB_TITLE;
include "views/includes/header.php"
?>

<div class="frontpage-bg position-relative py-5 mb-5" style='background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("<?php echo ROOT?>assets/picture/banner-dashboard.png");'>
    <div class="container text-white">
        <h2 class="display-4 display-4-fluid">Your Bookshelf</h1>
    </div>
</div>

<div class="container mt-3">
    <h3><i class="fa fa-tags"></i> Your Reserved Books</h3>
    
    <div class="row">

        <?php
        $__self->getAllReservationsOfUser()
        ?>
        
    </div>
</div>

<div class="container mt-3">
    <h3><i class="fa fa-book"></i> Your Submissions</h3>

    <div class="row">

        <div class="col-lg-4 my-2 bookcard-container">
            <div class="bookcard d-flex flex-column justify-content-center align-items-center rounded-lg mx-1 p-4 text-black web-reverse-transparent-hover" style="border-color:rgba(0,0,0); border-width:4px; border-style:dashed;">
                <a href="<?php Router::url("book/create") ?>" class="position-absolute w-100 h-100" style="z-index:0;"></a>
                <h2 class="no-interact"><i class="fa fa-plus fa-2x"></i></h2>
                <h3 class="no-interact" style="z-index:1;">New Book</h3>
            </div>
        </div>

        <?php
        $__self->getDashboardBooks();
        ?>

    </div>
</div>

<?php include "views/includes/footer.php" ?>