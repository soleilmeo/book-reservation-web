-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Mar 17, 2024 at 07:57 AM
-- Server version: 10.10.2-MariaDB
-- PHP Version: 8.2.0

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
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of books created by users.';

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_read_id`, `user_id`, `book_name`, `book_author`, `book_desc`, `book_banner_img`, `book_genre`, `book_is_published`, `book_reserve_days_limit`, `book_creation_date`, `book_update_date`, `is_book_featured`, `book_featured_order`, `last_featured_date`) VALUES
(1, '7c641lE4bcb4c6052O3c5F3d7636305cb6f01b781b43fO388', 1, 'The Wonderful Title Goes Here: The Book Subhead Goes Here', 'Author Name', 'This book delves into [blah subject blah], offering a comprehensive examination of the subject matter. Author Name provides a [something something] analysis of [e], drawing on [whatever] to illuminate the complexities of the topic.\r\n\r\nThe Wonderful Title Goes Here is a valuable resource for those seeking to broaden their knowledge of [something related?]. It is particularly suited for readers with an interest in [this book! this book!].', 'assets/u/qbanner/b07801e6157948359e338bfebdd00d08.jpg', 21, 1, 7, '2024-03-16 00:25:46', '2024-03-16 00:25:46', 0, NULL, NULL),
(2, 'oaa30eeabae9b739e0a7c9481o7Fe3A15f02lb487c9ee24b8', 2, 'One-Punch Man, Vol. 2 (2) Paperback â€“ September 1, 2015', 'Yusuke Murata, ONE', 'Life gets pretty boring when you can beat the snot out of any villain with just one punch.\r\n\r\nNothing about Saitama passes the eyeball test when it comes to superheroes, from his lifeless expression to his bald head to his unimpressive physique. However, this average-looking guy has a not-so-average problem&mdash;he just can&rsquo;t seem to find an opponent strong enough to take on!\r\n\r\nSaitama&rsquo;s easily taken out a number of monsters, including a crabby creature, a malicious mosquito girl and a muscly meathead. But his humdrum life takes a drastic turn when he meets Genos&mdash;a cyborg who wants to uncover the secret behind his strength!', 'assets/u/qbanner/73b8f5d2e10c4d2e8709462c4e0ef9bb.jpg', 15, 1, 12, '2024-03-16 00:31:43', '2024-03-16 00:31:43', 0, NULL, NULL),
(4, '9Ab6ccA23f5523bef0513824c0A0e4d0o160bFf8149dO7bL0', 1, 'The 7 Habits of Highly Effective People', 'Stephen R. Covey', 'This classic book outlines powerful principles for personal and professional effectiveness. Covey emphasizes proactive behavior, prioritization, and the importance of aligning actions with values. It&#039;s a timeless guide for anyone seeking to improve their effectiveness and achieve their goals.', 'assets/u/qbanner/f8263131b3134bde83b68fc1b66db774.png', 21, 1, 7, '2024-03-16 21:43:36', '2024-03-16 21:43:36', 0, NULL, NULL),
(5, '7a69co6580C057e064a57Bbl97fb087oF8c769e2acA2b6b54', 1, 'Atomic Habits', 'James Clear', 'Clear&#039;s book delves into the science of habit formation, providing actionable strategies for creating positive behaviors and breaking bad ones. He emphasizes the power of small, incremental changes and highlights the importance of environment in shaping habits. With practical advice and engaging anecdotes, &quot;Atomic Habits&quot; is a valuable resource for anyone looking to make lasting changes in their life.', 'assets/u/qbanner/9722222545664cef9ba4f63167a23628.png', 21, 0, 14, '2024-03-16 21:47:15', '2024-03-16 21:47:15', 0, NULL, NULL),
(6, 'C437l7451c2388E9693af6c64a19d5fo6ea85da342Fc70co8', 1, 'Mindset: The New Psychology of Success', 'Carol S.Dweck', 'Dweck&#039;s book explores the concept of mindset and its impact on achievement and personal growth. She distinguishes between a fixed mindset, where abilities are seen as innate and unchangeable, and a growth mindset, where talents can be developed through effort and perseverance. By adopting a growth mindset, readers can unlock their full potential and overcome obstacles more effectively.', 'assets/u/qbanner/9a4bf644eb5948e9820e6cd8394a3c81.png', 21, 0, 3, '2024-03-16 21:49:15', '2024-03-16 21:49:15', 0, NULL, NULL),
(7, '2bo030o1fe1D287c9a42c0f7ef4d677c3e674c0lb1e47f92d', 4, 'Gordon Ramsay&#39;s Home Cooking: Everything You Need to Know to Make Fabulous Food', 'Gordon Ramsay', 'In this book, Ramsay shares his expertise and passion for cooking with accessible recipes designed for home cooks. From simple weeknight meals to impressive dinner party dishes, each recipe is accompanied by helpful tips and techniques to elevate your cooking skills.', 'assets/u/qbanner/944fdb8ed9954ec287ac949f5b9ce863.png', 19, 0, 5, '2024-03-16 21:53:05', '2024-03-16 21:53:05', 0, NULL, NULL),
(8, '84dfb55cco9156593dL1177d5b783a79dB82e17o8c478a857', 4, 'The Food Lab: Better Home Cooking Through Science', 'J. Kenji LÃ³pez-Alt', 'J. Kenji L&oacute;pez-Alt combines scientific principles with practical kitchen wisdom in &quot;The Food Lab.&quot; Through experimentation and meticulous testing, he reveals the secrets behind classic dishes and offers innovative techniques for achieving superior results. With its thorough explanations and detailed recipes, this cookbook is a valuable resource for anyone interested in the science of cooking.', 'assets/u/qbanner/f7e40274b7704c4fa8345d007fa27e77.png', 19, 0, 5, '2024-03-16 21:58:00', '2024-03-16 21:58:00', 0, NULL, NULL),
(9, 'c1170Cee5B484o93da519f602c1oL34c9996e3d4d2A8bb33D', 4, 'Salt, Fat, Acid, Heat: Mastering the Elements of Good Cooking', 'Samin Nosrat', 'Samin Nosrat&#039;s approach to cooking emphasizes the fundamental elements of flavor and technique. Through engaging storytelling and vibrant illustrations, she demystifies the art of cooking, empowering readers to create delicious meals with confidence. This book is not just a collection of recipes but a guide to understanding the principles behind great cooking.', 'assets/u/qbanner/b134f414eabf498ca912732bd72c81fe.png', 19, 1, 3, '2024-03-16 22:00:05', '2024-03-16 22:00:05', 0, NULL, NULL),
(10, '92o25383921F2a5ob7a1caA31l4f305752b3f5a3c3776ff30', 4, 'Thug Kitchen: The Official Cookbook: Eat Like You Give a F*ck', 'Thug Kitchen', 'With its bold attitude and flavorful recipes, &quot;Thug Kitchen&quot; challenges the notion that healthy eating has to be boring. Packed with plant-based dishes and clever tips for maximizing flavor, this cookbook encourages readers to embrace a more vibrant and adventurous approach to cooking. Whether you&#039;re a seasoned chef or a novice in the kitchen, &quot;Thug Kitchen&quot; offers plenty of inspiration for delicious, nutritious meals.', 'assets/u/qbanner/64e64a6c36a4405fb7c2816973f3a4b9.png', 19, 0, 3, '2024-03-16 22:01:13', '2024-03-16 22:01:13', 0, NULL, NULL),
(11, 'c4af46b77o9l51B8d472819a727c6c9d2bcceo9e25130c9e3', 4, 'The Language Instinct: How the Mind Creates Language', 'Steven Pinker', 'Pinker explores the innate human capacity for language acquisition in this thought-provoking book. Drawing on evolutionary biology and linguistics, he argues that language is a fundamental aspect of human nature and delves into the mechanisms behind language acquisition and evolution.', 'assets/u/qbanner/0db896bc94e345c9a82684c0cc62683e.png', 26, 0, 3, '2024-03-16 22:02:36', '2024-03-16 22:02:36', 0, NULL, NULL),
(12, 'fb7f48cEba6DL7d3BD6d87db3bca6o8ff4o58562D321ca0b9', 4, 'A Brief History of Time', 'Stephen Hawking', '&quot;A Brief History of Time&quot; by Stephen Hawking is a captivating journey through the mysteries of the universe, offering profound insights into complex scientific concepts in an accessible manner. With eloquent prose, Hawking effortlessly navigates topics such as the nature of time, the origin of the cosmos, and the principles of quantum physics, engaging readers with thought-provoking explanations and theories. This seminal work not only educates but also inspires readers to ponder the fundamental questions about existence and our place in the vastness of space and time.\r\n\r\n', 'assets/u/qbanner/070b6892a02c4acc9041799e5564b2d8.png', 4, 0, 3, '2024-03-16 22:04:34', '2024-03-16 22:04:34', 0, NULL, NULL),
(15, '3553aee69188bd44o4f6Oc4cldbf66e77279d3cc264875d72', 4, 'One Piece', 'Eiichiro Oda', '&quot;One Piece&quot; is an epic manga series that has captivated audiences worldwide with its rich storytelling and vibrant characters. Eiichiro Oda&#039;s masterpiece takes readers on a thrilling adventure across the vast seas as Monkey D. Luffy and his crew pursue the legendary treasure, &quot;One Piece,&quot; while encountering formidable foes and forming unbreakable bonds along the way. With its blend of action, humor, and heartfelt moments, &quot;One Piece&quot; continues to stand as a timeless masterpiece in the realm of manga, beloved by fans of all ages. ', 'assets/u/qbanner/3fba3c541cb8408090d42f7f4e504d61.jpg', 15, 0, 14, '2024-03-16 22:10:12', '2024-03-16 22:10:12', 0, NULL, NULL),
(16, '35785o9C8e4fb4d6846eEl7B1c33aa29fe8d9268o27e47723', 4, 'HSK Standard Course', 'Jiang Liping', 'Another series by Jiang Liping, this set of textbooks is aligned with the HSK syllabus and covers vocabulary, grammar, listening, reading, and writing skills. The series consists of six levels, with each level corresponding to the six levels of the HSK exam.', 'assets/u/qbanner/8b845fd11dd94a58884aa4e3eb1cf0e3.png', 26, 0, 5, '2024-03-16 22:11:44', '2024-03-16 22:11:44', 0, NULL, NULL),
(17, '8D04E4ac71la3117CfD5e0e67972O11855O91e19884644d4B', 4, 'Easy French Step-by-Step', 'Myrna Bell Rochester', 'This book provides a structured approach to learning French, starting from basic concepts and gradually progressing to more advanced grammar and vocabulary. It includes clear explanations, examples, and exercises to reinforce learning.', 'assets/u/qbanner/c65901103aee40be88c8e9f76815f3fa.png', 26, 0, 5, '2024-03-16 22:16:53', '2024-03-16 22:16:53', 0, NULL, NULL),
(18, 'b4705OCaD12465c881647006af1dd6cD2823ceoe5c8fLaf9f', 4, 'Fluent in French: The Most Complete Study Guide to Learn French', 'Frederic Bibard', 'This study guide covers all aspects of learning French, including grammar, vocabulary, pronunciation, and cultural insights. It includes tips and strategies for effective language learning and practical exercises to reinforce learning.', 'assets/u/qbanner/6a1c5b58e5ea48b9b91db1fab6e2abf8.png', 26, 1, 3, '2024-03-16 22:17:45', '2024-03-16 22:17:45', 0, NULL, NULL),
(19, 'c2b8249a5aef96a796O3C911E3l6a988b6o3533eb4e8cf83f', 4, 'Talk to Me in Korean (textbooks &#38; workbooks)', 'Talk to Me in Korean', 'Created by the popular online resource Talk to Me in Korean, these textbooks offer a structured approach to learning Korean, focusing on practical conversational skills. The accompanying workbooks provide exercises to reinforce learning.', 'assets/u/qbanner/b3c5bbedf42f44a3816bd8cffc007ca5.png', 26, 0, 3, '2024-03-16 22:19:27', '2024-03-16 22:19:27', 0, NULL, NULL),
(20, '95e5c74c2b8l6f7e2d93o11d490cf6DfO3c5d432b8a869408', 4, 'Korean Grammar in Use', 'Ahn Jean-myung, Lee Kyung-ah, Han Hoo-youn', 'This series offers a systematic approach to learning Korean grammar, with clear explanations and plenty of practice exercises. It&#039;s suitable for learners of all levels, from beginner to advanced.', 'assets/u/qbanner/97aa0eedcd2d4d9e8aa688a0c18ddb68.png', 26, 0, 5, '2024-03-16 22:21:20', '2024-03-16 22:21:20', 0, NULL, NULL),
(21, '62C575895l0dDc28AF333a345eo64761eC0Ceoc00F612c68a', 4, 'Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', 'Harari provides a sweeping overview of human history, from the emergence of Homo sapiens to the present day, exploring key events, developments, and cultural shifts that have shaped our species.', 'assets/u/qbanner/9e174b9ae06f4a41b5b6e8e556375290.png', 8, 0, 3, '2024-03-16 22:23:54', '2024-03-16 22:23:54', 0, NULL, NULL),
(22, '2b0a07cf0cb743F3334fd6edA6aCa6a16O35a9833cD58l26o', 4, 'Guns, Germs, and Steel: The Fates of Human Societies', 'Jared Diamond', 'Diamond explores the broad patterns of human history, examining how geography, technology, and environmental factors have shaped the development of civilizations around the world.', 'assets/u/qbanner/18ddb6b9ed884f4aae28650fbb5cef18.png', 8, 1, 7, '2024-03-16 22:25:20', '2024-03-16 22:25:20', 0, NULL, NULL),
(23, 'fCcoa619db66o0bcaa312e955bc1D1927d74b7la4e8b52645', 4, 'World: Six Queens of Egypt ', 'Kara Cooney', 'When you think of Ancient Egyptian queens, Cleopatra probably comes to mind &mdash; but did you know that the various Egyptian dynasties boasted a whole host of prominent women? Cooney&rsquo;s When Women Ruled The World shifts the spotlight away from the more frequently discussed Egyptian pharaohs, placing attention on the likes of Hatshepsut, Nefertiti, and Cleopatra, all of whom commanded great armies, oversaw the conquering of new lands, and implemented innovative economic systems. In this captivating read, Cooney reveals more about these complex characters and explores why accounts of ancient empires have been so prone to placing powerful women on the margins of historical narratives. ', 'assets/u/qbanner/8c51e491df7c414aba195abb90fea525.png', 8, 0, 3, '2024-03-16 22:27:30', '2024-03-16 22:27:30', 0, NULL, NULL),
(24, 'F2Ac3o169bl9b4f50c131oe7163793c019FDe3c9747600fc8', 4, 'The Complete Sherlock Holmes', 'Arthur Conan Doyle', 'Sir Arthur Conan Doyle&rsquo;s Sherlock Holmes tales are rightly ranked among the seminal works of mystery and detective fiction. The handsome packaging and splendid illustrations in this limited edition collection more than befit that classic status. Included are all four full-length Holmes novels and more than forty short masterpieces&mdash;from The Adventures of Sherlock Holmes to The Case Book of Sherlock Holmes and more&hellip;', 'assets/u/qbanner/954802d7329b4e0ba4d699f9b58d9511.png', 11, 1, 3, '2024-03-16 22:30:03', '2024-03-16 22:30:03', 0, NULL, NULL),
(25, 'o9A407065196o9f2d243069894019Cb82l5B3c30C6269a6e7', 5, 'The Decline and Fall of the Roman Empire, Vol. 1 ', 'Edward Gibbon', 'Despite being published in 1776, Gibbon&rsquo;s work on the Roman Empire is still revered by historians today. Along with five other volumes of this monumental work, this text is considered one of the most comprehensive and pre-eminent accounts in the field. Gibbon offers theories on exactly how and why the Roman Empire fell, arguing controversially that it succumbed to barbarian attacks mainly due to the decline of &ldquo;civic virtue&rdquo; within Roman culture. If this thesis has piqued your interest, then we naturally suggest you start with Volume I to understand what exactly Gibbon considers &ldquo;virtue&rdquo; to be, and how it was lost. ', 'assets/u/qbanner/e398c9adbcfb4b19b43a03fb4354910c.png', 8, 0, 3, '2024-03-16 22:32:44', '2024-03-16 22:32:44', 0, NULL, NULL),
(26, '4e2cEl0f83bf7913B6349aafd8o415ob469d4f0E3392d25E7', 5, 'The History of the Ancient World: From the Earliest Accounts to the Fall of Rome ', 'Susan Wise Bauer', 'In this text, Bauer weaves together events that spanned continents and eras, from the East to the Americas. This book, described as an &ldquo;engrossing tapestry,&rdquo; primarily aims to connect tales of rulers to the everyday lives of those they ruled in vivid detail. With an eloquently explained model, she reveals how the ancient world shaped, and was shaped by, its peoples.', 'assets/u/qbanner/9be5128cf2cb4bb3a9da5a87437ff213.png', 8, 0, 7, '2024-03-16 22:35:37', '2024-03-16 22:35:37', 0, NULL, NULL),
(27, 'o095l84cb0oe47f3cd9f36db76Cbab923e34d09317749cf78', 5, 'Foundations of Chinese Civilization: The Yellow Emperor to the Han Dynasty ', 'Jing Liu', 'This comic by Beijing native Jing Liu turns history on its head by presenting it in a fun, digestible manner for anybody that has an interest in Chinese history (but isn&rsquo;t quite ready to tackle an 800-page book on the subject yet). Spanning nearly 3,000 years of ancient history, this comic covers the Silk Road, the birth of Confucianism and Daoism, China&#039;s numerous internal wars, and finally the process of modern unification.', 'assets/u/qbanner/be632bd23a1646b995dd327d569859c9.png', 8, 0, 6, '2024-03-16 22:37:25', '2024-03-16 22:37:25', 0, NULL, NULL),
(28, 'A8l5f24e01A35421520d057C90a4a8d3542702oc04E7Boc32', 5, 'Four Hundred Souls: A Community History of African America, 1619-2019 ', 'Ibram X. Kendi', 'While this isn&rsquo;t strictly a history book, Four Hundred Souls is certainly an eye-opening volume if you&rsquo;re looking to explore oft-hidden aspects of history. Thiscollection of essays, personal reflections, and short stories is written by ninety different authors, all providing unique insights into the experiences of Black Americans throughout history. Editors Kendi and Blain do a brilliant job of amalgamating a variety of emotions and perspectives: from the pains of slavery and its legacy to the heartfelt poetry of younger generations. If you&rsquo;re looking for your fix of African American Literature and nonfiction in one go, consider this your go-to.', 'assets/u/qbanner/a4eab2b866b0458eb3c4fc1ee5ab72e6.png', 8, 0, 5, '2024-03-16 22:38:38', '2024-03-16 22:38:38', 0, NULL, NULL),
(29, '0bf68067aee14ef7a1cB3e3af8deold4C18528a8o590be878', 5, 'INTRODUCTION TO CELL AND MOLECULAR BIOLOGY', 'K.Sathasivan, Ph.D.', 'Introduction to Cell and Molecular Biology covers the scientific approach to biology, basic chemistry for biology, cell structure, and function, cell communication and defense mechanisms, metabolism, respiration, photosynthesis, lipid metabolism, cell division, patterns of inheritance, DNA structure and replication, transcription and translation, gene regulation and recombinant DNA. This book provides a strong foundation for cell biology, biochemistry and, and molecular biology. This will help the reader before taking any advanced courses in biology or biotechnology.', 'assets/u/qbanner/f0fa610004f04bf587c0ac78464562b1.png', 6, 0, 7, '2024-03-16 22:42:38', '2024-03-16 22:42:38', 0, NULL, NULL),
(30, '59A2af7246l9cc4c0o022da3a7447f0o6f71ec069f6c86598', 5, 'Campbell Biology', ' Lisa A. Urry, Jane Reece, Neil Campbell, Michael Cain, Steven A. Wasserman', 'Explore the exciting and ever-evolving world of biology with the best-selling education program that has helped millions of students succeed. The eleventh edition of this popular text and media program features engaging narrative, stunning art and photography, and hands-on activities that challenge readers to deepen their understanding of key concepts. With a focus on the latest research and innovative tools like Problem-Solving Exercises, Visualizing Figures, and MasteringBiology online resources, this program is better than ever. Add Campbell Biology to your reading list today and unlock the secrets of life!', 'assets/u/qbanner/ffa8e2423d3f4d088c30c3c037ece1d0.png', 6, 0, 7, '2024-03-16 22:52:18', '2024-03-16 22:52:18', 0, NULL, NULL),
(31, 'oca5de0cbe70ae3241348o7178l2e34454ba9c0d78346C7e3', 5, 'Molecular Biology of the Cell', 'Bruce Alberts', 'This comprehensive textbook distills the vast knowledge of biology into concise principles and enduring concepts. The Sixth Edition is extensively updated with the latest research in the field of cell biology and enhanced with new, clear illustrations. Each chapter poses intriguing questions on challenging areas of future research, making this book the perfect framework for teaching and learning.', 'assets/u/qbanner/e7cd7085dacc4086aa3fa8a56cf025f0.png', 6, 0, 14, '2024-03-16 22:54:29', '2024-03-16 22:54:29', 0, NULL, NULL),
(32, '4927a9975e9l28fEoba77o5ea9d8a2807bdb7bc084da332c5', 5, 'The Selfish Gene', 'Richard Dawkins', 'Explore the fascinating world of evolutionary thought with this classic exposition. The author offers a gene&rsquo;s eye view of evolution, placing the importance on the units of information that persist, and viewing organisms as vehicles for their replication. This powerful and stylistically brilliant work galvanized the biology community, generating much debate and stimulating whole new areas of research. This 40th anniversary edition includes a new epilogue from the author discussing the continuing relevance of these ideas in evolutionary biology today, as well as the original prefaces and foreword. Join the millions of readers worldwide who have fallen in love with The Selfish Gene.', 'assets/u/qbanner/892091323c66491c8071f5862e2a0ce1.png', 6, 1, 10, '2024-03-16 22:55:43', '2024-03-16 22:55:43', 0, NULL, NULL),
(34, '14086529ldec4bD201b361619b8C7ea2f7986boo6510cec73', 5, 'Mama&#39;s Last Hug', 'Frans de Waal', 'Discover the emotional lives of animals in Mama&#039;s Last Hug. Follow the story of Mama, a chimpanzee matriarch who formed a deep bond with a biologist, and learn about the many ways in which humans and animals are connected. With colorful stories and riveting prose, Frans de Waal argues for better treatment and appreciation of animals, challenging the notion that humans alone experience a broad array of emotions. Prepare to open your heart and mind to the fascinating world of animal emotions.', 'assets/u/qbanner/a472953306134e73bc086702b9325956.png', 14, 0, 9, '2024-03-16 22:59:27', '2024-03-16 22:59:27', 0, NULL, NULL),
(35, '414f38b31bd5fa8b4eA048obe7l6c83130ce1c08024fo7dc9', 5, 'Biology', 'Sylvia S. Mader, Michael Windelspecht', 'This comprehensive seventh edition of a top-selling biology textbook, written by respected author/expert Sylvia Mader, features stunning new art and photographs and an integrated multimedia and supplements package. With its complete coverage of core biology concepts, students of all levels will benefit. Dr. Mader&#039;s descriptive writing style and careful pedagogy provide students with a firm grasp of how their bodies function, making it an exceptional choice for those studying biology. Plus, the text is fully customizable to fit any course.', 'assets/u/qbanner/833b7e9954884f5686e9cd0643fca4f6.png', 6, 0, 7, '2024-03-16 23:02:47', '2024-03-16 23:02:47', 0, NULL, NULL),
(36, 'da43o424a7f17ocl34dfD0e5a0aDcba962009a1ed65f6ca88', 5, 'Batman - The Long Halloween', 'Jeph Loeb, Tim Sale', 'A thrilling mystery unfolds as Batman works with Harvey Dent and Lieutenant James Gordon to catch a killer who strikes only on holidays. With each passing month, the clock ticks closer to the next victim&#039;s demise. Will Batman discover the killer&#039;s identity in time? This edition also explores the origins of Batman&#039;s nemesis, Two-Face. Get ready to be captivated till the very end.', 'assets/u/qbanner/8119cf23cb22469486902f06d25e1579.png', 11, 0, 14, '2024-03-16 23:04:27', '2024-03-16 23:04:27', 0, NULL, NULL),
(37, 'O53b3a2189a47767f69cf498cdf82c6a3l3cbed4630oC0867', 5, 'The Sandman Vol. 1 ', 'Neil Gaiman', 'Experience the magic of Neil Gaiman&#039;s graphic storytelling with THE SANDMAN VOL. 1: PRELUDES &amp; NOCTURNES, a masterpiece that explores the forces beyond life and death. Follow Morpheus, also known as Dream, as he embarks on a quest to recover his lost objects of power, encountering a slew of captivating and powerful characters like Lucifer and John Constantine along the way. This edition also includes &quot;The Sound of Her Wings,&quot; where readers are introduced to the whimsical Death. Don&#039;t miss out on this definitive Vertigo title and one of the finest achievements in graphic storytelling.', 'assets/u/qbanner/a661dc860e054c1bbdc10249804e7e12.png', 5, 0, 12, '2024-03-16 23:05:39', '2024-03-16 23:05:39', 0, NULL, NULL),
(38, '39f23ef4o34708C1857d3b5472a6obc6b78962A7E1cl6a685', 5, 'Detective Conan (Meitantei Conan)', 'Gosho Aoyama', 'This is a detective comic series, with action and a bit of humor and romance, written by Gosho Aoyama author. It was published the first time in 1995, then this series has been reprinted many times with better versions. This series talks about the Kudo Shinichi - highschool detective. While on a date with his childhood friend (later girlfriend) Ran Mouri, Shinichi encounters two men from a secret criminal organization who force feed him a strange poison that causes his body to shrink back to first grade age.', 'assets/u/qbanner/abce191e8bef44078dbb342c1fe75876.png', 15, 0, 10, '2024-03-16 23:07:19', '2024-03-16 23:07:19', 0, NULL, NULL),
(39, 'ff1e1laco2879505483Ed202Ae3C483192d40cecboa7e7b72', 5, 'The Lady Sherlock Series ', 'Sherry Thomas', 'Sherlock Holmes, but make him a disgraced society lady in Victorian times named Charlotte Holmes, who assumes the Sherlock moniker in order to obscure her identity! These mysteries are deliciously complicated and clever, and you definitely want to read them in order as they do feed into each other. Start with A Study in Scarlet Women.\r\n\r\n', 'assets/u/qbanner/9c40809e68134766af950de0ad8841f4.png', 11, 0, 14, '2024-03-16 23:09:54', '2024-03-16 23:09:54', 0, NULL, NULL),
(40, '475315EDeo44173683d90d62a1ld0c49aoe79d12cfaA0c9e2', 5, 'Aaron Falk Series ', 'Jane Harper', 'Aaron Falk is an investigator in Australia specializing in financial crimes, who seems to be drawn into murder at every turn. There are two books in his series currently&mdash;you&rsquo;ll want to start with The Dry, which sees Falk heading home for the first time in decades to confront a long-kept secret in the wake of his best friend&rsquo;s family&rsquo;s murder. Follow it up for Force of Nature.', 'assets/u/qbanner/368e2aafb3f04188b4f4c0eb2433a457.png', 11, 0, 13, '2024-03-16 23:11:16', '2024-03-16 23:11:16', 0, NULL, NULL),
(41, '77loe44cd0093144bb46d8O657886C0c9a9a7906417e185a5', 6, 'Bocchi the Rock!', ' Aki Hamaji', 'At the end of the first episode of *Bocchi the Rock!*, Kessoku Band has more or less bombed their first performance. Bocchi (n&eacute;e Gotou Hitori), who had been ready to announce her presence to the world as Kessoku Band&rsquo;s last-minute fill-in guitarist, anxiously retreated into a mango box, hidden within it as the band lurches and stumbles their way through their music. But in the aftermath as her bandmates Nijika and Ryou remark on the atrociousness they just played, Bocchi throws the box off herself and stumbles her way down frame in a fish-eye lens style camera shot. She stands before the two and declares that she&rsquo;ll muster the courage to talk to her classmates, which Nijika smiles at&hellip;and then Bocchi immediately heads out since she&rsquo;s drained her socially anxious battery for the day, but still resolving to improve herself for their sakes.\r\n\r\nIf any scene could encapsulate what makes *Bocchi the Rock!* such a creatively-rich series, it&rsquo;s this. This whole sequence is but the tip of the show&rsquo;s iceberg. There is a chaotic beauty in the show&rsquo;s reckless abandon, able to radically swing between whatever style it chooses to adopt at any given moment. And within it all, anxiety is presented as both comedic fuel and as a sincere obstacle towards one&rsquo;s sense of personal development.', 'assets/u/qbanner/aded65e721f144b6bdbd9ef39b7a2952.png', 15, 0, 10, '2024-03-17 10:34:35', '2024-03-17 10:34:35', 0, NULL, NULL),
(43, 'fd0e5cd88l2a1ec173D4eo9c1d0027E74E861ee80789ao519', 6, 'The Help: a novel', 'Kathryn Stockett', 'I have this terrible, dreary feeling in my diaphragm area this morning, and I&rsquo;m not positive what it&rsquo;s about, but I blame some of it on this book, which I am not going to finish. I have a friend who is mad at me right now for liking stupid stuff, but the thing is that I do like stupid stuff sometimes, and I think it would be really boring to only like smart things. What I don&rsquo;t like is when smart (or even middle-brained) writers take an important topic and make it petty through guessing about what they don&rsquo;t know. I can list you any number of these writers who would be fine if they weren&#039;t reaching into topics about which they have no personal experience (incidentally, all writers I&#039;m pretty sure my angry friend loves. For example, The Lovely Bones, The Kite Runner, Water for Elephants, Memoirs of a Geisha, etc.). These are the books for which I have no patience, topics that maybe someone with more imagination or self-awareness could have written about compassionately, without exploiting the victimization of the characters. They&rsquo;re books that hide lazy writing behind a topic you can&rsquo;t criticize. The Help is one of these.', 'assets/u/qbanner/7a610c0110044586b5948603d1f53170.jpg', 5, 1, 10, '2024-03-17 10:38:22', '2024-03-17 10:38:22', 0, NULL, NULL),
(45, 'fo7045c81oE1C939fBl3597b6aDfe079Ea68Ca627fFcb2a62', 2, 'Your Name', 'Shinkai Makoto', '&quot;Your Name&quot; by Shinkai Makoto is a masterpiece in the world of anime, skillfully blending humor, romance, and supernatural elements. The film crafts a captivating romantic story, adorned with stunning visuals and emotionally resonant music. The fusion of mythical elements and the message about love and the connection between humans and the universe make &quot;Your Name&quot; an unforgettable experience for anime enthusiasts.\r\n\r\n', 'assets/u/qbanner/ee388277887b42a38a9182c0e5d5f51f.jpg', 15, 0, 12, '2024-03-17 10:44:14', '2024-03-17 10:44:14', 0, NULL, NULL),
(46, '92939801dAf348cc3965l4Bf921ec9A743C4d08o8oa718304', 2, 'Alex Ferguson: 6 Years at United', 'Alex Ferguson, David Meek', 'The book &quot;Alex Ferguson: 6 Years at United&quot; focuses on Alex Ferguson&#039;s tenure as the manager of Manchester United for six years. It highlights the challenges and successes Ferguson experienced in leading the club to the forefront of English football. It provides an insightful look into the journey of one of the greatest football managers in history.', 'assets/u/qbanner/7d606fdd00624c2e874d314141e970a3.png', 9, 1, 14, '2024-03-17 10:45:55', '2024-03-17 10:45:55', 0, NULL, NULL),
(47, '25a349eC788a59e2e808fa339415e3oc0a0f7e1cc24o29l81', 6, 'The Lovely Bones', 'Alice Sebold', 'Out of my entire reading list for 2022, more people commented about The Lovely Bones by Alice Sebold than all of the other books combined!\r\nThe Lovely Bones begins with the tragic death of a fourteen-year-old girl, Susie Salmon (like the fish). From there, we follow Susie&rsquo;s family and friends as well as Susie&rsquo;s murderer.\r\n\r\nThe Lovely Bones starts off very strong, and the impulse to read more is almost overwhelming. However, the book is downhill from there.\r\nWriting a book that begins with a death is very unusual. Most authors usually begin in the middle. Unfortunately, the death is the most interesting part of the book, so the rest of the book simply dragged. In my opinion, the plot was interesting, but the execution lacked.\r\n', 'assets/u/qbanner/59331de1af7949a8b2bb7736359f4267.png', 5, 0, 10, '2024-03-17 10:48:17', '2024-03-17 10:48:17', 0, NULL, NULL),
(49, '6f0474638d48o704513c1c47fdo400d9dcd7881825c4l53ec', 6, 'Sword Catcher', 'Cassandra Clare', 'What a wild ride, I actually really really enjoyed this and it&#039;s left me with SO many questions. The characters are so in depth and also very secretive that you don&#039;t know who is doing what. This is what I love about Cassandra Clare and her books.\r\n\r\nI had such high hopes for this book but then was wary after the finale that was Chain of Thorns, but so so happy with this! Can&#039;t wait for what&#039;s to come.\r\n\r\n------------\r\nI love the Shadowhunter world, BUT I&#039;m so glad it&#039;s something completely new! I&#039;m ridiculously excited to see what we&#039;re going to get!\r\n', 'assets/u/qbanner/c1143ae105ce418db80e3c08c278e64f.jpg', 5, 1, 8, '2024-03-17 10:53:27', '2024-03-17 10:53:27', 0, NULL, NULL),
(51, '1F84b7lcb4004ddfc114073cA26d469o0AOc84001e993DB8C', 6, 'A Court This Cruel &#38; Lovely', 'Stacia Stark', 'This book, one word&hellip; incredible. Magic, slow burn, enemies to lovers and secrets. If you liked A Court of Thorns and Roses, then this is THE book to read.\r\n\r\nThe dual POV was much appreciated for Lorian and Prisca&rsquo;s characters. Prisca&rsquo;s character growth was phenomenal. She starts off as a weak, weary girl and comes out the other end a strong, independent, woman you wouldn&rsquo;t want to mess with. Laurien is broody, snarky and mysterious but he has so much charm you can&rsquo;t help but call him your next book boyfriend. I can&rsquo;t WAIT to dive into the next books in the series.\r\n\r\nThe narrators did an extraordinary job at capturing the character&rsquo;s essence in this novel. Their voices were soothing and easy to listen to. I very much enjoyed the audiobook version of this book because of them.\r\n\r\nI received this audiobook ARC via NetGalley and Dreamscape media in exchange for an honest review.\r\n', 'assets/u/qbanner/a1c2c467399a4e6f8d5ec4b1eb0716b5.jpg', 5, 0, 11, '2024-03-17 10:58:14', '2024-03-17 10:58:14', 0, NULL, NULL),
(52, 'cBcc336c0Eae91C9O4a68898F6e5l788c1E43B5cd4794f8oe', 6, 'Don&#39;t Let Her Stay', 'Nicola Sanders', 'This was twisty and gripping. Aww, I thought I had it!\r\n\r\nJoanne is married to Richard and they have a 4-month-old baby girl, Evie. They live in a gorgeous 5-bedroom country home in a quiet village near Chertsey. Richard has an adult daughter Chloe with his late wife but is estranged when he remarried. Chloe has a change of heart and wants to be part of her half-sister&#039;s life and decides she wants to move in.\r\n\r\nGrab your popcorn! Don&#039;t Let Her Stay is an engaging and fast-paced psych thriller. I really enjoy the story which at first seemed pretty tame, but I had a lot of fun with this family dynamic. I feel that someone will have to constantly look over their shoulder. Who can you really trust?\r\n\r\nI listened to this terrific audiobook narrated by Penelope Rawlins. I enjoyed her voice and glad I have two more books read by her on my shelf.\r\n', 'assets/u/qbanner/1e67bdfcfa9a424c84175ca6ff6b6c20.jpg', 5, 0, 7, '2024-03-17 11:01:20', '2024-03-17 11:01:20', 0, NULL, NULL),
(53, 'd78aol33D9096d6079cD8501185f34ddccc4E86ce75o0Bbf6', 7, 'The Kite Runner ', 'Khaled Hosseini', 'Finished this book about a month ago but it&#039;s taken me this long to write a review about it because I have such mixed feelings about it. It was a deeply affecting novel, but mostly not in a good way. I really wanted to like it, but the more I think about what I didn&#039;t like about the book, the more it bothers me. I even downgraded this review from two stars to one from the time I started writing it to the time I finished.', 'assets/u/qbanner/7f5891340548440eb82204144fe82095.png', 5, 0, 14, '2024-03-17 11:10:26', '2024-03-17 11:10:26', 0, NULL, NULL),
(55, '4bc09l02e1C702B7804ac0427b9fc7b23b90ccc2o19a2o649', 7, 'The Great Divide', 'Cristina HenrÃ­quez', 'The descriptive writing in this book instantly transported me to Panama in 1907: sweltering heat and pouring rain, endless mud sucking at boots, humid air thick as soup, stinging mosquitos, crowded streets and vendors singing their wares, exotic fruits dripping with sweetness, the vibrant green jungle; men and machines shoveling and digging and hauling away dirt, clay, and rocks in such varied colors that they &ldquo;flamed in the sun like a vast open wound&rdquo;. I&rsquo;m telling you, I was there.', 'assets/u/qbanner/6c2172b5ada74beebf4b7b3ead008e5b.jpg', 5, 0, 9, '2024-03-17 11:14:53', '2024-03-17 11:14:53', 0, NULL, NULL),
(56, 'o7f8f505412936d142dE2923595a03o8c9ab45lde0a8cbe69', 7, 'Weyward', 'Emilia Hart', 'In 2019, twenty-nine-year-old Kate Ayres flees London to escape an abusive relationship and finds sanctuary in Weyward Cottage, Crows Beck, Cumbria &ndash; a property left for her by her late Aunt Violet. As she embarks on rebuilding her life, her curiosity about the property prompts her to research her family history. As she learns more about her incredible legacy and the women who came before her, not only does Kate begin to see herself in a new light but also understands that she too possesses the power to take control of her life just like her ancestors.\r\n\r\nIn 1942, sixteen-year-old Violet Ayres leads a suffocating life in her home at Orton Hall where lives with her father and younger brother. She does not know much about her late mother except for what she overhears in hushed conversations among the household staff. She dreams of becoming a scientist, studying animals and traveling the world. But an unfortunate turn of events finds her cast out of her home, fending for herself alone in a cottage that once belonged to her mother.\r\n\r\nIn 1619, twenty-one-year-old Altha Weyward, a healer with a deep connection to nature just like her late mother Jennet is on trial after the death of a man in her village. Accused of witchcraft and imprisoned in a dark cell, she waits for the verdict which will seal her fate.\r\n', 'assets/u/qbanner/400279f99d1b4d19bc8bf241b054436b.jpg', 5, 0, 8, '2024-03-17 11:17:09', '2024-03-17 11:17:09', 0, NULL, NULL),
(57, 'oc0F9o3b10042ccF5e4le0378d9Eb339afe9d730258c74407', 7, 'The Covenant of Water', 'Abraham Verghese', '&ldquo;All families have secrets, but not all secrets are meant to deceive.&rdquo;\r\n\r\nDr. Abraham Verghese&rsquo;s The Covenant of Water follows three generations of an Indian Malayali Christian family in Kerala spanning from 1900 to the 1970s. As the novel begins, we meet twelve-year-old Mariamma preparing for her wedding day. Her groom is a forty-year-old widower with a young son &ndash; the owner of a vast expanse of land in Parambil. Unbeknownst to her at the time of marriage (and revealed to her after a tragic loss) is the fact that her husband&rsquo;s side of the family is plagued by a &ldquo;condition&rdquo; that has caused several family tragedies related to drowning across generations. We follow Mariamma or Big Ammachi as she is called and her family through the following decades, and how the condition impacts the lives of those whom she holds dear. Parallel to the Parambil narrative, we also follow the stories of Digby Kilgour, a Scottish doctor who joins the Indian Medical Services in British India as well as Dr. Rune Orquist, who devotes his life to the care of leprosy patients. Though the different threads of the story might seem a tad disjointed, the author weaves these threads into an expansive, breathtakingly beautiful narrative.\r\n', 'assets/u/qbanner/a952ebdc470d45a98dd88a4de9d3364f.jpg', 5, 0, 11, '2024-03-17 11:19:06', '2024-03-17 11:19:06', 0, NULL, NULL),
(58, '2oB67ed93aF6e3c834b0o4fcd75cal1603847f8fe702d9c62', 7, 'Lady Tan&#39;s Circle of Women', 'Lisa See', 'Set in China in the 1500&rsquo;s, Lady Tan&rsquo;s Circle of Women is dazzling, extraordinary, and fascinating.\r\n\r\nLisa See meticulously researched this book, and, at the same time, it never bogs down the storytelling. It has loads of strong female characters with complex relationships and power structures.\r\n\r\nThis would make an excellent book club pick and here are a few of my thoughts/observations while reading this book:\r\n\r\n1) There were many different women who had power to be cruel but went down a different path\r\n2) Tan Yunxian spends years embroidering shoes for her husband&rsquo;s family. How does this contrast with the disposable society that we have nowadays where we throw away most things within a year?\r\n3) What do we do for beauty/honor/those we love?\r\n4) Moxibustion is still used today to treat a variety of conditions. Why isn&rsquo;t it offered more commonly in Western society?\r\n\r\nAction-packed from the first chapter and highly addictive, I would highly recommend for anyone who likes strong female characters or books set in China.\r\n', 'assets/u/qbanner/f7f2d51767b74c0998f97b0d3c5d5b80.jpg', 5, 1, 4, '2024-03-17 11:22:05', '2024-03-17 11:22:05', 0, NULL, NULL),
(59, '188aO82ala9594E37c2De40e368o7fce22ad9ec47e21d77c8', 7, 'The Keeper of Hidden Books', 'Madeline Martin', 'Meticulously researched and beautifully penned, The Keeper of Hidden Books by Madeline Martin is a remarkable work of historical fiction. Set in Warsaw, Poland between 1939 and 1945, the story is presented from the first-person &ndash;perspective of Zofia Nowak, a young girl, a year away from completing her secondary education in 1939 as she lives through the German occupation of Poland. This a story of friendship, loyalty, sacrifice, survival and the power of literature in fostering hope and inspiring courage and selflessness in difficult times.\r\n\r\nZofia&rsquo;s world revolves around her family, her best friend Janina and her love for books. As WWII rages on, she and her friends start a book club they refer to as the &ldquo;anti-Hitler&rdquo; book club (later christened &ldquo;The Bandit Book Club&rdquo;) where they read and discuss books that have been banned by the Nazi regime. Zofia and Janina also volunteer at the Warsaw library &ndash; a place that becomes a sanctuary for those who lose their home due to the devastation in the aftermath of the bombings and those who find solace in the pages of a book. Zofia&rsquo;s older brother leaves in the middle of the night to fight in the war, her father is arrested and she and her mother lose their home. Zofia bears witness to the horrors of war &ndash; air raids and destruction of their beloved city, persecution of Jews and banning, confiscation and destruction of books not approved by the regime. When Janina and her family along with other Jewish families are moved into a Jewish ghetto and the Nazis begin to take over the libraries and reading rooms around the city, Zofia and her friends take it upon themselves to help as many people as they can, save books from being pulped and develop an underground library system, finding ways for readers to access the books they want.\r\n', 'assets/u/qbanner/654d23cb44ac41e6a839a779ed9c6dc9.jpg', 5, 1, 13, '2024-03-17 11:24:14', '2024-03-17 11:24:14', 0, NULL, NULL),
(60, 'e0o776a218b8c95c740a3o7fdfc7b10e565l191e73be3d38c', 7, 'The Vaster Wilds', 'Lauren Groff', 'After reading Groff&rsquo;s 2021 release, Matrix (which was also one of my fav reads of the year), I instantly fell in love with her writing. So after learning about her upcoming book, The Vaster Wilds, I&rsquo;ve been anxiously awaiting the chance to read her breathtaking work again.\r\n\r\nThe Vaster Wilds is set in early colonial America (in the Jamestown settlement) and is about a servant girl. The girl is a caretaker for a disabled child but when the settlement is struck by famine and the child is dying from starvation, the girl decides she&#039;s had enough and heads for the wilderness. With only a few items and a spark inside of her, the girl goes on a physical and spiritual adventure to discover the new world around her.\r\n\r\nNo modern author can capture the reader and make them a part of the story the way Groff can.\r\nLauren Groff&rsquo;s writing is a total visceral, sensory experience. She describes our protagonists surroundings in such detail that the reader also feels, sees, hears, and at times smells, what the character is experiencing. The writing in The Vaster Wilds is powerful and lyrical with its spiritual prose and haunting beauty. Just like Matrix, this is one that will stay with me long after finishing it.\r\n', 'assets/u/qbanner/777f011a81934680a5214397965a7bd5.jpg', 5, 0, 8, '2024-03-17 11:27:07', '2024-03-17 11:27:07', 0, NULL, NULL),
(61, '0ob5dbleF9375e52o1b54004d3c0cb00e7a4943b131c70ba6', 6, 'Vera Wong&#39;s Unsolicited Advice for Murderers', 'Jesse Q. Sutanto', 'I am here to &ldquo;spill the tea&rdquo; on the Audible version of this book-narrated by the wonderfully engaging Eunice Wong-this has been my FAVORITE audible listen so far this year!!\r\n\r\nVera Wong was born a rat, but she should have been a rooster-that is according to the characteristics of the signs in the Chinese horoscope!\r\n\r\nOwner of Vera Wang&rsquo;s World Famous Tea Shop in San Francisco&rsquo;s China Town, she wakes up promptly at 430 AM each morning, without an alarm, texts her Gen Z son, Tilly, with &ldquo;helpful&rdquo; advice, and makes her way downstairs to open the store.\r\n\r\nThe shop is struggling, and she only has one regular customer despite being an expert in the lost Art of preparing tea. Then one morning, Vera finds a dead man in the middle of her tea shop with a flash drive in his outstretched hand.\r\n', 'assets/u/qbanner/4391cd00299c486dbe19c8c8e10badf5.jpg', 5, 1, 6, '2024-03-17 11:29:11', '2024-03-17 11:29:11', 0, NULL, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Comment groups for use in "comments" table';

--
-- Dumping data for table `comment_groups`
--

INSERT INTO `comment_groups` (`book_id`, `comment_group_id`) VALUES
(1, 1),
(2, 2),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12),
(15, 15),
(16, 16),
(17, 17),
(18, 18),
(19, 19),
(20, 20),
(21, 21),
(22, 22),
(23, 23),
(24, 24),
(25, 25),
(26, 26),
(27, 27),
(28, 28),
(29, 29),
(30, 30),
(31, 31),
(32, 32),
(34, 34),
(35, 35),
(36, 36),
(37, 37),
(38, 38),
(39, 39),
(40, 40),
(41, 41),
(43, 43),
(45, 45),
(46, 46),
(47, 47),
(49, 49),
(51, 51),
(52, 52),
(53, 53),
(55, 55),
(56, 56),
(57, 57),
(58, 58),
(59, 59),
(60, 60),
(61, 61);

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of books under reservation.' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reserve_id`, `user_id`, `book_id`, `reserve_status`, `reserve_days`, `reserve_create_date`, `receive_date`) VALUES
(1, 1, 2, 1, 3, '2024-03-16 00:33:13', '2024-03-16 00:33:13'),
(2, 4, 4, 1, 3, '2024-03-16 21:53:42', '2024-03-16 21:53:42'),
(4, 5, 24, 1, 3, '2024-03-16 22:34:09', '2024-03-16 22:34:09');

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Account Credentials Storage. Go to user_info for information';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `user_privilege_rank`, `creation_date`) VALUES
(1, 'libraria', 'admin@mail.example', '$2y$10$j52IRbNlmg0lJAx8bj4nSuxTM6lo16R16dk.P7.ql.MN9BqhF6dn6', 1, '2024-03-16 00:15:14'),
(2, 'anotheruser', 'user@another.user', '$2y$10$aXD0FJeihKGlVn550aXK3udKiOvg6EG6qW3lQ9nbzJXjaZyDwTEeG', 0, '2024-03-16 00:27:04'),
(4, 'user1', 'tr@gmail.com', '$2y$10$qVN2n68dpn3aUyG03OWr4.pIDednLmjk.v.cuMtjGEPsj/knHZgh2', 0, '2024-03-16 21:51:34'),
(5, 'trntr', 'trntr49@gmail.com', '$2y$10$/dOeg3ntkNTLR/xLQN5KAOgSTHLIzOH0aXJ0EjDDv0ZkEjQ.SfQqK', 0, '2024-03-16 22:31:19'),
(6, 'jjk', 'jkrow@yahoo.com', '$2y$10$xMjlrX3y8v/2e0hiQ.evku.IjAIpZEIXv/GdXMFdd/.NvCzXX.uri', 0, '2024-03-17 10:30:36'),
(7, 'kln', 'kln345@gmail.com', '$2y$10$NqDsVBo1q.5HZLmN2tTrXe4SjNK0TCtcEdKkRfmT01.i5C4ZtFDWq', 0, '2024-03-17 11:08:46');

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
(1, 'Libraria', 'assets/u/avatar/87c6413a455e457a947319b88f4449f5.png', 'Hello, I am Libraria, the mastermind of this website.', 'Libraria, the best library platform that nobody uses.\r\n\r\nI am so sad and miserable.', '[1,4,5,6]', 4),
(2, 'John Hopkins Jr.', NULL, 'I play tag with my books.', '', '[2,44,45,46]', 4),
(4, 'user1', NULL, NULL, NULL, '[7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24]', 18),
(5, 'trntr', NULL, NULL, NULL, '[25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40]', 16),
(6, 'jjk', NULL, NULL, NULL, '[41,42,43,47,48,49,50,51,52,61]', 10),
(7, 'kln', NULL, NULL, NULL, '[53,54,55,56,57,58,59,60]', 8);

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
