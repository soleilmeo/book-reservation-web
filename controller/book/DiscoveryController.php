<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}
class DiscoveryController extends Controller {
    // properties


    public function __construct() {
        parent::__construct();
    }

    public function index() {
        include "views/book/discover.php";
    }

}