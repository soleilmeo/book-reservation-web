<?php
if (!isset($PAGE_TITLE)) {
    $PAGE_TITLE = GlobalConfig::BASE_WEB_TITLE;
}

if (!isset($BODY_STYLE)) {
    $BODY_STYLE = "";
}

if (!isset($HEADER_INCLUDES)) {
    $HEADER_INCLUDES = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="Libraria!" content="A platform for literary, a library for the community. Reserve your favorite books today!"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href= "<?php Router::url(); ?>assets/css/base.css">

<?php
echo $HEADER_INCLUDES;
?>

<script>const __ROOT = "<?php echo ROOT ?>";</script>

<title><?php echo $PAGE_TITLE?></title>
</head>
<body style="<?php echo $BODY_STYLE ?>">
    <nav class="navbar navbar-expand-lg navbar-dark <?php if (isset($HEADER_TRANSPARENT) && $HEADER_TRANSPARENT): ?>bg-transparent<?php else: ?>bg-dark<?php endif; ?> fixed-top">
        <div class="container">
        <a href="<?php Router::url(); ?>" class="navbar-brand"><i class="fas fa-book"></i> Libraria!</a>
        <button class="navbar-toggler" data-target="#my-nav" data-toggle="collapse" aria-controls="my-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="my-nav" class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="<?php Router::url(); ?>"><i class="fas fa-home"></i> Home<span class="sr-only">(current)</span></a>
                </li>
                <!--
                <li class="nav-item active">
                    <a class="nav-link" href="<?php Router::url(); ?>book/discover"><i class="fas fa-chess-pawn"></i> Discover<span class="sr-only">(current)</span></a>
                </li>-->
                <?php if (isset($HEADER_HIDE_LINKS) && $HEADER_HIDE_LINKS): ?>
                <?php else: ?>
                <li class="nav-item active">
                    <a class="nav-link" href="<?php Router::url(); ?>book/create"><i class="fas fa-pen"></i> Create<span class="sr-only">(current)</span></a>
                </li>
                <?php if($_SESSION['logged_in']):?>
                <li class="nav-item active">
                    <a class="nav-link" href="<?php Router::url(); ?>book/dashboard"><i class="fas fa-cubes"></i> Bookshelf<span class="sr-only">(current)</span></a>
                </li>
                <?php endif;?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php Router::url(); ?>help" ><i class="fas fa-question-circle"></i> Help</a>
                </li>
                <?php if($_SESSION['logged_in']):?>
                    <li class="nav-item">
                    <a class="nav-link" href="<?php Router::url(); ?>user/<?php echo $_SESSION['user_id'];?>" ><i class="fas fa-user    "></i> <?php echo $_SESSION['username'];?></a>
                     </li>
                    <li class="nav-item">
                    <a class="nav-link" href="<?php Router::url(); ?>logout" ><i class="fas fa-door-open"></i> Logout</a>
                     </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php Router::url(); ?>login" ><i class="fas fa-user"></i> Login</a>
                </li>
                <?php endif;?>
                <?php endif;?>
            </ul>
        </div>      
        </div>
    </nav>

    <span class="nav-anchor"></span>

    <section id="PAGE-FITTER">