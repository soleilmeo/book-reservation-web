<?php
class ReservationStatus {
    // Runs on PHP 8.0 so no enums
    public const PENDING = 0;
    public const RESERVED = 1;
    public const RETURNING = 2;
    // Usually, cancelled reservations are deleted from database instead, so this is never used
}