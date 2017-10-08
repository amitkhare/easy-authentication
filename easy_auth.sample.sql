-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 08, 2017 at 09:29 AM
-- Server version: 5.7.13-0ubuntu0.16.04.2
-- PHP Version: 7.0.22-2+ubuntu16.04.1+deb.sury.org+4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `easy_auth`
--

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'https://avatars.io/static/default_128.jpg',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `firstname`, `middlename`, `lastname`, `gender`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 1, 'Amit', NULL, NULL, 'male', 'https://avatars.io/static/default_128.jpg', '2017-09-26 19:00:42', '2017-09-26 19:00:42'),
(2, 2, 'Admin', NULL, NULL, 'male', 'https://avatars.io/static/default_128.jpg', '2017-09-26 19:01:08', '2017-09-26 19:01:08');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role` varchar(255) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role`, `created_at`, `updated_at`) VALUES
(1, 'user', '2016-12-30 07:53:17', NULL),
(2, 'editor', '2016-12-30 07:53:17', NULL),
(3, 'moderator', '2016-12-30 07:53:41', NULL),
(4, 'admin', '2016-12-30 07:53:41', NULL),
(5, 'superadmin', '2016-12-30 07:54:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL DEFAULT '0.0.0.0',
  `user_agent` text,
  `referrer` varchar(100) NOT NULL DEFAULT 'Not Available',
  `session_data` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 NOT NULL,
  `password` varchar(100) NOT NULL,
  `mobile` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `allowed_tokens` int(2) NOT NULL DEFAULT '3',
  `email_verification_hash` varchar(255) DEFAULT NULL,
  `password_recovery_hash` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `mobile`, `allowed_tokens`, `email_verification_hash`, `password_recovery_hash`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'amit', 'amit@khare.co.in', '$2y$10$ciHYmVAoVf0faLm97pXET.AaiZycm4fcnhE4Us7wd/B0o7ZHbCyqK', NULL, 3, '', NULL, 1, '2017-09-26 19:00:42', '2017-09-26 19:00:42'),
(2, 'admin', 'admin@github.net', '$2y$10$ciHYmVAoVf0faLm97pXET.AaiZycm4fcnhE4Us7wd/B0o7ZHbCyqK', NULL, 3, NULL, NULL, 1, '2017-09-26 19:01:08', '2017-09-26 19:02:53');

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

CREATE TABLE `users_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`id`, `user_id`, `role_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 1, 2),
(4, 1, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role` (`role`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `users_roles`
--
ALTER TABLE `users_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
