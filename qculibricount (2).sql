-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 03:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qculibricount`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(20) DEFAULT NULL,
  `max_capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `max_capacity`) VALUES
(1, 'admin', 'admin123', 50);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `log_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`log_id`, `student_id`, `time_in`, `time_out`) VALUES
(1, 1, '2025-12-12 18:35:54', '2025-12-12 18:35:56'),
(2, 1, '2025-12-12 18:35:58', '2025-12-12 18:35:59'),
(3, 1, '2025-12-12 18:37:25', '2025-12-12 18:37:28'),
(4, 1, '2025-12-12 18:37:26', '2025-12-12 18:37:27'),
(5, 1, '2025-12-12 18:37:29', '2025-12-12 18:37:32'),
(6, 1, '2025-12-12 18:37:29', '2025-12-12 18:37:32'),
(7, 1, '2025-12-12 18:37:30', '2025-12-12 18:37:31'),
(8, 1, '2025-12-12 18:37:30', '2025-12-12 18:37:31'),
(9, 1, '2025-12-12 18:37:30', '2025-12-12 18:37:31'),
(10, 1, '2025-12-12 18:37:30', '2025-12-12 18:37:31'),
(11, 1, '2025-12-12 18:37:30', '2025-12-12 18:37:31'),
(12, 1, '2025-12-12 18:37:36', '2025-12-12 18:37:39'),
(13, 1, '2025-12-12 18:37:36', '2025-12-12 18:37:39'),
(14, 1, '2025-12-12 18:37:36', '2025-12-12 18:37:39'),
(15, 1, '2025-12-12 18:37:37', '2025-12-12 18:37:39'),
(16, 1, '2025-12-12 18:37:37', '2025-12-12 18:37:38');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_number` varchar(20) DEFAULT NULL,
  `Firstname` varchar(50) DEFAULT NULL,
  `Middlename` varchar(20) DEFAULT NULL,
  `Lastname` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_number`, `Firstname`, `Middlename`, `Lastname`) VALUES
(1, '-1392', 'Franz Remnant', 'Regunda', 'Reyes'),
(2, '-1402', 'Jerv Christian', 'Atienza', 'Gaño'),
(3, '24-1499', 'COck', 'Balls', 'Lmao'),
(4, '24-1574', 'Kurt Adrian', 'L', 'Uy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
