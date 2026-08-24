-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2026 at 02:04 AM
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
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `start_date`, `end_date`, `created_by`, `created_at`) VALUES
(1, 'DRESSING CODE ON MONDAY', 'The KNEC official have agreed that the dressing code for monday should be black and white for men and women', '2026-06-08', '2027-06-08', 1, '2026-08-23 22:14:46');

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
(4, 9, '2026-06-24', '10:05:00', NULL, 'Present'),
(5, 16, '2026-06-26', '08:00:00', NULL, 'Present');

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
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_code` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `details`
--

INSERT INTO `details` (`id`, `fullname`, `email`, `department`, `position`, `phone`, `salary`, `date_created`, `employee_code`, `photo`) VALUES
(1, 'David okoth', 'okothdvdodhiambo@gmail.com', 'managerial', 'Manager', '2540758935', 150000.00, '2026-06-15 23:33:00', NULL, NULL),
(2, 'steven jarongo', 'jarongosteve@gmail.com', 'managerial', 'Assistance manager', '0700459843', 100000.00, '2026-06-15 23:42:34', NULL, NULL),
(7, 'Mary Moraa', 'moraa254@gmail.come', 'Chef', 'Head', '01416020512', 30000.00, '2026-06-22 00:04:07', NULL, NULL),
(8, 'Koleta Nyambura', 'Nyamburakolet@gmail.com', 'Health', 'Head of health', '0755522432', 25000.00, '2026-06-22 00:07:20', NULL, NULL),
(9, 'Maryan Anyango', 'anyangomary@gmail.com', 'managerial', 'Secretary', '0724586542', 74000.00, '2026-06-22 00:10:50', NULL, NULL),
(15, 'Kevin mark', 'markkevo@gmail.com', 'Health', 'Cleaner', '0115987638', 10000.00, '2026-06-25 21:42:16', NULL, NULL),
(22, 'David Devix', 'deviedave@gmail.com', 'Security', 'Chief security', '+2542540758935', 20000.00, '2026-07-18 02:20:09', 'EMP022', '1784341209_.trashed-1741531918-IMG_20250207_174953.jpg'),
(23, 'coutts Rawson', 'rawsoncoutts@gmail.com', 'IT', 'Senior Manager', '0115945361', 145000.00, '2026-07-23 03:21:20', 'EMP023', '1784776879_IMG_20241008_190738.jpg'),
(24, 'Henry Maxswel', 'henrymaxswel@gmail.com', 'Finance', 'Accountant', '0748596128', 81550.00, '2026-07-23 03:33:27', 'EMP024', '1784777607_IMG_20250106_102718.jpg'),
(25, 'Felix Jadhiwa', 'jadhiwafelix@gmail.com', 'Enginearing', 'electrict enginear', '0116263265', 57250.00, '2026-07-23 03:36:33', 'EMP025', '1784777793_IMG_20241126_131441.jpg'),
(26, 'MBATHA YVONNE NTHENYA', 'mbathayvonne8@gmail.com', 'Health', 'Theatre Tech', '0797444511', 70000.00, '2026-07-27 20:42:54', 'EMP026', '1785184973_IMG_20241018_165219.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(20) DEFAULT NULL
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
(6, 'tony omondi', 'omonditony@gmail.com', '$2y$10$35d8urZ134vzVMkhulVT1e.Y/26ovZqTDLE1GheK.lMYQFsXLdajy', 'employee'),
(7, 'yvone Anyona', 'anyonayvon@gmail.com', '$2y$10$XRQwS5MoOZoH5wCWUuUHOuB/coysjr0gMeI9Dqno0XCINU4jRJOWG', 'employee'),
(8, 'Hesbon Odoyo', 'odoyohesbon@gmail.com', '$2y$10$/m8BiiD20dSI5JsAuLzKCO7Afgu9yxwr91APH7S1ofR0cqK5q6L2O', 'employee'),
(9, 'Henry Maxwel', 'henrymaxswelodhiambo@gmail.com', '$2y$10$s3.UbENTb1vNwBPhSC0/ZOzep5n7ZUCkxfUHaSvw41jYYoHm2XPSG', 'employee'),
(10, 'Mitchel Atieno', 'atienomitchel@gmail.com', '$2y$10$J0FYs.Rf.kFGFzn/s7OpNux6ZEF.mFaNbjrpRgZlyY89tiBRgjUQ.', 'employee'),
(11, 'Christine Enock', 'christineongaro@yahoo.com', '$2y$10$QManDBktPZrrp6lABB/rYefmVCAYqNgt2ufDExnMWNcnC/0jj7ESG', 'employee'),
(12, 'okothdvdodhiambo@gmail.com', 'okothdvdodhiambo@gmail.com', '$2y$10$J4gaAIsCsobVKQoNWsi/mOXNQreHmgueq.ZO3s0RFDDKWmeqt3Bk.', 'employee'),
(13, 'davi@443', 'okothdvdodhiambo@gmail.com', '$2y$10$s9/eJnE6DDwWscD2dNVRF..B/QZv6sZvAV/SFcjlKuLw43mNz4UlW', 'employee'),
(14, 'june ken', 'kenjune@gmail.com', '$2y$10$js/gDnu5maKTAZ12hEhnF.nRFRkzguOEeKBKy6qfPuxguYx8W.7Z6', 'employee'),
(15, 'David Devix', 'deviedave@gmail.com', '$2y$10$li53LdzZkmnBnNZ8GOOEoeKqzQX.bHPowJl.nGU7Y8UEWTf1Z.gRi', 'employee'),
(16, 'coutts Rawson', 'rawsoncoutts@gmail.com', '$2y$10$3l8x1rbw9yPyFV8DLJklYexg25DUTHfyq3pkKfpLL1.q6lOcY.ad2', 'employee'),
(17, 'Henry Maxswel', 'henrymaxswel@gmail.com', '$2y$10$XQdNAgra4LQcGhzzB001s.ETrl6PaD3Qu44UzGov10gGZTliXaDdS', 'employee'),
(18, 'Felix Jadhiwa', 'jadhiwafelix@gmail.com', '$2y$10$UGpF/yi47REWBp7ItKCNVuZcI1sFkttQXvvg2UazrpJRRYadvwEdq', 'employee'),
(19, 'MBATHA YVONNE NTHENYA', 'mbathayvonne8@gmail.com', '$2y$10$2ldwiiT8aP7Bd1F5mGQJtOqQcRaZqw106ll.TaGfYIOWjK5TbvdPS', 'employee');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `start_date`, `end_date`, `created_at`) VALUES
(1, 'INNOVATIOM SUMMIT', 'Agenda are many as follows', '2026-08-12', '2026-09-12', '2026-07-27 20:24:05');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `reason`, `status`, `request_date`) VALUES
(1, 1, 'Sick Leave', '2026-07-18', '2026-07-24', 'Going to seek medical attention due to current situation', 'Approved', '2026-07-18 04:00:25'),
(2, 6, 'Emergency Leave', '2026-07-22', '2026-07-24', 'just emergency to solve', 'Pending', '2026-07-18 04:31:35'),
(3, 5, 'Maternity Leave', '2026-07-21', '2026-07-28', 'leave for one week', 'Rejected', '2026-07-18 04:36:50');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Unread','Read') DEFAULT 'Unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `employee_id`, `message`, `status`, `created_at`) VALUES
(1, 4, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(2, 5, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(3, 6, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(4, 7, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(5, 8, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(6, 9, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(7, 10, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(8, 11, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(9, 12, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(10, 13, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(11, 14, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(12, 15, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(13, 16, 'New announcement: DRESSING CODE ON MONDAY', 'Read', '2026-08-23 22:14:46'),
(14, 17, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(15, 18, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46'),
(16, 19, 'New announcement: DRESSING CODE ON MONDAY', 'Unread', '2026-08-23 22:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `payment_requests`
--

CREATE TABLE `payment_requests` (
  `id` int(11) NOT NULL,
  `employee_email` varchar(100) NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `employee_code` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `paypal_email` varchar(100) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `request_month` varchar(20) DEFAULT NULL,
  `request_year` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_requests`
--

INSERT INTO `payment_requests` (`id`, `employee_email`, `employee_name`, `employee_code`, `amount`, `currency`, `payment_method`, `phone_number`, `paypal_email`, `account_number`, `request_month`, `request_year`, `status`, `created_at`) VALUES
(1, 'deviedave@gmail.com', 'David Devix', 'EMP01', 45000.00, 'KES', '0', '0758935569', '', '', 'August', 2026, 'Paid', '2026-07-24 01:20:25');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `employee_email` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_reference` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `paid_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `details`
--
ALTER TABLE `details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `payment_requests`
--
ALTER TABLE `payment_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
