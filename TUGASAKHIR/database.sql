-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 09, 2026 at 12:23 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digedu`
--

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE `forum_replies` (
  `id` int NOT NULL,
  `thread_id` int NOT NULL,
  `user_id` int NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `forum_replies`
--

INSERT INTO `forum_replies` (`id`, `thread_id`, `user_id`, `isi`, `created_at`) VALUES
(5, 4, 4, 'gatau', '2026-06-02 08:01:33');

-- --------------------------------------------------------

--
-- Table structure for table `forum_threads`
--

CREATE TABLE `forum_threads` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` enum('Matematika','Bahasa Indonesia','Bahasa Inggris','Penalaran Umum','Umum') COLLATE utf8mb4_unicode_ci DEFAULT 'Umum',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `forum_threads`
--

INSERT INTO `forum_threads` (`id`, `user_id`, `judul`, `isi`, `kategori`, `created_at`) VALUES
(4, 6, 'nanya soal kuadrat', 'ini maksud soalnya apa ya ?', 'Matematika', '2026-06-02 07:59:45');

-- --------------------------------------------------------

--
-- Table structure for table `tryout_results`
--

CREATE TABLE `tryout_results` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `paket` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai` int NOT NULL,
  `total_soal` int NOT NULL,
  `benar` int NOT NULL,
  `waktu_menit` int NOT NULL,
  `dikerjakan` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tryout_results`
--

INSERT INTO `tryout_results` (`id`, `user_id`, `paket`, `nilai`, `total_soal`, `benar`, `waktu_menit`, `dikerjakan`) VALUES
(7, 4, 'Tryout UTBK #1 – Penalaran Umum', 0, 5, 0, 1, '2026-06-01 19:12:24'),
(8, 6, 'Tryout UTBK #1 – Penalaran Umum', 0, 5, 0, 1, '2026-06-02 07:56:51'),
(9, 6, 'Tryout UTBK #1 – Penalaran Umum', 20, 5, 1, 2, '2026-06-02 07:58:56'),
(10, 6, 'Tryout UTBK #1 – Penalaran Umum', 20, 5, 1, 1, '2026-06-03 10:24:01'),
(11, 6, 'Tryout UTBK #1 – Penalaran Umum', 0, 5, 0, 1, '2026-06-03 10:42:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('siswa','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'siswa',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(4, 'muhammad zaqi', 'muhammadzaqi@gmail.com', '$2y$10$UkIzzj9eBViK.kEY.E6/HeqCRWD/DMQf1EwjClwOoR/1zc5BOGPbO', 'siswa', '2026-06-01 19:11:59'),
(5, 'epep', 'jumpshot@gmail.com', '$2y$10$lDcBmiMYQ37.FHH5VjEZDOKzADpWhGmVcmXBn6drpmjg1O.CsGOWa', 'siswa', '2026-06-02 07:33:36'),
(6, 'duta', 'duta@gmail.com', '$2y$10$1WyjJ2HXkzSjr4JmzG1na.U0vGo8uhRDwhFEKlnJlCEPcUhcbMFMC', 'siswa', '2026-06-02 07:56:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thread_id` (`thread_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_threads`
--
ALTER TABLE `forum_threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tryout_results`
--
ALTER TABLE `tryout_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `forum_threads`
--
ALTER TABLE `forum_threads`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tryout_results`
--
ALTER TABLE `tryout_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD CONSTRAINT `forum_replies_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `forum_threads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_threads`
--
ALTER TABLE `forum_threads`
  ADD CONSTRAINT `forum_threads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tryout_results`
--
ALTER TABLE `tryout_results`
  ADD CONSTRAINT `tryout_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
