<!-- Book creation page -->
<?php include "views/includes/header.php" ?>

<section class="mb-4" id="BOOK_CREATE_PAGETITLE">
<div class="frontpage-bg position-relative py-5" style='background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url("<?php echo ROOT?>assets/picture/banner-create.jpg");'>
    <div class="container text-white">
        <h2 class="display-4 display-4-fluid">Submit New Book</h1>
        <p class="lead">For others to enjoy.</p>
    </div>
</div>
</section>

<section id="BOOK_CREATE_PANEL">
    <div class="container">
        <div class="d-flex flex-column w-100">
            <?php
            $rq_name =  isset($_GET['book_name']) ? $_GET['book_name'] : "";
            $rq_sub = isset($_GET['book_author']) ? $_GET['book_author'] : "";
            $rq_desc = isset($_GET['book_desc']) ? $_GET['book_desc'] : "";
            $rq_cat = isset($_GET['book_genre']) ? $_GET['book_genre'] : -1;

            if (isset($_GET["err"]) && strlen($_GET["err"]) > 0) {
                echo '<p class="text-danger">'.htmlspecialchars($_GET["err"]).'</p>';
            }
            ?>
            <a class="mb-4"  href="<?php Router::url("book/dashboard") ?>"><i class="fa fa-arrow-left mr-1"></i> Back to Bookshelf</a>
            <form id="creationForm" action="<?php echo ROOT;?>book/create" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <input id="qtitle" class="form-control" type="text" name="book_name" placeholder="Book Name" autocomplete="off" value="<?php echo htmlspecialchars($rq_name); ?>" maxlength="100" required>
                </div>
                <div class="form-group">
                    <input id="qsub" class="form-control" type="text" name="book_author" placeholder="Book Author" autocomplete="off" value="<?php echo htmlspecialchars($rq_sub); ?>" maxlength="100">
                </div>
                <div class="form-group">
                    <label for="qdesc"><i class="fa fa-pen"></i> Book Description</label>
                    <textarea id="qdesc" name="book_desc" class="web-resize-none w-100" rows="12" placeholder="" maxlength="5000"><?php echo htmlspecialchars($rq_desc); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="qbanner"><i class="fa fa-image"></i> Book Cover (Max 5MB)</label>
                    <input id="qbanner" type="file" name="book_banner" accept="image/png,image/jpeg">
                </div>
                <div class="form-group">
                    <label for="qgenre"><i class="fa fa-book"></i> Book Category</label>
                    <select id="qgenre" name="book_genre">
                        <option value=0 <?php if ($rq_cat > count(GlobalConfig::BOOK_GENRES) || $rq_cat < 0): ?>selected<?php endif;?>>Choose...</option>
                        <?php
                        {
                            $ind = 0;
                            foreach(GlobalConfig::BOOK_GENRES as $item){
                                if ($rq_cat == $ind) {
                                    echo "<option value='$ind' selected>$item</option>";    
                                } else echo "<option value='$ind'>$item</option>";
                                $ind++;
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="qmaxborrowdays"><i class="fa fa-calendar"></i> Maximum Reservation Days </label>
                    <select id="qmaxborrowdays" name="book_max_reserve_days">
                        <?php
                        {
                            $ind = 1;
                            for (; $ind <= GlobalConfig::MAX_RESERVE_DAYS; $ind++) {
                                if ($ind == GlobalConfig::MAX_RESERVE_DAYS) {
                                    echo "<option value='$ind' selected>$ind</option>";
                                } else echo "<option value='$ind'>$ind</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <p class="mb-3">After creation, you will be able to add more details about your book in your Bookshelf (Dashboard) and make it available for reservations. You can edit these settings later!</p>
                <?php CSRF::outputToken();?>
                <button type="submit" class="btn btn-outline-success btn-block btn-lg" name="book_authormit_create"><i class="fa fa-plus" aria-hidden="true"></i> Create!</button>
            </form>
        </div>
    </div>
</section>

<?php
// If user has modified the form, warn them about the unsaved changes.
$EXTRA_FOOTER_INCLUDES = '<script>$("#creationForm").dirty({preventLeaving: true});</script>';
include "views/includes/footer.php" ?>