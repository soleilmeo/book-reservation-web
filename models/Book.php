<?php

class BookCreateErrors {
    public const NONE = 1;
    public const UNKNOWN = 2;
    public const REQUIRED_FIELD_UNFILLED = 3;
    public const EXCEED_TITLE_LIMIT = 4;
    public const EXCEED_SUB_LIMIT = 5;
    public const EXCEED_DESC_LIMIT = 6;
    public const INVALID_FILE = 7;
    public const INCORRECT_FILE_TYPE = 8;
    public const EXCEED_FILE_LIMIT = 9;
    public const SERVER_ERROR = 10;
    public const DEAD_SESSION = 11;
    public const INVALID_REQUEST = 12;
}

class Book {

    public const BANNER_SAVE_PATH = "assets/u/qbanner/";
    public const THUMB_SAVE_PATH = "assets/u/qthumb/";
    public const MAX_COLLISION = 10;

    public const TITLE_MAX_CHARS = 100; // Standard maximum characters
    public const AUTHOR_MAX_CHARS = 100;
    public const DESC_MAX_CHARS = 5000;
    public const DESC_MAX_CHARS_HARD_CAP = 6000;
    public const LIMIT_ERROR_MARGIN = 255;

    public $conn;

    protected $error = BookCreateErrors::NONE;
    protected $errorStr = "None";
    public $errors = [];
    public $book = [];
    public $bookData = [];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private static function calculateLimitWithErrorMargin($limit) {
        return ceil($limit + (self::LIMIT_ERROR_MARGIN * ($limit / self::LIMIT_ERROR_MARGIN)));
    }

    private function setError($err, $errstr) {
        $this->error = $err;
        $this->errorStr = $errstr;
    }

    public function getError() {
        return $this->error;
    }

    public function getErrorStr() {
        return $this->errorStr;
    }

    public static function generateBookID() {
        srand(time());
        $strong = true;
        $qv4 = UUID::v4(true).bin2hex(openssl_random_pseudo_bytes(4, $strong)).'7cool';
        $qid = str_shuffle($qv4.bin2hex(random_bytes(2)));
        $iter_map = [3, 5, 8, 12, 19];
        $iter_range = [
            $iter_map[rand(0,4)],
            $iter_map[rand(0,4)]
        ];
        $iter_count = abs($iter_range[1] - $iter_range[0]) + 2;
        $len = strlen($qid);
        $iter_count_constraint = min($len, $iter_count);
        for ($i = 0, $c=$iter_count_constraint; $i < $c; $i++) {
            $ci = random_int(0, $len - 1);
            $qid[$ci] = strtoupper($qid[$ci]);
        }
        return $qid;
    }

    public function doesBookExist($qid, $noAssignment = false, $getRatings = false) {
        $sql = "SELECT * FROM books WHERE book_read_id = ?";
        //if ($getRatings) $sql = "SELECT q.*, COALESCE(SUM(qr.rating = 1), 0) AS likes, COALESCE(SUM(qr.rating = 0), 0) AS dislikes FROM books q, book_ratings qr WHERE q.book_read_id = ? AND q.book_id = qr.book_id";
        if ($getRatings) $sql = "SELECT q.*, COALESCE(SUM(qr.rating = 1), 0) AS likes, COALESCE(SUM(qr.rating = 0), 0) AS dislikes FROM books q LEFT JOIN book_ratings qr ON q.book_id = qr.book_id WHERE q.book_read_id = ?";
        $stmt = $this->conn->prepare($sql);

        // Possible unsanitized qid. take caution

        $stmt->bind_param("s", $qid);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if ($noAssignment) {
            if(!empty($data) && isset($data["book_id"])) {
                return true; // book found
            }  else {
                return false; // book not found
            }
        } else {
            $this->book = $data; // grab assoc array if book exists else empty
            if(!empty($data) && isset($data["book_id"])) {
                return true; // book found
            }  else {
                return false; // book not found
            }
        }
    }

