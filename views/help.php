<?php
$PAGE_TITLE = "Help".GlobalConfig::BASE_WEB_TITLE;
include "views/includes/header.php"
?>

<div class="frontpage-bg position-relative py-5 mb-5" style='background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("<?php echo ROOT?>assets/picture/placeholderbg.jpg");'>
    <div class="container text-white">
        <h4 class="display-4 display-4-fluid">Helpdesk</h4>
    </div>
</div>

<div class="container">
    <h4 class="text-wrap"><i class="fa fa-info-circle"></i> Frequently Asked Questions</h4>

    <div class="mt-4 text-wrap">

        <p><b>What does this website do?</b></p>
        <p class="pl-2">
            To make reservations for books. You can do that face-to-face with our Librarian, though, so this webpage is for when you don't feel like walking.
        </p>

        <p><b>How do you register an account here? I cannot see "Register" anywhere.</b></p>
        <p class="pl-2">
            Click "Login" on the top right header of this page.
            <br>If you cannot see it, on the top left, there will be an icon that looks like this: ☰
            <br>Tap on it, then tap "Login".
            <br>Scroll down or look to the left, you will see a Register form.
            <br>
            <?php if (Auth::loggedIn()): ?>
                <br>It won't display when you're logged in, like you are now. Instead, that button becomes your profile. You can click it to see your profile.</a>
            <?php else: ?>
                <br>Or you can click <a href="<?php Router::url("login"); ?>">here</a>.
            <?php endif; ?>
        </p>

        <p><b>How can I edit my profile?</b></p>
        <p class="pl-2">
            <?php if (!Auth::loggedIn()): ?>
                You're currently not logged in, so you cannot do that. <a href="<?php Router::url("login"); ?>">Please login or create an account first.</a>
                <br> In case you're logged in, y<?php else: ?>Y<?php endif; ?>ou can enter your profile through the top left button (with your account name), as noted on the second question.
            <br>Then, in that page, you will see a text that says <b><i class="fa fa-edit"></i> Edit Profile</b> on the top left, just above your profile card.
            <br>Click on it, and voila.
        </p>

        <p><b>Can you submit your own books here?</b></p>
        <p class="pl-2">
            Yes, unless you're banned.
            <?php if (!Auth::loggedIn()): ?>
                <br>Please <a href="<?php Router::url("login"); ?>">login or create an account</a> first to do that.
            <?php endif; ?>
        </p>

        <p><b>How to reserve a book?</b></p>
        <p class="pl-2">
            It's simple!
            <br>Find a book you like, then find a button that says <b><i class="fa fa-tag"></i> Reserve</b>, then click on it. You will receive the book from our librarian.
            <br>Remember that you can only reserve <b><?php echo GlobalConfig::MAX_RESERVATIONS_PER_USER ?> book<?php echo (GlobalConfig::MAX_RESERVATIONS_PER_USER > 1 ? 's' : '') ?> at a time.
            <br>
            <br>Each books have its own reservation day limit, so remember to return it on time.</b>
            <br>To return the book, please go on the page of that book, then click <b><i class="fa fa-undo"></i> Return Book</b>.
            <br>After that, bring the book to our librarian. They will return it to you.
            <br>
            <br>Sometimes, a book can be reserved by many other readers. This will put you in a queue, so that when a book is returned, others will borrow it instantly.
            <br>So please <b>remember to return your book on time</b>, or we will have to blast your name on our list.
        </p>

        <p><b>How to submit a book?</b></p>
        <p class="pl-2">
            Press <a href="<?php Router::url("book/create"); ?>"><b><i class="fas fa-pen"></i> Create</b></a> in the navigation bar.
            After creating a submission, it will not be public by default. You will have to change that by <b>editing it</b>. Please refer to the next question if you don't know how.
            <br><b>Currently, you cannot change your book cover once it's created, so please select your cover carefully.</b>
            <br>
            <br>Once a reservation is made, we will send our staff to collect the book from you. Make sure you have the book ready.
            <br>When your book is returned, or you feel like making a donation to the library, you're free to cancel existing reservations made for your books.
        </p>

        <p><b>How to edit a book?</b></p>
        <p class="pl-2">
            You can edit your own book by visiting your book page, then find a blue button that says <b><i class="fa fa-cog"></i> Edit</b>.
            There, you can edit your publicity settings and delete your own book.
        </p>

        <p><b>How to delete your book?</b></p>
        <p class="pl-2">
            <b><i class="fa fa-cog"></i> Edit</b> your book, as mentioned above. Scroll down and you'll find a big red button which says <b><i class="fa fa-trash mr-1" aria-hidden="true"></i> Delete Book</b>.
        </p>

        <p><b>Can I become an Admin?</b></p>
        <p class="pl-2">
            Ask our Librarians!
            <br>You can visit them in our library. We trained them to say no, by the way.
        </p>

        <p><b>I cannot understand you.</b></p>
        <p class="pl-2">
           :&#40;
        </p>
    </div>

    <h4 class="text-wrap"><i class="fa fa-phone"></i> Contact us</h4>
    <div class="mt-4 text-wrap">
        <p>Here's our address and phone number for live help.</p>
        <p class="pl-2">
        711-2880 Nulla St.
        <br>Mankato Mississippi 96522
        <br>(257) 563-7401
        </p>
    </div>
</div>

<?php
include "views/includes/footer.php"
?>