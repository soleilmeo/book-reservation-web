<?php
class ReservationController extends Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function reserve() {
        if (!Auth::loggedIn()) {
            Router::redirect("login");
        } else {
            if (isset($this->req["target_book_id"]) && !empty($this->req["target_book_id"])) {
                $book = new Book($this->conn);
                $result = $book->reserve($this->req["target_book_id"], $this->req["reserve_days"]);
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

    public function returnBook() {
        if (!Auth::loggedIn()) {
            Router::redirect("login");
        } else {
            if (isset($this->req["target_book_id"]) && !empty($this->req["target_book_id"])) {
                $book = new Book($this->conn);
                $result = $book->returnBook($this->req["target_book_id"]);
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

    public function retrieveBook() {
        if (!Auth::loggedIn()) {
            Router::redirect("login");
        } else {
            if (isset($this->req["target_book_id"]) && !empty($this->req["target_book_id"])) {
                $book = new Book($this->conn);
                $result = $book->processReturn($this->req["target_book_id"]);
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

    public function unreserve() {
        if (!Auth::loggedIn()) {
            Router::redirect("login");
        } else {
            if (isset($this->req["target_book_id"]) && !empty($this->req["target_book_id"])) {
                $book = new Book($this->conn);
                $result = $book->unreserve($this->req["target_book_id"]);
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