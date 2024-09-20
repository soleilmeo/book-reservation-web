<?php
$PAGE_TITLE = $viewingUserProfile['display_name']." - ".GlobalConfig::BASE_WEB_TITLE;
include "views/includes/header.php" ?>

<div class="profile-bg" style="background-image:linear-gradient(rgba(0,0,0,0), 60%, white), url(<?php Router::url("assets/picture/banner-profile.png")?>);">

</div>

<div class="container bg-white shadow px-md-5 px-4 py-4 mb-3 position-relative" style="margin-top: -25vh; border-radius: 16px;">
    <?php if(Auth::loggedIn() && ($currentSession->getSessionUserId() == $viewingUserId || $currentSession->getSessionPrivilege() >= PrivilegeRank::ADMIN)): ?>
        <a class="position-absolute text-white shadow pr-4" style="right:0; margin-top:-4rem;" href="<?php Router::url("profile/edit".($currentSession->getSessionPrivilege() >= PrivilegeRank::ADMIN ? "?userId=".$viewingUserId : "")) ?>"><i class="fa fa-edit"></i> Edit Profile</a>
    <?php endif; ?>

    <?php if($viewingUserInfo["user_privilege_rank"] < 0): ?>
        <div class="alert alert-danger">User is banned</div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-2">
            <img
            style="background-image: url('<?php echo ($viewingUserProfile['user_image'] ? Router::toUrl($viewingUserProfile['user_image']) : Router::toUrl("assets/picture/pfp-default.png")) ?>');"
            class="profile-pfp profile-pfp-big"></img>
        </div>

        <div class="col-md-10 pl-4 pr-md-0 pr-4 mt-md-0 mt-2">
            <h2><?php echo $viewingUserProfile['display_name']?>
            <?php if($viewingUserInfo["user_privilege_rank"] > 0): ?>
            <img class="no-interact" src="<?php Router::url(); ?>assets/badges/admin.ico"></img></h2>
            <?php endif;?>

            <h4 class="text-secondary">@<?php echo $viewingUserInfo['username']?></h4>

            <p id="SHORTDESC" class="lead">"<?php
                echo Placeholder::put($viewingUserProfile['user_shortdesc'], "Status unknown")
                ?>"
            </p>
        </div>
    </div>
    
    <hr>

    <?php
    $__userdescription_html = $viewingUserProfile['user_desc'];
    ?>
    <div id="TRIMMEDDESC">
        <p>
        <?php
        // Shorten string after a certain amount of characters or after exceeding a certain number of line breaks
        $__desc_shortened = false;
        $__newline_shortened = false;
        if (substr_count($__userdescription_html, "\n") >= 4) {
            // Find location of the 4th occurence of <br>
            $location = strpos($__userdescription_html, "\n");
            for ($i=0; $i < 4; $i++) { 
                $offset = $location;
                $location = strpos($__userdescription_html, "\n", $offset + 1);
                if (!$location || $location < $offset) {
                    $location = $offset;
                    break;
                }
            }
            $location = min($location, 500);
            echo nl2br(trim(substr($__userdescription_html, 0, $location)).($location >= 500 ? "..." : "")."\n");
            $__newline_shortened = true;
            $__desc_shortened = true;
        }

        if (strlen($__userdescription_html) > 500 && !$__newline_shortened) {
            echo nl2br(trim(substr($__userdescription_html, 0, 500))."...\n");
            $__desc_shortened = true;
        }
        ?>
        </p>
        
        <?php if($__desc_shortened): ?>
            <p class="text-center"><a class="text-primary" id="desc-readmore" style="cursor: pointer;"><i class="fa fa-chevron-down"></i> Read more</a></p>
        <?php endif; ?>
    </div>
    <div id="LONGDESC" <?php if($__desc_shortened): ?>style="display: none;"<?php endif; ?>>
        <p>
        <?php
        echo nl2br($__userdescription_html);
        ?>
        </p>
        
        <?php if($__desc_shortened): ?>
            <p class="text-center"><a class="text-primary" id="desc-readless" style="cursor: pointer;"><i class="fa fa-chevron-up"></i> Read less</a></p>
        <?php endif; ?>
        </div>

    <hr>

    <div class="d-flex flex-md-row flex-column align-items-center justify-content-around lead py-3">
        <?php
        $userBookCount = $bookmodel->countPublishedBooksOfUser($viewingUserId);
        $userBookRating = ceil($bookmodel->calculateUserRatingScore($viewingUserId) * 1000)/10;
        ?>
        <p class="px-4"><?php echo $userBookCount." book".($userBookCount == 1 ? "" : "s")." available" ?></p>
        <p class="px-4"><?php echo $userBookRating."% overall rating" ?></p>
    </div>

    <hr>

    <h4>Best Books<span class="text-black-50 lead"> from <?php echo $viewingUserProfile['display_name'] ?></span></h4>
    <div class="row">
        <?php
        $discoveryCarousel->displayMostRatedBooksOfUser($viewingUserId);
        ?>
    </div>

    <hr>

    <h4>Latest Books<span class="text-black-50 lead"> from <?php echo $viewingUserProfile['display_name'] ?></span></h4>
    <div class="row">
        <?php
        $discoveryCarousel->displayBooksOfUser($viewingUserId);
        ?>
    </div>

</div>

<?php
$EXTRA_FOOTER_INCLUDES = '<script src="'.ROOT.'assets/js/profile.js"></script>';
include "views/includes/footer.php"
?>