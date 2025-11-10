-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 03:58 PM
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
-- Database: `capstone_try2_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` varchar(36) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `user_id`, `created_at`, `updated_at`) VALUES
('55a71d72-b6f3-11f0-947e-38a746026eda', '559ea09d-b6f3-11f0-947e-38a746026eda', '2025-11-01 07:21:07', '2025-11-01 07:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` varchar(36) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `membership_expiry` date DEFAULT NULL,
  `qr_code_data` text DEFAULT NULL,
  `current_weight_kg` decimal(5,2) DEFAULT NULL,
  `current_height_cm` decimal(5,2) DEFAULT NULL,
  `goal_calories_kcal` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `user_id`, `phone_number`, `membership_expiry`, `qr_code_data`, `current_weight_kg`, `current_height_cm`, `goal_calories_kcal`, `created_at`, `updated_at`) VALUES
('0772c38a-3329-471f-a2f6-26fcb68edb10', '710d6aa9-8f42-40ca-818a-bfa69ceb8670', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-10 14:09:38', '2025-11-10 14:09:38'),
('4393f3ff-58cf-442d-a4ab-0218efd4369a', '40947c8c-dbee-4d2b-87c4-c8ebbd607e1d', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-01 07:31:34', '2025-11-01 07:31:34'),
('558ed31d-b6f3-11f0-947e-38a746026eda', '5586f63d-b6f3-11f0-947e-38a746026eda', '123-456-7890', '2024-12-31', '{\"user_id\": \"5586f63d-b6f3-11f0-947e-38a746026eda\", \"access_level\": \"member\"}', 75.50, 170.00, 2000, '2025-11-01 07:21:07', '2025-11-01 07:21:07'),
('9b0ed54d-74dd-4a43-ad09-c4acf3e420f1', '1ecbb217-26ac-4548-84ea-8fb3630bdb8e', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-08 14:53:08', '2025-11-08 14:53:08');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_otps`
--

CREATE TABLE `password_reset_otps` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `token_expires_at` datetime DEFAULT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_otps`
--

INSERT INTO `password_reset_otps` (`id`, `email`, `otp_code`, `reset_token`, `created_at`, `expires_at`, `token_expires_at`, `used`) VALUES
(23, 'banilaaya@gmail.com', '245080', NULL, '2025-11-10 09:45:24', '2025-11-10 10:55:24', NULL, 0),
(26, 'airlights.essential@gmail.com', '074374', NULL, '2025-11-10 14:13:05', '2025-11-10 15:23:05', NULL, 0),
(29, 'arianebanila2@gmail.com', '946839', '5a3c066344143e7bc95d9962cab9f447e33bfe158a3b8e55cf3c289afc719ff8', '2025-11-10 14:29:01', '2025-11-10 15:39:01', '2025-11-10 15:48:32', 0);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_rate_limit`
--

CREATE TABLE `password_reset_rate_limit` (
  `id` int(11) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `attempts` int(11) DEFAULT 1,
  `first_attempt` datetime NOT NULL,
  `last_attempt` datetime NOT NULL,
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_rate_limit`
--

INSERT INTO `password_reset_rate_limit` (`id`, `identifier`, `attempts`, `first_attempt`, `last_attempt`, `locked_until`) VALUES
(3, 'a4a4d65238fff95274dd087168365c532915b1722695f0821f8160411dc51c4c', 3, '2025-11-10 17:44:29', '2025-11-10 17:45:24', '2025-11-10 22:28:32'),
(4, '83f2ef3f08c81524d12c2bb2169a444f5525f3fb5caff589ef48e47e87ba2e7c', 3, '2025-11-10 22:09:55', '2025-11-10 22:13:05', '2025-11-10 22:43:56'),
(6, '00ebbd8c8828fd6c4108d692e284f5079e44ed09dc3623c0b3abd9a0b9984b50', 1, '2025-11-10 22:26:09', '2025-11-10 22:26:09', NULL),
(8, 'a682ee1d74b428787f37039ec357c9f602b737fad7fc342b3e58cd1bbb6d6e8d', 1, '2025-11-10 22:29:01', '2025-11-10 22:29:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainer_id` varchar(36) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`trainer_id`, `user_id`, `specialization`, `bio`, `contact_number`, `created_at`, `updated_at`) VALUES
('559a6657-b6f3-11f0-947e-38a746026eda', '5592adf1-b6f3-11f0-947e-38a746026eda', 'Bodybuilding, HIIT', 'Experienced trainer with a passion for helping clients achieve their strength and fitness goals.', '098-765-4321', '2025-11-01 07:21:07', '2025-11-01 07:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `user_type` enum('member','trainer','admin') NOT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_picture` varchar(255) DEFAULT 'default_profile.png',
  `profile_image_data` longblob DEFAULT NULL,
  `profile_image_mime` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `first_name`, `last_name`, `user_type`, `status`, `created_at`, `updated_at`, `profile_picture`, `profile_image_data`, `profile_image_mime`) VALUES
('1ecbb217-26ac-4548-84ea-8fb3630bdb8e', 'banilaaya@gmail.com', '$2y$10$CufJ.2tKjzp4ZTbqOuHf/u4e3fi5PTLkzNrhhyY8Ee7UK63fcdTKq', 'Aya', '', 'member', 'active', '2025-11-08 14:53:08', '2025-11-10 09:25:36', 'default_profile.png', NULL, NULL),
('40947c8c-dbee-4d2b-87c4-c8ebbd607e1d', 'arianebanila2@gmail.com', '$2y$10$FByi8qt/X7ZLl/Njki0PGuKnFFl/HCUh4SQj7qyfGgvv9ZUTAxTQy', 'Ariane', 'Banila', 'member', 'active', '2025-11-01 07:31:34', '2025-11-10 14:28:23', 'default_profile.png', NULL, NULL),
('5586f63d-b6f3-11f0-947e-38a746026eda', 'sample.member@example.com', '$2y$10$Q7rZ.C1M2s0X5P3j6Q1n4u8O9i0J.K.L.M.N.O.P.Q.R.S.T.U.V', 'Jane', 'Doe', 'member', 'active', '2025-11-01 07:21:07', '2025-11-01 07:21:07', 'default_profile.png', NULL, NULL),
('5592adf1-b6f3-11f0-947e-38a746026eda', 'trainer.mike@example.com', '$2y$10$Q7rZ.C1M2s0X5P3j6Q1n4u8O9i0J.K.L.M.N.O.P.Q.R.S.T.U.V', 'Mike', 'Stevens', 'trainer', 'active', '2025-11-01 07:21:07', '2025-11-01 07:21:07', 'default_profile.png', NULL, NULL),
('559ea09d-b6f3-11f0-947e-38a746026eda', 'admin.boss@example.com', '$2y$10$Q7rZ.C1M2s0X5P3j6Q1n4u8O9i0J.K.L.M.N.O.P.Q.R.S.T.U.V', 'Admin', 'User', 'admin', 'active', '2025-11-01 07:21:07', '2025-11-01 07:21:07', 'default_profile.png', NULL, NULL),
('710d6aa9-8f42-40ca-818a-bfa69ceb8670', 'airlights.essential@gmail.com', '$2y$10$F2cfvKJDY8BfPBuD/LmZXuZbpdZHI0tusbIubbVcoKlq5lv7vZt8S', 'Ariane', 'Banila', 'member', 'active', '2025-11-10 14:09:38', '2025-11-10 14:09:38', 'default_profile.png', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `password_reset_otps`
--
ALTER TABLE `password_reset_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `otp_code` (`otp_code`),
  ADD KEY `expires_at` (`expires_at`),
  ADD KEY `idx_reset_token` (`reset_token`);

--
-- Indexes for table `password_reset_rate_limit`
--
ALTER TABLE `password_reset_rate_limit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_identifier` (`identifier`),
  ADD KEY `idx_locked_until` (`locked_until`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainer_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `password_reset_otps`
--
ALTER TABLE `password_reset_otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `password_reset_rate_limit`
--
ALTER TABLE `password_reset_rate_limit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainers`
--
ALTER TABLE `trainers`
  ADD CONSTRAINT `trainers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
