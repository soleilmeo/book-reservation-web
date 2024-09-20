</section>
    
<footer class="pt-3 pb-5 mt-4 bg-dark text-light" <?php if (isset($FOOTER_HIDDEN) && $FOOTER_HIDDEN) { ?>hidden<?php } ?>>
        <div class="container pb-4 pt-3">
            <p><i class="fas fa-book"></i> Copyright Libraria Ltd. 2024. All rights reserved.</p>
        </div>
    </footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script src="<?php Router::url("assets/js/ext/jquery.dirty.js");?>"></script>
<script src="<?php Router::url("assets/js/fetch.js") ?>"></script>
<script src="<?php Router::url("assets/js/ext/bootstrap-notif.js"); ?>"></script>
<?php
// Extra includes BEHIND the necessary library scripts
if (isset($EXTRA_FOOTER_INCLUDES)) {
    echo $EXTRA_FOOTER_INCLUDES;
}
 ?>
</body>
</html>

<?php 
if (isset($PAGE_TITLE)) {
    $PAGE_TITLE = null;
}
if(defined("GLOBAL_DEBUG")) var_dump($_SESSION);
?>