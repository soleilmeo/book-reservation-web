<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}
// Read [and write - NOT IMPLEMENTED] .ini files usually used for configuration in this project
class Config {
    public static function read($filename, $property = NULL) {
        if ($property==NULL) return parse_ini_file($filename);
        else return parse_ini_file($filename)[$property];
    }
}
?>