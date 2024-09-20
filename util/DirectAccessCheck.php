<?php
// Deflect direct access to library files (which should not be placed in public folder in the first place...)
// Do not include this anywhere in the project. This is just a sample.
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}
?>