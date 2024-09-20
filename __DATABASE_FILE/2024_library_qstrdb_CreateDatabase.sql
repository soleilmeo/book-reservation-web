-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 15, 2024 at 06:28 PM
-- Server version: 10.10.2-MariaDB
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `2024_library_qstrdb`
--
CREATE DATABASE IF NOT EXISTS `2024_library_qstrdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `2024_library_qstrdb`;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `book_id` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'User-created book ID (Auto-increment base)',
  `book_read_id` varchar(255) NOT NULL COMMENT 'Book''s actual ID used for sharing and accessing them. Pre-processed and constant upon creation. Must be unique!',
  `user_id` bigint(13) UNSIGNED NOT NULL COMMENT 'Book creator''s user ID',
  `book_name` varchar(255) NOT NULL DEFAULT 'Very Cool Book' COMMENT 'Book name',
  `book_author` varchar(255) DEFAULT 'Unknown' COMMENT 'Book author',
  `book_desc` text DEFAULT NULL COMMENT 'Book description',
  `book_banner_img` varchar(255) DEFAULT NULL COMMENT 'Book banner image',
  `book_genre` int(11) DEFAULT NULL COMMENT 'Book genre/category ID (Genres are stored in config.php, indexes correspond with their IDs)',
  `book_is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Is book published or private?',
  `book_reserve_days_limit` int(8) NOT NULL DEFAULT 0 COMMENT 'Maximum reservation days.',
  `book_creation_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Book creation date',
  `book_update_date` datetime DEFAULT current_timestamp() COMMENT 'Book last update date',
  `is_book_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Is book featured on the front page? Usually picked by the site admins',
  `book_featured_order` int(11) DEFAULT NULL COMMENT 'If book is featured, it should have an order',
  `last_featured_date` datetime DEFAULT NULL COMMENT 'When was the last time this book was featured?',
  PRIMARY KEY (`book_id`),
  UNIQUE KEY `book_readIDs` (`book_read_id`),
  KEY `books_ibfk_1` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of books created by users.';

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_read_id`, `user_id`, `book_name`, `book_author`, `book_desc`, `book_banner_img`, `book_genre`, `book_is_published`, `book_reserve_days_limit`, `book_creation_date`, `book_update_date`, `is_book_featured`, `book_featured_order`, `last_featured_date`) VALUES
(1, '7c641lE4bcb4c6052O3c5F3d7636305cb6f01b781b43fO388', 1, 'The Wonderful Title Goes Here: The Book Subhead Goes Here', 'Author Name', 'This book delves into [blah subject blah], offering a comprehensive examination of the subject matter. Author Name provides a [something something] analysis of [e], drawing on [whatever] to illuminate the complexities of the topic.\r\n\r\nThe Wonderful Title Goes Here is a valuable resource for those seeking to broaden their knowledge of [something related?]. It is particularly suited for readers with an interest in [this book! this book!].', 'assets/u/qbanner/b07801e6157948359e338bfebdd00d08.jpg', 21, 1, 7, '2024-03-16 00:25:46', '2024-03-16 00:25:46', 0, NULL, NULL),
(2, 'oaa30eeabae9b739e0a7c9481o7Fe3A15f02lb487c9ee24b8', 2, 'One-Punch Man, Vol. 2 (2) Paperback â€“ September 1, 2015', 'Yusuke Murata, ONE', 'Life gets pretty boring when you can beat the snot out of any villain with just one punch.\r\n\r\nNothing about Saitama passes the eyeball test when it comes to superheroes, from his lifeless expression to his bald head to his unimpressive physique. However, this average-looking guy has a not-so-average problem&mdash;he just can&rsquo;t seem to find an opponent strong enough to take on!\r\n\r\nSaitama&rsquo;s easily taken out a number of monsters, including a crabby creature, a malicious mosquito girl and a muscly meathead. But his humdrum life takes a drastic turn when he meets Genos&mdash;a cyborg who wants to uncover the secret behind his strength!', 'assets/u/qbanner/73b8f5d2e10c4d2e8709462c4e0ef9bb.jpg', 15, 1, 12, '2024-03-16 00:31:43', '2024-03-16 00:31:43', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book_ratings`
--