    public function didUserLikedBook($userId, $qid) {
        $sql = "SELECT COALESCE(qr.rating, -1) AS rating FROM books q, book_ratings qr WHERE q.book_read_id = ? AND q.book_id = qr.book_id AND qr.user_id = ?";
        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("ss", $qid, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if(!empty($data) && isset($data["rating"])) {
            return $data["rating"];
        } else {
            return -1;
        }
    }

    public function submitRating($userId, $qid, $doLike) {
        // doLike must be 0 (dislike), 1 (like) or -1 (undecided)
        // if -1 then remove entry
        $sql = "INSERT INTO book_ratings(relation, user_id, book_id, rating) VALUES(?, ?,(SELECT book_id FROM books WHERE book_read_id = ?),?) ON DUPLICATE KEY UPDATE rating = ?";
        if ($doLike < 0) {
            $sql = "DELETE FROM book_ratings WHERE user_id = ? AND book_id = (SELECT book_id FROM books WHERE book_read_id = ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $userId, $qid);
        } else {
            $stmt = $this->conn->prepare($sql);
            $unq = $userId.$qid;
            $stmt->bind_param("sssss", $unq, $userId, $qid, $doLike, $doLike);
        }
        $stmt->execute();
        if ($stmt->affected_rows >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getBooksByUser($userId, $limit = -1, $chronological_descend = true) {
        $sql = "SELECT * FROM books WHERE user_id = ? ".($chronological_descend ? "ORDER BY book_creation_date DESC ": "").($limit > 0 ? "LIMIT ?" : "")."";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($d = $result->fetch_assoc()) {
            array_push($data, $d);
        }
        return $data;
    }

    public function getUserCreatedBooks($userId) {
        $user = UserFactory::create($this->conn);
        if ($user->userProfileExistsById($userId)) {
            $userprofile = $user->getProfileData();
            if (!isset($userprofile['created_books'])) {
                return [];
            } else {
                $arr = json_decode($userprofile['created_books']);
                return $arr;
            }
        }
        return false;
    }

    public function reloadUserCreatedBooks($userId) {
        // Dangerous: This will WIPE all user created book data in order to scan all created books to reassign!
        // This should only be used when user encounters certain issues like data mismatch
        $sql = "SELECT book_id FROM books WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($d = $result->fetch_column()) {
            array_push($data, $d);
        }

        if ($json = json_encode($data)) {
            // Submit new data of user-created books of the user to database
            $sql = "UPDATE user_info SET created_books = ?, book_count = ? WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $count = count($data);
            $stmt->bind_param("sss", $json, $count, $userId);
            $stmt->execute();

            if (defined("GLOBAL_DEBUG")) {
                if ($stmt->affected_rows !== 1) {
                    echo "Book data reload affected none other than 1 rows (when it's supposed to be 1 user only), validation is required";
                }
            }
        }
    }

    public function bookGetCommentSection($qid) {
        $sql = "SELECT cmtgrp.comment_group_id FROM books q, comment_groups cmtgrp WHERE q.book_read_id = ? AND q.book_id = cmtgrp.book_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $qid);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        if (isset($data["comment_group_id"]) && !is_null($data["comment_group_id"])) {
            return $data["comment_group_id"];
        }
        return -1;
    }

    public function countPublishedBooksOfUser($userId) {
        $sql = "SELECT COUNT(q.book_id) as count FROM books q WHERE q.user_id = ? AND q.book_is_published = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = $result->fetch_assoc();
        if (!empty($data['count'])) {
            return $data['count'];
        } else {
            return 0;
        }
    }

    public function calculateUserRatingScore($userId) {
        // This does not count rating submitted by the creator of the books.
        $sql = "SELECT COALESCE(SUM(q.book_id = qr.book_id AND qr.rating = 1), 0) as likes, COALESCE(SUM(q.book_id = qr.book_id AND qr.rating = 0), 0) AS dislikes FROM books q, book_ratings qr WHERE q.user_id = ? AND qr.user_id <> ? AND q.book_is_published = 1 GROUP BY q.book_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $totalVotes = 0;
        $totalLikes = 0;

        while ($data = $result->fetch_assoc()) {
            $totalVotes += intval($data['likes']) + intval($data['dislikes']);
            $totalLikes += intval($data['likes']);
        }

        if ($totalVotes == 0) return 0;
        else return $totalLikes / $totalVotes;
    }

    public function create($post, $files, $fn) {
        $this->setError(BookCreateErrors::NONE, "None");

        // Create Book from given POST and FILES requests (as well as field names)
        $proceedExecution = true;

        // Checking valid values
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Not POST request");
            }
            return false;
        }

