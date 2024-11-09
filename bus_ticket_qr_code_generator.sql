-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2024 at 06:18 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bus ticket qr code generator`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `travel_date` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `total_price` float NOT NULL,
  `final_price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `p_id`, `bus_id`, `booking_date`, `travel_date`, `status`, `total_price`, `final_price`) VALUES
(1, 4, 22, '2024-11-05', '2024-11-06', 'confirmed', 1000, 920),
(114, 4, 22, '2024-11-05', '2024-11-06', 'confirmed', 1500, 1375),
(115, 4, 22, '2024-11-05', '2024-11-06', 'confirmed', 1500, 1375),
(116, 4, 22, '2024-11-05', '2024-11-06', 'confirmed', 1000, 920),
(118, 4, 22, '2024-11-05', '2024-11-06', 'confirmed', 500, 460),
(133, 4, 25, '2024-11-05', '2024-11-06', 'confirmed', 1600, 1472),
(134, 4, 25, '2024-11-05', '2024-11-06', 'confirmed', 4800, 4320),
(135, 16, 22, '2024-11-05', '2024-11-06', 'canceled', 500, 450),
(136, 16, 26, '2024-11-06', '2024-11-07', 'confirmed', 2400, 2160),
(137, 16, 30, '2024-11-06', '2024-11-07', 'canceled', 1500, 1350);

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `bus_id` int(11) NOT NULL,
  `bus_type` varchar(50) NOT NULL,
  `destination` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `bus_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`bus_id`, `bus_type`, `destination`, `price`, `departure_time`, `arrival_time`, `bus_name`) VALUES
(22, 'Micro Bus', 'Chitwan', 500.00, '08:30:00', '15:00:00', 'Bus A'),
(23, 'Micro Bus', 'Pokhara', 500.00, '08:00:00', '15:00:00', 'Bus A'),
(25, 'Mini Bus', 'Pokhara', 800.00, '19:00:00', '02:00:00', 'Mini bus Pokhara'),
(26, 'Deluxe Bus', 'Pokhara', 1200.00, '11:00:00', '19:00:00', 'Deluxe Bus Pokhara'),
(30, 'Mini Bus', 'kirtipur', 500.00, '11:42:00', '09:00:00', 'Test');

-- --------------------------------------------------------

--
-- Table structure for table `bus_type`
--

CREATE TABLE `bus_type` (
  `bus_type_id` int(11) NOT NULL,
  `bus_type` varchar(20) NOT NULL,
  `num_seats` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_type`
--

INSERT INTO `bus_type` (`bus_type_id`, `bus_type`, `num_seats`) VALUES
(2, 'Mini Bus', 25),
(5, 'Deluxe Bus', 30),
(16, 'Micro Bus', 15);

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `offer_id` int(11) NOT NULL,
  `offer_name` varchar(100) DEFAULT NULL,
  `offer_percentage` int(11) DEFAULT NULL,
  `min_bookings` int(11) DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`offer_id`, `offer_name`, `offer_percentage`, `min_bookings`, `valid_from`, `valid_until`) VALUES
(21, 'Special offer', 8, 0, '2024-11-05', '2024-11-07'),
(22, 'special offer', 10, 0, '2024-11-06', '2024-11-13'),
(23, 'Offer', 15, 0, '2024-11-06', '2024-11-13');

-- --------------------------------------------------------

--
-- Table structure for table `operators_detail`
--

