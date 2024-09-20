<?php 
if (!Auth::loggedIn()) {
    Router::redirect("login");
    return;
} // Registered users only

if ($isBookPublished || $isBookOwner) {
    $PAGE_TITLE = "Edit - ".$viewingBookInfo['book_name']." - ".GlobalConfig::BASE_WEB_TITLE;
 }
 include "views/includes/header.php";
?>
<!--<link rel="stylesheet" href= "<?php Router::url(); ?>assets/css/editor.css">-->
<script>
    const __BOOK_ID = "<?php echo $viewingBookInfo["book_read_id"] ?>";
</script>

<section class="mb-4" id="BOOK_EDIT_PAGETITLE">
    <div class="bookpage-editor-bg mb-3" style="background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('<?php Router::url($viewingBookInfo["book_banner_img"]); ?>');">
        <div class="container text-white" style="padding:90px 10px;text-shadow: 0 0 8px #000000;">
            <h4 class="display-4 display-4-fluid font-weight-bold">Edit your book</h4>
            <p class="lead">Add more details to your book for it to be ready for reservations.</p>
        </div>
    </div>
</section>

<div class="container mb-5" id="BOOK_EDITOR">
    <div class="d-flex flex-column w-100">
        <a href="<?php Router::url("book/".$viewingBookInfo["book_read_id"]) ?>"><i class="fa fa-arrow-left mr-1"></i> Back to Book page</a>
        <p id="qerror-displayer" class="text-danger"></p>
        <form id="bookeditor-master-form" action="<?php echo ROOT;?>book/edit" method="post" enctype="multipart/form-data">
        <input type="hidden" name="book_id" value="<?php echo $viewingBookInfo["book_read_id"] ?>" ?>
            <div class="form-group">
                <label for="qtitle"><i class="fa fa-pen"></i> Book Title<span class="text-danger">*</span></label>
                <input id="qtitle" class="form-control" type="text" name="book_name" placeholder="Book Name" autocomplete="off" value="<?php echo $viewingBookInfo["book_name"] ?>"  maxlength="100" required>
            </div>
            <div class="form-group">
                <input id="qsub" class="form-control" type="text" name="book_author" placeholder="Book Author" autocomplete="off" value="<?php echo $viewingBookInfo["book_author"] ?>"  maxlength="100">
            </div>

            <div class="form-group">
                <label for="qdesc"><i class="fa fa-pen"></i> Book Description</label>
                <textarea id="qdesc" name="book_desc" class="web-resize-none w-100" rows="12" placeholder="" maxlength="5000"><?php echo $viewingBookInfo["book_desc"] ?></textarea>
            </div>

            <hr>

            <div class="form-group" hidden>
                <label for="qbanner"><i class="fa fa-image"></i> Book Cover (Max 5MB)</label>
                <input id="qbanner" type="file" name="book_banner" id="fileupload" disabled>
            </div>
            
            <div class="form-group">
                <label for="qgenre"><i class="fa fa-book"></i> Book Category</label>
                <select id="qgenre" name="book_genre">
                    <?php
                    {
                        $ind = 0;
                        foreach(GlobalConfig::BOOK_GENRES as $item){
                            if ($viewingBookInfo["book_genre"] == $ind) {
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
                        $ind_compare_to = $viewingBookInfo["book_reserve_days_limit"];
                        if ($ind_compare_to <= 0) {
                            $ind_compare_to = GlobalConfig::MAX_RESERVE_DAYS;
                        }
                        for (; $ind <= GlobalConfig::MAX_RESERVE_DAYS; $ind++) {
                            if ($ind == $ind_compare_to) {
                                echo "<option value='$ind' selected>$ind</option>";
                            } else echo "<option value='$ind'>$ind</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="qpublicity"><i class="fa fa-eye"></i> Publicity Option</label>
                <select id="qpublicity" name="book_publicity">
                    <option value=0 <?php if(!$isBookPublished): ?>selected<?php endif; ?>>Private</option>
                    <option value=1 <?php if($isBookPublished): ?>selected<?php endif; ?>>Public</option>
                </select>
            </div>

            <?php CSRF::outputToken();?>
            <button id="qsave_btn" type="button" class="btn btn-primary btn-block btn-lg" name="book_save"><i class="fa fa-save mr-1" aria-hidden="true"></i> Save</button>
            <p class="text-center mb-2 mt-2">or</p>
            <button type="button" class="btn btn-danger btn-block btn-lg" name="book_delete" data-toggle="modal" data-target="#book-delete-modal" data-book-id="<?php echo $viewingBookInfo["book_read_id"] ?>"><i class="fa fa-trash mr-1" aria-hidden="true"></i> Delete Book</button>
        </form>
    </div>

</div>

<div class="modal fade" id="book-delete-modal" tabindex="-1" role="dialog" aria-labelledby="book-delete-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="book-delete-title"><i class="fa fa-trash"></i> Confirm Removal</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            You are about to delete: <b>"<?php echo $viewingBookInfo["book_name"] ?>"</b>.<br><br>This will remove your book from the website. All pending reservations will be instantly cancelled. This action is FINAL and cannot be undone!<br><br>Are you sure?
            <br><br><i>If you want your book to not be visible to others, you can instead set your Publicity Option to Private.</i>
            </div>
            <div class="modal-footer">
                <form id="bookdeletion" action="<?php echo ROOT;?>book/delete" method="post" enctype="multipart/form-data">
                    <input id="book-delete-confirm-target" name="target_book_id" type="hidden" value="" hidden>
                    <button id="btn-confirm-book-delete" type="submit" class="btn btn-danger">Delete</button>
                    <button id="btn-unconfirm-book-delete" type="button" class="btn btn-primary" data-dismiss="modal">No, do not delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (count($_reserve_queue) > 0): ?>

    <div class="modal fade" id="book-save-modal" tabindex="-1" role="dialog" aria-labelledby="book-save-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="book-save-title"><i class="fa fa-save"></i> Save Warning</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                You are about change <b>"<?php echo $viewingBookInfo["book_name"] ?>"</b>'s vibililty to Private.
                <br><br>Your book currently has <?php echo count($_reserve_queue) ?> ongoing reservation<?php echo (count($_reserve_queue) > 1 ? "s" : "") ?>.
                <br><br>Saving with <b>Publicity</b> set to <b>Private</b> will remove all current reservations made by users. This action is irreversible. Are you sure?
                <br><br><i>Make sure that the book is still within your possession before proceeding with this action.</i>
                </div>
                <div class="modal-footer">
                        <button id="btn-confirm-book-save" class="btn btn-danger">Save</button>
                        <button id="btn-unconfirm-book-save" type="button" class="btn btn-primary" data-dismiss="modal">No, don't save yet</button>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php
// If user has modified the form, warn them about the unsaved changes.
$EXTRA_FOOTER_INCLUDES = '<script src="'.ROOT.'assets/js/editor.js"></script>';
include "views/includes/footer.php"; ?>