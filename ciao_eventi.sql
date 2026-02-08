-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2025 at 04:10 PM
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
-- Database: `ciao_eventi`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `category` enum('Festival','Party','Concert','Nightlife','Social','Music') NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(200) NOT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `banner_image` varchar(255) DEFAULT NULL,
  `ticket_link` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `likes_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','edited','deleted') DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `category`, `event_date`, `event_time`, `location`, `venue`, `banner_image`, `ticket_link`, `user_id`, `likes_count`, `created_at`, `status`, `updated_at`) VALUES
(7, 'Flashback Night', 'Experience an unforgettable musical night with the band Flashback, bringing high-energy performances and crowd-favorite hits to Galle.', 'Music', '2026-01-02', '20:00:00', 'Galle', 'public ground', '694fc4c684937_1766835398.jpg', 'https://www.tickets.lk', 5, 3, '2025-12-27 11:36:38', 'active', '2025-12-27 12:26:16'),
(8, 'DJ Party', 'Get ready to dance the night away at the ultimate DJ Party in Kandy! Experience pulsating beats, electrifying lights, and non-stop energy as the DJ spins the hottest tracks to keep the crowd moving.', 'Nightlife', '2026-05-02', '20:00:00', 'Kandy', 'public ground', '694fc57bac8dd_1766835579.jpg', 'https://www.tickets.lk', 4, 3, '2025-12-27 11:39:39', 'active', '2025-12-27 12:54:03'),
(9, 'Party Night', 'Join us for an electrifying Party Night in Kandy! Enjoy nonstop music, vibrant lights, and an energetic atmosphere that will keep you dancing all night long.', 'Party', '2026-02-06', '22:00:00', 'Kandy', 'Peradeniya Garden', '694fc60455307_1766835716.jpg', 'https://www.tickets.lk', 6, 0, '2025-12-27 11:41:56', 'edited', '2025-12-27 12:39:38');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `event_id`, `created_at`) VALUES
(69, 5, 7, '2025-12-27 11:36:42'),
(72, 4, 8, '2025-12-27 11:39:47'),
(74, 4, 7, '2025-12-27 11:39:49'),
(95, 6, 7, '2025-12-27 12:26:16'),
(107, 6, 8, '2025-12-27 12:39:55'),
(109, 5, 8, '2025-12-27 12:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `profile_pic`, `bio`, `created_at`, `updated_at`) VALUES
(4, 'ggs', 'piyarathna0352289743@gmail.com', '$2y$10$5B4JUpm0UMdp1CHAYDNjW.uNpfaCYni7AWjyI7NNWMxUGn7Q/a35C', NULL, NULL, NULL, '2025-12-27 09:12:47', '2025-12-27 09:12:47'),
(5, 'kamal', 'yoyooff81@gmail.com', '$2y$10$rka9qRilKfMmbNgf3mU0p.fKrhzr8uowkw7GFb0MNjUnCF7htYqiu', NULL, NULL, NULL, '2025-12-27 11:14:02', '2025-12-27 11:14:02'),
(6, 'Amara', 'thinkpadoff8@gmail.com', '$2y$10$TFO9SLvbI/PB50LYv3ssduEUbq94R7vrpWD9F2KgSF6rib71e5Qe6', NULL, NULL, NULL, '2025-12-27 11:40:23', '2025-12-27 11:40:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`event_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