DROP TABLE IF EXISTS `book_ratings`;
CREATE TABLE IF NOT EXISTS `book_ratings` (
  `relation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique rating relation for detecting duplicate rating. Structure: user_id + book_read_id',
  `user_id` bigint(13) UNSIGNED NOT NULL COMMENT 'User ID of the user who submitted the rating',
  `book_id` bigint(16) UNSIGNED NOT NULL COMMENT 'Book ID of the book that got rated',
  `rating` tinyint(1) NOT NULL COMMENT 'Proposed rating. 1 means Good, 0 means Bad',
  UNIQUE KEY `relation` (`relation`),
  KEY `book_id` (`book_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Ratings for books proposed by users.';

--
-- Dumping data for table `book_ratings`
--

INSERT INTO `book_ratings` (`relation`, `user_id`, `book_id`, `rating`) VALUES
('27c641lE4bcb4c6052O3c5F3d7636305cb6f01b781b43fO388', 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` bigint(13) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Sequential comment ID',
  `user_id` bigint(13) UNSIGNED NOT NULL COMMENT 'User ID of the author''s comment',
  `username` varchar(32) NOT NULL COMMENT 'Username of the author''s comment',
  `comment_group_id` bigint(13) UNSIGNED DEFAULT NULL COMMENT 'Every comment belongs to a group. This determines where the comment will show up in different pages. If a comment has no ID, it is considered "removed"',
  `comment` text NOT NULL DEFAULT '' COMMENT 'Submitted comment',
  `reply_to` bigint(13) UNSIGNED DEFAULT NULL COMMENT 'Comment ID where this comment is replying to. Try not to make replies to a reply unless you want it to become a Reddit comment section',
  `comment_post_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Datetime of comment creation',
  PRIMARY KEY (`comment_id`),
  KEY `user_id` (`user_id`),
  KEY `username` (`username`),
  KEY `comment_group_id` (`comment_group_id`),
  KEY `reply_to` (`reply_to`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User comments';

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `user_id`, `username`, `comment_group_id`, `comment`, `reply_to`, `comment_post_date`) VALUES
(1, 2, 'anotheruser', 1, 'This book is very good.', NULL, '2024-03-15 17:32:07'),
(2, 1, 'libraria', 1, 'Thank you!', 1, '2024-03-15 17:32:36');

-- --------------------------------------------------------

--
-- Table structure for table `comment_groups`
--

DROP TABLE IF EXISTS `comment_groups`;
CREATE TABLE IF NOT EXISTS `comment_groups` (
  `book_id` bigint(16) UNSIGNED NOT NULL COMMENT 'Numeric book ID (not book_read_id) of the book page with a comment section',
  `comment_group_id` bigint(13) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Comment group ID. A book that implements comments should add an entry to this table first, then the comments will be automatically assigned to that group',
  PRIMARY KEY (`comment_group_id`),
  KEY `book_id` (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Comment groups for use in "comments" table';

--
-- Dumping data for table `comment_groups`
--

INSERT INTO `comment_groups` (`book_id`, `comment_group_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `reserve_id` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Book reservation ID.',
  `user_id` bigint(13) UNSIGNED NOT NULL COMMENT 'User ID.',
  `book_id` bigint(16) UNSIGNED NOT NULL COMMENT 'ID of book being reserved.',
  `reserve_status` int(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Reservation status.',
  `reserve_days` int(8) UNSIGNED NOT NULL COMMENT 'Number of days of book-borrowing.',
  `reserve_create_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'The date and time this reservation request is made.',
  `receive_date` datetime DEFAULT NULL COMMENT 'Datetime when user receives the book (start of reservation)',
  PRIMARY KEY (`reserve_id`),
  KEY `user_id_update` (`user_id`),
  KEY `book_id_update` (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of books under reservation.' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reserve_id`, `user_id`, `book_id`, `reserve_status`, `reserve_days`, `reserve_create_date`, `receive_date`) VALUES
(1, 1, 2, 1, 3, '2024-03-16 00:33:13', '2024-03-16 00:33:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(13) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary User Id',
  `username` varchar(32) NOT NULL COMMENT 'Username of user',
  `email` varchar(255) NOT NULL COMMENT 'Email of user',
  `password` varchar(255) NOT NULL COMMENT 'Hashed password of user',
  `user_privilege_rank` int(11) NOT NULL DEFAULT 0 COMMENT 'Grants superuser (admin) rights if higher than 0. By default, 1 grants full control.',
  `creation_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'User''s registration date',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Account Credentials Storage. Go to user_info for information';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `user_privilege_rank`, `creation_date`) VALUES
(1, 'libraria', 'admin@mail.example', '$2y$10$j52IRbNlmg0lJAx8bj4nSuxTM6lo16R16dk.P7.ql.MN9BqhF6dn6', 1, '2024-03-16 00:15:14'),
(2, 'anotheruser', 'user@another.user', '$2y$10$aXD0FJeihKGlVn550aXK3udKiOvg6EG6qW3lQ9nbzJXjaZyDwTEeG', 0, '2024-03-16 00:27:04');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

DROP TABLE IF EXISTS `user_info`;
CREATE TABLE IF NOT EXISTS `user_info` (
  `user_id` bigint(13) UNSIGNED NOT NULL COMMENT 'Primary User ID',
  `display_name` varchar(255) NOT NULL COMMENT 'User''s display name',
  `user_image` varchar(255) DEFAULT NULL COMMENT 'User''s profile picture',
  `user_shortdesc` varchar(512) DEFAULT NULL COMMENT 'User''s short description',
  `user_desc` text DEFAULT NULL COMMENT 'User''s long description',
  `created_books` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '(JSON) List of book IDs created by the user.',
  `book_count` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Number of books created by user',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User information. For login credentials, go to users.';

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`user_id`, `display_name`, `user_image`, `user_shortdesc`, `user_desc`, `created_books`, `book_count`) VALUES
(1, 'Libraria', 'assets/u/avatar/87c6413a455e457a947319b88f4449f5.png', 'Hello, I am Libraria, the mastermind of this website.', 'Libraria, the best library platform that nobody uses.\r\n\r\nI am so sad and miserable.', '[1]', 1),
(2, 'John Hopkins Jr.', NULL, 'I play tag with my books.', '', '[2]', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `book_ratings`
--
ALTER TABLE `book_ratings`
  ADD CONSTRAINT `book_ratings_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `book_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`comment_group_id`) REFERENCES `comment_groups` (`comment_group_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_4` FOREIGN KEY (`reply_to`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comment_groups`
--
ALTER TABLE `comment_groups`
  ADD CONSTRAINT `comment_groups_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `book_id_update` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id_update` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
