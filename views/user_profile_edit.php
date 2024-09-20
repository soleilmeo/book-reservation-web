<?php
$PAGE_TITLE = "Edit profile: ".$viewingUserProfile['display_name']." - ".GlobalConfig::BASE_WEB_TITLE;
$HEADER_INCLUDES = '<link rel="stylesheet" href= "'.ROOT.'assets/css/profile-editor.css">';
include "views/includes/header.php"
?>

<?php
$profileEditResult = false;
$profileEditErr = "";
$profilePreviouslyEdited = Session::investigateDelivery("profileEditResult", $profileEditResult) && Session::investigateDelivery("profileEditErr", $profileEditErr);
if (!$profilePreviouslyEdited) {
    $profileEditResult = false;
    $profileEditErr = "";
}
?>

<script>
const __profileEditReport = {
    /*crsf: "",*/
    invoked: <?php echo $profilePreviouslyEdited ? 'true' : 'false' ?>,
    success: <?php echo $profileEditResult ? 'true' : 'false' ?>,
    err: "<?php echo $profileEditErr ?>"
};
</script>

<div class="container pt-4">
    <a href="javascript:void(0)" onclick="history.back()"><i class="fa fa-arrow-left mr-1"></i> Go back</a>
    <h2 class="mt-3">Editing <a class="text-secondary" href="<?php Router::url("user/".$viewingUserId) ?>">@<?php echo $viewingUserInfo["username"]; ?></a>'s profile</h2>
    <?php if($viewingUserId != $currentSession->getSessionUserId()): ?>
    <div class="alert alert-info">
        <p class="mb-0"><i class="fa fa-user-edit mr-1"></i> You are currently editing another user's profile. Proceed at your own risk.</p>
    </div>
    <?php endif; ?>
    <div id="TIPS" class="mt-3">
        <ul>
            <li>To change your avatar, click on the dashed circle with your avatar. If no avatar is set, it will be blank.</li>
            <li>You cannot unset your avatar once saved.</li>
            <li>Fields with <span class="text-danger">*</span> are required and should not be left blank.</li>
            <li>Please follow the community guidelines! We haven't written it anyway so you don't have to worry about it yet.</li>
        </ul>
    </div>
    <form class="mt-3" id="profile-editor-form" action="<?php echo ROOT;?>profile/edit" method="post" enctype="multipart/form-data">
        <input type="hidden" name="target" value="<?php echo $viewingUserId ?>">
        <div class="row px-4">
            <div class="col-md-9 pr-md-5 pr-0 pl-0">
                <div class="form-group">
                    <label for="p-name">Display Name<span class="text-danger">*</span> <i class="text-black-50">(up to 28 characters)</i></label>
                    <input id="p-name" class="form-control" type="text" name="display_name" placeholder="Your desired display name..." autocomplete="off" value="<?php echo $viewingUserProfile["display_name"] ?>" minlength="1" maxlength="28" required>
                </div>

                <div class="form-group">
                    <label for="p-shortdesc">Tagline <i class="text-black-50">(up to 126 characters)</i></label>
                    <input id="p-shortdesc" class="form-control" type="text" name="shortdesc" placeholder="Your catchphrase, signature speech..." autocomplete="off" value="<?php echo $viewingUserProfile["user_shortdesc"] ?>" maxlength="126">
                </div>
            </div>

            <div class="col-md-3 text-center">
                <div class="form-group d-flex flex-column">
                    <input id="p-pfp" type="file" name="avatar" id="fileupload" accept="image/png,image/jpeg" hidden>
                    <label class="profile-pfp-slot align-self-center position-relative cover" for="p-pfp">
                        <img id="p-pfp-preview" src="<?php echo ($viewingUserProfile['user_image'] ? Router::toUrl($viewingUserProfile['user_image']) : Router::toUrl("assets/picture/pfp-default.png")) ?>" class="profile-preview-pfp no-interact"></img>

                        <div class="profile-pfp-slot-design"></div>
                        <p class="mb-0" style="translate:0 -150%">
                        <i class="fa fa-user-circle"></i><br>
                        Change Avatar<br>(Max 5MB)
                        </p>
                    </label>
                </div>
                <p id="avatar-feedback"><i class="fa fa-question-circle mt-1"></i> No new avatar selected</p>
                <!--<p><span class="text-success"><i class="fa fa-check mt-1"></i> Avatar OK </span><a id="avt-revert" class="badge badge-info user-select-none"><i class="fa fa-undo"></i></a></p>-->
                <!--<p><span class="text-danger"><i class="fa fa-times mt-1"></i> Invalid format type</span></p>-->
                <!--<p><span class="text-danger"><i class="fa fa-times mt-1"></i> File must be less than 5MB</span></p>-->
            </div>
        </div>

        <hr>

        <div class="form-group">
            <label for="p-desc">Description <i class="text-black-50">(up to 2000 characters)</i></label>
            <textarea id="p-desc" name="description" class="web-resize-none w-100" rows="10" maxlength="2000"><?php echo $viewingUserProfile["user_desc"] ?></textarea>
        </div>

        <p class="text-center">Please double check your information before saving. Once saved, every changes made cannot be undone.</p>

        <?php CSRF::outputToken();?>
        <button id="save_btn" type="submit" class="btn btn-success btn-block btn-lg" name="confirm-save"><i class="fa fa-save mr-1" aria-hidden="true"></i> Confirm Save</button>
    </form>
</div>

<?php
$EXTRA_FOOTER_INCLUDES = '<script src="'.ROOT.'assets/js/profile-editor.js"></script>';
include "views/includes/footer.php"
?>