CREATE TABLE `operators_detail` (
  `o_id` int(11) NOT NULL,
  `Firstname` varchar(20) NOT NULL,
  `Lastname` varchar(20) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(25) NOT NULL,
  `Security_Question` varchar(255) NOT NULL,
  `Security_Answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operators_detail`
--

INSERT INTO `operators_detail` (`o_id`, `Firstname`, `Lastname`, `Email`, `Password`, `Security_Question`, `Security_Answer`) VALUES
(2, 'Alice', 'Paxton', 'alice@gmail.com', 'alice123', '', ''),
(4, 'sita', 'kc', 'sita@gmail.com', 'Sita@@', 'What is your favorite movie?', 'titanic'),
(5, 'Saliza', 'Maharjan', 'maharjan.saliza@gmail.com', 'saliza', 'What city were you born in?', 'Kathmandu');

-- --------------------------------------------------------

--
-- Table structure for table `passengers_detail`
--

CREATE TABLE `passengers_detail` (
  `p_id` int(11) NOT NULL,
  `Firstname` char(20) NOT NULL,
  `Lastname` char(20) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(25) NOT NULL,
  `Security_Question` varchar(255) NOT NULL,
  `Security_Answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passengers_detail`
--

INSERT INTO `passengers_detail` (`p_id`, `Firstname`, `Lastname`, `Email`, `Password`, `Security_Question`, `Security_Answer`) VALUES
(4, 'Sakura', 'Maharjan', 'sakura@gmail.com', 'sakura123', '', ''),
(14, 'Rina', 'Shahi', 'rina@gmail.com', 'Rina@123', 'What is your favorite food?', 'momo'),
(15, 'Samyam', 'Shrestha', 'samyamshrestha2@gmail.com', 'sam123', 'What is your favorite food?', 'momo'),
(16, 'Saliza', 'Maharjan', 'maharjan.saliza@gmail.com', 'Saliza', 'What was the name of your first pet?', 'lucy');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `seat_id` int(11) NOT NULL,
  `bus_id` int(11) DEFAULT NULL,
  `booking_id` int(11) NOT NULL,
  `seat_number` varchar(10) DEFAULT NULL,
  `status` enum('available','reserved') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`seat_id`, `bus_id`, `booking_id`, `seat_number`, `status`) VALUES
(95, 22, 115, '5B', 'reserved'),
(97, 22, 115, '4B', 'reserved'),
(98, 22, 116, '4C', 'reserved'),
(100, 22, 118, '2B', 'reserved'),
(101, 22, 114, '3A', 'reserved'),
(102, 22, 114, '4A', 'reserved'),
(103, 22, 114, '5A', 'reserved'),
(104, 22, 115, '3B', 'reserved'),
(105, 22, 116, '3C', 'reserved'),
(121, 25, 133, '6C', 'reserved'),
(122, 25, 133, '6D', 'reserved'),
(123, 25, 134, '4C', 'reserved'),
(124, 25, 134, '5A', 'reserved'),
(125, 25, 134, '5B', 'reserved'),
(126, 25, 134, '5C', 'reserved'),
(127, 25, 134, '6A', 'reserved'),
(128, 25, 134, '6B', 'reserved'),
(129, 22, 135, '5C', 'available'),
(130, 26, 136, '8B', 'reserved'),
(131, 26, 136, '8C', 'reserved'),
(132, 30, 137, '5C', 'available'),
(133, 30, 137, '6C', 'available'),
(134, 30, 137, '6D', 'available');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `fk_pid` (`p_id`),
  ADD KEY `fk_busid` (`bus_id`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`bus_id`);

--
-- Indexes for table `bus_type`
--
ALTER TABLE `bus_type`
  ADD PRIMARY KEY (`bus_type_id`),
  ADD UNIQUE KEY `bus_type` (`bus_type`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`offer_id`);

--
-- Indexes for table `operators_detail`
--
ALTER TABLE `operators_detail`
  ADD PRIMARY KEY (`o_id`);

--
-- Indexes for table `passengers_detail`
--
ALTER TABLE `passengers_detail`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`seat_id`),
  ADD KEY `bus_id` (`bus_id`),
  ADD KEY `fk_booking_id` (`booking_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `bus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `bus_type`
--
ALTER TABLE `bus_type`
  MODIFY `bus_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `offer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `operators_detail`
--
ALTER TABLE `operators_detail`
  MODIFY `o_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `passengers_detail`
--
ALTER TABLE `passengers_detail`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_busid` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pid` FOREIGN KEY (`p_id`) REFERENCES `passengers_detail` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `fk_booking_id` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
