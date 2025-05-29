-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 04:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `report_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quarterly_summaries`
--

CREATE TABLE `quarterly_summaries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `total_score` int(11) DEFAULT NULL,
  `average_score` float DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `report_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `report_date`) VALUES
(1, 2, '2025-05-29'),
(2, 1, '2025-05-29'),
(3, 6, '2025-05-29');

-- --------------------------------------------------------

--
-- Table structure for table `report_tasks`
--

CREATE TABLE `report_tasks` (
  `id` int(11) NOT NULL,
  `report_id` int(11) DEFAULT NULL,
  `task_content` text DEFAULT NULL,
  `self_grade` enum('A','B','C','D','E','F') DEFAULT NULL,
  `self_score` int(11) DEFAULT NULL,
  `management_grade` enum('A','B','C','D','E','F') DEFAULT NULL,
  `management_score` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `manager_grade` varchar(2) DEFAULT NULL,
  `manager_score` int(11) DEFAULT NULL,
  `manager_comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report_tasks`
--

INSERT INTO `report_tasks` (`id`, `report_id`, `task_content`, `self_grade`, `self_score`, `management_grade`, `management_score`, `comment`, `graded_by`, `manager_grade`, `manager_score`, `manager_comment`) VALUES
(1, 1, 'Hello world', 'C', 50, NULL, NULL, NULL, NULL, 'B', 80, 'report noted'),
(2, 1, 'day break show', 'C', 50, NULL, NULL, NULL, NULL, 'B', 80, 'noted'),
(3, 1, 'we a live now', 'B', 80, NULL, NULL, NULL, NULL, 'B', 80, 'wefwe'),
(4, 1, 'noted', 'C', 50, NULL, NULL, NULL, NULL, 'F', 39, 'erf'),
(5, 2, 'Hello Thursday', 'B', 80, NULL, NULL, NULL, NULL, 'C', 50, 'report noted'),
(6, 2, 'Hello world', 'B', 80, NULL, NULL, NULL, NULL, 'B', 80, 'report noted'),
(7, 2, 'day break show as ended', 'C', 50, NULL, NULL, NULL, NULL, 'D', 49, 'report noted'),
(8, 2, 'work still going', 'C', 50, NULL, NULL, NULL, NULL, 'C', 50, 'report noted'),
(9, 3, 'hello sir', 'C', 50, NULL, NULL, NULL, NULL, 'C', 50, 'ok');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `role` enum('staff','admin','corp-member','intern','supervisor','director','ceo') DEFAULT 'staff',
  `status` enum('pending','approved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `department`, `gender`, `role`, `status`, `created_at`) VALUES
(1, 'koniku femi', 'koniku@gmail.com', '$2y$10$dTUcVps2Y4TgPpErY/8Ye.3iuC1GlnXs3D6QreXLpcgvfZmcEoRtu', 'Program', 'Male', 'supervisor', 'approved', '2025-05-28 21:11:04'),
(2, 'mary ita', 'mary@gmail.com', '$2y$10$Unew3.Is.aP//zxLTnasrOCQdrDU36.beGo.1NAfjTUIO4zN5t8Aq', 'Program', 'Male', 'staff', 'approved', '2025-05-28 21:15:21'),
(3, 'ope yemi', 'ope@gmail.com', '$2y$10$BTZ8c2YLXeBnrSXLa44xluz1tsm0KyYbwSqyrI1EDipgsvyxKXDie', 'Program', 'Male', 'director', 'approved', '2025-05-28 21:16:07'),
(4, 'niran', 'niran@gmail.com', '$2y$10$twk5/mlAfOdgWwZHMVdove9.kb2FnZni7bRPI4DwqoCe6rJFl6HhK', 'ICT', 'Female', 'ceo', 'approved', '2025-05-28 21:21:55'),
(5, 'sumbo olatoye', 'olatoye@gmail.com', '$2y$10$AKBHFhNk3cBBFhlx6Bdlpu0X.6NvB07u.8hDX8BxEUWEes1GSU376', 'Program', 'Female', 'staff', 'approved', '2025-05-29 08:16:18'),
(6, 'temitope', 'tope@gmail.com', '$2y$10$7xdXi9.vR2.B5UurTktT/.X3cjA5oBp/ClXqFL1GIrUZ9Hd70a8Dm', 'Account', 'Male', 'staff', 'approved', '2025-05-29 12:32:58'),
(7, 'admin admin', 'admin@gmail.com', '$2y$10$Via86XJDGdlxGAhPx8U6C..8xFZCXtSrihY2ZrraUarzgHZK2xYia', 'Admin', 'Male', 'director', 'approved', '2025-05-29 13:54:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quarterly_summaries`
--
ALTER TABLE `quarterly_summaries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `report_tasks`
--
ALTER TABLE `report_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`);

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
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quarterly_summaries`
--
ALTER TABLE `quarterly_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `report_tasks`
--
ALTER TABLE `report_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report_tasks`
--
ALTER TABLE `report_tasks`
  ADD CONSTRAINT `report_tasks_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
