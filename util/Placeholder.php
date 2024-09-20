<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}
// Putting placeholder on empty strings
class Placeholder {
    public static function put($v, $placeholder = "Nothing") {
        if (!isset($v) || (is_string($v) && strlen($v) < 1)) return $placeholder;
        else return $v;
    }
}
?>