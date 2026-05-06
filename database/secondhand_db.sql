-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 08:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `secondhand_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Mobiteli'),
(2, 'Laptopi'),
(3, 'Namještaj'),
(4, 'Auto dijelovi');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `user1_id`, `user2_id`, `product_id`, `created_at`) VALUES
(3, 1, 3, 3, '2025-12-14 16:55:21'),
(6, 4, 1, 3, '2025-12-25 14:58:17'),
(7, 4, 1, 2, '2025-12-25 15:03:23'),
(9, 1, 4, 9, '2026-03-08 14:07:06'),
(10, 17, 4, 9, '2026-05-03 19:09:47'),
(11, 18, 4, 9, '2026-05-06 13:25:27'),
(12, 18, 17, 15, '2026-05-06 14:54:45'),
(17, 18, 1, 2, '2026-05-06 16:37:24');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `product_id`, `user_id`, `comment_text`, `created_at`) VALUES
(8, 3, 17, 'dsadsads', '2026-05-04 17:37:15'),
(9, 3, 17, 'dasdsadas', '2026-05-04 17:37:17'),
(10, 3, 17, 'dsadsadas', '2026-05-04 17:37:18'),
(11, 3, 17, 'dsadsadsa', '2026-05-04 17:37:20');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(7, 4, 9, '2025-12-14 19:48:00'),
(13, 1, 12, '2026-03-08 15:22:10'),
(14, 1, 6, '2026-03-08 15:27:43');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(20) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `chat_id`, `sender_id`, `message_text`, `file_path`, `file_type`, `file_name`, `created_at`, `is_read`) VALUES
(1, 7, 4, 'dsa', NULL, NULL, NULL, '2025-12-25 16:39:03', 0),
(11, 6, 4, 's', NULL, NULL, NULL, '2025-12-25 16:51:57', 0),
(12, 6, 4, 'sdasdas', NULL, NULL, NULL, '2025-12-25 16:52:59', 0),
(13, 6, 4, 's', NULL, NULL, NULL, '2025-12-25 16:53:47', 0),
(14, 6, 4, 'dsadas', NULL, NULL, NULL, '2025-12-25 16:59:15', 0),
(15, 3, 1, '123', NULL, NULL, NULL, '2025-12-25 20:14:14', 0),
(16, 9, 1, 'Hu', NULL, NULL, NULL, '2026-03-08 15:07:10', 0),
(17, 9, 1, '4.png', 'uploads/chat/msg_9_1772985248_7379.png', 'image', '4.png', '2026-03-08 16:54:08', 0),
(18, 10, 17, 'cyxcyxcyx', NULL, NULL, NULL, '2026-05-03 21:20:15', 0),
(19, 10, 17, 'dasdsa', NULL, NULL, NULL, '2026-05-03 21:22:58', 0),
(20, 10, 17, 'image.png', 'uploads/chat/msg_10_1777836605_2645.png', 'image', 'image.png', '2026-05-03 21:30:05', 0),
(21, 11, 18, 'dsadsadsdasd', NULL, NULL, NULL, '2026-05-06 15:25:31', 0),
(22, 11, 18, 'dsadsa', NULL, NULL, NULL, '2026-05-06 15:34:29', 0),
(23, 11, 18, 'dsadsad', NULL, NULL, NULL, '2026-05-06 15:46:37', 0),
(24, 11, 18, 'ddasdas', NULL, NULL, NULL, '2026-05-06 15:46:38', 0),
(25, 11, 18, 'dsadsa', NULL, NULL, NULL, '2026-05-06 15:46:39', 0),
(26, 11, 18, 'dasdas', NULL, NULL, NULL, '2026-05-06 15:47:55', 0),
(27, 11, 18, 'dsad', NULL, NULL, NULL, '2026-05-06 16:03:18', 0),
(28, 11, 18, 'd', NULL, NULL, NULL, '2026-05-06 16:03:18', 0),
(29, 11, 18, 'dsad', NULL, NULL, NULL, '2026-05-06 16:03:19', 0),
(30, 11, 18, 'das', NULL, NULL, NULL, '2026-05-06 16:03:19', 0),
(31, 11, 18, 's', NULL, NULL, NULL, '2026-05-06 16:03:19', 0),
(32, 11, 18, 'asd', NULL, NULL, NULL, '2026-05-06 16:03:19', 0),
(33, 11, 18, 'as', NULL, NULL, NULL, '2026-05-06 16:03:20', 0),
(34, 11, 18, 'as', NULL, NULL, NULL, '2026-05-06 16:03:20', 0),
(35, 11, 18, 'asdas', NULL, NULL, NULL, '2026-05-06 16:03:20', 0),
(36, 11, 18, 'as', NULL, NULL, NULL, '2026-05-06 16:03:20', 0),
(37, 11, 18, 'as', NULL, NULL, NULL, '2026-05-06 16:03:20', 0),
(38, 11, 18, 'd', NULL, NULL, NULL, '2026-05-06 16:03:20', 0),
(39, 11, 18, 'asd', NULL, NULL, NULL, '2026-05-06 16:03:21', 0),
(40, 11, 18, 'ad', NULL, NULL, NULL, '2026-05-06 16:03:21', 0),
(41, 11, 18, 'adsa', NULL, NULL, NULL, '2026-05-06 16:03:21', 0),
(42, 11, 18, 'as', NULL, NULL, NULL, '2026-05-06 16:03:21', 0),
(43, 12, 18, 'dsa', NULL, NULL, NULL, '2026-05-06 16:54:55', 1),
(44, 12, 18, 'dsadsadsadsad', NULL, NULL, NULL, '2026-05-06 17:01:12', 1),
(45, 12, 18, 'dasdsadsad', NULL, NULL, NULL, '2026-05-06 17:01:13', 1),
(46, 12, 18, 'dsadsadsa', NULL, NULL, NULL, '2026-05-06 17:01:15', 1),
(47, 12, 18, 'dsadasdsad', NULL, NULL, NULL, '2026-05-06 17:01:17', 1),
(48, 12, 18, 'dsadsadasdsa', NULL, NULL, NULL, '2026-05-06 17:01:18', 1),
(49, 17, 18, 'dasdsadsadsadsa', NULL, NULL, NULL, '2026-05-06 18:37:26', 0),
(50, 12, 17, 'dasda', NULL, NULL, NULL, '2026-05-06 18:39:46', 1),
(51, 12, 18, 'dasdsadsadas', NULL, NULL, NULL, '2026-05-06 18:41:32', 1),
(52, 12, 18, 'dasdas', NULL, NULL, NULL, '2026-05-06 18:41:34', 1),
(53, 12, 17, 'image.png', 'uploads/chat/msg_12_1778085961_1634.png', 'image', 'image.png', '2026-05-06 18:46:01', 1),
(54, 17, 18, 'das', NULL, NULL, NULL, '2026-05-06 18:57:25', 0),
(56, 17, 18, 'dasdsa', NULL, NULL, NULL, '2026-05-06 19:08:05', 0),
(57, 17, 18, 'dsa', NULL, NULL, NULL, '2026-05-06 19:08:06', 0),
(58, 17, 18, 'sad', NULL, NULL, NULL, '2026-05-06 19:08:06', 0),
(59, 17, 18, 'asd', NULL, NULL, NULL, '2026-05-06 19:08:07', 0),
(60, 17, 18, 'a', NULL, NULL, NULL, '2026-05-06 19:08:07', 0),
(61, 17, 18, 'sa', NULL, NULL, NULL, '2026-05-06 19:08:08', 0),
(62, 17, 18, 'das', NULL, NULL, NULL, '2026-05-06 19:08:09', 0),
(63, 17, 18, 'asdasd', NULL, NULL, NULL, '2026-05-06 19:08:09', 0),
(64, 17, 18, 'sad', NULL, NULL, NULL, '2026-05-06 19:08:09', 0),
(65, 17, 18, 'cc', NULL, NULL, NULL, '2026-05-06 19:08:24', 0),
(66, 12, 18, 'dsadsadsdadas', NULL, NULL, NULL, '2026-05-06 20:19:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `product_id`, `buyer_id`, `seller_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, 4, 1, 'accepted', '2025-12-25 17:09:34', '2025-12-25 21:10:53'),
(4, 2, 17, 1, 'cancelled', '2026-05-03 21:31:51', '2026-05-03 21:42:06'),
(5, 5, 17, 1, 'pending', '2026-05-04 19:18:46', '2026-05-04 19:18:46');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `product_condition` enum('new','used') DEFAULT 'used',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `category_id`, `title`, `description`, `price`, `location`, `product_condition`, `created_at`) VALUES
(2, 1, 1, 'iPhone11', 'Odličan, malo korišten', 650.00, 'Tuzla', 'used', '2025-12-14 16:15:32'),
(3, 1, 2, 'iPhone11', 'Odličan, malo korišten', 650.00, 'Tuzla', 'used', '2025-12-14 16:15:46'),
(5, 1, 3, 'iPhone11', 'Odličan, malo korišten', 650.00, 'Tuzla', 'used', '2025-12-14 16:16:01'),
(6, 1, 4, 'iPhone11', 'Odličan, malo korišten', 650.00, 'Tuzla', 'used', '2025-12-14 16:16:04'),
(9, 4, 3, 'a', 'aa', 123.00, 'Srebrenik', 'new', '2025-12-14 19:25:53'),
(12, 1, 3, '?!&amp;amp;amp;quot;=!#!&amp;amp;amp;quot;', 'Novoaaa', 15000.00, 'Lukavac', 'new', '2026-03-08 14:17:05'),
(15, 17, 1, 'asd', 'dsa', 2.00, 'dsa', 'new', '2026-05-04 18:10:24');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`) VALUES
(1, 9, 'uploads/products/9/1765740353_4315.webp'),
(4, 12, 'uploads/products/12/1772979425_3431.png'),
(5, 12, 'uploads/products/12/1772979425_9174.png'),
(6, 12, 'uploads/products/12/1772979425_9626.png'),
(7, 12, 'uploads/products/12/1772979425_3927.png'),
(8, 12, 'uploads/products/12/1772979425_2980.png'),
(9, 12, 'uploads/products/12/1772979425_4368.png'),
(15, 15, 'uploads/products/15/1777918224_5608.png'),
(16, 15, 'uploads/products/15/1777918444_3698.png'),
(17, 15, 'uploads/products/15/1777918527_2129.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `NAME`, `email`, `PASSWORD`, `role`, `avatar`, `bio`, `phone`, `location`, `created_at`) VALUES
(1, 'Maid', 'maid@test.com', '$2y$10$/Op0WQhf86ZZDtGyJ82A2.0HEtc7u/CvDt5oZF.ims3Lv.4ZS5Zmy', 'admin', 'uploads/avatars/avatar_1_1772978960.jpg', 'Prodaja', '061-234-567', 'Tuzla', '2025-12-14 15:46:11'),
(3, 'Maid2', 'maid2@test.com', '$2y$10$sVsJseScIgz61PWIvRO8ruAHA1kRCbGwWSn12Vdz1BQGIfNgdgAtu', 'user', NULL, NULL, NULL, NULL, '2025-12-14 16:54:57'),
(4, 'Maid', 'maid.zuka@gmail.com', '$2y$10$lGoGSe4UB.x0IEJ/EKg0I.t/jGtz2mzhN8im.U9MERM1DbhsKflCm', 'user', 'uploads/avatars/avatar_4_1766694382.png', NULL, NULL, NULL, '2025-12-14 18:33:00'),
(7, 'Maid', 'admin@example.com', '$2y$10$LqaZAg3dGY2KxV7yAmB05upS5NE9FcI6oGUjsvGencN3Eb.X5aDMC', 'user', NULL, NULL, NULL, NULL, '2025-12-14 18:33:47'),
(16, 'Maid', 'maid@gmail.com', '$2y$10$tUhycNcfnaUZ96i58TNye.h00W1p9/5nCJsCbC8qAah5141nGFLJ.', 'user', NULL, NULL, NULL, NULL, '2026-03-08 12:40:28'),
(17, 'Roki', 'roki@gmail.com', '$2y$10$O3MBQQQ2CKBS0DsVjMMp7ukTkvTEx4YsZ4N0Lx4DH4aJe94PyAW7y', 'admin', 'uploads/avatars/avatar_17_1777835408.png', 'aa', NULL, '', '2026-05-03 17:09:29'),
(18, 'ssss', 's@gmail.com', '$2y$10$AVpSux7iBMtUq1/9xDaL9uNF0gNCCU2wZz4icIAePHbJy1GLHZtmK', 'user', 'uploads/avatars/avatar_18_1778008651.jpg', '', NULL, '', '2026-05-04 18:33:21'),
(19, 'dsa', 'das', '$2y$10$21EXUObCuSvhjYDMqKyJmu/GcSY6J7f2cwKzmIMJsvQ57fuu4EB2O', 'user', NULL, NULL, NULL, NULL, '2026-05-04 18:40:56'),
(20, 'Admin', 'admin@admin.com', '$2y$10$dgPWwBW3PDRSaYPlpFG0dO3MwA6XZk2uEyuqmhWLw1d32nSWQm70i', 'admin', NULL, NULL, NULL, NULL, '2026-05-06 18:31:03'),
(21, 'User', 'user@user.com', '$2y$10$EiUDZqKMPTBW/MD26xNm7evHGFKC3d4ZAjPBgAub5vaSBdbWmAz5e', 'user', NULL, NULL, NULL, NULL, '2026-05-06 18:32:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user1_id` (`user1_id`),
  ADD KEY `user2_id` (`user2_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_fav` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_buyer` (`buyer_id`),
  ADD KEY `idx_orders_seller` (`seller_id`),
  ADD KEY `idx_orders_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chats_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chats_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_chat` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
