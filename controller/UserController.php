<?php
if (!defined('ROOT')) {
    http_response_code(404);
    header("Location: /404");
    exit;
}
class UserController extends Controller
{

    public $errors = [];

    public function __construct()
    {

        parent::__construct();
    }

    public function getLogin()
    {
        if (!Auth::loggedIn()) {
            $user = new User($this->conn);
            $user->initAdmin(); // Create admin if there's none
            include "views/login.php";
        } else {
            Router::redirect("");
        }
    }

    public function login()
    {
        echo "<br>logging in user<br>";
        var_dump($this->req);
    }

    public function create()
    {
        //extract out vars from $this->req  username = $this->req['username']
        $user = new User($this->conn);
        $username = $this->req['username']; // req == $_POST['username']
        $pw = $this->req['password'];
        $email = $this->req['email'];
        $pw_confirm = $this->req['password-confirm'];
        // to check if username exists create a new User model, (check existing methods)
        // check if username exists (it shouldn't)
        if ($user->userExists($username)) {
            $this->errors['username'] = "Username already exists.";
        }

        // does username contain invalid characters? (only allow letters, numbers and certain characters)
        if (!self::isNameValid($username)) {
            $this->errors['specialchars'] = "Name must have no special characters (exceptions: ._- (dot, underscore and minus)).";
        }

        // check username and pw
        if (strlen($username) < 3) {
            $this->errors['userlength'] = "Username must have at least 3 characters or more.";
        }

        if (strlen($pw) < 8) {
            $this->errors['pwlength'] = "Password must have at least 8 characters or more.";
        }

        // validate user email filter_var($email, FILTER_VALIDATE_EMAIL)
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors['email'] = "Email is invalid.";
        }

        // check passwords match
        if ($pw != $pw_confirm) {
            $this->errors['password_match'] = "Password confirmation does not match with your proposed password.";
        }
        if (empty($this->errors)) {
            // call createNewUser method on the User Model
            // don't forget to hash the password before insert
            $result = $user->createNewUser($username, $pw, $email);
            if ($result == false) {
                if (defined("GLOBAL_DEBUG")) var_dump($result);
                else {
                    Session::setDelivery("loginErr", "Unknown error occurred while creating an account.");
                    Router::redirect("login");
                }
            } else {
                Session::acknowledgeUpdate([
                    'logged_in' => true,
                    'username' => $username,
                    'user_id' => $result,
                    'privilege' => 0
                ]);
                header("Location:" . ROOT . "?msg=Successful-login");
            }
        } else {
            Session::setDelivery("loginErr", 'Account creation failed due to the following reasons:<ul class="mb-0"><li>'.implode('</li><li>', $this->errors).'</li></ul>');
            header("Location:" . ROOT . "login");
        }
    }

    public function validateLogin()
    {
        var_dump($this->req); // the $this->req === $_POST
        $user = UserFactory::create($this->conn);
        if ($user->userExists($this->req['username'])) {
            $userdata = $user->getUserData();
            if (password_verify($this->req['password'], $userdata['password'])) {
                // Check if user is banned
                $isBanned = $userdata['user_privilege_rank'] < 0;
                if ($isBanned) {
                    Session::setDelivery("loginErr", "Access denied: User account has low permission level.");
                    Router::redirect("login");
                    return;
                }

                Session::acknowledgeUpdate([
                    'logged_in' => true,
                    'username' => $userdata['username'],
                    'user_id' => $userdata['user_id'],
                    'privilege' => $userdata['user_privilege_rank']
                ]);
                Router::redirect("?msg=login-successful");
            } else {
                Session::setDelivery("loginErr", "Login failed: Incorrect password!");
                Router::redirect("login");
            }
        } else {
            // user not found error
            //echo "User not found";
            Session::setDelivery("loginErr", "Login failed: User not found!");
            Router::redirect("login");
        }
    }

    public function refreshSession($refreshPage = false)
    {
        if (Auth::loggedIn()) {
            $user = UserFactory::create($this->conn);
            if ($user->userIdExists($_SESSION['user_id'])) {
                $userdata = $user->getUserData();
                Session::acknowledgeUpdate([
                    'logged_in' => true,
                    'username' => $userdata['username'],
                    'user_id' => $userdata['user_id'],
                    'privilege' => $userdata['user_privilege_rank']
                ]);
                if ($refreshPage) {
                    if (isset($_GET['url'])) header("Location:" . $_GET['url']);
                    else echo "<script>window.location.reload()</script>";
                }
                return $user;
            }
        }
        return false;
    }

    public function isNameValid($username)
    {
        return !preg_match('/[^A-Za-z0-9._-]/', $username);
    }
}
