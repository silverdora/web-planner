-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Apr 06, 2026 at 05:59 PM
-- Server version: 12.2.2-MariaDB-ubu2404
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `planner`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `user_id`, `name`) VALUES
(1, 3, 'life'),
(4, 7, 'study'),
(5, 7, 'sport'),
(6, 7, 'hungarian');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `priority` varchar(25) NOT NULL,
  `status` varchar(25) NOT NULL,
  `due_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `category_id`, `priority`, `status`, `due_date`) VALUES
(3, 2, 'give Zorro lekker stukjes', 'miauw', NULL, 'high', 'in progress', '2026-04-03 08:30:00'),
(7, 3, 'give Dasha koffie', '', NULL, 'high', 'in progress', '2026-04-10 13:41:00'),
(9, 3, 'miaw11', 'прям сильно', 1, 'high', 'done', '2026-04-23 15:30:00'),
(32, 7, 'math homework', 'p.22 ex.3', 4, 'medium', 'done', '2026-05-04 19:52:00'),
(33, 7, 'read article', '', 6, 'low', 'created', '2026-04-22 19:53:00'),
(34, 7, 'call granny', '', NULL, 'high', 'created', '2026-04-13 19:54:00'),
(35, 7, 'clean boots', NULL, 5, 'low', 'created', '2026-04-13 19:56:05'),
(36, 7, 'workout', NULL, 5, 'meduim', 'created', '2026-04-19 19:56:05'),
(37, 7, 'buy tequila', NULL, NULL, 'low', 'created', '2026-04-19 19:56:05'),
(38, 7, 'yoga', NULL, 5, 'meduim', 'created', '2026-04-19 19:56:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`) VALUES
(1, 'john_doe', 'john.doe@example.com', '$2y$12$NML4DSTuNJilklqx2l04IevFPUcxIV/374yDUnxvFtXUqdwss4xxa'),
(2, 'jane_doe', 'jane.doe@example.com', '$2y$12$3hbLNm9c.MHqAOXA6ovZkuC1iIX3LiQa/D1UetLJaNQT4XmBaCqX.'),
(3, 'Andrey Tikhonov', 'andrey.tikhonov@example.com', '$2y$12$8M/rnser3RjgYWhIMbr4ROxRtisQt9pP8l/ZEZ2XoYybTbIRf.xRy'),
(5, 'hehek', '0@hehe.k', '$2y$12$gVTHJhbC06sXnmpAEYItKuwi9/L8HfHhVK1MvMMkE3ws.RW4JPjCy'),
(7, 'Anna van der Test', 'annavandertest@example.com', '$2y$12$Kgk6lq8oBKQRDIAFGNYsl.GTy8XdNY1TBsvbJo9ObmyWKZ5gyPmrq');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_task_user` (`user_id`),
  ADD KEY `category_id` (`category_id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_user_category` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_task_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_task_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
