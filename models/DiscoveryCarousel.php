<?php

class DiscoveryCarousel {
    public $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function hasFeaturedPosts() {
        $sql = "SELECT book_id FROM books WHERE is_book_featured = 1 AND book_is_published = 1 LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return !empty($result->fetch_assoc());
    }

    public function displayFeatured() {
        //$sql = "SELECT book_read_id, book_name, book_author, book_banner_img FROM books WHERE is_book_featured = 1 AND book_is_published = 1 ORDER BY book_featured_order DESC";
        $sql = "SELECT u.user_id, u.username, q.book_read_id, q.book_name, q.book_author, q.book_banner_img FROM books q, users u WHERE q.is_book_featured = 1 AND q.book_is_published = 1 AND q.user_id = u.user_id ORDER BY book_featured_order DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $contnet_indicators = "";
        $content_inner = "";
        $firstfeed = "active";
        $ind = 0;

        $featuredPosts = [];
        while ($book = $result->fetch_assoc()) {
            include "views/discovery/dcdiscovery.php";
            //array_push($featuredPosts, $featured);
        }

        if (empty($featuredPosts)) return false;
        else {
            //include "views/discovery/dcfeatured.php";
        }
    }

    public function displayDiscovery($count = 100, $randomized = false) {
        $sql = "SELECT u.user_id, u.username, q.book_read_id, q.book_name, q.book_author, q.book_banner_img FROM books q, users u WHERE q.book_is_published = 1 AND q.user_id = u.user_id ".($randomized ? "ORDER BY RAND()" : "")." LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $count);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($book = $result->fetch_assoc()) {
            include "views/discovery/dcdiscovery.php";
        }
    }

    public function displayEmbedDiscoveryOnBookPage($count = 4, $randomized = false, $excludeId = null) {
        // $excludeId must be the book ID (not book_read_id) - only the server can assign this value, so I don't use bind_param. There's another reason to it too...
        $sql = "SELECT u.user_id, u.username, q.book_read_id, q.book_name, q.book_author, q.book_banner_img FROM books q, users u WHERE ".(!is_null($excludeId) ? "q.book_id <> ".$excludeId." AND " : "")."q.book_is_published = 1 AND q.user_id = u.user_id ".($randomized ? "ORDER BY RAND()" : "")." LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $count);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($book = $result->fetch_assoc()) {
            include "views/discovery/dcembeddiscovery.php";
        }
    }

    public function displayBooksOfUser($userid, $count = 4, $showPrivate = false) {
        $sql = "SELECT q.book_read_id, q.book_name, q.book_author, q.book_banner_img FROM books q WHERE ".(!$showPrivate ? "q.book_is_published = 1 AND " : " ")."q.user_id = ? ORDER BY q.book_creation_date DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $userid, $count);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasBook = false;

        while ($book = $result->fetch_assoc()) {
            $hasBook = true;
            include "views/discovery/duserdiscovery.php";
        }

        if (!$hasBook) {
            include "views/discovery/duserdiscovery.php";
        }
    }

    public function displayMostRatedBooksOfUser($userid, $count = 4, $showPrivate = false) {
        $sql = "SELECT q.book_read_id, q.book_name, q.book_author, q.book_banner_img, COALESCE(SUM(q.book_id = qr.book_id AND qr.rating = 1), 0) - COALESCE(SUM(q.book_id = qr.book_id AND qr.rating = 0), 0) AS rating FROM books q, book_ratings qr WHERE ".(!$showPrivate ? "q.book_is_published = 1 AND " : " ")."q.user_id = ? GROUP BY q.book_id ORDER BY rating DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $userid, $count);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasBook = false;

        while ($book = $result->fetch_assoc()) {
            $hasBook = true;
            include "views/discovery/duserdiscovery.php";
        }

        if (!$hasBook) {
            include "views/discovery/duserdiscovery.php";
        }
    }
}

?>