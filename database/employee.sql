-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2024 at 07:01 PM
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
-- Database: `agriculture_product_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL,
  `employee_name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `road_no` varchar(50) DEFAULT NULL,
  `house_no` varchar(50) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `office_id` int(11) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employee_id`, `employee_name`, `phone`, `email`, `road_no`, `house_no`, `hire_date`, `role`, `office_id`, `password`) VALUES
(1, 'soumic', '01704442185', 'soumicshahriar1@gmail.com', 'Block-c, road-5, Bashundhara Residential Area', '51', '2024-11-27', 'Market Manager', 1001, '1234'),
(2, 'shahriar', '01779552185', 'shahriar1@gmail.com', 'Block-c, road-5, Bashundhara Residential Area', '7', '2024-11-27', 'Agriculture Officer', 1002, '1234'),
(3, 'mizu', '01763086924', 'mizu@gmail.com', 'Block-c, road-2, Bashundhara Residential Area', '37', '2024-11-27', 'Food Quality Officer', 1001, '1234'),
(4, 'rifat', '01779552185', 'rifat@gmail.com', 'Block-c, road-2, Bashundhara Residential Area', '61', '2024-11-01', 'Warehouse manager', 1002, '1234'),
(5, 'neela', '01763086924', 'neela@gmail.com', 'Block-c, road-2, Bashundhara Residential Area', '50', '2024-11-01', 'Warehouse manager', 1001, '1234'),
(6, 'goutom', '01704442185', 'goutom@gmail.com', 'Block-c, road-2, Bashundhara Residential Area', '7', '2024-11-08', 'Admin', 1002, '1234'),
(7, 'Ayush', '01779552185', 'ayush@gmail.com', 'Block-c, road-2, Bashundhara Residential Area', '51', '2024-11-08', 'Warehouse manager', 1002, '1234');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `office_id` (`office_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`office_id`) REFERENCES `government_office` (`office_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
