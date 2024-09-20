<?php
if (!defined('ROOT'))
{
    http_response_code(404);
    header("Location: /404");
    exit;
}

class GlobalConfig {
    public const HOSTNAME = "localhost";
    public const BASE_WEB_TITLE = "Libraria";

    // REQUIRED DATABASES
    public const GENERAL_DB = "2024_library_qstrDB";

    public const MAX_RESERVE_DAYS = 14;
    public const MAX_RESERVATIONS_PER_USER = 3; // Each user can only reserve a specified total amount of books

    public const DEFAULT_AUTHOR_NAME = "Unknown"; // Edit with caution since this is not sanitized. Better just leave it as it is.

    // GLOBAL BOOK CONFIGURATION
    public const BOOK_GENRES = [
        // Each entry index represents Genre ID, for example BOOK_GENRES[0] is genre "Unknown" with genre ID of 0
        "Unknown", // 0
        "General",
        "Combo", // A mix of genres
        "Math",
        "Physics",
        "Literature", // 5
        "Science",
        "Geography",
        "History",
        "Sports",
        "Arts", // 10
        "Adventure",
        "Logic",
        "Games",
        "Life",
        "Entertainment", // 15
        "Supernatural",
        "Programming",
        "Technology",
        "Food",
        "Finance", // 20
        "Self-improvement",
        "Space",
        "Plushies",
        "Cheese",
        "Traveling", // 25
        "Linguistics", // 26
    ];
}
?>