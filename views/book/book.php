<!-- Page for displaying selected book's information -->
<?php // AKA preview book page handled by PreviewBookController
if ($isBookPublished || $isBookOwner) {
    $PAGE_TITLE = $viewingBookInfo['book_name'] . " - " . GlobalConfig::BASE_WEB_TITLE;
}
include "views/includes/header.php" ?>

<script>
    const __BOOK_ID = "<?php echo $viewingBookInfo["book_read_id"] ?>";
</script>

<div class="container d-flex flex-column justify-content-center">

    <?php if(!$isBookPublished): ?>
        <div class="alert alert-warning mt-3 mb-0">This book is not visible to others since it is set to Private. <a href="<?php Router::url("book/" . $viewingBookInfo["book_read_id"] . "/edit"); ?>"><i class="fa fa-cog ml-1"></i> Change publish settings</a></div>
    <?php endif; ?>

    <?php if ($is_reserving_this_book): ?>
        <?php if ($reserve_state == ReservationStatus::PENDING): ?>
            <div class="alert alert-secondary mt-3  mb-0">You have reserved this book and is currently placed in a queue. Your place in the queue: <?php echo $queue_number ?></div>
        <?php elseif($reserve_state == ReservationStatus::RETURNING): ?>
            <div class="alert alert-warning mt-3 mb-0">You are currently returning this book. Please return the book to the owner as soon as possible.</div>
        <?php elseif($is_overdue && $reserve_state == ReservationStatus::RESERVED): ?>
            <div class="alert alert-danger mt-3 mb-0">Reservation period for this book has expired. Please return your book as soon as possible by clicking "Return Book" below.</div>
        <?php elseif(!$is_overdue && $reserve_state == ReservationStatus::RESERVED): ?>
            <div class="alert alert-success mt-3 mb-0">This book is currently reserved for you! Remember to return this book before <?php echo $_reserve_deadline->format("l, F jS, Y (h:i A)") ?>.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="row gx-4 gx-lg-5 align-items-center mt-3">
        <div class="web-img-thumbnail-container rounded col-md-6 mx-2 mx-md-0" style="background-image: url('<?php echo Router::toUrl($viewingBookInfo["book_banner_img"]) ?>');" draggable="false"></div>
        <div class="col-md-6">
            <div class="d-flex flex-column container align-self-end mb-3">
                <h4 class="text-break h3 display-4-fluid font-weight-bold mt-2 mt-md-0">
                    <?php
                    echo $viewingBookInfo['book_name'];
                    ?>
                </h4>
                <h4 class="lead mb-3 text-break">by <?php echo $viewingBookInfo['book_author'] ?></h4>

                <?php
                if ($viewingBookInfo['is_book_featured']
                || $viewingBookInfo['user_id'] == 1
                || count($_reserve_queue) > 0) :
                ?>
                    <p class="lead flex-grow-0 no-interact">
                        <?php
                        if ($viewingBookInfo['is_book_featured']) :
                        ?>
                            <span class="badge badge-warning"><i class="fa fa-star"></i> Featured</span>
                        <?php endif; ?>

                        <?php
                        if ($viewingBookInfo['user_id'] == 1) :
                        ?>
                            <span class="badge badge-danger"><i class="fa fa-book"></i> Libraria's</span>
                        <?php endif; ?>

                        <?php
                        if (count($_reserve_queue) > 0) :
                        ?>
                            <span class="badge badge-primary">Currently Reserved by <?php echo count($_reserve_queue).(count($_reserve_queue) > 1 ? ' people' : ' person') ?></span>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

                <div class="usercard d-flex flex-column align-self-start px-3 py-2 w-100 mt-2 mb-2 text-dark rounded" data-uid="<?php echo $viewingBookInfo['user_id'] ?>" style="background-color:#ececef;margin-bottom:12px;">
                    <p class="font-italic mb-1" style="font-size:20px;">Submitted by ...</p>
                    <p class="text-secondary mb-0">@...</p>
                </div>

                <div class="book-rating-container d-flex flex-row align align-content-center mb-2 px-3 py-2 rounded">
                    <p class="mb-0">Book Rating: </p>
                    <div class="book-rating ml-2">
                        <p class="mb-0">
                            <b id="book-like-count">0</b>
                            <i type="submit" id="book-like-btn" class="fa fa-thumbs-up"></i>
                            &nbsp;|&nbsp;
                            <b id="book-dislike-count">0</b>
                            <i type="submit" id="book-dislike-btn" class="fa fa-thumbs-down"></i>
                        </p>
                    </div>
                </div>

                <div class="row w-100 mt-2 align-self-center">

                    <?php if ($isBookOwner) : ?>

                        <div class="col-md-12 mb-2">
                            <?php if ($viewingBook->isBeingReturned($viewingBookInfo["book_read_id"])): ?>
                                <a data-toggle="modal" data-target="#book-retrieve-confirm-modal" class="btn btn-primary btn-block btn-lg h-100"><i class="fa fa-check"></i> Confirm Retrieval</a>
                            <?php elseif (count($_reserve_queue) > 0): ?>
                                <a data-toggle="modal" data-target="#book-retrieve-withdraw-modal" class="btn btn-danger btn-block btn-lg h-100"><i class="fa fa-times-circle"></i> Withdraw</a>
                            <?php else: ?>
                                <a class="btn web-light-outline-success btn-outline-success btn-block btn-lg h-100 disabled" aria-disabled="true"><i class="fa fa-tag"></i> Reserve</a>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-12">
                            <a href="<?php Router::url("book/" . $viewingBookInfo["book_read_id"] . "/edit"); ?>" class="btn btn-primary btn-block btn-lg h-100"><i class="fa fa-cog"></i> Edit</a>
                        </div>

                    <?php elseif (!Auth::loggedIn()) : ?>
                        <div class="col-md-12">
                                <a href="<?php Router::url("login") ?>" class="btn btn-success btn-block btn-lg h-100"><i class="fa fa-tag"></i> Reserve</a>
                        </div>

                    <?php else: ?>

                        <div class="col-md-12">
                            <?php if ($is_reserving_this_book): ?>
                                <?php if($reserve_state == ReservationStatus::RESERVED): ?>
                                    <a data-toggle="modal" data-target="#book-reserve-return-modal" class="btn btn-danger btn-block btn-lg h-100"><i class="fa fa-undo"></i> Return Book</a>
                                <?php elseif ($reserve_state == ReservationStatus::PENDING): ?>
                                    <a data-toggle="modal" data-target="#book-reserve-cancel-modal" class="btn btn-danger btn-block btn-lg h-100"><i class="fa fa-times"></i> Cancel Reservation</a>
                                <?php elseif ($reserve_state == ReservationStatus::RETURNING): ?>
                                    <a class="btn btn-danger btn-block btn-lg h-100 disabled">Waiting for Return</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a data-toggle="modal" data-target="#book-reserve-confirm-modal" class="btn btn-success btn-block btn-lg h-100"><i class="fa fa-tag"></i> Reserve</a>
                            <?php endif; ?>
                        </div>

                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<hr>

