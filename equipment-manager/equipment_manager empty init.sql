-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2025 at 01:27 AM
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
-- Database: `equipment_manager`
--

-- --------------------------------------------------------

--
-- Table structure for table `equipment_other`
--

CREATE TABLE `equipment_other` (
  `equipment_other_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL,
  `date_show` date NOT NULL,
  `date_hide` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `extension_cords`
--

CREATE TABLE `extension_cords` (
  `extension_cord_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `color` varchar(12) NOT NULL,
  `length` int(11) NOT NULL COMMENT 'in feet'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interface_boxes`
--

CREATE TABLE `interface_boxes` (
  `interface_box_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `type` set('USB','Parallel','Other') NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laptops`
--

CREATE TABLE `laptops` (
  `laptop_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `brand` varchar(30) NOT NULL,
  `operating_system` set('Win 11+','Win 10','Win 7/8','Older Windows','Linux','Other') NOT NULL,
  `port_type` set('None','Built-in','PCMCIA','USB Adapter','Other') NOT NULL,
  `quizmachine_version` varchar(15) NOT NULL,
  `login_username` varchar(30) NOT NULL,
  `login_password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monitors`
--

CREATE TABLE `monitors` (
  `monitor_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `size` set('19','21','22','24','27','32','Other') NOT NULL COMMENT 'In inches.',
  `resolution` set('HD (1080)','UHD (3840)','Other') NOT NULL,
  `connection_type` set('HDMI','VGA','DVI','DisplayPort','Other') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pads`
--

CREATE TABLE `pads` (
  `pad_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `color` set('Red','Blue','Green','Yellow','Other') NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `powerstrips`
--

CREATE TABLE `powerstrips` (
  `powerstrip_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `brand` varchar(20) NOT NULL,
  `model` varchar(20) NOT NULL,
  `color` varchar(15) NOT NULL,
  `outlet_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projectors`
--

CREATE TABLE `projectors` (
  `projector_id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `brand` varchar(12) NOT NULL,
  `lumens` int(11) NOT NULL,
  `resolution` set('800x600','1024x768','1280x800','1920x1080','3840x2160 (4K)','Other') NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `registration_id` int(11) NOT NULL,
  `name_first` char(25) NOT NULL,
  `name_last` char(25) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `district` varchar(65) NOT NULL,
  `event_id` int(11) NOT NULL COMMENT 'foreign key event'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equipment_other`
--
ALTER TABLE `equipment_other`
  ADD PRIMARY KEY (`equipment_other_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `extension_cords`
--
ALTER TABLE `extension_cords`
  ADD PRIMARY KEY (`extension_cord_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `interface_boxes`
--
ALTER TABLE `interface_boxes`
  ADD PRIMARY KEY (`interface_box_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `laptops`
--
ALTER TABLE `laptops`
  ADD PRIMARY KEY (`laptop_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `monitors`
--
ALTER TABLE `monitors`
  ADD PRIMARY KEY (`monitor_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `pads`
--
ALTER TABLE `pads`
  ADD PRIMARY KEY (`pad_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `powerstrips`
--
ALTER TABLE `powerstrips`
  ADD PRIMARY KEY (`powerstrip_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `projectors`
--
ALTER TABLE `projectors`
  ADD PRIMARY KEY (`projector_id`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`registration_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `laptops`
--
ALTER TABLE `laptops`
  MODIFY `laptop_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `equipment_other`
--
ALTER TABLE `equipment_other`
  ADD CONSTRAINT `equipment_other_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`registration_id`);

--
-- Constraints for table `extension_cords`
--
ALTER TABLE `extension_cords`
  ADD CONSTRAINT `extension_cords_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`registration_id`);

--
-- Constraints for table `interface_boxes`
--
ALTER TABLE `interface_boxes`
  ADD CONSTRAINT `interface_boxes_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`registration_id`);

--
-- Constraints for table `monitors`
--
ALTER TABLE `monitors`
  ADD CONSTRAINT `monitors_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`registration_id`);

--
-- Constraints for table `pads`
--
ALTER TABLE `pads`
  ADD CONSTRAINT `pads_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`registration_id`);

--
-- Constraints for table `powerstrips`
--
ALTER TABLE `powerstrips`
  ADD CONSTRAINT `powerstrips_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`registration_id`);

--
-- Constraints for table `projectors`
--
ALTER TABLE `projectors`
  ADD CONSTRAINT `projectors_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registration` (`registration_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
