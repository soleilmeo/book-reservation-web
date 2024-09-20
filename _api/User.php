<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    exit;
}

// Basic User API
if (isset($_GET['id'])) {
    include "_api/headers/jsonstd.php";

    $userId = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $user = UserFactory::create(DB::getConn());
    if ($user->userIdExists($userId) && $user->userProfileExistsById($userId)) {

        $userdata = $user->getUserData();
        $userprofile = $user->getProfileData();

        $json = json_encode([
            "username" => $userdata["username"],
            "displayName" => $userprofile["display_name"],
            "userProfileImage" => $userprofile["user_image"],
            "userId" => $userdata["user_id"],
            "userPrivilege" => $userdata["user_privilege_rank"],
            "joinDate" => $userdata["creation_date"],

            "shortDesc" => Placeholder::put($userprofile["user_shortdesc"], ""),
            "description" => Placeholder::put($userprofile["user_desc"], ""),
            // Do not use these! Use Book API to fetch them instead.
            //"createdBookCount" => $user->profile["book_count"],
            //"createdBooks" => Placeholder::put(json_decode($user->profile["created_books"]), [])
        ]);
        if ($json === false) {
            http_response_code(500);
        } else {
            echo $json;
        }
        return;
    } else {
        http_response_code(404);
        return;
    }
}

?>

<section>
    <h2>Libraria User API</h2>
    <p><b>GET</b> /api/user?id={id} - Gets user from User ID</p>
</section>
<hr>
<p>Libraria Ltd. 2024</p>