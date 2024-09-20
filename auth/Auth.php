<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}

class Auth {
    public const AUTO_SESSION_REFRESH_CD = 2; // seconds

    public static function loggedIn() {
        // return a boolean
        if($_SESSION['logged_in']) {
            return true;
        } else {
            return false;
        }
    }

    public static function getCurrentSession($autoRefresh = true, $refreshSession = false) : Session {
        if (self::loggedIn()) {
            $currentSession = new Session;
            if ( $refreshSession || ($autoRefresh && abs(time() - $currentSession->getSessionLastUpdate()) > self::AUTO_SESSION_REFRESH_CD )) {
                // Refresh session of user
                $uc = new UserController;
                $uc->refreshSession();
            }
        }
        return new Session;
    }
}