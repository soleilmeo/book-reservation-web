<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}

class Session {
    public $session;

    public function __construct() {
        $this->session = $_SESSION;
    }

    public function update() {
        $_SESSION['last_update'] = time();
        $this->session = $_SESSION;
    }

    //
    public static function resetSession() {
        // Reset is NOT Refresh! This will reset current session. Usually occurs when user logs out.
        $_SESSION = [];
        session_destroy();
        
        $_SESSION['last_update'] = time();
    }

    public static function acknowledgeUpdate(array $sessionData = []) {
        foreach ($sessionData as $k => $v) {
            $_SESSION[$k] = $v;
        }
        $_SESSION['last_update'] = time();
    }

    // Temporary delivery without issuing GET requests - repercussions currently unknown
    // Q: Is this reliable?
    public static function setDelivery(string $packageName, $packageContents) {
        if (!isset($_SESSION['delivery']) || !is_array($_SESSION['delivery'])) {
            $_SESSION['delivery'] = [];
        }
        $_SESSION['delivery'][$packageName] = $packageContents;
    }

    public static function investigateDelivery($packageName, &$return) {
        // Check delivery and receive it.
        if (isset($_SESSION['delivery']) && count($_SESSION['delivery']) > 0) {
            if (!isset($_SESSION['delivery'][$packageName])) {
                return false;
            }
            $return = $_SESSION['delivery'][$packageName];
            unset($_SESSION['delivery'][$packageName]);
            return true;
        }
        return false;
    }

    public static function finishDelivery() {
        // Clears delivery. Automatically done by index.html, should be done manually if included anywhere else
        unset($_SESSION['delivery']);
    }
    //

    public function getSessionUserId() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
            if (isset($_SESSION['user_id'])) {
                return $_SESSION['user_id'];
            }
        }
        return false;
    }

    public function getSessionPrivilege() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
            if (isset($_SESSION['privilege'])) {
                return $_SESSION['privilege'];
            }
        }
        return false;
    }

    public function getSessionUsername() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
            if (isset($_SESSION['username'])) {
                return $_SESSION['username'];
            }
        }
        return false;
    }

    public function getSessionLastUpdate() {
        if (!isset($_SESSION['last_update'])) {
            $_SESSION['last_update'] = time();
        }
        return $_SESSION['last_update'];
    }
}
?>