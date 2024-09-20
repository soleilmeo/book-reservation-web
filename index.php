<?php
// Initialization
session_start();
//define("GLOBAL_DEBUG", true);

define("ROOT", substr($_SERVER['PHP_SELF'], 0,-9));

function removeSlashes($string)
{
    $string=implode("",explode("\\",$string));
    return stripslashes(trim($string));
}

include "enum/PrivilegeRank.php";
include "enum/ReservationStatus.php";

include "util/Config.php";
include "util/Placeholder.php";
include "util/UUID.php";

include "config.php";

include "auth/Session.php";
if(!isset($_SESSION['logged_in'])) {
    $_SESSION['logged_in'] = false;
}

include "core/DB.php";
DB::createInstance();
DB::connect(GlobalConfig::HOSTNAME, "root", "", GlobalConfig::GENERAL_DB);

include "models/IUser.php";
include "factories/UserFactory.php";

include "controller/Controller.php";
include "controller/HomeController.php";
include "controller/UserController.php";
include "controller/UserProfileController.php";
include "controller/ReservationController.php";
include "controller/book/DiscoveryController.php";
include "controller/book/BookController.php";
include "controller/CommentController.php";

include "models/Book.php";
include "models/DiscoveryCarousel.php";

include "middleware/CSRF.php";
include "auth/Auth.php";

include "core/Router.php";
include "core/web.php";

// When pages finished offering deliveries, discard any undelivered packages
web(function() {
    if ($_SERVER['REQUEST_METHOD'] != "POST") Session::finishDelivery();
});

//var_dump(Config::read("info.ini"));
?>