<?php
class Member implements IUser {
    private $base;

    public function __construct($conn) {
        $this->base = new User($conn);
    }

    public function initAdmin() {
        $this->base->initAdmin();
    }

    public function userExists($username) {
        return $this->base->userExists($username);
    }

    public function userIdExists($userId) {
        return $this->base->userIdExists($userId);
    }

    public function createNewUser($username, $pw, $email, $privilege = 0) {
        return $this->base->createNewUser($username, $pw, $email, $privilege);
    }

    public function createNewUserProfile($userId, $username) {
        // warning: this function is not sanitized since this is called within a pre-sanitized function
        // so either don't use this outside that or if you do for some reason pls add k thx bye
        return $this->base->createNewUserProfile($userId, $username);
    }

    public function userProfileExistsById($userId) {
        return $this->base->userProfileExistsById($userId);
    }

    public function editProfile($userId, $post, $files) {
        $currentSession = Auth::getCurrentSession();
        $currentUserId = $currentSession->getSessionUserId();
        if ($currentSession->getSessionPrivilege() < PrivilegeRank::ADMIN && $currentUserId != $userId) {
            $this->base->error = "Edit permission denied.";
            return false;
        }

         return $this->base->editProfile($userId, $post, $files);
    }

    public function saveProfile($userId, $newValues, $deleteValues = []) {
        $currentSession = Auth::getCurrentSession(); // modified
        if ($userId != $currentSession->getSessionUserId() && $currentSession->getSessionPrivilege() <  PrivilegeRank::ADMIN) {
            return false;
        }

        return $this->base->saveProfile($userId, $newValues, $deleteValues);
    }

    public function getError() {
        return $this->base->getError();
    }

    public function getUserData() {
        return $this->base->getUserData();
    }

    public function getProfileData() {
        return $this->base->getProfileData();
    }
}