<div class="container" style="margin-top:-32px;">
    <div class="d-flex flex-column justify-content-center align-content-center align-items-center my-2">

        <hr style="opacity: 0;">

        <div class="d-flex flex-xl-row flex-column position-relative w-100">
            <section id="BOOKDESC" class="flex-grow-1">

                <?php if ($isAdmin): ?>
                    <?php if ($viewingBookInfo['is_book_featured']): ?>
                        <a data-toggle="modal" data-target="#book-toggle-featured-modal" class="btn btn-danger btn-block btn-lg mb-2"><i class="fa fa-star-half"></i> Remove Featured</a>
                    <?php else: ?>
                        <a data-toggle="modal" data-target="#book-toggle-featured-modal" class="btn btn-warning btn-block btn-lg mb-2"><i class="fa fa-star"></i> Make Featured</a>
                    <?php endif; ?>

                    <div class="modal fade" id="book-toggle-featured-modal" tabindex="-1" role="dialog" aria-labelledby="book-toggle-featured-title" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <?php if ($viewingBookInfo['is_book_featured']): ?>
                                        <h5 class="modal-title" id="book-toggle-featured-title"><i class="fa fa-star-half"></i> Unfeature</h5>
                                    <?php else: ?>
                                        <h5 class="modal-title" id="book-toggle-featured-title"><i class="fa fa-star"></i> Feature</h5>
                                    <?php endif; ?>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                    <div class="modal-body">
                                        <?php if ($viewingBookInfo['is_book_featured']): ?>
                                            <p>Remove <b><?php echo $viewingBookInfo["book_name"] ?></b> as Featured Book?</p>
                                        <?php else: ?>
                                            <p>Make <b><?php echo $viewingBookInfo["book_name"] ?></b> a Featured Book?</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="modal-footer">
                                        <form id="bookfeature" action="<?php echo ROOT;?>book/feature" method="post" enctype="multipart/form-data">
                                            <input id="book-feature-target" name="target_book_id" type="hidden" value="<?php echo $viewingBookInfo["book_read_id"] ?>" hidden>
                                            <button id="btn-confirm-book-feature" type="submit" class="btn btn-danger">Proceed</button>
                                            <button id="btn-unconfirm-book-feature" type="button" class="btn btn-primary" data-dismiss="modal">Of course not</button>
                                        </form>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <p class="align-self-start"><?php
                {
                    $bookdesc_html = nl2br($viewingBookInfo['book_desc']);

                    if (strlen($bookdesc_html) > 0) {
                        echo $bookdesc_html;
                    } else {
                        ?><i>No description provided.</i><?php
                    }
                }
                ?></p>

                <hr />

                <h4>Comments</h4>
                <div>
                    <?php if (Auth::loggedIn()) : ?>
                        <form id="bookcmtf" <?php if ($viewingBookCommentSectionId >= 1) : ?>action="<?php echo ROOT; ?>book/comment" <?php endif; ?> method="post" enctype="multipart/form-data">
                            <div class="form-group flex-grow-1">
                                <input type="hidden" name="book_comment_section_id" value="<?php echo $viewingBookCommentSectionId ?>">
                                <textarea id="qcomment" name="book_comment" class="web-resize-none w-100 p-2" rows="3" placeholder="Leave a comment..." required maxlength="2000" <?php if ($viewingBookCommentSectionId < 1) : ?>disabled<?php endif; ?>></textarea>
                            </div>
                            <?php CSRF::outputToken(); ?>
                            <button type="submit" class="btn btn-primary btn-block btn-lg" name="book_comment_submit" <?php if ($viewingBookCommentSectionId < 1) : ?>disabled<?php endif; ?>><i class="far fa-comment ml-1" aria-hidden="true"></i> Comment</button>
                        </form>
                    <?php else : ?>
                        <p class="text-secondary">You must log in in order to post a comment.</p>
                    <?php endif; ?>
                </div>

                <div class="web-comment-container web-default-bottom-margin mt-3" data-comment-id="<?php echo $viewingBookCommentSectionId ?>">
                    <p class="text-center text-secondary">Comments are loading...</p>
                </div>

            </section>

            <div class="ml-5" style="width:3px; background-color:lightgray;"></div>

            <section id="BOOKRECOMMEND" class="mt-xl-0 mt-4 ml-0 ml-xl-5 text-wrap">
                <div class="d-flex flex-column web-default-bottom-margin">
                    <h3><i class="fa fa-chess-rook mr-1 mb-3"></i> More Books</h3>
                    <div class="row">

                        <?php
                        $discoveryCarousel->displayEmbedDiscoveryOnBookPage($embedDiscoveryLimit, $embedDiscoveryRandom, $viewingBookInfo["book_id"]);
                        ?>

                    </div>
                </div>
            </section>
        </div>

        <?php if (Auth::loggedIn()) : ?>

            <?php if ($_reserve_total_count < GlobalConfig::MAX_RESERVATIONS_PER_USER): ?>
                <div class="modal fade" id="book-reserve-confirm-modal" tabindex="-1" role="dialog" aria-labelledby="book-reserve-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="book-reserve-title"><i class="fa fa-tag"></i> Place Reservation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                            <form id="bookreserve" action="<?php echo ROOT;?>book/reserve" method="post" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <p class="mb-0">You are about to reserve book: <b><?php echo $viewingBookInfo["book_name"] ?></b></p>
                                    <p>How many days would you like to borrow this book from <b>@<?php echo $submitterInfo["username"] ?></b>?</p>
                                    <div class="form-group">
                                        <p class="text-center">I'd like to reserve for 
                                        <select id="book-reserve-days" name="reserve_days">
                                            <?php
                                            {
                                                $ind = 1;
                                                $maxReserveDaysLimit = $viewingBookInfo["book_reserve_days_limit"] == 0 ? GlobalConfig::MAX_RESERVE_DAYS : $viewingBookInfo["book_reserve_days_limit"];
                                                for (; $ind <= $maxReserveDaysLimit; $ind++) {
                                                    if ($ind == 1) {
                                                        echo "<option value='$ind' selected>$ind</option>";
                                                    } else echo "<option value='$ind'>$ind</option>";
                                                }
                                            }
                                            ?>
                                        </select> day(s)</p>

                                        <p><i>Upon clicking "Reserve", you understand that you have to wait until your turn. Once the reservation is cancelled, you cannot reclaim your queue. We will notify you when it's your turn.</i></p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                        <input id="book-reserve-target" name="target_book_id" type="hidden" value="<?php echo $viewingBookInfo["book_read_id"] ?>" hidden>
                                        <button id="btn-confirm-book-reserve" type="submit" class="btn btn-success">Reserve</button>
                                        <button id="btn-unconfirm-book-reserve" type="button" class="btn btn-primary" data-dismiss="modal">Go Back</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="modal fade" id="book-reserve-confirm-modal" tabindex="-1" role="dialog" aria-labelledby="book-reserve-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="book-reserve-title"><i class="fa fa-tag"></i> Place Reservation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                                <div class="modal-body">
                                    <p class="mb-0">Cannot reserve book: <b><?php echo $viewingBookInfo["book_name"] ?></b></p>
                                    <p>You can only have <?php echo GlobalConfig::MAX_RESERVATIONS_PER_USER?> active reservations per account. Please cancel or do a book return on your old reservations if necessary.</p>
                                </div>
                                <div class="modal-footer">
                                        <button id="btn-okay-sure" type="button" class="btn btn-primary" data-dismiss="modal">Okay</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="modal fade" id="book-reserve-cancel-modal" tabindex="-1" role="dialog" aria-labelledby="book-cancel-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="book-cancel-title"><i class="fa fa-times"></i> Cancel Reservation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">You are about to cancel book: <b><?php echo $viewingBookInfo["book_name"] ?></b></p>
                            <p>Cancelling reservations will reset your queue if you ever want to reserve this book again.</p>
                        </div>
                        <div class="modal-footer">
                            <form id="bookcancel" action="<?php echo ROOT;?>book/unreserve" method="post" enctype="multipart/form-data">
                                <input id="book-reserve-cancel-target" name="target_book_id" type="hidden" value="<?php echo $viewingBookInfo["book_read_id"] ?>" hidden>
                                <button id="btn-confirm-book-reserve-cancel" type="submit" class="btn btn-danger">Confirm cancel</button>
                                <button id="btn-unconfirm-book-reserve-cancel" type="button" class="btn btn-primary" data-dismiss="modal">No, do not cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="book-reserve-return-modal" tabindex="-1" role="dialog" aria-labelledby="book-return-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="book-return-title"><i class="fa fa-undo"></i> Return Book</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">You are about to return book: <b><?php echo $viewingBookInfo["book_name"] ?></b></p>
                            <p>Thank you for using our service. Remember to send the book back to the owner!</p>
                        </div>
                        <div class="modal-footer">
                            <form id="bookreturn" action="<?php echo ROOT;?>book/return" method="post" enctype="multipart/form-data">
                                <input id="book-reserve-return-target" name="target_book_id" type="hidden" value="<?php echo $viewingBookInfo["book_read_id"] ?>" hidden>
                                <button id="btn-confirm-book-reserve-return" type="submit" class="btn btn-danger">Return the book</button>
                                <button id="btn-unconfirm-book-reserve-return" type="button" class="btn btn-primary" data-dismiss="modal">No, not yet</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($isBookOwner && count($_reserve_queue) > 0): ?>

                <div class="modal fade" id="book-retrieve-confirm-modal" tabindex="-1" role="dialog" aria-labelledby="book-retrieve-confirm-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="book-retrieve-confirm-title"><i class="fa fa-check"></i> Confirm Book Retrieval</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0"><a href="<?php Router::url("user/".$reserverInfo["user_id"]) ?>">@<?php echo $reserverInfo["username"] ?></a> wanted to return: <b><?php echo $viewingBookInfo["book_name"] ?></b></p>
                                <p>Only confirm retrieval after your book has arrived to your residence. Please check thoroughly before proceeding.</p>
                            </div>
                            <div class="modal-footer">
                                <form id="bookretrieve" action="<?php echo ROOT;?>book/confirmretrieve" method="post" enctype="multipart/form-data">
                                    <input id="book-retrieve-target" name="target_book_id" type="hidden" value="<?php echo $viewingBookInfo["book_read_id"] ?>" hidden>
                                    <button id="btn-confirm-book-retrieve-cancel" type="submit" class="btn btn-danger">Confirm retrieval</button>
                                    <button id="btn-unconfirm-book-retrieve-cancel" type="button" class="btn btn-primary" data-dismiss="modal">Haven't retrieved yet</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="book-retrieve-withdraw-modal" tabindex="-1" role="dialog" aria-labelledby="book-retrieve-withdraw-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="book-retrieve-withdraw-title"><i class="fa fa-times-circle"></i> Cancellation Confirmation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0">You are about to cancel <a href="<?php Router::url("user/".$reserverInfo["user_id"]) ?>">@<?php echo $reserverInfo["username"] ?></a>'s reservation on book: <b><?php echo $viewingBookInfo["book_name"] ?></b></p>
                                <p>Only confirm withdrawal after your book has arrived to your residence. Please check thoroughly before proceeding.</p>
                                <p class="mb-0"><i>This will immediately remove them from your book reservation list. Note that users can always re-reserve your book, but they may have to wait longer.</i></p>
                            </div>
                            <div class="modal-footer">
                                <form id="bookwithdraw" action="<?php echo ROOT;?>book/confirmwithdraw" method="post" enctype="multipart/form-data">
                                    <input id="book-withdraw-target" name="target_book_id" type="hidden" value="<?php echo $viewingBookInfo["book_read_id"] ?>" hidden>
                                    <button id="btn-confirm-book-withdraw-cancel" type="submit" class="btn btn-danger">Withdraw</button>
                                    <button id="btn-unconfirm-book-withdraw-cancel" type="button" class="btn btn-primary" data-dismiss="modal">No, don't</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

            <div class="modal fade" id="cmt-delete-confirm-modal" tabindex="-1" role="dialog" aria-labelledby="cmt-delete-deletetitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cmt-delete-deletetitle"><i class="fa fa-trash-alt"></i> Delete Comment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to <b>delete the comment?</b>
                        </div>
                        <div class="modal-footer">
                            <form id="bookcmtdelete" <?php if ($viewingBookCommentSectionId >= 1) : ?>action="<?php echo ROOT; ?>book/cmtdelete" <?php endif; ?> method="post" enctype="multipart/form-data">
                                <input id="cmt-delete-confirm-target" name="target_comment_id" type="hidden" value="" hidden>
                                <button id="cmt-delete-confirm-btn-yes" type="submit" class="btn btn-danger">Yes, delete</button>
                                <button id="cmt-delete-confirm-btn-no" type="button" class="btn btn-primary" data-dismiss="modal">No, don't</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php
$EXTRA_FOOTER_INCLUDES = '
<script src="' . ROOT . 'assets/js/usercard.js"></script>
<script src="' . ROOT . 'assets/js/comment.js"></script>
<script src="' . ROOT . 'assets/js/bookrating.js"></script>
';
include "views/includes/footer.php" ?>