<?php

// This will send a raw comment page as response.
if (isset($_GET["cid"]) && isset($_GET["offset"]) && isset($_GET["loadBeforeDate"])) {
    include "_api/headers/jsonstd.php";

    // If offset is 0, these are counted as initial comments. Otherwise, it's considered extended.
    $DEFAULT_INITIAL_COMMENT_COUNT = 6;
    $DEFAULT_EXTENDED_COMMENT_COUNT = 8;
    $DEFAULT_EXTENDED_REPLIES_COUNT = 6;

    // Sanitized input
    $cid = filter_var($_GET["cid"], FILTER_SANITIZE_NUMBER_INT);
    $offset = filter_var($_GET["offset"], FILTER_SANITIZE_NUMBER_INT);
    $loadBeforeDate = filter_var($_GET["loadBeforeDate"], FILTER_SANITIZE_NUMBER_INT); // Usually the query is set from Newest goes first which could cause data repetition after receiving request to extent, so this is used to mitigate that issue
    $loadBeforeDate = $loadBeforeDate / 1000;

     // Default selection query (for fetching normal comments)
    $sql = 'SELECT cmt.comment_id, cmt.user_id, cmt.username, cmt.comment FROM comments cmt, comment_groups cmtgrp WHERE UNIX_TIMESTAMP(cmt.comment_post_date) <= ? AND cmtgrp.comment_group_id = ? AND cmtgrp.comment_group_id = cmt.comment_group_id AND cmt.reply_to IS NULL ORDER BY cmt.comment_post_date DESC LIMIT ?, ?;';
    // Query for checking comments that have at least 1 reply
    $sql_replies = 'SELECT DISTINCT cmt.reply_to FROM comments cmt, comment_groups cmtgrp WHERE cmtgrp.comment_group_id = ? AND cmtgrp.comment_group_id = cmt.comment_group_id AND cmt.reply_to IS NOT NULL ORDER BY cmt.comment_post_date;';
    // Query for checking display names and profile pictures of the commenters (? = joined array, the array is processed by PHP)
    $sql_names = 'SELECT up.user_id, up.display_name, up.user_image, u.user_privilege_rank FROM users u, user_info up WHERE u.user_id = up.user_id AND FIND_IN_SET(up.user_id, ?) > 0 GROUP BY up.user_id;';

    $replyOf = 0;
    $isExtendingReplies = false;
    if (isset($_GET["replyOf"])) {
        $replyOf = filter_var($_GET["replyOf"], FILTER_SANITIZE_NUMBER_INT);
        // Get a set number of replies of the assigned comment
        $isExtendingReplies = true;
        // General idea: replies must tie with replyOf, meaning reply_to = replyOf, then order it in chronologically descending order to show the oldest reply first (and because of this, loadBeforeDate is redundant as newest ones will be loaded in its stead, no longer worry about meeting duplicated after offsetting things unless someone do something funny to the database)
        // Relation with cid is also unnecesssary since these actually only reply to one comment (replyOf) belonging to only ONE cid. So set cid to whatever since it'll ignore it anyway.
        $sql = 'SELECT DISTINCT cmt.comment_id, cmt.user_id, cmt.username, cmt.comment FROM comments cmt, comment_groups cmtgrp WHERE cmt.reply_to IS NOT NULL AND cmt.reply_to = ? ORDER BY cmt.comment_post_date LIMIT ?, ?;';
    }
    // Else, get a set number of comments (with an initial set number of replies)

    // This is for checking if there's more comments ahead.
    $USING_LOAD_COMMENT_COUNT = $isExtendingReplies ? $DEFAULT_EXTENDED_REPLIES_COUNT : ($offset > 0 ? $DEFAULT_EXTENDED_COMMENT_COUNT : $DEFAULT_INITIAL_COMMENT_COUNT);
    $LOOKAHEAD_COMMENT_COUNT = $USING_LOAD_COMMENT_COUNT + 1;

    $conn = DB::getConn();

    $stmt = $conn->prepare($sql);
    if ($isExtendingReplies) {
        $stmt->bind_param("sss", $replyOf, $offset, $LOOKAHEAD_COMMENT_COUNT);
    } else {
        $stmt->bind_param("ssss", $loadBeforeDate, $cid, $offset, $LOOKAHEAD_COMMENT_COUNT);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $commentsWithReplies = [];
    $result_replies = null;
    if (!$isExtendingReplies) {
        // Check what comments have replies
        $stmt = $conn->prepare($sql_replies);
        $stmt->bind_param("s", $cid);
        $stmt->execute();
        $result_replies = $stmt->get_result();

        while ($commentWithReplies = $result_replies->fetch_array()) {
            array_push($commentsWithReplies, $commentWithReplies[0]);
        }
        //var_dump($commentsWithReplies);
    }

    // Now load comments
    $comments = [];
    $usersInvolved = [];
    $usernameAssoc = [];
    $pfpAssoc = [];
    $authorityAssoc = [];
    $isThereMoreComments = false;
    $ind = 0;
    while ($comment = $result->fetch_assoc()) {
        $ind++;
        if ($ind > $USING_LOAD_COMMENT_COUNT) {
            // There are more comments ahead!
            $isThereMoreComments = true;
        } else {
            // Load comment
            if (!$isExtendingReplies) $comment["has_replies"] = in_array($comment["comment_id"], $commentsWithReplies);
            array_push($comments, $comment);
            $commentAuthorId = $comment["user_id"];
            if (!isset($usernameAssoc[$commentAuthorId])) {
                array_push($usersInvolved, $commentAuthorId);
                $usernameAssoc[$commentAuthorId] = true;
            }
            if (!isset($pfpAssoc[$commentAuthorId])) {
                $pfpAssoc[$commentAuthorId] = Router::toUrl("assets/picture/pfp-default.png");
            }
            if (!isset($authorityAssoc[$commentAuthorId])) {
                $authorityAssoc[$commentAuthorId] = 0;
            }
        }
    }

    $joinedUsersInvolved = join(",", $usersInvolved);
    // Check display names of users
    $stmt = $conn->prepare($sql_names);
    $stmt->bind_param("s", $joinedUsersInvolved);
    $stmt->execute();
    $result_displayname = $stmt->get_result();
    while ($commentAuthor = $result_displayname->fetch_assoc()) {
        $targetUid = $commentAuthor["user_id"];
        if (isset($usernameAssoc[$targetUid])) {
            $usernameAssoc[$targetUid] = $commentAuthor["display_name"];
        }
        if (isset($pfpAssoc[$targetUid])) {
            if (isset($commentAuthor["user_image"]) && !is_null($commentAuthor["user_image"])) $pfpAssoc[$targetUid] = Router::toUrl($commentAuthor["user_image"]);
        }
        if (isset($authorityAssoc[$targetUid])) {
            $authorityAssoc[$targetUid] = $commentAuthor["user_privilege_rank"];
        }
    }

    $commentsHTML = "";
    $currentSession = new Session;
    $newReplySelectors = [];
    foreach ($comments as $comment) {
        $formid = 'qzform_repto_id_'.$comment["comment_id"];
        if (!$isExtendingReplies) array_push($newReplySelectors, $formid);

        $commentsHTML = $commentsHTML.'<div id="ucid-qz-'.$comment["user_id"].$comment["comment_id"].'" class="d-flex flex-row">
            <a href="'.Router::toUrl("user/".$comment['user_id']).'"><img style="background-image: url('.(isset($pfpAssoc[$comment['user_id']]) ? $pfpAssoc[$comment['user_id']] : Router::toUrl("assets/picture/pfp-default.png")).');" class="web-comment-profile bg-white rounded-circle ml-3 flex-shrink-0"/></a>
            <div class="web-comment">'.(
            (Auth::loggedIn() && ($currentSession->getSessionUserId() == $comment['user_id'] || $currentSession->getSessionPrivilege() >= PrivilegeRank::ADMIN)) ?
            '<image alt="delete" src="'.Router::toUrl("assets/ui/delete-32x32.svg").'" type="button" class="web-comment-delete-button" data-toggle="modal" data-target="#cmt-delete-confirm-modal" data-comment-id="'.$comment["comment_id"].'"></image>' :
            ''
            ).'
                <p style="font-size:18px;margin-bottom:8px;"><a href="'.Router::toUrl("user/".$comment['user_id']).'"><b>'.(isset($usernameAssoc[$comment['user_id']]) ? $usernameAssoc[$comment['user_id']] : $comment['username']).'</b> '.
                ($authorityAssoc[$comment['user_id']] >= PrivilegeRank::ADMIN ? ' <img class="no-interact" src="'.Router::toUrl().'assets/badges/admin.ico"></img>' : '')
                .' <i style="font-size:14px;color:gray;">@'.$comment['username'].'</i></a></p>
                <p>'.nl2br($comment['comment']).'</p>
            </div>
        </div>';

        if (isset($comment["has_replies"]) && $comment["has_replies"]) {
            $commentsHTML = $commentsHTML.'<div class="web-replies-container" data-for="'.$comment["comment_id"].'">
            </div>
            '.(
                (Auth::loggedIn()) ?
                '<form id="'.$formid.'" class="d-flex flex-row" method="post" enctype="multipart/form-data">
                    <div class="form-group mb-0 flex-grow-1" style="width: calc(100% - 96px);margin-left: 96px;">
                        <input type="hidden" class="web-replyto-data-cid" name="book_comment_cid" value="'.$cid.'">
                        <input type="hidden" class="web-replyto-data" name="book_comment_reply_to" value="'.$comment["comment_id"].'">
                        <textarea name="book_comment_reply" class="web-color-trans-smooth web-resize-none h-100 w-100 p-2 mb-0" rows="1" placeholder="Leave a reply..." required></textarea>
                    </div>
                    '.CSRF::insertToken().'
                    <button type="submit" class="web-reply-submit-button web-color-trans-smooth btn btn-primary btn-block ml-2" style="width:46px;" name="book_comment_reply_submit"><i class="far fa-comment" aria-hidden="true"></i></button>
                </form>' :
                ''
            ).'
            <button type="button" class="web-btn-morereplies btn btn-outline-secondary" data-for="'.$comment["comment_id"].'">Show more replies</button>';
        } else {
            if (!$isExtendingReplies) {
                $commentsHTML = $commentsHTML.(
                    (Auth::loggedIn()) ?
                    '<form id="'.$formid.'" class="d-flex flex-row" method="post" enctype="multipart/form-data">
                        <div class="form-group mb-0 flex-grow-1" style="width: calc(100% - 96px);margin-left: 96px;">
                            <input type="hidden" class="web-replyto-data-cid" name="book_comment_cid" value="'.$cid.'">
                            <input type="hidden" class="web-replyto-data" name="book_comment_reply_to" value="'.$comment["comment_id"].'">
                            <textarea name="book_comment_reply" class="web-color-trans-smooth web-resize-none h-100 w-100 p-2 mb-0" rows="1" placeholder="Leave a reply..." required></textarea>
                        </div>
                        '.CSRF::insertToken().'
                        <button type="submit" class="web-reply-submit-button web-color-trans-smooth btn btn-primary btn-block ml-2" style="width:46px;" name="book_comment_reply_submit"><i class="far fa-comment" aria-hidden="true"></i></button>
                    </form>' :
                    ''
                );
            }
        }
    }

    if ($isThereMoreComments && !$isExtendingReplies && $offset == 0) {
        $commentsHTML = $commentsHTML.'<button type="button" class="web-btn-morecomments btn btn-outline-secondary" data-for="'.$cid.'">Show more comments</button>';
    }

    // Pack response into a JSON object.
    $finalResult = [
        "newReplySelectors" => $newReplySelectors,
        "extendedCount" => $ind,
        "isThereMoreComments" => $isThereMoreComments, // Can also be used to see if there are more replies ahead, not just comments
        "html" => $commentsHTML,
    ];
    $json = json_encode($finalResult);
    if ($json === false) {
        echo "[]";
    } else {
        echo $json;
    }
    /*
    var_dump($comments);
    var_dump($result);
    var_dump($result_replies);
    */
    return;
}
?>

<div class="d-flex flex-row">
    <img src="" class="web-comment-profile rounded-circle ml-3 bg-dark flex-shrink-0"/>
    <div class="web-comment">
        <image alt="delete" src="<?php Router::url("assets/ui/delete-32x32.svg"); ?>" type="button" class="web-comment-delete-button" data-target="1"></image>
        <p style="font-size:18px;margin-bottom:8px;"><b>Commenter</b> <i style="font-size:14px;color:gray;">@commenter</i></p>
        <p>This is a comment.</p>
    </div>
</div>

<div class="web-replies-container" data-for="1">
    <div class="d-flex flex-row">
        <img src="" class="web-comment-profile rounded-circle ml-3 bg-dark flex-shrink-0"/>
        <div class="web-comment">
            <p style="font-size:18px;margin-bottom:8px;"><b>Commenter</b> <i style="font-size:14px;color:gray;">@commenter</i></p>
            <p>This is a comment.</p>
        </div>
    </div>
</div>
<button type="button" class="web-btn-morereplies btn btn-outline-secondary" data-for="">Show more replies</button>
<button type="button" class="web-btn-morecomments btn btn-outline-secondary" data-for="">Show more comments</button>