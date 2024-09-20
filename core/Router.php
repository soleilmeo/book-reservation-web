<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}
class Router {
    // static properties
    public static $route;
    public static $url;
    public static $found = false;
    public static $param;
    public static $uriParam;


    public static function get($route, $function) {
        self::$route = $route;
        if(isset($_GET['url'])) {
            self::$url = $_GET['url'];
        } else {
            self::$url = "";
        }
        self::getParam();

        if((self::$route == self::$url || self::$route."/" == self::$url) && $_SERVER['REQUEST_METHOD'] == "GET") {
            self::$found = true;
            $function->__invoke(self::$param);
        } 
    }

    // Check if webpage has router-like uri params and run it accordingly
    // For example user want to view profile of a user with user ID = 2, user goes to /user/2, therefore baseRoute "user" should be checked with "2" as parameter
    public static function get_from($baseRoute, $function) {
        if(isset($_GET['url'])) {
            self::$url = $_GET['url'];
        } else {
            self::$url = "";
        }
        self::$route = self::$url;

        self::getParam();
        
        if(/*preg_match('/^\/?'.filter_var($baseRoute, FILTER_SANITIZE_ADD_SLASHES).'\/([a-z0-9].*?(?=[\/?#\s]|$))/', self::$route, $matches)*/
        preg_match_all('/(?:[^\/\n]|\/\/)+/i', self::$route, $matches) && $_SERVER['REQUEST_METHOD'] == "GET") {
            $matches = $matches[0];
            if (count($matches) > 1) {
                if ($baseRoute == "") {
                    // Base route is root
                    self::$found = true;
                    array_shift($matches);
                    self::$uriParam = $matches;
                    $function->__invoke(self::$param, self::$uriParam);
                } else {
                    // Base route is not root but another thing
                    $assumeBaseRoute = $matches[0];
                    if ($assumeBaseRoute == $baseRoute) {
                        self::$found = true;
                        array_shift($matches);
                        self::$uriParam = $matches;
                        $function->__invoke(self::$param, self::$uriParam);
                    }
                }
            }
        }
    }

    public static function get_among($routeList, $function) {
        foreach ($routeList as $route) {
            self::$route = $route;
            if(isset($_GET['url'])) {
                self::$url = $_GET['url'];
            } else {
                self::$url = "";
            }

            self::getParam();

            if((self::$route == self::$url || self::$route."/" == self::$url) && $_SERVER['REQUEST_METHOD'] == "GET") {
                self::$found = true;
                $function->__invoke(self::$param);
            } 
        }
    }

    public static function post($route, $function) {
        self::$route = $route;
        if(isset($_GET['url'])) {
            self::$url = $_GET['url'];
        } else {
            self::$url = "";
        }

        if((self::$route == self::$url || self::$route."/" == self::$url) && $_SERVER['REQUEST_METHOD'] == "POST") {
            self::$found = true;
            $function->__invoke();
        } 
    }

    public static function getParam() {
        if(stripos(self::$route, "{") !== false) {
            // explode the route and url so they can be matched
            $routeArr = explode("/", self::$route);
            $urlArr = explode("/", self::$url);
            // var_dump(self::$route);
            // var_dump($routeArr);
            // var_dump(self::$url);
            // var_dump($urlArr);
            // extract the dynamic value from the url (ie "1")
            self::$param = end($urlArr);
            // var_dump(self::$param);
            // remove the wildcard placeholder from the route (ie "{id})
            array_pop($routeArr);
            //add the dynamic value $param to the route in place of the
            // wildcard
            array_push($routeArr, self::$param);
            // var_dump($routeArr);
            // convert route and url back to string so they
            // can be compared
            self::$route = implode("/", $routeArr);
            self::$url = implode("/", $urlArr);
            // var_dump(self::$route);
            // var_dump(self::$url);
  
        }
    }

    public static function redirect($url, array $get = []) {
        $params = "";
        if (count($get) > 0) {
            $params = "?";
            foreach ($get as $k => $v) {
                if ($params !== "?") {
                    $params = $params."&";
                }
                $params = $params.htmlspecialchars($k."=".urlencode($v));
            }
        }
        header("Location: ".ROOT.$url.$params);
    }

    public static function assignGetParams($url, array $get = []) {
        $params = "";
        if (count($get) > 0) {
            $params = "?";
            foreach ($get as $k => $v) {
                if ($params !== "?") {
                    $params = $params."&";
                }
                $params = $params.htmlspecialchars($k."=".urlencode($v));
            }
        }
        return ROOT.$url.$params;
    }
    
    public static function toUrl($url = "") {
        return ROOT.$url;
    }

    public static function url($url = "") {
        echo ROOT.$url;
    }
}