<?php
if (!defined('ROOT')) {
    http_response_code(404);
    header("Location: /404");
    exit;
}
class BookController extends Controller
{
    // properties
    public const FIELD_NAMES = [
        "name" => "book_name",
        "sub" => "book_author",
        "desc" => "book_desc",
        "banner" => "book_banner",
        "genre" => "book_genre",
        "borrowdays" => "book_max_reserve_days",
        "published" => "book_publicity",
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function navigate($qid, $params) {
        $currentSession = Auth::getCurrentSession();
        $viewingBook = new Book($this->conn);
        $submitter = UserFactory::create($this->conn);
        $reserver = UserFactory::create($this->conn);
        $viewingBookId = $qid;

        $bookRootPath = "";
        if (isset($params[0])) {
            $bookRootPath = $params[0];
        }

        if ($viewingBook->doesBookExist($viewingBookId)) {
            $viewingBookInfo = $viewingBook->book;
            $isBookOwner = false;
            $isAdmin = false;

            $is_reserving_this_book = false;
            $queue_number = 0;
            $reserve_state = ReservationStatus::PENDING;

            $_reserve_days = 0;

            $_reserve_total_count = 0;

            $_reserve_time = null;
            $_reserve_deadline = null;

            $is_overdue = false;

            $submitterInfo = [];
            $reserverInfo = [];

            if ($submitter->userIdExists($viewingBookInfo["user_id"])) {
                $submitterInfo = $submitter->getUserData();
            }

            if ($currentSession) {
                if ($currentSession->getSessionUserId() === $viewingBookInfo['user_id']) $isBookOwner = true;
                if ($currentSession->getSessionPrivilege() >= PrivilegeRank::ADMIN) $isAdmin = true;

                $queue_number = $viewingBook->getUserReservationQueueNumber($qid, $currentSession->getSessionUserId());
                if ($queue_number != -1) {
                    $is_reserving_this_book = true;
                }
                $reserve_state_db = $viewingBook->getReservationQueueOfUser($qid, $currentSession->getSessionUserId());
                if ($reserve_state_db != false) {
                    $reserve_state = $reserve_state_db["reserve_status"];

                    $_reserve_days = $reserve_state_db["reserve_days"];

                    if ($reserve_state_db["receive_date"]) {
                        $_reserve_time = new DateTime($reserve_state_db["receive_date"]);
                        $_reserve_deadline = $_reserve_time->add(new DateInterval("P".$_reserve_days."D"));
                    }
                }

                $_datetime_now = new DateTime();
                $is_overdue = $_reserve_deadline ? $_datetime_now > $_reserve_deadline : false;

                $_reserve_total_count = count($viewingBook->getAllReservationsOfUser($currentSession->getSessionUserId()));
            }

            $isBookPublished = $viewingBookInfo['book_is_published'];

            $viewingBookCommentSectionId = $viewingBook->bookGetCommentSection($viewingBookInfo["book_read_id"]);

            $_reserve_queue = [];
            $_reserve_queue = $viewingBook->getReservationQueue($viewingBookInfo["book_read_id"]);

            if (count($_reserve_queue) > 0 and $reserver->userIdExists($_reserve_queue[0]["user_id"])) {
                $reserverInfo = $reserver->getUserData();
            }

            switch ($bookRootPath) {
                case "edit": {
                    if (!Auth::loggedIn()) Router::redirect("login");

                    // --------------- Edit book submission page
                    if ($isBookOwner || $isAdmin) {
                        include "views/book/dashboard/editor.php";
                        return;
                    }
                    break;
                }

                default: {
                    // -------------- Book main page
                    $discoveryCarousel = new DiscoveryCarousel($this->conn);
                    $embedDiscoveryLimit = 4;
                    $embedDiscoveryRandom = true;

                    if ($isBookPublished) {
                        // Everyone can see this book page since it is published
                        // Do some things here
                        include "views/book/book.php";
                        return;
                    } else {
                        // This book is private for everyone except the creator and an admin
                        if ($isBookOwner || $isAdmin) {
                            include "views/book/book.php";
                            return;
                        }
                    }
                }

            }

            // Please make a "return" statement when you reach the destination page or else this will appear
            include "views/_403.php";

        } else {
            include "views/_404.php";
        }
    }

    public function getCreate()
    {
        if (!Auth::loggedIn()) Router::redirect("login");
        else include "views/book/create.php";
    }

    public function getDashboard() {
        if (!Auth::loggedIn()) Router::redirect("login");
        else {
            $__self = $this;
            include "views/book/dashboard/dashboard.php";
        }
    }

    public function getAllReservationsOfUser() {
        if (!Auth::loggedIn()) return;
        else {
            $currentSession = Auth::getCurrentSession();
            $userId = $currentSession->getSessionUserId();

            $book_c = new Book($this->conn);
            $reservebooks = $book_c->getAllReservedBooksOfUser($userId);
            if (count($reservebooks) <= 0) {
                ?>
                <p class="mt-3 mx-auto">You have not reserved any books.</p>
                <?php
            }
            foreach ($reservebooks as $book) {
                $reservation = $book_c->getReservationQueueOfUser($book["book_read_id"], $userId);
                $in_queue = $book_c->getUserReservationQueueNumber($book["book_read_id"], $userId);
                include "views/book/dashboard/includes/reservebooks.php";
            }
        }
    }

    public function getDashboardBooks() {
        if (!Auth::loggedIn()) return;
        else {
            $currentSession = Auth::getCurrentSession();
            $userId = $currentSession->getSessionUserId();

            $book = new Book($this->conn);
            $userBooks = $book->getBooksByUser($userId);
            foreach ($userBooks as $book) {
                include "views/book/dashboard/includes/userbooks.php";
            }
        }
    }

    public function create()
    {
        if (!Auth::loggedIn()) {
            Router::redirect("login");
        } else {
            if (empty($this->req[self::FIELD_NAMES['name']])) {
                $postreq = $this->req;
                $postreq["err"] = "Book name is required and should not be left empty!";
                Router::redirect("book/create", $postreq);
            } else {
                var_dump($this->req);
                $book = new Book($this->conn);
                $result = $book->create($this->req, $this->files, self::FIELD_NAMES);
                if ($result && $result !== false) {
                    Router::redirect("book/".$result[1]);
                } else {
                    $postreq = $this->req;
                    $postreq["err"] = $book->getErrorStr();
                    Router::redirect("book/create", $postreq);
                    //echo 'Returned error: '.$book->getError().': '.$book->getErrorStr().'<br><a href="'.Router::assignGetParams("book/create", $this->req).'">Return to main site</a>';
                }
                return $result;
            }
        }
        return false;
    }

    public function update()
    {
        // Currently the most appropriate behavior for now...
        if (!Auth::loggedIn()) {
            http_response_code(403);
        } else {
            if (!isset($this->req["book_id"])) {
                return false;
            }
            if (empty($this->req[self::FIELD_NAMES['name']])) {
                $postreq = $this->req;
                echo "Book name is required and should not be left empty!";
                return false;
            } else {
                $book = new Book($this->conn);
                $result = $book->update($this->req, $this->files, self::FIELD_NAMES);
                if ($result && $result !== false) {
                    echo "success";
                } else {
                    echo $book->getErrorStr();
                    //echo 'Returned error: '.$book->getError().': '.$book->getErrorStr().'<br><a href="'.Router::assignGetParams("book/create", $this->req).'">Return to main site</a>';
                }
                return $result;
            }
        }
        return false;
    }

    public function delete() {
        if (!Auth::loggedIn()) {
            Router::redirect("login");
        } else {
            if (isset($this->req["target_book_id"]) && !empty($this->req["target_book_id"])) {
                $book = new Book($this->conn);
                $result = $book->delete($this->req["target_book_id"]);
                if ($result && $result !== false) {
                    include "views/book/deleted.php";
                } else {
                    $postreq = [];
                    $postreq["err"] = $book->getErrorStr();
                    include "views/sthwrong.php";
                    //echo 'Returned error: '.$book->getError().': '.$book->getErrorStr().'<br><a href="'.Router::assignGetParams("book/create", $this->req).'">Return to main site</a>';
                }
                return $result;
            }
            include "views/sthwrong.php";
        }
        return false;
    }

    public function postRating() {
        // This will be used in an AJAX statement, so a response code should be appropriate
        if (!Auth::loggedIn()) {
            http_response_code(403);
        } else {
            $currentSession = new Session;
            $uid = $currentSession->getSessionUserId();
            if (!isset($this->req['book_id'])) return false;
            if (!isset($this->req['book_rating'])) return false;
            $qid = filter_var($this->req['book_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $action = $this->req['book_rating'];
            $book = new Book($this->conn);
            $result = $book->submitRating($uid, $qid, $action);

            if (!$result) {
                echo -2;
            } else {
                echo $action;
            }

            return $result;
        }
        return false;
    }

    public function featureBook() {
        if (!Auth::loggedIn()) {
            Router::redirect("login");
        } else {
            if (isset($this->req["target_book_id"]) && !empty($this->req["target_book_id"])) {
                $book = new Book($this->conn);
                $result = $book->feature($this->req["target_book_id"]);
                if ($result && $result !== false) {
                    Router::redirect("book/".$this->req["target_book_id"]);
                } else {
                    $postreq = [];
                    $postreq["err"] = $book->getErrorStr();
                    include "views/sthwrong.php";
                    //echo 'Returned error: '.$book->getError().': '.$book->getErrorStr().'<br><a href="'.Router::assignGetParams("book/create", $this->req).'">Return to main site</a>';
                }
                return $result;
            }
            include "views/sthwrong.php";
        }
        return false;
    }
}
