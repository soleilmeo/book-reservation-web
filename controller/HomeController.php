<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}
class HomeController extends Controller {
    // properties


    public function __construct() {
        //echo "child class instantiated";
        parent::__construct();
    }

    public function index() {
        $discoveryCarousel = new DiscoveryCarousel($this->conn);
        include "views/home.php";
    }

}

