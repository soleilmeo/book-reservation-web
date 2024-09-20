<?php
$_reserve_state = $reservation["reserve_status"];
$_reserve_days = $reservation["reserve_days"];

$_reserve_time = null;
$_reserve_deadline = null;
if ($reservation["receive_date"]) {
    $_reserve_time = new DateTime($reservation["receive_date"]);
    $_reserve_deadline = $_reserve_time->add(new DateInterval("P".$_reserve_days."D"));
}
$_datetime_now = new DateTime();
$is_overdue = $_reserve_deadline ? $_datetime_now > $_reserve_deadline : false;
?>

<div class="col-lg-4 my-2 bookcard-container">
    <div class="bookcard d-flex flex-column justify-content-end border rounded-lg shadow mx-1 p-4 text-white" style="background-image:linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.8)),url(<?php Router::url($book["book_banner_img"])?>)">
        <a href="<?php echo ROOT."book/".$book["book_read_id"] ?>" class="position-absolute w-100 h-100" style="z-index:0;"></a>
        <h3 style="pointer-events:none;user-select:none;"><?php echo $book["book_name"] ?></h3>
        <p style="pointer-events:none;user-select:none;">by <?php echo Placeholder::put($book["book_author"], "") ?></p>
        <?php if($_reserve_deadline != null): ?>
            <p style="pointer-events:none;user-select:none;"><i>Due date: <?php echo $_reserve_deadline->format("l, F jS, Y (h:i A)") ?></i></p>
        <?php endif; ?>

        <p style="z-index:1;"><span class="badge <?php echo ( $_reserve_state == ReservationStatus::PENDING ? "badge-secondary" : (!$is_overdue && !($_reserve_state == ReservationStatus::RETURNING) ? "badge-success" : "badge-danger") ) ?>">
            <?php if($_reserve_state == ReservationStatus::PENDING): ?>
                <i class="fa fa-hourglass"></i> In Queue: <?php echo $in_queue ?>
            <?php elseif($_reserve_state == ReservationStatus::RETURNING): ?>
                <i class="fa fa-truck"></i> Returning
            <?php elseif($is_overdue && $_reserve_state == ReservationStatus::RESERVED): ?>
                <i class="fa fa-exclamation-circle"></i> Overdue
            <?php elseif(!$is_overdue && $_reserve_state == ReservationStatus::RESERVED): ?>
                <i class="fa fa-hand-paper"></i> In Possession
            <?php endif; ?>
        </p>
    </div>
</div>