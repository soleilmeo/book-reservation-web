<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    exit;
}
// Basic Book API
if (isset($_GET['id'])) {
    include "_api/headers/jsonstd.php";

    $qid = filter_var($_GET['id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $book = new Book(DB::getConn());
    if ($book->doesBookExist($qid, false, true)) {
        $out = $book->book;
        $currentSession = new Session();
        $isOwner = false;
        $isAdmin = false;
        if (!Auth::loggedIn()) {
            // Do not check private books!
            if (!$out['book_is_published']) {
                http_response_code(404);
                return;
            }
        } else {
            $isOwner = $out['user_id'] === $currentSession->getSessionUserId();
            $isAdmin = $currentSession->getSessionPrivilege() >= PrivilegeRank::ADMIN;
            if (!$out['book_is_published']) {
                // Only owners and admins can view publish
                if (!$isOwner && !$isAdmin) {
                    http_response_code(404);
                    return;
                }
            }
        }
        $json = "{}";
        if (isset($_GET['ratingOnly'])) {
            $didUserLike = -1;
            if (Auth::loggedIn()) {
                $currentSession = new Session;
                $uid = $currentSession->getSessionUserId();
                $didUserLike = $book->didUserLikedBook($uid, $qid);
            }
            $json = json_encode([
                "likes" => $out["likes"],
                "dislikes" => $out["dislikes"],
                "userLiked" => $didUserLike
            ]);
        } else {
            $json = json_encode([
                "bookId" => $out["book_read_id"],
                "creatorId" => $out["user_id"],
                "title" => $out["book_name"],
                "author" => Placeholder::put($out["book_author"], ""),
                "description" => Placeholder::put($out["book_desc"], ""),
                "bannerImage" => $out["book_banner_img"],
                "genre" => $out["book_genre"],
                "genreStr" => GlobalConfig::BOOK_GENRES[$out["book_genre"]],
                "maxReserveDays" => $out["book_max_reserve_days"] > 0 ? $out["book_max_reserve_days"] : GlobalConfig::MAX_RESERVE_DAYS,
                "published" => $out["book_is_published"],
                "dateCreated" => $out["book_creation_date"],
                "dateUpdated" => $out["book_update_date"],
                "featured" => $out["is_book_featured"],
                "lastFeatureDate" => $out["last_featured_date"],
                "likes" => $out["likes"],
                "dislikes" => $out["dislikes"],
            ]);
        }
        
        if ($json === false) {
            http_response_code(500);
        } else {
            echo $json;
        }
        return;
    } else {
        http_response_code(404);
        return;
    }
} else if (isset($_GET['userId'])) {
    include "_api/headers/jsonstd.php";
    
    $userId = filter_var($_GET['userId'], FILTER_SANITIZE_NUMBER_INT);
    // Get all public books created by a user.
    $sql = "SELECT * FROM books WHERE user_id = ? AND book_is_published = 1";
    if (isset($_GET['showPrivate'])) {
        if (Auth::loggedIn()) {
            $currentSession = new Session;
            if ($currentSession->getSessionUserId() == $userId || $currentSession->getSessionPrivilege() >= PrivilegeRank::ADMIN) {
                $sql = "SELECT * FROM books WHERE user_id = ?";
            }
        }
    }
    $stmt = DB::getConn()->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $bookList = [];
    while ($out = $res->fetch_assoc()) {
        $map = [
            "bookId" => $out["book_read_id"],
            "creatorId" => $out["user_id"],
            "title" => $out["book_name"],
            "subtext" => Placeholder::put($out["book_author"], ""),
            "description" => Placeholder::put($out["book_desc"], ""),
            "bannerImage" => $out["book_banner_img"],
            "genre" => $out["book_genre"],
            "genreStr" => GlobalConfig::BOOK_GENRES[$out["book_genre"]],
            "published" => $out["book_is_published"],
            "dateCreated" => $out["book_creation_date"],
            "dateUpdated" => $out["book_update_date"],
            "featured" => $out["is_book_featured"],
            "lastFeatureDate" => $out["last_featured_date"],
        ];
        array_push($bookList, $map);
    }
    $json = json_encode($bookList);
    if ($json === false) echo "[]";
    else echo $json;
    return;
}
?>

<section>
    <h2>Libraria Book API</h2>
    <p><b>GET</b> /api/book?id={id} - Gets book from book shared ID</p>
    <p><b>GET</b> /api/book?ratingOnly=1 - Required parameter: id={id}. Only provides rating of the book with the equivalent ID. This also shows if the user liked the book or not.</p>
    <p><b>GET</b> /api/book?userId={id} - Get all published books created by a user.</p>
    <p><b>GET</b> /api/book?showPrivate=1 - Get all books created by a user, including private ones. Only works for book owners, else it behaves like the one above.</p>
</section>
<hr>
<p>Libraria Ltd. 2024</p>