<?php
$HEADER_TRANSPARENT = true;
$HEADER_HIDE_LINKS = true;
include "views/includes/header.php";
// TODO: Change this later
?>
<div class="position-fixed vw-100 vh-100" style="background-image: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.75)), url(<?php Router::url("assets/picture/banner-login.jpeg") ?>); background-size: cover; background-position: center; background-repeat: no-repeat; top: 0; z-index:-1;"></div>
<div class="jumbotron jumbotron-fluid bg-transparent mt-5">
    <div class="container text-white">
        <?php
            $errMsg = "";
            if (Session::investigateDelivery("loginErr", $errMsg)) {
                ?>
                <div class=" alert alert-danger mb-3">
                    <?php
                    echo nl2br($errMsg);
                    ?>
                </div>
                <?php
            }
        ?>
        <div class="row">
            <div class="col-md-6 pr-md-5 mb-md-0 mb-5">
                <h3 class="mb-3 text-center">Already a Member?</h3>
                <h3 class="mb-3"><i class="fa fa-plus-circle" aria-hidden="true"></i> Login</h3>
                <form action="<?php echo ROOT; ?>login" method="post">
                    <div class="form-group">
                        <label for="my-input">Username</label>
                        <input id="my-input" class="form-control" type="text" name="username" placeholder="" autocomplete="off" required minlength="3">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" class="form-control" type="password" name="password" placeholder="" autocomplete="off" required minlength="8">
                    </div>
                    <?php CSRF::outputToken(); ?>
                    <button type="submit" class="btn btn-primary btn-block btn-lg" name="login"><i class="fa fa-plus-circle" aria-hidden="true"></i> Login</button>

                </form>
            </div>

            <div class="col-md-6 pl-md-5">
            <h3 class="mb-3 text-center">Don't have an account?</h3>
                <h3 class="mb-3"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</h3>
                <form action="<?php echo ROOT; ?>create/user" method="post">
                    <div class="form-group">
                        <label for="my-input">Choose a username <i class="text-white-50">(at least 3 characters)</i></label>
                        <input id="my-input" class="form-control" type="text" name="username" placeholder="" autocomplete="off" required minlength="3">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" class="form-control" type="email" name="email" placeholder="" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="password">Password <i class="text-white-50">(at least 8 characters)</i></label>
                        <input id="password" class="form-control" type="password" name="password" placeholder="" autocomplete="off" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="password-confirm">Confirm Password</label>
                        <input id="password-confirm" class="form-control" type="password" name="password-confirm" placeholder="" required minlength="8">
                    </div>
                    <?php CSRF::outputToken(); ?>
                    <button type="submit" class="btn btn-success btn-block btn-lg" name="register"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</button>
                </form>
            </div>

        </div>
    </div>
</div>
<p class="text-center text-white-50 no-interact">Libraria Ltd. 2024</p>

<?php
$FOOTER_HIDDEN = true;
include "views/includes/footer.php"; ?>