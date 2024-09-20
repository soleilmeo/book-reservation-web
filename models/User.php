<?php
class User implements IUser {
    public $conn;
    public $error = "";
    public $errors = [];
    public $user = [];
    public $profile = [];
    public $users = [];
    public $username;
    public $user_email;
    private $hash;
    public $user_role;

    public const DISPLAY_NAME_MAX_CHARS = 28;
    public const SHORTDESC_MAX_CHARS = 126;
    public const DESC_MAX_CHARS = 2000;
    public const LIMIT_ERROR_MARGIN = 255;
    public const AVATAR_SAVE_PATH = "assets/u/avatar/";
    public const MAX_COLLISION = 10;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private static function calculateLimitWithErrorMargin($limit) {
        return ceil($limit + (self::LIMIT_ERROR_MARGIN * ($limit / self::LIMIT_ERROR_MARGIN)));
    }

    public function initAdmin() {
        $sql = "SELECT * from users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows < 1) {
            $this->createNewUser("libraria", "itec2024", "admin@mail.example", 1);
        }
    }

    public function userExists($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);

        $username_f = strtolower($username);
        $username_f = filter_var($username_f, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username_f = filter_var($username_f, FILTER_SANITIZE_URL);

        $stmt->bind_param("s", $username_f);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->user = $result->fetch_assoc(); // grab assoc array if user exists else empty
        if(!empty($this->user)) {
            return true; // user found
        }  else {
            return false; // user not found
        }
    }

    public function userIdExists($userId) {
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);

        // Unsanitized data. take caution

        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->user = $result->fetch_assoc(); // grab assoc array if user exists else empty
        if(!empty($this->user)) {
            return true; // user found
        }  else {
            return false; // user not found
        }
    }

    public function createNewUser($username, $pw, $email, $privilege = 0) {
        $username_f = $username;
        $username_f = filter_var($username_f, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username_f = filter_var($username_f, FILTER_SANITIZE_URL);
        if ($username != $username_f) return false; // Contains invalid characters

        // hash the password
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        // create sql statement for profile creation
        // create sql statement with ???? placeholders
        $sql = "INSERT INTO users(username, password, email, user_privilege_rank) VALUES(?,?,?,?)";

        // create stmt and call prepate method (on $this->conn)
        $stmt = $this->conn->prepare($sql);
        // bind param
        $stmt->bind_param("ssss", $username, $hash, $email, $privilege);
        // execute  
        $stmt->execute();
        // if affected_rows === 1 success , else error
        if($stmt->affected_rows === 1) {
            $insert_id = $stmt->insert_id;
            $this->createNewUserProfile($insert_id, $username_f);
            return $insert_id;
        } else {
            return false;
        }
    }

    public function createNewUserProfile($userId, $username) {
        // warning: this function is not sanitized since this is called within a pre-sanitized function
        // so either don't use this outside that or if you do for some reason pls add k thx bye
        $sql = "INSERT INTO user_info(user_id, display_name) VALUES(?,?)";

        // create stmt and call prepate method (on $this->conn)
        $stmt = $this->conn->prepare($sql);
        // bind param
        $stmt->bind_param("ss", $userId, $username);
        // execute  
        $stmt->execute();
        // if affected_rows === 1 success , else error
        if($stmt->affected_rows === 1) {
            return true; // success
        } else {
            return false; // failure
        }
    }

    public function userProfileExistsById($userId) {
        $sql = "SELECT * FROM user_info WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);

        // Unsanitized data. take caution

        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->profile = $result->fetch_assoc(); // grab assoc array if user exists else empty
        if(!empty($this->profile)) {
            return true; // user profile found
        }  else {
            return false; // user profile not found
        }
    }

    public function editProfile($userId, $post, $files) {
         // Is user present?
         if (!Auth::loggedIn()) {
            $this->error = "User session expired.";
            return false;
        }

        $proceedExecution = true;

        // DISPLAY NAME (REQUIRED)

        $DNAME_MAX = self::calculateLimitWithErrorMargin(self::DISPLAY_NAME_MAX_CHARS);
        $displayname_f = filter_var($post["display_name"], FILTER_SANITIZE_SPECIAL_CHARS);
        $displayname_f = strlen(trim($displayname_f)) <= $DNAME_MAX ? $displayname_f : false;
        if ($displayname_f === false) {
            $this->error = "Display name must be below ".$DNAME_MAX."characters.";
            return false;
        }
        if ($displayname_f !== false && strlen(trim($displayname_f)) < 1) {
            $this->error = "Display name field is required.";
            return false;
        }

        // SHORT DESCRIPTION (OPTIONAL)
        $SDESC_MAX = self::calculateLimitWithErrorMargin(self::SHORTDESC_MAX_CHARS);
        $shortdesc_f = filter_var($post["shortdesc"], FILTER_SANITIZE_SPECIAL_CHARS);
        $shortdesc_f = strlen(trim($shortdesc_f)) <= $SDESC_MAX ? $shortdesc_f : false;
        if ($shortdesc_f === false) {
            $this->error = "Tagline must not exceed ".$SDESC_MAX."characters.";
            return false;
        }

        // DESCRIPTION (OPTIONAL)
        $DESC_MAX = self::calculateLimitWithErrorMargin(self::DESC_MAX_CHARS);
        $desc_f = filter_var($post["description"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $desc_f = strlen(trim($desc_f)) <= $DESC_MAX ? $desc_f : false;
        if ($desc_f === false) {
            $this->error = "Description exceeded maximum limit.";
            return false;
        }

        // AVATAR (OPTIONAL, though cannot be removed once set)

        $hasAvatar = true;
        // Are you there?
        if (!isset($files["avatar"])) {
            if (defined("GLOBAL_DEBUG")) {
            }
            $hasAvatar = false;
        }

        if ($hasAvatar) {
            // Make sure everything is OK
            if ($files["avatar"]['error'] != 0) {
                $this->error = "Uploaded avatar is invalid.";
                $hasAvatar = false;
            }
        }

        $target_dir = self::AVATAR_SAVE_PATH;
        $allowUpload = true;
        // Target save path on the server
        $target_file   = "";
        if ($hasAvatar) {
            // Upload time!
            // Extension
            $imageFileType = pathinfo(basename($files["avatar"]["name"]), PATHINFO_EXTENSION);
            try {
                $target_file   = $target_dir . UUID::v4(true) . "." . $imageFileType;
            } catch (Exception $e) {
                $this->error = "Exception thrown while uploading file, details: " . $e;
                $proceedExecution = false;
            }
            if (!$proceedExecution) return false;
            $maxfilesize = 5242880; //5MB
            $allowtypes = array('jpg', 'png', 'jpeg', 'jfif', 'pjpeg', 'pjp');
            $maxCollisions = self::MAX_COLLISION;

            // Image?
            $check = getimagesize($files["avatar"]["tmp_name"]);
            if ($check !== false) {
                $allowUpload = true;
            } else {
                $this->error = "Avatar is not an image.";
                $allowUpload = false;
            }

            // Name collision?
            try {
                while (file_exists($target_file) && $maxCollisions-- >= 0) {
                    $target_file = $target_dir . UUID::v4(true) . "." . $imageFileType;
                }
            } catch (Exception $e) {
                $this->error = "Exception thrown while uploading file at checking stage, details: " . $e;
                $proceedExecution = false;
            }
            if (!$proceedExecution) {
                return false;
            }
            if (file_exists($target_file)) {
                $this->error = "Max collisions exceeded, avatar cannot be uploaded.";
                $allowUpload = false;
                return false;
            }

            // Size?
            if ($files["avatar"]["size"] > $maxfilesize) {
                $this->error = "File cannot exceed $maxfilesize bytes in size.";
                $allowUpload = false;
                return false;
            }

            // File type checking
            if (!in_array($imageFileType, $allowtypes)) {
                $this->error = "Unsupported file type, currently only support the following: JPG, PNG, JPEG, JFIF, PJPEG, PJP";
                $allowUpload = false;
                return false;
            }
            if (!$proceedExecution) return false;
            // Everything seems to be OK! File transferring to server will be done last assuming there's nothing else in the way
        } else {
            $allowUpload = false;
        }

        if (!$proceedExecution) return false;

        // Transfer file to server. This should be done last
        if ($allowUpload) {
            // Move to server
            if (move_uploaded_file($files["avatar"]["tmp_name"], $target_file)) {
                if (defined("GLOBAL_DEBUG")) {
                    // This interferes with request output. Only use when really need it
                    //echo "Upload success";
                }
            } else {
                $this->error = "An error occurred while trying to upload avatar.";
                $proceedExecution = false;
            }
        }
            /*
        } else {
            $this->error = "No upload specified or upload is ignored, skipping file uploading";
        }
        */

        if (!$proceedExecution) return false;

        // Update profile.
        $avatar_img_path = $target_file; // No need to Router::toUrl since the main page will direct that to the root, database doesn't need to know that
        if (!($hasAvatar && $allowUpload)) {
            $avatar_img_path = null;
        }

        $newValues = [
            "display_name" => $displayname_f,
            "user_image" => $avatar_img_path,
            "user_shortdesc" => $shortdesc_f,
            "user_desc" => $desc_f
        ];
        return $this->saveProfile($userId, $newValues);
    }

    public function saveProfile($userId, $newValues, $deleteValues = []) {
        /*
        example
        $newValues = [
            "user_desc" => "hello"
        ]

        $deleteValues = ["user_image", "user_shortdesc"]

        deleteValues does not accept display_name.
        */
        
        if (is_null($newValues) || empty($newValues)) {
            return true;
        }

        $changes = [
            "display_name" => (isset($newValues["display_name"]) ? $newValues["display_name"] : null),
            "user_image" => (isset($newValues["user_image"]) ? $newValues["user_image"] : null),
            "user_shortdesc" => (isset($newValues["user_shortdesc"]) ? $newValues["user_shortdesc"] : null),
            "user_desc" => (isset($newValues["user_desc"]) ? $newValues["user_desc"] : null)
        ];

        $setstatement = "SET ";
        $settypes = "s";
        $setvalues = [];
        $firstEntry = true;
        foreach ($changes as $key => $value) {
            if (in_array($key, $deleteValues, true)) {
                array_push($setvalues, null);
            } else {
                if (is_null($value)) continue;
                else array_push($setvalues, $value);
            }
            if (!$firstEntry) {
                $setstatement = $setstatement.", ";
            } else {
                $firstEntry = false;
            }
            $setstatement = $setstatement.$key." = ? ";
            $settypes = $settypes."s";
        }
        if (empty($setvalues)) return true;

        $sql = "UPDATE user_info ".$setstatement."WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);

        switch ($settypes) {
            case 'ss':
                $stmt->bind_param($settypes, $setvalues[0], $userId);
                break;
            
            case 'sss':
                $stmt->bind_param($settypes, $setvalues[0], $setvalues[1], $userId);
                break;
            
            case 'ssss':
                $stmt->bind_param($settypes, $setvalues[0], $setvalues[1], $setvalues[2], $userId);
                break;
            
            case 'sssss':
                $stmt->bind_param($settypes, $setvalues[0], $setvalues[1], $setvalues[2], $setvalues[3], $userId);
                break;
                
            default:
                return true;
        }

        $stmt->execute();
        if ($stmt->affected_rows >= 1) {
            return true;
        } else {
            $this->error = "No changes were made.";
            return false;
        }
    }

    public function getError() {
        return $this->error;
    }

    public function getUserData() {
        return $this->user;
    }

    public function getProfileData() {
        return $this->profile;
    }
}