        // Is user present?
        if (!Auth::loggedIn()) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::DEAD_SESSION, "User session expired or not logged in");
            }
            return false;
        }
        $creator = Auth::getCurrentSession();
        $creatorId = $creator->getSessionUserId();
        $creatorCreatedBooks = $this->getUserCreatedBooks($creatorId);
        if (is_null($creatorCreatedBooks) || !is_array($creatorCreatedBooks) || $creatorCreatedBooks === false) {
            if (defined('GLOBAL_DEBUG')) {
                $this->setError(BookCreateErrors::SERVER_ERROR, "Cannot create book - prior book data may be invalid. Contact an administrator to diagnose this issue.");
            }
            return false;
        }

        // TITLE (REQUIRED)

        $TITLEMAX_CHARS = self::calculateLimitWithErrorMargin(self::TITLE_MAX_CHARS);
        $title_f = filter_var($post[$fn["name"]], FILTER_SANITIZE_SPECIAL_CHARS);
        $title_f = strlen(trim($title_f)) <= $TITLEMAX_CHARS ? $title_f : false;
        if ($title_f === false) {
            $this->setError(BookCreateErrors::EXCEED_TITLE_LIMIT, "Title field exceeded max limit of ".self::TITLE_MAX_CHARS."characters!");
            return false;
        }
        if ($title_f !== false && strlen(trim($title_f)) < 1) {
            $this->setError(BookCreateErrors::REQUIRED_FIELD_UNFILLED, "Title field is required and should not be left empty!");
            return false;
        }

        // Author (OPTIONAL, substituted by UNKNOWN)
        $AUTHORMAX_CHARS = self::calculateLimitWithErrorMargin(self::AUTHOR_MAX_CHARS);
        $author_f = filter_var($post[$fn["sub"]], FILTER_SANITIZE_SPECIAL_CHARS);
        $author_f = strlen(trim($author_f)) <= $AUTHORMAX_CHARS ? $author_f : false;
        $author_f = strlen($author_f) <= 0 ? GlobalConfig::DEFAULT_AUTHOR_NAME : $author_f;
        if ($author_f === false) {
            $this->setError(BookCreateErrors::EXCEED_SUB_LIMIT, "Author field exceeded max limit of ".self::AUTHOR_MAX_CHARS."characters!");
            return false;
        }

        // DESCRIPTION (OPTIONAL)
        $DESCMAX_CHARS = min(self::calculateLimitWithErrorMargin(self::DESC_MAX_CHARS), self::DESC_MAX_CHARS_HARD_CAP);
        $desc_f = filter_var($post[$fn["desc"]], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $desc_f = strlen(trim($desc_f)) <= $DESCMAX_CHARS ? $desc_f : false;
        if ($desc_f === false) {
            $this->setError(BookCreateErrors::EXCEED_DESC_LIMIT, "Description exceeded max limit of ".self::DESC_MAX_CHARS."characters!");
            return false;
        }

        // CATEGORY (DEFAULT UNKNOWN (0))
        $category_f = (int)filter_var($post[$fn["genre"]], FILTER_SANITIZE_NUMBER_INT);
        if ($category_f === false) $category_f = 0;

        if ($category_f >=  count(GlobalConfig::BOOK_GENRES) || $category_f < 0) {
            $category_f = 0;
        }

        // BANNER (OPTIONAL)

        $hasBanner = true;
        // Are you there?
        if (!isset($files[$fn["banner"]])) {
            if (defined("GLOBAL_DEBUG")) {
                echo "File not found in " . $fn["banner"];
            }
            $hasBanner = false;
        }

        if ($hasBanner) {
            // Make sure everything is OK
            if ($files[$fn["banner"]]['error'] != 0) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::INVALID_FILE, "File error");
                }
                $hasBanner = false;
            }
        }

        $target_dir = self::BANNER_SAVE_PATH;
        $allowUpload = true;
        // Target save path on the server
        $target_file   = "";
        if ($hasBanner) {
            // Upload time!
            // Extension
            $imageFileType = pathinfo(basename($files[$fn["banner"]]["name"]), PATHINFO_EXTENSION);
            try {
                $target_file   = $target_dir . UUID::v4(true) . "." . $imageFileType;
            } catch (Exception $e) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::UNKNOWN, "Exception thrown while uploading file, details: " . $e);
                }
                $proceedExecution = false;
            }
            if (!$proceedExecution) return false;
            $maxfilesize = 524288000; //5MB*100 because why not
            $allowtypes = array('jpg', 'png', 'jpeg');
            $maxCollisions = self::MAX_COLLISION;

            // Image?
            if (isset($_POST["submit"])) {
                $check = getimagesize($files[$fn["banner"]]["tmp_name"]);
                if ($check !== false) {
                    $allowUpload = true;
                } else {
                    if (defined("GLOBAL_DEBUG")) {
                        $this->setError(BookCreateErrors::INVALID_FILE, "Not an image");
                    }
                    $allowUpload = false;
                }
            }

            // Name collision?
            try {
                while (file_exists($target_file) && $maxCollisions-- >= 0) {
                    $target_file   = $target_dir . UUID::v4(true) . "." . $imageFileType;
                }
            } catch (Exception $e) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::UNKNOWN, "Exception thrown while uploading file at checking stage, details: " . $e);
                }
                $proceedExecution = false;
            }
            if (!$proceedExecution) {
                return false;
            }
            if (file_exists($target_file)) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::SERVER_ERROR, "Max collisions exceeded, file cannot be uploaded");
                }
                $allowUpload = false;
                return false;
            }

            // Size?
            if ($files[$fn["banner"]]["size"] > $maxfilesize) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::EXCEED_FILE_LIMIT, "File cannot exceed $maxfilesize bytes in size");
                }
                $allowUpload = false;
                return false;
            }

            // File type checking
            if (!in_array($imageFileType, $allowtypes)) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::INVALID_FILE, "Unsupported file type, currently only allow JPG, PNG, JPEG");
                }
                $allowUpload = false;
                return false;
            }
            if (!$proceedExecution) return false;
            // Everything seems to be OK! File transferring to server will be done last assuming there's nothing else in the way
        } else {
            $allowUpload = false;
        }

        // Generate a unique key of book
        $bookkey = "";
        try {
            $bookkey = self::generateBookID();
            $maxCollisions = self::MAX_COLLISION;
            while ($this->doesBookExist($bookkey, true) && $maxCollisions-- >= 0) {
                $bookkey = self::generateBookID();
            }
        } catch (Exception $e) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::UNKNOWN, "Exception occurred while giving book ID, error: ".$e);
            }
            $proceedExecution = false;
        }

        if ($this->doesBookExist($bookkey, true)) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::SERVER_ERROR, "Max collision exceeded while checking for book. Possible collision with: ".$bookkey);
            }
            $proceedExecution = false;
        }

        if (!$proceedExecution) return false;

        // Transfer file to server. This should be done last
        if ($allowUpload) {
            // Move to server
            if (move_uploaded_file($files[$fn["banner"]]["tmp_name"], $target_file)) {
                if (defined("GLOBAL_DEBUG")) {
                    echo "File " . basename($files[$fn["banner"]]["name"]) .
                        " has been uploaded successfully";

                    echo "Saved as: " . $target_file;
                }
            } else {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::SERVER_ERROR, "An error occurred while trying to save file");
                }
                $proceedExecution = false;
            }
        } else {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::NONE, "No upload specified or upload is ignored, skipping file uploading");
            }
        }

        if (!$proceedExecution) return false;

        $book_borrow_days = $post[$fn["borrowdays"]] > 0 ? $post[$fn["borrowdays"]] : GlobalConfig::MAX_RESERVE_DAYS;

        // Add newly created book into database (default is private)
        $sql = "INSERT INTO books(book_read_id, user_id, book_name, book_author, book_desc, book_banner_img, book_reserve_days_limit, book_genre) VALUES (?,?,?,?,?,?,?,?)";
        $stmt = $this->conn->prepare($sql);

        // Possible unsanitized qid. take caution

        $banner_img_path = $target_file; // No need to Router::toUrl since the main page will direct that to the root, database doesn't need to know it
        if (!($hasBanner && $allowUpload)) {
            $banner_img_path = "assets/picture/banner-default.jpg";
        }
        
        $stmt->bind_param("ssssssss", $bookkey, $creatorId, $title_f, $author_f, $desc_f, $banner_img_path, $book_borrow_days, $category_f);
        $stmt->execute();

        if($stmt->affected_rows === 1) {
            $bookId = $stmt->insert_id;

            array_push($creatorCreatedBooks, $stmt->insert_id);
            $json = json_encode($creatorCreatedBooks);
            if ($json === false) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::SERVER_ERROR, "Cannot update created book data due to a JSON error. You can still edit and users can still make reservvations, but there may be unknown side effects. Please contact an administrator for a database refresh.");
                }
            } else {
                // Submit new data of user-created books of the user to database
                $sql = "UPDATE user_info SET created_books = ?, book_count = ? WHERE user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $count = count($creatorCreatedBooks);
                $stmt->bind_param("sss", $json, $count, $creatorId);
                $stmt->execute();

                if (defined("GLOBAL_DEBUG")) {
                    if ($stmt->affected_rows !== 1) {
                        $this->setError(BookCreateErrors::UNKNOWN, "Book data update affected none other than 1 rows, validation is required");
                    }
                }
            }

            // Add a comment group for this book
            $sql = "INSERT INTO comment_groups(book_id) VALUES(?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $bookId);
            $stmt->execute();
            if ($stmt->affected_rows !== 1) {
                $this->setError(BookCreateErrors::SERVER_ERROR, "Failed to create comment group for book. Comment section may not functional.");
            }
            return [$bookId, $bookkey]; // book is created!
        }  else {
            return false;
        }
    }

    public function update($post, $files, $fn) {
        // For book updating, POST information SHOULD contain target book ID, or this won't work.
        $this->setError(BookCreateErrors::NONE, "None");

        // Update Book from given POST and FILES requests (as well as field names)
        $proceedExecution = true;
        $qid = "";

        // Checking valid values
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Not POST request");
            }
            return false;
        }

        // Is user present?
        if (!Auth::loggedIn()) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::DEAD_SESSION, "User session expired or not logged in");
            }
            return false;
        }
        $creator = Auth::getCurrentSession();
        $creatorId = $creator->getSessionUserId();
        $creatorCreatedBooks = $this->getUserCreatedBooks($creatorId);
        if (is_null($creatorCreatedBooks) || !is_array($creatorCreatedBooks) || $creatorCreatedBooks === false) {
            if (defined('GLOBAL_DEBUG')) {
                $this->setError(BookCreateErrors::SERVER_ERROR, "Cannot update book - prior book data may be invalid. Contact an administrator to diagnose this issue.");
            }
            return false;
        }

        if (!isset($post["book_id"])) {
            $this->setError(BookCreateErrors::INVALID_REQUEST, "Cannot update book - book ID is undefined.");
            return false;
        }
        $qid = $post["book_id"];

        // Check if this user is authorized to do this.
        if ($creator->getSessionPrivilege() < PrivilegeRank::ADMIN) {
            $sql = "SELECT user_id FROM books WHERE book_read_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $qid);
            $stmt->execute();
            $results = $stmt->get_result();
            $data = $results->fetch_assoc();
            if (isset($data["user_id"]) && !is_null($data["user_id"]) && $data["user_id"] == $creatorId) {
                // User has permission
            } else {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Request denied");
                return false;
            }
        }

        // TITLE (REQUIRED)

         $TITLEMAX_CHARS = self::calculateLimitWithErrorMargin(self::TITLE_MAX_CHARS);
        $title_f = filter_var($post[$fn["name"]], FILTER_SANITIZE_SPECIAL_CHARS);
        $title_f = strlen(trim($title_f)) <= $TITLEMAX_CHARS ? $title_f : false;
        if ($title_f === false) {
            $this->setError(BookCreateErrors::EXCEED_TITLE_LIMIT, "Title field exceeded max limit of ".self::TITLE_MAX_CHARS."characters!");
            return false;
        }
        if ($title_f !== false && strlen(trim($title_f)) < 1) {
            $this->setError(BookCreateErrors::REQUIRED_FIELD_UNFILLED, "Title field is required and should not be left empty!");
            return false;
        }

        // AUTHOR (OPTIONAL, substituted by UNKNOWN)
        $AUTHORMAX_CHARS = self::calculateLimitWithErrorMargin(self::AUTHOR_MAX_CHARS);
        $author_f = filter_var($post[$fn["sub"]], FILTER_SANITIZE_SPECIAL_CHARS);
        $author_f = strlen(trim($author_f)) <= $AUTHORMAX_CHARS ? $author_f : false;
        $author_f = strlen($author_f) <= 0 ? GlobalConfig::DEFAULT_AUTHOR_NAME : $author_f;
        if ($author_f === false) {
            $this->setError(BookCreateErrors::EXCEED_SUB_LIMIT, "Author field exceeded max limit of ".self::AUTHOR_MAX_CHARS."characters!");
            return false;
        }

        // DESCRIPTION (OPTIONAL)
        $DESCMAX_CHARS = min(self::calculateLimitWithErrorMargin(self::DESC_MAX_CHARS), self::DESC_MAX_CHARS_HARD_CAP);
        $desc_f = filter_var($post[$fn["desc"]], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $desc_f = strlen(trim($desc_f)) <= $DESCMAX_CHARS ? $desc_f : false;
        if ($desc_f === false) {
            $this->setError(BookCreateErrors::EXCEED_DESC_LIMIT, "Description exceeded max limit of ".self::DESC_MAX_CHARS."characters!");
            return false;
        }

        // CATEGORY (DEFAULT UNKNOWN (0))
        $category_f = (int)filter_var($post[$fn["genre"]], FILTER_SANITIZE_NUMBER_INT);
        if ($category_f === false) $category_f = 0;

        if ($category_f >=  count(GlobalConfig::BOOK_GENRES) || $category_f < 0) {
            $category_f = 0;
        }

        // BANNER (OPTIONAL)

        $hasBanner = true;
        // Are you there?
        if (!isset($files[$fn["banner"]])) {
            if (defined("GLOBAL_DEBUG")) {
            }
            $hasBanner = false;
        }

        if ($hasBanner) {
            // Make sure everything is OK
            if ($files[$fn["banner"]]['error'] != 0) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::INVALID_FILE, "File error");
                }
                $hasBanner = false;
            }
        }

        $target_dir = self::BANNER_SAVE_PATH;
        $allowUpload = true;
        // Target save path on the server
        $target_file   = "";
        if ($hasBanner) {
            // Upload time!
            // Extension
            $imageFileType = pathinfo(basename($files[$fn["banner"]]["name"]), PATHINFO_EXTENSION);
            try {
                $target_file   = $target_dir . UUID::v4(true) . "." . $imageFileType;
            } catch (Exception $e) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::UNKNOWN, "Exception thrown while uploading file, details: " . $e);
                }
                $proceedExecution = false;
            }
            if (!$proceedExecution) return false;
            $maxfilesize = 5242880; //5MB
            $allowtypes = array('jpg', 'png', 'jpeg');
            $maxCollisions = self::MAX_COLLISION;

            // Image?
            if (isset($_POST["submit"])) {
                $check = getimagesize($files[$fn["banner"]]["tmp_name"]);
                if ($check !== false) {
                    $allowUpload = true;
                } else {
                    if (defined("GLOBAL_DEBUG")) {
                        $this->setError(BookCreateErrors::INVALID_FILE, "Not an image");
                    }
                    $allowUpload = false;
                }
            }

            // Name collision?
            try {
                while (file_exists($target_file) && $maxCollisions-- >= 0) {
                    $target_file   = $target_dir . UUID::v4(true) . "." . $imageFileType;
                }
            } catch (Exception $e) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::UNKNOWN, "Exception thrown while uploading file at checking stage, details: " . $e);
                }
                $proceedExecution = false;
            }
            if (!$proceedExecution) {
                return false;
            }
            if (file_exists($target_file)) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::SERVER_ERROR, "Max collisions exceeded, file cannot be uploaded");
                }
                $allowUpload = false;
                return false;
            }

            // Size?
            if ($files[$fn["banner"]]["size"] > $maxfilesize) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::EXCEED_FILE_LIMIT, "File cannot exceed $maxfilesize bytes in size");
                }
                $allowUpload = false;
                return false;
            }

            // File type checking
            if (!in_array($imageFileType, $allowtypes)) {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::INVALID_FILE, "Unsupported file type, currently only allow JPG, PNG, JPEG");
                }
                $allowUpload = false;
                return false;
            }
            if (!$proceedExecution) return false;
            // Everything seems to be OK! File transferring to server will be done last assuming there's nothing else in the way
        } else {
            $allowUpload = false;
        }

        if (!$proceedExecution) return false;

        // Transfer file to server. This should be done last
        if ($allowUpload) {
            // Move to server
            if (move_uploaded_file($files[$fn["banner"]]["tmp_name"], $target_file)) {
                if (defined("GLOBAL_DEBUG")) {
                }
            } else {
                if (defined("GLOBAL_DEBUG")) {
                    $this->setError(BookCreateErrors::SERVER_ERROR, "An error occurred while trying to save file");
                }
                $proceedExecution = false;
            }
        } else {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::NONE, "No upload specified or upload is ignored, skipping file uploading");
            }
        }

        if (!$proceedExecution) return false;

        $shouldPublish = isset($post[$fn["published"]]) ? $post[$fn["published"]] : 0;
        $res_queue = $this->getReservationQueue($qid);
        if (count($res_queue) > 0 && (!$shouldPublish || $shouldPublish == 0)) {
            // There are still people reserving this book while this book is public.
            // Delete all reservations related
            $this->processReturnAll($qid);
        }

        $book_borrow_days = $post[$fn["borrowdays"]] > 0 ? $post[$fn["borrowdays"]] : GlobalConfig::MAX_RESERVE_DAYS;

        // Update book.
        if ($allowUpload) {
            $sql = "UPDATE books SET book_name = ?, book_author = ?, book_desc = ?, book_banner_img = ?, book_genre = ?, book_reserve_days_limit = ?, book_is_published = ? WHERE book_read_id = ?";
        } else {
            $sql = "UPDATE books SET book_name = ?, book_author = ?, book_desc = ?, book_genre = ?, book_reserve_days_limit = ?, book_is_published = ? WHERE book_read_id = ?";
        }
        $stmt = $this->conn->prepare($sql);

        $banner_img_path = $target_file; // No need to Router::toUrl since the main page will direct that to the root, database doesn't need to know it
        if (!($hasBanner && $allowUpload)) {
            $banner_img_path = "assets/picture/banner-default.jpg";
        }
        
        if ($allowUpload) {
            $stmt->bind_param("ssssssss", $title_f, $author_f, $desc_f, $banner_img_path, $category_f, $book_borrow_days, $shouldPublish, $qid);
        } else {
            $stmt->bind_param("sssssss", $title_f, $author_f, $desc_f, $category_f, $book_borrow_days, $shouldPublish, $qid);
        }
        $stmt->execute();

        /*
        if($stmt->affected_rows === 1) {
            return true; // book is updated!
        }  else {
            return false;
        }
        */
        return true;
    }

    public function delete($qid) {
        $this->setError(BookCreateErrors::NONE, "None");

        // Checking valid values
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Not POST request");
            }
            return false;
        }

        // Is user present?
        if (!Auth::loggedIn()) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::DEAD_SESSION, "User session expired or not logged in");
            }
            return false;
        }
        $creator = Auth::getCurrentSession();
        $creatorId = $creator->getSessionUserId();
        $creatorCreatedBooks = $this->getUserCreatedBooks($creatorId);
        if (is_null($creatorCreatedBooks) || !is_array($creatorCreatedBooks) || $creatorCreatedBooks === false) {
            if (defined('GLOBAL_DEBUG')) {
                $this->setError(BookCreateErrors::SERVER_ERROR, "Cannot delete book - prior book data may be invalid. Contact an administrator to diagnose this issue.");
            }
            return false;
        }

        // Check if this user is authorized to do this.
        if ($creator->getSessionPrivilege() < PrivilegeRank::ADMIN) {
            $sql = "SELECT user_id FROM books WHERE book_read_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $qid);
            $stmt->execute();
            $results = $stmt->get_result();
            $data = $results->fetch_assoc();
            if (isset($data["user_id"]) && !is_null($data["user_id"]) && $data["user_id"] == $creatorId) {
                // User has permission
            } else {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Request denied");
                return false;
            }
        }

        $sql = "DELETE FROM books WHERE book_read_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $qid);
        $stmt->execute();
        if ($stmt->affected_rows === 1) {
            return true;
        } else {
            return false;
        }
    }

    private function updateReservations($qid) {
        $queue = $this->getReservationQueue($qid);
        if (count($queue) > 0) {
            // First reserve ID will be next one to have the book (unless it is returning)
            $data = $queue[0];
            $first_reserve_id = $data["reserve_id"];

            if ($data["reserve_status"] == ReservationStatus::PENDING) {
                $reserve_status = ReservationStatus::RESERVED;

                $sql = "UPDATE reservations SET reserve_status = ?, receive_date = CURRENT_TIMESTAMP() WHERE reserve_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ss", $reserve_status, $first_reserve_id);
                $stmt->execute();
            }
        }
    }

    public function isReservedByUser($qid, $uid) { // niche use, use getUserReservationQueueNumber() if you need queue number
        $sql = "SELECT resv.user_id FROM reservations resv, books q WHERE q.book_read_id = ? AND resv.user_id = ? AND resv.book_id = q.book_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $qid, $uid);
        $stmt->execute();
        $results = $stmt->get_result();
        $data = $results->fetch_assoc();
        if ($data) {
            return true;
        }
        return false;
    }

    public function getReservationQueue($qid) {
        $sql = "SELECT resv.* FROM reservations resv, books q WHERE resv.book_id = q.book_id AND q.book_read_id = ? ORDER BY resv.reserve_create_date";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $qid);
        $stmt->execute();
        $results = $stmt->get_result();
        $data = [];
        while ($result = $results->fetch_assoc()) {
            array_push($data, $result);
        }
        return $data;
    }

    public function getAllReservationsOfUser($uid) {
        $sql = "SELECT * FROM reservations WHERE user_id = ? ORDER BY reserve_create_date";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $results = $stmt->get_result();
        $data = [];
        while ($result = $results->fetch_assoc()) {
            array_push($data, $result);
        }
        return $data;
    }

    public function getAllReservedBooksOfUser($uid) {
        $book_ids = [];
        $book_query = "";
        {
            $sql = "SELECT book_id FROM reservations WHERE user_id = ? ORDER BY reserve_create_date";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $uid);
            $stmt->execute();
            $results = $stmt->get_result();
            while ($result = $results->fetch_assoc()) {
                array_push($book_ids, $result["book_id"]);
            }
        }

        if (count($book_ids) <= 0) return [];

        $book_query = join(",", $book_ids);

        $sql = "SELECT q.* FROM reservations resv, books q WHERE q.book_id IN ($book_query) AND resv.user_id = ? AND q.book_id = resv.book_id ORDER BY resv.reserve_create_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $results = $stmt->get_result();
        $data = [];
        while ($result = $results->fetch_assoc()) {
            array_push($data, $result);
        }
        return $data;
    }


    public function getReservationQueueOfUser($qid, $uid) {
        $sql = "SELECT resv.* FROM reservations resv, books q WHERE resv.book_id = q.book_id AND q.book_read_id = ? AND resv.user_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $qid, $uid);
        $stmt->execute();
        $results = $stmt->get_result();
        $result = $results->fetch_assoc();
        return $result;
    }

    public function getUserReservationQueueNumber($qid, $uid) {
        $data = $this->getReservationQueue($qid);
        $queueNo = 0;
        foreach ($data as $row) {
            if ($row["user_id"] == $uid) {
                return $queueNo;
            }
            $queueNo++;
        }
        return -1;
    }

    public function reserve($qid, $reserve_days) {
        // Place reservation on books
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Not POST request");
            }
            return false;
        }

        if (!Auth::loggedIn()) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::DEAD_SESSION, "User session expired or not logged in");
            }
            return false;
        }

        if (!$reserve_days || $reserve_days <= 0) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Invalid reservation date");
            }
            return false;
        }

        $session = Auth::getCurrentSession();
        $user_id = $session->getSessionUserId();

        if ($this->isReservedByUser($qid, $user_id)) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "User already reserved this book");
            }
            return false;
        }

        if (count($this->getAllReservationsOfUser($user_id)) >= GlobalConfig::MAX_RESERVATIONS_PER_USER) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "User exceeded maximum reservation count, return books to have more slots (current maximum is ".GlobalConfig::MAX_RESERVATIONS_PER_USER.")");
            }
            return false;
        }
        
        $q_sid = -1;
        {
            $sql = "SELECT book_id FROM books WHERE book_read_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $qid);
            $stmt->execute();
            $results = $stmt->get_result();
            if ($result = $results->fetch_assoc()) {
                $q_sid = $result["book_id"];
            }
        }

        $reserveState = ReservationStatus::PENDING;

        if ($q_sid > 0) {
            $sql = "INSERT INTO reservations(user_id, book_id, reserve_status, reserve_days) VALUES(?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssss", $user_id, $q_sid, $reserveState, $reserve_days);
            $stmt->execute();
            if($stmt->affected_rows === 1) {
                $insert_id = $stmt->insert_id;
                $this->updateReservations($qid);
                return $insert_id; // Your reservation ID
            } else {
                return false;
            }
        }
        return false;
    }

    public function returnBook($qid) {
        // Place reservation on books
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Not POST request");
            }
            return false;
        }

        if (!Auth::loggedIn()) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::DEAD_SESSION, "User session expired or not logged in");
            }
            return false;
        }

        $session = Auth::getCurrentSession();
        $user_id = $session->getSessionUserId();
        
        $q_sid = -1;
        {
            $sql = "SELECT book_id FROM books WHERE book_read_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $qid);
            $stmt->execute();
            $results = $stmt->get_result();
            if ($result = $results->fetch_assoc()) {
                $q_sid = $result["book_id"];
            }
        }

        if ($q_sid > 0) {
            {
                $sql = "SELECT reserve_status FROM reservations WHERE user_id = ? AND book_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ss", $user_id, $q_sid);
                $stmt->execute();
                $results = $stmt->get_result();
                if ($result = $results->fetch_assoc()) {
                    if ($result["reserve_status"] != ReservationStatus::RESERVED) {
                        // To enter RETURN state must require reserve_status to be ::RESERVED
                        if (defined("GLOBAL_DEBUG")) {
                            $this->setError(BookCreateErrors::INVALID_REQUEST, "To enter RETURN state must require reserve_status to be ::RESERVED");
                        }
                        return false;
                    }
                }
            }

            $reserveState = ReservationStatus::RETURNING;

            $sql = "UPDATE reservations SET reserve_status = ? WHERE user_id = ? AND book_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", $reserveState, $user_id, $q_sid);
            $stmt->execute();

            $this->updateReservations($qid);
            return true;
        }
        return false;
    }

    public function isBeingReturned($qid) {
        $queue = $this->getReservationQueue($qid);
        if (count($queue) > 0) {
            // First reserve ID is the one returning this book.
            $data = $queue[0];
            return $data["reserve_status"] == ReservationStatus::RETURNING;
        }
        return false;
    }

    // Use case: User's reservation status is still in PENDING state
    public function unreserve($qid) {
        // Remove reservation
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Not POST request");
            }
            return false;
        }

        if (!Auth::loggedIn()) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::DEAD_SESSION, "User session expired or not logged in");
            }
            return false;
        }

        $session = Auth::getCurrentSession();
        $user_id = $session->getSessionUserId();

        $data = $this->getReservationQueueOfUser($qid, $user_id);
        $reserve_id = -1;
        if ($data) {
            $reserve_id = $data["reserve_id"];
            $reserve_status = $data["reserve_status"];

            if ($reserve_status != ReservationStatus::PENDING) {
                return false;
            }

            $sql = "DELETE FROM reservations WHERE reserve_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $reserve_id);
            $stmt->execute();
            if ($stmt->affected_rows === 1) {
                $this->updateReservations($qid);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // Use case: User requested to return the book, then Book Owner successfully retrieved the book.
    // Use case 2: User forgot to click Return request, and Book Owner either have the book returned or got a replacement.
    // Use case 3: Book Owner wants to deny service to a User for some reasons.
    // Will be seen on Book Owner's page once User placed a reservation.
    // This must be used by the Owner of the book. (Admins can do it too)
    public function processReturn($qid) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Not POST request");
            }
            return false;
        }

        if (!Auth::loggedIn()) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::DEAD_SESSION, "User session expired or not logged in");
            }
            return false;
        }

        $session = Auth::getCurrentSession();
        $user_id = $session->getSessionUserId();

        // Check if this user is authorized to do this.
        if ($session->getSessionPrivilege() < PrivilegeRank::ADMIN) {
            $sql = "SELECT user_id FROM books WHERE book_read_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $qid);
            $stmt->execute();
            $results = $stmt->get_result();
            $data = $results->fetch_assoc();
            if (isset($data["user_id"]) && !is_null($data["user_id"]) && $data["user_id"] == $user_id) {
                // User has permission
            } else {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Request denied");
                return false;
            }
        }

        // Copy-pasted from unreserve()
        $queue = $this->getReservationQueue($qid);
        if (count($queue) > 0) {
            // First reserve ID is the one returning this book.
            $data = $queue[0];

            $reserve_id = -1;
            if ($data) {
                $reserve_id = $data["reserve_id"];

                $sql = "DELETE FROM reservations WHERE reserve_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("s", $reserve_id);
                $stmt->execute();
                if ($stmt->affected_rows === 1) {
                    $this->updateReservations($qid);
                    return true;
                } else {
                    return false;
                }
            }
            return false;;
        } else {
            return false;
        }
    }

    // Use case: Editing the Publicity option while Users placed reservations on the book.
    public function processReturnAll($qid) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Not POST request");
            }
            return false;
        }

        if (!Auth::loggedIn()) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::DEAD_SESSION, "User session expired or not logged in");
            }
            return false;
        }

        $session = Auth::getCurrentSession();
        $user_id = $session->getSessionUserId();

        // Check if this user is authorized to do this.
        if ($session->getSessionPrivilege() < PrivilegeRank::ADMIN) {
            $sql = "SELECT user_id FROM books WHERE book_read_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $qid);
            $stmt->execute();
            $results = $stmt->get_result();
            $data = $results->fetch_assoc();
            if (isset($data["user_id"]) && !is_null($data["user_id"]) && $data["user_id"] == $user_id) {
                // User has permission
            } else {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Request denied");
                return false;
            }
        }

        $q_sid = -1;
        {
            $sql = "SELECT book_id FROM books WHERE book_read_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $qid);
            $stmt->execute();
            $results = $stmt->get_result();
            if ($result = $results->fetch_assoc()) {
                $q_sid = $result["book_id"];
            }
        }
        
        if ($q_sid > 0) {
            $sql = "DELETE FROM reservations WHERE book_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $q_sid);
            $stmt->execute();
            return true;
        }
        return false;
    }

    public function feature($qid) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Not POST request");
            }
            return false;
        }

        if (!Auth::loggedIn()) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::DEAD_SESSION, "User session expired or not logged in");
            }
            return false;
        }

        $session = Auth::getCurrentSession();
        $user_id = $session->getSessionUserId();

        // Check if this user is authorized to do this.
        if ($session->getSessionPrivilege() < PrivilegeRank::ADMIN) {
            if (defined("GLOBAL_DEBUG")) {
                $this->setError(BookCreateErrors::INVALID_REQUEST, "Permission denied");
            }
            return false;
        }

        $featured_rank = 1;
        $is_featured = 0;
        // Check if book is already featured. If it is featured, remove it from featured.
        {
            $sql = "SELECT is_book_featured FROM books WHERE book_read_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $qid);
            $stmt->execute();

            $results = $stmt->get_result();
            if ($result = $results->fetch_assoc()) {
                $is_featured = $result["is_book_featured"];
            }
        }

        if ($is_featured == 0) $is_featured = 1;
        else $is_featured = 0;

        $sql = "UPDATE books SET is_book_featured = ?, book_featured_order = ? WHERE book_read_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $is_featured, $featured_rank, $qid);
        $stmt->execute();

        return true;
    }
}