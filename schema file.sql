-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2026 at 01:28 AM
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
-- Database: `staff`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `attendance_date`, `check_in`, `check_out`, `status`) VALUES
(1, 1, '2026-06-24', '08:00:00', NULL, 'Present'),
(2, 2, '2026-06-24', '08:00:00', NULL, 'Present'),
(3, 3, '2026-06-24', '09:00:00', NULL, 'Present'),
(4, 9, '2026-06-24', '10:05:00', NULL, 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `details`
--

CREATE TABLE `details` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `details`
--

INSERT INTO `details` (`id`, `fullname`, `email`, `department`, `position`, `phone`, `salary`, `date_created`) VALUES
(1, 'David okoth', 'okothdvdodhiambo@gmail.com', 'managerial', 'Manager', '2540758935', 150000.00, '2026-06-15 23:33:00'),
(2, 'steven jarongo', 'jarongosteve@gmail.com', 'managerial', 'Assistance manager', '0700459843', 100000.00, '2026-06-15 23:42:34'),
(7, 'Mary Moraa', 'moraa254@gmail.come', 'Chef', 'Head', '01416020512', 0.00, '2026-06-22 00:04:07'),
(8, 'Koleta Nyambura', 'Nyamburakolet@gmail.com', 'Health', 'Head of health', '0755522432', 25000.00, '2026-06-22 00:07:20'),
(9, 'Maryan Anyango', 'anyangomary@gmail.com', 'managerial', 'Secretary', '0724586542', 74000.00, '2026-06-22 00:10:50'),
(14, 'Henry Maxswel', 'henrymaxswel@gmail.com', 'Finance', 'Accountant', '0748596328', 81550.52, '2026-06-25 20:01:47'),
(15, 'Kevin mark', 'markkevo@gmail.com', 'Health', 'Cleaner', '0115987638', 10000.00, '2026-06-25 21:42:16');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('super_admin','admin') DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'davi@443', 'okothdvdodhiambo@gmail.com', '$2y$10$43MO3tlFIU4noKtMx4pavOZ0svjvyUpURDiltHCFsaFZH8FRE8pqe', 'super_admin'),
(2, 'davi@443', 'okothdvdodhiambo@gmail.com', '$2y$10$pV0aTlQySllFEeoCU6GBD.LqPhxgj2keDthjssn5dhyuVulB8z.py', 'super_admin'),
(3, 'davi@443', 'okothdvdodhiambo@gmail.com', '$2y$10$khT5l6FwftOBks8jg20bz.1HNwNVSrDkVLiho9JQogS81aHvXjbje', 'super_admin'),
(4, '5589-A:B4', 'judgeotieno@gmail.com', '$2y$10$qJH3ALCP359PjD7E6iptW.13YqZc4j8DDJPoPTsp6jOINVBiPLlJG', 'admin'),
(5, 'david', 'ronaldouma@gmail.com', '$2y$10$MqydYn/Les/boHBy9wdZee5uf5/sqtbiFJ6yI9gYxkyjY.NZMiN8W', 'admin'),
(6, 'tony omondi', 'omonditony@gmail.com', '$2y$10$35d8urZ134vzVMkhulVT1e.Y/26ovZqTDLE1GheK.lMYQFsXLdajy', ''),
(7, 'yvone Anyona', 'anyonayvon@gmail.com', '$2y$10$XRQwS5MoOZoH5wCWUuUHOuB/coysjr0gMeI9Dqno0XCINU4jRJOWG', ''),
(8, 'Hesbon Odoyo', 'odoyohesbon@gmail.com', '$2y$10$/m8BiiD20dSI5JsAuLzKCO7Afgu9yxwr91APH7S1ofR0cqK5q6L2O', ''),
(9, 'Henry Maxwel', 'henrymaxswelodhiambo@gmail.com', '$2y$10$s3.UbENTb1vNwBPhSC0/ZOzep5n7ZUCkxfUHaSvw41jYYoHm2XPSG', ''),
(10, 'Mitchel Atieno', 'atienomitchel@gmail.com', '$2y$10$J0FYs.Rf.kFGFzn/s7OpNux6ZEF.mFaNbjrpRgZlyY89tiBRgjUQ.', ''),
(11, 'Christine Enock', 'christineongaro@yahoo.com', '$2y$10$QManDBktPZrrp6lABB/rYefmVCAYqNgt2ufDExnMWNcnC/0jj7ESG', ''),
(12, 'okothdvdodhiambo@gmail.com', 'okothdvdodhiambo@gmail.com', '$2y$10$J4gaAIsCsobVKQoNWsi/mOXNQreHmgueq.ZO3s0RFDDKWmeqt3Bk.', ''),
(13, 'davi@443', 'okothdvdodhiambo@gmail.com', '$2y$10$s9/eJnE6DDwWscD2dNVRF..B/QZv6sZvAV/SFcjlKuLw43mNz4UlW', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `details`
--
ALTER TABLE `details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `details`
--
ALTER TABLE `details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
