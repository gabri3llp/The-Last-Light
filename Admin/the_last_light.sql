-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2026 at 08:29 AM
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
-- Database: `the_last_light`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(2, 'Food'),
(5, 'Materials'),
(1, 'Medicine'),
(7, 'Quests'),
(6, 'Services'),
(4, 'Shelter'),
(3, 'Weapons');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `notice_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `notice_id`, `user_id`, `comment`, `created_at`) VALUES
(2, 5, 4, 'Test', '2026-08-26 20:40:13'),
(3, 7, 2, 'Very good energy bars', '2026-08-27 01:12:35'),
(6, 9, 4, 'Nice brick', '2026-08-27 02:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `notice_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `trade_value` varchar(50) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `settlement` varchar(50) NOT NULL,
  `rarity` enum('common','uncommon','rare','epic','legendary','mythic') NOT NULL DEFAULT 'common',
  `status` enum('active','expired','completed') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `accepted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`notice_id`, `user_id`, `category_id`, `title`, `description`, `trade_value`, `image_path`, `settlement`, `rarity`, `status`, `created_at`, `accepted_by`) VALUES
(1, 1, 1, 'Healing Herd', 'Fresh herbs found near the old bridge, shout out to my girl Ellie!', '2 MRI rations', 'healing-herbs.png', ' Ravens Hollow', 'rare', 'active', '2026-08-16 19:01:05', NULL),
(3, 1, 3, 'Shiv', 'Stab that mf clicker 2x', 'A brick', 'notice_6a8331eede7fe0.88952121.jpeg', 'Pittsburgh', 'uncommon', 'active', '2026-08-17 18:08:14', NULL),
(5, 3, 4, 'No Shelter for the week', 'You canT stay at my safe house for a week', 'Medkit', NULL, 'Ravens hook', 'legendary', 'active', '2026-08-26 18:16:05', NULL),
(7, 3, 2, 'Fresh energy bars', 'Food Rations', 'A box of ammo', NULL, 'Jacksonville', 'common', 'completed', '2026-08-26 23:55:42', 2),
(9, 3, 3, 'A brick', 'TO fit people with', 'A box of ammo', NULL, 'Jacksonville', 'common', 'completed', '2026-08-27 02:40:31', 4),
(11, 3, 3, 'A pistol', 'A pistol in good condition', '2x Medkit', 'notice_6a8fd349848846.88260629.png', 'Jacksonville', 'epic', 'active', '2026-08-27 08:03:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `settlement` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('survivor','merchant','admin') NOT NULL DEFAULT 'survivor',
  `trust_rating` decimal(2,1) NOT NULL DEFAULT 3.0,
  `profile_img` varchar(255) DEFAULT 'default.jpg',
  `joined_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `settlement`, `email`, `password_hash`, `role`, `trust_rating`, `profile_img`, `joined_at`) VALUES
(1, 'testuser', 'Ravens Hollow', 'testMEEE@gmail.com', 'Password', 'survivor', 3.0, 'default.jpg', '2026-08-16 18:57:26'),
(2, 'Gabriel', 'Ravens hook', 'Test@gmail.com', '$2y$10$vKSM1UlfJJTgEMR1HSMCle5LI13RswcWad4DryIPVo8tuF9.vyYWK', 'survivor', 1.5, 'user_2_1787785694.jpeg', '2026-08-18 16:09:46'),
(3, 'Kyle', 'Jacksonville', 'Kyle@gmail.com', '$2y$10$C2uZnXZAK9jAc5oV9hnEYuyMHqdIeC3RAG2z6HewTkinH0GO6YAlG', 'survivor', 1.0, 'user_3_1787811018.jpeg', '2026-08-24 14:01:11'),
(4, 'GabrielA', 'Ravens hook', 'Gabriel@gmail.com', '$2y$10$q0kqkvzOi73Yg3JYOhHGyO3wJxqToIF4wB7NphOiSoAjUjtupeS/u', 'admin', 2.0, 'default.jpg', '2026-08-26 20:29:14');

-- --------------------------------------------------------

--
-- Table structure for table `user_declined_notices`
--

CREATE TABLE `user_declined_notices` (
  `user_id` int(11) NOT NULL,
  `notice_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_declined_notices`
--

INSERT INTO `user_declined_notices` (`user_id`, `notice_id`) VALUES
(2, 6),
(3, 5),
(4, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `notice_id` (`notice_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`notice_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_declined_notices`
--
ALTER TABLE `user_declined_notices`
  ADD PRIMARY KEY (`user_id`,`notice_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `notice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`notice_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `notices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `notices_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
