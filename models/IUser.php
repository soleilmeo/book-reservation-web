<?php

interface IUser {
    public function initAdmin();

    public function userExists($username);

    public function userIdExists($userId);

    public function createNewUser($username, $pw, $email, $privilege = 0);

    public function createNewUserProfile($userId, $username);

    public function userProfileExistsById($userId);

    public function editProfile($userId, $post, $files); // Needs permissions

    public function saveProfile($userId, $newValues, $deleteValues = []); // Needs permissions

    public function getError();

    public function getUserData(); // Only access this data after any call of userExists and userIdExists!

    public function getProfileData(); // Only access this data after any call of userProfileExistsById!
}