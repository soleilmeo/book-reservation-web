<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}
class UserProfileController extends Controller {
    // properties

    public function __construct() {
        parent::__construct();
    }

    public function view($userId) {
        $viewingUser = UserFactory::create($this->conn);
        $viewingUserId = $userId;

        if ($viewingUser->userIdExists($viewingUserId)) {
            $viewingUserInfo = $viewingUser->getUserData();
            $currentSession = new Session();
            $discoveryCarousel = new DiscoveryCarousel($this->conn);
            $bookmodel = new Book($this->conn);
            if (!$viewingUser->userProfileExistsById($viewingUserId)) {
                // Profile does not exist, create one then invoke the function again
                $viewingUser->createNewUserProfile($viewingUserId, $viewingUserInfo['username']);
                // If fail again then 404 time
                if (!$viewingUser->userProfileExistsById($viewingUserId)) include "views/_404.php";
                else {
                    $viewingUserProfile = $viewingUser->getProfileData();
                    include "views/user.php";
                }
            } else {
                $viewingUserProfile = $viewingUser->getProfileData();
                include "views/user.php";
            }
        } else {
            include "views/_404.php";
        }
    }

    public function viewEditor() {
        if (!Auth::loggedIn()) {
            include "views/_403.php";
            return;
        }

        $currentSession = new Session;
        $userId = $currentSession->getSessionUserId();

        if ($currentSession->getSessionPrivilege() >= PrivilegeRank::ADMIN && isset($_GET["userId"])) {
            $userId = $_GET["userId"];
        }

        $viewingUser = UserFactory::create($this->conn);
        $viewingUserId = $userId;

        if ($viewingUser->userIdExists($viewingUserId)) {
            $viewingUserInfo = $viewingUser->getUserData();
            if (!$viewingUser->userProfileExistsById($viewingUserId)) {
                // Profile does not exist, create one then invoke the function again
                $viewingUser->createNewUserProfile($viewingUserId, $viewingUserInfo['username']);
                // If fail again then 404 time
                if (!$viewingUser->userProfileExistsById($viewingUserId)) include "views/_404.php";
                else {
                    $viewingUserProfile = $viewingUser->getProfileData();
                    include "views/user_profile_edit.php";
                }
            } else {
                $viewingUserProfile = $viewingUser->getProfileData();
                include "views/user_profile_edit.php";
            }
        } else {
            include "views/_404.php";
        }
    }

    public function commitEdit() {
        if (!Auth::loggedIn()) {
            include "views/_403.php";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] != "POST") {
            Router::redirect("404");
            return;
        }

        $currentSession = new Session;
        $userId = $currentSession->getSessionUserId();

        if ($currentSession->getSessionPrivilege() >= PrivilegeRank::ADMIN && isset($this->req['target'])) {
            $userId = $this->req['target'];
        }

        $viewingUser = UserFactory::create($this->conn);
        $viewingUserId = $userId;

        $commitChanges = function($currentSession, $viewingUser, $viewingUserId, $viewingUserInfo) {
            $viewingUserProfile = $viewingUser->getProfileData();
            $result = $viewingUser->editProfile($viewingUserId, $this->req, $this->files);
            if (!$result) {
                Session::setDelivery("profileEditErr", $viewingUser->error);
            } else {
                Session::setDelivery("profileEditErr", "");
            }
            Session::setDelivery("profileEditResult", $result);

            if ($currentSession->getSessionPrivilege() >= PrivilegeRank::ADMIN) {
                // If user has high privilege
                Router::redirect("profile/edit", ["userId" => $viewingUserId]);
            } else {
                Router::redirect("profile/edit");
            }
        };

        if ($viewingUser->userIdExists($viewingUserId)) {
            $viewingUserInfo = $viewingUser->getUserData();
            if (!$viewingUser->userProfileExistsById($viewingUserId)) {
                // Profile does not exist, create one then invoke the function again
                $viewingUser->createNewUserProfile($viewingUserId, $viewingUserInfo['username']);
                // If fail again then 404 time
                if (!$viewingUser->userProfileExistsById($viewingUserId)) include "views/_404.php";
                else {
                    $commitChanges($currentSession, $viewingUser, $viewingUserId, $viewingUserInfo);
                }
            } else {
                $commitChanges($currentSession, $viewingUser, $viewingUserId, $viewingUserInfo);
            }
        } else {
            include "views/_404.php";
        }
    }

}