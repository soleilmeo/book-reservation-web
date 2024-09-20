<?php
function web($finalcallback) {
    if (!defined('ROOT'))
    {
        http_response_code(404);
        header("Location: /404");
        exit;
    }

    Router::get("ping", function() {
        echo "<h1>PONG<h1>";
    });

    // --------------------------
    // Landing page
    //

    Router::get("", function() {
        $home = new HomeController;

        $home->index();
        if (defined("GLOBAL_DEBUG")) {
            var_dump($home->req);
            var_dump($home->params);
        }
    });

    Router::get("help", function() {
        include "views/help.php";
    });

    // --------------------------
    // User account
    //

    Router::post("create/user", function() {
        $user = new UserController;
        $user->create();
    });

    Router::get("login", function() {
    $user = new UserController;
    $user->getLogin();
    });

    Router::post("login", function() {
        $user = new UserController;
        $user->validateLogin();
    });

    Router::get("logout", function() {
        Session::resetSession();
        header("Location:" . ROOT);
    });

    // User profile
    Router::get_from("user", function($_, $params) {
        $userId = $params[0];
        $profile = new UserProfileController;
        $profile->view($userId);
    });

    Router::get("profile/edit", function() {
        $profile = new UserProfileController;
        $profile->viewEditor();
    });

    Router::post("profile/edit", function() {
        $profile = new UserProfileController;
        $profile->commitEdit();
    });

    // --------------------------
    // Book browse and reservation
    //

    Router::get_among(["discover", "book/discover"], function() {
        $qdiscover = new DiscoveryController;

        $qdiscover->index();
        if (defined("GLOBAL_DEBUG")) {
            var_dump($qdiscover->req);
            var_dump($qdiscover->params);
        }
    });

    Router::get("book/create", function() {
        // This should redirect user to login screen if unregistered.
        $qcreate = new BookController;

        $qcreate->getCreate();
        if (defined("GLOBAL_DEBUG")) {
            var_dump($qcreate->req);
            var_dump($qcreate->params);
        }
    });

    Router::post("book/create", function() {
        $qcreate = new BookController;
        $return = $qcreate->create();
        if (defined("GLOBAL_DEBUG")) {
            if ($return) {
                echo "SUCCESS";
            } else {
                echo "FAILED...";
            }
        }
    });

    Router::post("book/edit", function() {
        $book = new BookController;
        $result = $book->update();
    });

    Router::get("book/comment", function() {
        include "views/book/comment.php";
    });

    Router::post("book/comment", function() {
        $cmt = new CommentController;
        $result = $cmt->postComment();
    });

    Router::post("book/reply", function() {
        $cmt = new CommentController;
        $result = $cmt->postReply();
    });

    Router::post("book/cmtdelete", function() {
        $cmt = new CommentController;
        $result = $cmt->deleteComment();
    });

    Router::post("book/rate", function() {
        $book = new BookController;
        $result = $book->postRating();
    });

    Router::post("book/delete", function() {
        $book = new BookController;
        $result = $book->delete();
    });

    Router::post("book/reserve", function() {
        $reservation = new ReservationController;
        $result = $reservation->reserve();
    });

    Router::post("book/unreserve", function() {
        $reservation = new ReservationController;
        $result = $reservation->unreserve();
    });

    Router::post("book/return", function() {
        $reservation = new ReservationController;
        $result = $reservation->returnBook();
    });

    Router::post("book/confirmretrieve", function() {
        $reservation = new ReservationController;
        $result = $reservation->retrieveBook();
    });

    Router::post("book/confirmwithdraw", function() {
        $reservation = new ReservationController;
        $result = $reservation->retrieveBook();
    });

    Router::post("book/feature", function() {
        $book = new BookController;
        $result = $book->featureBook();
    });

    Router::get("book/dashboard", function() {
        $qdashboard = new BookController;
        $qdashboard->getDashboard();
    });

    if (!Router::$found) {
        Router::get_from("book", function($_, $params) {
            // Missing feature: This should redirect user back to discover page IF no book ID is provided OR book is private
            $book_id = $params[0];
            array_shift($params);
            $qpreview = new BookController;
            $qpreview->navigate($book_id, $params);
            if (defined("GLOBAL_DEBUG")) {
                var_dump($qpreview->req);
                var_dump($qpreview->params);
            }
        });
    }

    // --------------------------
    // API mock-ups
    //

    // User
    Router::get("api", function() {
        include "_api/intro.php";
    });

    Router::get("api/user", function() {
        include "_api/User.php";
    });

    Router::get("api/book", function() {
        include "_api/Book.php";
    });

    // default 404
    if(Router::$found === false) {
        include "views/_404.php";
    }

    $finalcallback->__invoke();
}