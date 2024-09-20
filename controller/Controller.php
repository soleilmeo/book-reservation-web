<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}
class Controller {
    public $conn;
    public $req;
    public $params;
    public $files;

    public function __construct() {
        $this->conn = DB::getConn();
        $this->req = $_POST;
        $this->params = $_GET;
        $this->files = $_FILES;
       // CSRF::checkToken($this->req);
    }
}