<?php
include "models/User.php";
include "models/user/Member.php";
include "models/user/Admin.php";

class UserFactory {
    // IMPORTANT If user tries to modify something that needs permissions (privilege) remember to use UserFactory to give them valid perms!!!
    // (see IUser to know which ones need UserFactory)

    // It's OK to do "new User()" otherwise, but it'll be more confusing so don't do that

    public static function create($db_conn): IUser {
        $currentSession = Auth::getCurrentSession(false);
        switch ($currentSession->getSessionPrivilege()) {
            default:
            case false:
            case 0:
                // Normal User
                return new Member($db_conn);
            case 1:
                // Admin
                return new Admin($db_conn);
        }
    }
}