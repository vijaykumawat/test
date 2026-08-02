-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2026 at 08:37 PM
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
-- Database: `gbinsura_crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(16) NOT NULL,
  `attendance_date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `status` enum('Present','Absent','Half Day','Leave') NOT NULL DEFAULT 'Present',
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '2024-01-01 00:00:00',
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `attendance_date`, `check_in_time`, `check_out_time`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(3, '1f4c280d781e9380', '2026-07-26', '22:48:00', NULL, 'Present', 'Auto check-in on login', '2026-07-26 22:48:32', '2026-07-26 22:48:32'),
(4, '207c49c0f2fc6493', '2026-07-27', '23:40:00', '23:45:00', 'Present', 'Auto check-out on logout', '2026-07-27 23:40:11', '2026-07-27 23:45:13'),
(5, '1f4c280d781e9380', '2026-07-27', '23:41:00', '23:45:00', 'Present', 'Auto check-out on logout', '2026-07-27 23:41:08', '2026-07-27 23:45:25'),
(6, '1f4c280d781e9380', '2026-07-29', '00:17:00', NULL, 'Present', 'Auto check-in on login', '2026-07-29 00:17:42', '2026-07-29 00:17:42'),
(7, '207c49c0f2fc6493', '2026-07-29', '22:45:00', '22:59:00', 'Present', 'Auto check-out on logout', '2026-07-29 22:45:27', '2026-07-29 22:59:03'),
(8, '207c49c0f2fc6493', '2026-07-30', '00:08:00', '01:55:00', 'Present', 'Auto check-out on logout', '2026-07-30 00:08:01', '2026-07-30 01:55:31'),
(9, '1f4c280d781e9380', '2026-07-30', '00:12:00', '01:48:00', 'Present', 'Auto check-out on logout', '2026-07-30 00:12:26', '2026-07-30 01:48:25'),
(10, '207c49c0f2fc6493', '2026-07-31', '07:02:00', '22:11:00', 'Present', 'Auto check-out on logout', '2026-07-31 07:02:43', '2026-07-31 22:11:14'),
(11, '1f4c280d781e9380', '2026-07-31', '15:12:00', '21:12:00', 'Present', 'Auto check-out on logout', '2026-07-31 15:12:35', '2026-07-31 21:12:02'),
(12, '207c49c0f2fc6493', '2026-08-01', '08:17:00', '23:53:00', 'Present', 'Auto check-out on logout', '2026-08-01 08:17:45', '2026-08-01 23:53:34'),
(13, '1f4c280d781e9380', '2026-08-01', '08:17:00', '15:05:00', 'Present', 'Auto check-out on logout', '2026-08-01 08:17:58', '2026-08-01 15:05:41');

-- --------------------------------------------------------

--
-- Table structure for table `data`
--

CREATE TABLE `data` (
  `regDate` varchar(12) NOT NULL,
  `regDateMonth` varchar(9) NOT NULL,
  `regNumber` varchar(10) NOT NULL,
  `ownerName` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `vehicleMaker` varchar(40) NOT NULL,
  `vehicleModel` varchar(80) NOT NULL,
  `fuelType` text NOT NULL,
  `saleAmt` varchar(9) NOT NULL,
  `seatCapacity` varchar(2) NOT NULL,
  `cubicCapacity` int(4) NOT NULL,
  `mobile` varchar(14) NOT NULL,
  `expiryDate` varchar(12) NOT NULL,
  `prevInsuCompany` text NOT NULL,
  `finance` varchar(40) NOT NULL,
  `telecaller` text NOT NULL,
  `dataUploadDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `actionTaken` int(1) NOT NULL,
  `isImportant` int(1) NOT NULL,
  `alreadySale` int(1) NOT NULL,
  `modifiyDate` datetime NOT NULL,
  `isIntrested` int(1) NOT NULL,
  `saleInGb` int(1) NOT NULL,
  `recordId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data`
--

INSERT INTO `data` (`regDate`, `regDateMonth`, `regNumber`, `ownerName`, `address`, `vehicleMaker`, `vehicleModel`, `fuelType`, `saleAmt`, `seatCapacity`, `cubicCapacity`, `mobile`, `expiryDate`, `prevInsuCompany`, `finance`, `telecaller`, `dataUploadDate`, `actionTaken`, `isImportant`, `alreadySale`, `modifiyDate`, `isIntrested`, `saleInGb`, `recordId`) VALUES
('12-06-2026', 'June', 'MH16PQ9876', 'Sachin Deshmukh', 'Pune, Maharashtra', 'Mahindra', 'XUV500', 'Diesel', '1700000', '7', 1197, '9876543233', '12-06-2027', 'United India Insurance', 'SBI Bank', '207c49c0f2fc6493', '2026-07-31 04:42:18', 1, 0, 0, '2026-07-31 00:00:00', 1, 0, 1),
('30-03-2026', 'March', 'KA06UV1234', 'Deepa Nair', 'Mysore, Karnataka', 'Volkswagen', 'Polo', 'Diesel', '900000', '5', 998, '9876509876', '30-03-2027', 'Bajaj Allianz', 'SBI Bank', '207c49c0f2fc6494', '2026-07-31 06:49:32', 1, 1, 0, '2026-07-31 00:00:00', 1, 0, 2),
('25-01-2026', 'January', 'WB20JK3456', 'Shreya Das', 'Kolkata, West Bengal', 'Tata', 'Nexon', 'Electric', '1400000', '5', 998, '9988701122', '25-01-2027', 'Oriental Insurance', 'Axis Bank', '207c49c0f2fc6494', '2026-07-31 06:49:44', 1, 0, 0, '2026-07-31 00:00:00', 0, 0, 3),
('01-06-2026', 'June', 'MH12AB1234', 'Rajesh Sharma', 'Pune, Maharashtra', 'Maruti', 'Suzuki Swift', 'Petrol', '450000', '5', 1197, '9876543210', '01-06-2027', 'ICICI Lombard', 'HDFC Bank', '207c49c0f2fc6493', '2026-07-31 04:47:27', 1, 0, 0, '2026-07-31 00:00:00', 1, 0, 4),
('28-01-2026', 'January', 'WB21JK3456', 'Priyanka Sen', 'Kolkata, West Bengal', 'Tata', 'Altroz', 'Petrol', '750000', '5', 998, '9988701133', '28-01-2027', 'Oriental Insurance', 'Axis Bank', '207c49c0f2fc6493', '2026-07-31 08:55:28', 1, 1, 0, '2026-07-31 00:00:00', 2, 0, 5),
('15-05-2026', 'May', 'DL8CAF5678', 'Anita Verma', 'Delhi, India', 'Hyundai', 'Creta', 'Diesel', '1200000', '5', 998, '9123456789', '15-05-2027', 'Bajaj Allianz', 'Axis Bank', '207c49c0f2fc6493', '2026-07-31 08:56:12', 0, 1, 0, '2026-07-29 18:45:02', 0, 0, 6),
('10-06-2026', 'June', 'MH14PQ9876', 'Arjun Kulkarni', 'Pune, Maharashtra', 'Mahindra', 'Thar', 'Diesel', '1500000', '4', 1197, '9876543211', '10-06-2027', 'United India Insurance', 'SBI Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 7),
('08-02-2026', 'February', 'UP17GH8765', 'Manoj Kumar', 'Lucknow, Uttar Pradesh', 'Ford', 'Figo', 'Diesel', '600000', '5', 1197, '9123409888', '08-02-2027', 'HDFC Ergo', 'HDFC Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 8),
('05-02-2026', 'February', 'UP16GH8765', 'Vikas Singh', 'Lucknow, Uttar Pradesh', 'Ford', 'Ecosport', 'Petrol', '800000', '5', 1197, '9123409876', '05-02-2027', 'HDFC Ergo', 'HDFC Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 9),
('25-04-2026', 'April', 'GJ3CD7890', 'Suresh Mehta', 'Ahmedabad, Gujarat', 'Honda', 'Amaze', 'Petrol', '800000', '5', 1197, '9988776622', '25-04-2027', 'Tata AIG', 'SBI Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 10),
('15-03-2026', 'March', 'KA07EF4321', 'Anjali Menon', 'Bangalore, Karnataka', 'Toyota', 'Yaris', 'Petrol', '1200000', '5', 998, '9876501222', '15-03-2027', 'New India Assurance', 'ICICI Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 11),
('24-05-2026', 'May', 'DL11RS2345', 'Rekha Sharma', 'Delhi, India', 'Renault', 'Duster', 'Diesel', '1000000', '5', 998, '9123456777', '24-05-2027', 'Future Generali', 'HDFC Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 12),
('12-05-2026', 'May', 'DL10AB5678', 'Neha Gupta', 'Delhi, India', 'Hyundai', 'i20', 'Diesel', '1100000', '5', 998, '9123456799', '12-05-2027', 'Bajaj Allianz', 'Axis Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 13),
('22-05-2026', 'May', 'DL9RS2345', 'Kavita Sharma', 'Delhi, India', 'Renault', 'Kwid', 'Petrol', '500000', '5', 998, '9123456780', '22-05-2027', 'Future Generali', 'HDFC Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 14),
('02-06-2026', 'June', 'MH15XY4321', 'Prakash Jadhav', 'Pune, Maharashtra', 'Maruti', 'Baleno', 'Petrol', '650000', '5', 1197, '9876543222', '02-06-2027', 'ICICI Lombard', 'HDFC Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 15),
('18-04-2026', 'April', 'GJ2LM7654', 'Ramesh Patel', 'Surat, Gujarat', 'Nissan', 'Magnite', 'Petrol', '700000', '5', 1197, '9988776611', '18-04-2027', 'ICICI Lombard', 'Axis Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 16),
('10-03-2026', 'March', 'KA05MN4321', 'Meena Rao', 'Bangalore, Karnataka', 'Toyota', 'Innova', 'Diesel', '1600000', '7', 998, '9876501234', '10-03-2027', 'New India Assurance', 'ICICI Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 17),
('20-04-2026', 'April', 'GJ1XY7890', 'Sunil Patil', 'Ahmedabad, Gujarat', 'Honda', 'City', 'CNG', '950000', '5', 1197, '9988776655', '20-04-2027', 'Tata AIG', 'SBI Bank', '207c49c0f2fc6493', '2026-07-29 13:15:02', 0, 0, 0, '2026-07-29 18:45:02', 0, 0, 18);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employeeId` char(16) NOT NULL,
  `name` varchar(50) NOT NULL,
  `dateOfBirth` date DEFAULT NULL,
  `gender` text DEFAULT 'Other',
  `phoneNumber` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hireDate` date DEFAULT NULL,
  `jobTitle` varchar(100) DEFAULT NULL,
  `employmentStatus` text DEFAULT 'Active',
  `salary` decimal(12,2) DEFAULT NULL,
  `bonusEligible` tinyint(1) DEFAULT 0,
  `benefits` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isActive` tinyint(1) DEFAULT 1,
  `nationalId` varchar(30) DEFAULT NULL,
  `bankAccountNumber` varchar(50) DEFAULT NULL,
  `workLocation` varchar(100) DEFAULT NULL,
  `profilePhoto` varchar(255) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employeeId`, `name`, `dateOfBirth`, `gender`, `phoneNumber`, `address`, `hireDate`, `jobTitle`, `employmentStatus`, `salary`, `bonusEligible`, `benefits`, `createdAt`, `updatedAt`, `isActive`, `nationalId`, `bankAccountNumber`, `workLocation`, `profilePhoto`, `pincode`, `username`, `password`, `email`) VALUES
('1f4c280d781e9380', 'Shubham Lage', '2000-12-31', 'Male', '9323895687', NULL, '2026-07-26', 'Admin', 'Active', NULL, 0, NULL, '2026-07-26 11:48:14', '2026-07-26 11:48:14', 1, NULL, '', '', '', NULL, 'Admin', 'Admin', 'shubham@gmail.com'),
('207c49c0f2fc6493', 'Jyoti Netke', '1992-05-14', 'FEMALE', '9623919509', 'Belapur Bk., Ahmadnagar, Maharashtra -', '2026-07-26', 'telecaller', 'Active', 0.00, 0, NULL, '2026-07-26 11:49:39', '2026-07-26 11:49:39', 1, NULL, '', '', NULL, '413715', 'laxmi', 'laxmi', '');

-- --------------------------------------------------------

--
-- Table structure for table `employeeloginhistory`
--

CREATE TABLE `employeeloginhistory` (
  `id` int(11) NOT NULL,
  `employeeId` char(16) NOT NULL,
  `loginTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `logoutTime` timestamp NULL DEFAULT NULL,
  `status` enum('LoggedIn','LoggedOut') DEFAULT 'LoggedIn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employeeloginhistory`
--

INSERT INTO `employeeloginhistory` (`id`, `employeeId`, `loginTime`, `logoutTime`, `status`) VALUES
(4, '1f4c280d781e9380', '2026-07-26 17:18:32', NULL, 'LoggedIn'),
(5, '207c49c0f2fc6493', '2026-07-27 18:10:10', '2026-07-27 18:10:19', 'LoggedOut'),
(6, '1f4c280d781e9380', '2026-07-27 18:11:08', '2026-07-27 18:11:44', 'LoggedOut'),
(7, '207c49c0f2fc6493', '2026-07-27 18:12:23', '2026-07-27 18:14:17', 'LoggedOut'),
(8, '207c49c0f2fc6493', '2026-07-27 18:15:00', '2026-07-27 18:15:13', 'LoggedOut'),
(9, '1f4c280d781e9380', '2026-07-27 18:15:19', '2026-07-27 18:15:25', 'LoggedOut'),
(10, '1f4c280d781e9380', '2026-07-27 18:29:12', NULL, 'LoggedIn'),
(11, '1f4c280d781e9380', '2026-07-28 18:47:41', NULL, 'LoggedIn'),
(12, '207c49c0f2fc6493', '2026-07-29 17:15:26', NULL, 'LoggedIn'),
(13, '207c49c0f2fc6493', '2026-07-29 17:22:58', '2026-07-29 17:29:02', 'LoggedOut'),
(14, '207c49c0f2fc6493', '2026-07-29 17:29:09', NULL, 'LoggedIn'),
(15, '207c49c0f2fc6493', '2026-07-29 18:38:00', '2026-07-29 18:42:21', 'LoggedOut'),
(16, '1f4c280d781e9380', '2026-07-29 18:42:26', '2026-07-29 18:42:34', 'LoggedOut'),
(17, '207c49c0f2fc6493', '2026-07-29 18:42:38', '2026-07-29 18:42:49', 'LoggedOut'),
(18, '1f4c280d781e9380', '2026-07-29 18:42:54', '2026-07-29 18:45:11', 'LoggedOut'),
(19, '207c49c0f2fc6493', '2026-07-29 18:45:15', '2026-07-29 19:28:37', 'LoggedOut'),
(20, '207c49c0f2fc6493', '2026-07-29 19:31:24', '2026-07-29 19:52:03', 'LoggedOut'),
(21, '207c49c0f2fc6493', '2026-07-29 19:54:07', '2026-07-29 20:18:06', 'LoggedOut'),
(22, '1f4c280d781e9380', '2026-07-29 20:18:18', '2026-07-29 20:18:25', 'LoggedOut'),
(23, '207c49c0f2fc6493', '2026-07-29 20:18:29', '2026-07-29 20:25:31', 'LoggedOut'),
(24, '207c49c0f2fc6493', '2026-07-29 20:25:35', NULL, 'LoggedIn'),
(25, '207c49c0f2fc6493', '2026-07-30 02:42:49', NULL, 'LoggedIn'),
(26, '207c49c0f2fc6493', '2026-07-31 01:32:42', '2026-07-31 05:02:02', 'LoggedOut'),
(27, '207c49c0f2fc6493', '2026-07-31 05:02:05', '2026-07-31 05:14:18', 'LoggedOut'),
(28, '207c49c0f2fc6493', '2026-07-31 05:14:20', '2026-07-31 07:17:52', 'LoggedOut'),
(29, '207c49c0f2fc6493', '2026-07-31 07:18:02', '2026-07-31 09:42:23', 'LoggedOut'),
(30, '207c49c0f2fc6493', '2026-07-31 07:22:13', '2026-07-31 07:29:36', 'LoggedOut'),
(31, '1f4c280d781e9380', '2026-07-31 09:42:35', '2026-07-31 09:43:22', 'LoggedOut'),
(32, '207c49c0f2fc6493', '2026-07-31 09:43:29', '2026-07-31 11:57:07', 'LoggedOut'),
(33, '1f4c280d781e9380', '2026-07-31 11:57:16', '2026-07-31 11:58:42', 'LoggedOut'),
(34, '207c49c0f2fc6493', '2026-07-31 11:58:45', '2026-07-31 12:44:11', 'LoggedOut'),
(35, '1f4c280d781e9380', '2026-07-31 12:45:10', '2026-07-31 15:42:02', 'LoggedOut'),
(36, '207c49c0f2fc6493', '2026-07-31 15:42:04', '2026-07-31 16:39:07', 'LoggedOut'),
(37, '207c49c0f2fc6493', '2026-07-31 16:39:14', '2026-07-31 16:41:14', 'LoggedOut'),
(38, '1f4c280d781e9380', '2026-07-31 16:41:27', NULL, 'LoggedIn'),
(39, '207c49c0f2fc6493', '2026-08-01 02:47:45', '2026-08-01 02:47:51', 'LoggedOut'),
(40, '1f4c280d781e9380', '2026-08-01 02:47:58', '2026-08-01 03:31:44', 'LoggedOut'),
(41, '207c49c0f2fc6493', '2026-08-01 03:31:48', '2026-08-01 03:55:50', 'LoggedOut'),
(42, '1f4c280d781e9380', '2026-08-01 03:55:59', '2026-08-01 09:35:41', 'LoggedOut'),
(43, '207c49c0f2fc6493', '2026-08-01 09:47:12', '2026-08-01 18:23:33', 'LoggedOut'),
(44, '207c49c0f2fc6493', '2026-08-01 18:23:36', NULL, 'LoggedIn');

-- --------------------------------------------------------

--
-- Table structure for table `employee_preferences`
--

CREATE TABLE `employee_preferences` (
  `employeeId` varchar(16) NOT NULL,
  `visible_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`visible_fields`)),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_preferences`
--

INSERT INTO `employee_preferences` (`employeeId`, `visible_fields`, `updated_at`) VALUES
('207c49c0f2fc6493', '[\"regDate\",\"regDateMonth\",\"expiryDate\",\"vehicleMaker\",\"vehicleModel\",\"fuelType\",\"saleAmt\",\"seatCapacity\",\"cubicCapacity\",\"prevInsuCompany\",\"finance\",\"telecaller\",\"registrationNumber\"]', '2026-08-01 15:22:27');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `historyId` int(11) NOT NULL,
  `recordId` varchar(16) NOT NULL,
  `status` varchar(50) NOT NULL,
  `remark` varchar(155) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`historyId`, `recordId`, `status`, `remark`, `dateCreated`) VALUES
(1, '1', 'Intrested - Quote Sent', 'ddd', '2026-07-31 04:49:08'),
(2, '64933', 'Not Intrested', '', '2026-07-29 20:08:17'),
(3, '64933', 'Not Intrested', '', '2026-07-29 20:08:37'),
(4, '64933', 'Already Sale', '', '2026-07-29 20:08:50'),
(5, '64933', 'Tommorrow - Cust coming to office', '', '2026-07-29 20:10:27'),
(6, '2', 'Call Not Received - Quote Sent', '', '2026-07-31 04:49:08'),
(7, '3', 'Call Done - Cust not available in city', 'sss', '2026-07-31 04:49:08'),
(8, '4', 'Call Not Received - Quote Sent', 'quote send', '2026-07-31 04:49:08'),
(9, '1', 'Intrested - Quote Sent', 'First record', '2026-07-31 04:49:08'),
(10, '2', 'Intrested - Quote Sent', 'second record', '2026-07-31 04:49:08'),
(11, '3', 'Call Done - Cust not available in city', 'third record', '2026-07-31 04:49:08'),
(12, '4', 'Intrested - Quote Sent', 'fourth record', '2026-07-31 04:49:08'),
(13, '5', 'Not Intrested', 'quote send', '2026-07-31 08:55:28');

-- --------------------------------------------------------

--
-- Table structure for table `id_map`
--

CREATE TABLE `id_map` (
  `old_id` char(16) NOT NULL,
  `new_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `id_map`
--

INSERT INTO `id_map` (`old_id`, `new_id`) VALUES
('02f7353b04bf4396', 1),
('143007c279876476', 2),
('1a362bc79af85968', 3),
('2513725128609421', 4),
('3135690bec802634', 5),
('3518206314167198', 6),
('433ea44f069b7413', 7),
('476cfe05c21b8949', 8),
('630ac3ac492d7945', 9),
('939ba2476a201076', 10),
('945912f60a9d6068', 11),
('a148c0f2cd281189', 12),
('a14a07b98a1a8029', 13),
('a4abad9ae80e4064', 14),
('b52a394a6f147810', 15),
('b59af88921b68383', 16),
('d183ee0accd34827', 17),
('dc9c4e4b38353946', 18);

-- --------------------------------------------------------

--
-- Table structure for table `lead_history`
--

CREATE TABLE `lead_history` (
  `history_id` int(11) NOT NULL,
  `record_id` int(11) NOT NULL,
  `telecaller_id` char(16) DEFAULT NULL,
  `leadStatus` enum('contacted','interested','not_interested','closed_firm','closed_outside') NOT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(2, '2024-06-16-000001', 'App\\Database\\Migrations\\CreateAttendanceTable', 'default', 'App', 1781551992, 1);

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `policy_id` int(11) NOT NULL,
  `policy_number` varchar(40) NOT NULL,
  `holder_name` varchar(60) NOT NULL,
  `company_name` varchar(30) NOT NULL,
  `vehicle_number` varchar(10) NOT NULL,
  `insurance_type` varchar(10) NOT NULL,
  `mobileNo` varchar(10) NOT NULL,
  `cashback` int(5) NOT NULL,
  `telecaller` varchar(16) NOT NULL,
  `premium` varchar(10) NOT NULL,
  `policyType` varchar(15) NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `file_path` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policies`
--

INSERT INTO `policies` (`policy_id`, `policy_number`, `holder_name`, `company_name`, `vehicle_number`, `insurance_type`, `mobileNo`, `cashback`, `telecaller`, `premium`, `policyType`, `issue_date`, `expiry_date`, `file_path`, `created_at`, `updated_at`) VALUES
(23, '6304091880', 'Mr Santosh Kisan Jagtap', 'Tata Aig', 'MH12WJ9213', 'Vehicle', '', 0, '207c49c0f2fc6493', '', '', '2026-05-30', '2027-05-29', 'writable/uploads/policies/1785500158_8e22fdbceecfa4ac2714.pdf', '2026-07-31 06:45:58', '2026-07-31 06:45:58'),
(24, '202501380598000-00', 'RANJEET TOURS AND TRAVELS', 'SBI General', 'MH05DK5823', 'Vehicle', '', 0, '207c49c0f2fc6493', '', '', '2025-05-27', '2026-05-26', 'writable/uploads/policies/1785500999_383394be2f2dc97a7271.pdf', '2026-07-31 06:59:59', '2026-07-31 06:59:59'),
(25, '6270360656', 'Mr LATA RAJESH GAIKWAD', 'QuickInsure', 'MH14BF2323', 'Vehicle', '', 3000, '', '', 'Fresh', '2026-05-30', '2027-05-29', 'writable/uploads/policies/1785554341_d8379685d6a3cabfb912.pdf', '2026-08-01 03:26:28', '2026-07-31 21:56:28'),
(26, '215037/31/27/001453', 'M/S. SHRI SAI TOURS', 'Shriram', 'MH12XM2463', 'Vehicle', '9123456789', 1500, '207c49c0f2fc6493', '1200000', 'Fresh', '2026-05-30', '2027-05-29', 'writable/uploads/policies/1785555128_60c7b32fed604df62d29.pdf', '2026-08-01 03:37:30', '2026-07-31 22:07:30'),
(27, '132/02/21/0527/MTP/1010500825', 'Dear SHIVANYA TOURS AND TRAVELS', 'Generali Central', 'MH12VF3650', 'Vehicle', '', 0, '207c49c0f2fc6493', '', '', '2026-05-29', '2027-05-28', 'writable/uploads/policies/1785555595_04945c6731d73713be5d.pdf', '2026-07-31 22:09:55', '2026-07-31 22:09:55');

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE `subscription` (
  `id` int(11) NOT NULL,
  `receiver_name` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `screenshot` varchar(100) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `employeeId` char(16) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `status` enum('Active','Expired','Cancelled') DEFAULT 'Active',
  `amount` decimal(10,2) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `employeeId`, `startDate`, `endDate`, `status`, `amount`, `createdAt`, `updatedAt`) VALUES
(3, '1f4c280d781e9380', '2026-07-26', '2026-08-26', 'Active', 100.00, '2026-07-26 11:48:24', '2026-07-26 11:48:24'),
(4, '207c49c0f2fc6493', '2026-07-26', '2026-08-26', 'Active', 100.00, '2026-07-26 11:49:46', '2026-07-26 11:49:46');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `record_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `dataset_type` varchar(50) NOT NULL,
  `telecaller_id` varchar(16) DEFAULT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`record_id`, `batch_id`, `dataset_type`, `telecaller_id`, `status`, `data`, `uploaded_at`) VALUES
(1, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Rajesh Sharma\",\"address\":\"Pune, Maharashtra\",\"contact\":\"9876543210\",\"regDate\":\"01-06-2026\",\"regDateMonth\":\"June\",\"expiryDate\":\"01-06-2027\",\"vehicleMaker\":\"Maruti\",\"vehicleModel\":\"Suzuki Swift\",\"fuelType\":\"Petrol\",\"saleAmt\":\"450000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"1197\",\"prevInsuCompany\":\"ICICI Lombard\",\"finance\":\"HDFC Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"MH12AB1234\",\"dddd\":\"ee\"}', '2026-08-01 03:53:33'),
(2, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Anita Verma\",\"address\":\"Delhi, India\",\"contact\":\"9123456789\",\"regDate\":\"15-05-2026\",\"regDateMonth\":\"May\",\"expiryDate\":\"15-05-2027\",\"vehicleMaker\":\"Hyundai\",\"vehicleModel\":\"Creta\",\"fuelType\":\"Diesel\",\"saleAmt\":\"1200000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"998\",\"prevInsuCompany\":\"Bajaj Allianz\",\"finance\":\"Axis Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"DL8CAF5678\",\"dddd\":\"ee\"}', '2026-08-01 03:53:33'),
(3, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Sunil Patil\",\"address\":\"Ahmedabad, Gujarat\",\"contact\":\"9988776655\",\"regDate\":\"20-04-2026\",\"regDateMonth\":\"April\",\"expiryDate\":\"20-04-2027\",\"vehicleMaker\":\"Honda\",\"vehicleModel\":\"City\",\"fuelType\":\"CNG\",\"saleAmt\":\"950000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"1197\",\"prevInsuCompany\":\"Tata AIG\",\"finance\":\"SBI Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"GJ1XY7890\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(4, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Meena Rao\",\"address\":\"Bangalore, Karnataka\",\"contact\":\"9876501234\",\"regDate\":\"10-03-2026\",\"regDateMonth\":\"March\",\"expiryDate\":\"10-03-2027\",\"vehicleMaker\":\"Toyota\",\"vehicleModel\":\"Innova\",\"fuelType\":\"Diesel\",\"saleAmt\":\"1600000\",\"seatCapacity\":\"7\",\"cubicCapacity\":\"998\",\"prevInsuCompany\":\"New India Assurance\",\"finance\":\"ICICI Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"KA05MN4321\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(5, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Vikas Singh\",\"address\":\"Lucknow, Uttar Pradesh\",\"contact\":\"9123409876\",\"regDate\":\"05-02-2026\",\"regDateMonth\":\"February\",\"expiryDate\":\"05-02-2027\",\"vehicleMaker\":\"Ford\",\"vehicleModel\":\"Ecosport\",\"fuelType\":\"Petrol\",\"saleAmt\":\"800000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"1197\",\"prevInsuCompany\":\"HDFC Ergo\",\"finance\":\"HDFC Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"UP16GH8765\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(6, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Shreya Das\",\"address\":\"Kolkata, West Bengal\",\"contact\":\"9988701122\",\"regDate\":\"25-01-2026\",\"regDateMonth\":\"January\",\"expiryDate\":\"25-01-2027\",\"vehicleMaker\":\"Tata\",\"vehicleModel\":\"Nexon\",\"fuelType\":\"Electric\",\"saleAmt\":\"1400000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"998\",\"prevInsuCompany\":\"Oriental Insurance\",\"finance\":\"Axis Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"WB20JK3456\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(7, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Arjun Kulkarni\",\"address\":\"Pune, Maharashtra\",\"contact\":\"9876543211\",\"regDate\":\"10-06-2026\",\"regDateMonth\":\"June\",\"expiryDate\":\"10-06-2027\",\"vehicleMaker\":\"Mahindra\",\"vehicleModel\":\"Thar\",\"fuelType\":\"Diesel\",\"saleAmt\":\"1500000\",\"seatCapacity\":\"4\",\"cubicCapacity\":\"1197\",\"prevInsuCompany\":\"United India Insurance\",\"finance\":\"SBI Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"MH14PQ9876\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(8, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Kavita Sharma\",\"address\":\"Delhi, India\",\"contact\":\"9123456780\",\"regDate\":\"22-05-2026\",\"regDateMonth\":\"May\",\"expiryDate\":\"22-05-2027\",\"vehicleMaker\":\"Renault\",\"vehicleModel\":\"Kwid\",\"fuelType\":\"Petrol\",\"saleAmt\":\"500000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"998\",\"prevInsuCompany\":\"Future Generali\",\"finance\":\"HDFC Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"DL9RS2345\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(9, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Ramesh Patel\",\"address\":\"Surat, Gujarat\",\"contact\":\"9988776611\",\"regDate\":\"18-04-2026\",\"regDateMonth\":\"April\",\"expiryDate\":\"18-04-2027\",\"vehicleMaker\":\"Nissan\",\"vehicleModel\":\"Magnite\",\"fuelType\":\"Petrol\",\"saleAmt\":\"700000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"1197\",\"prevInsuCompany\":\"ICICI Lombard\",\"finance\":\"Axis Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"GJ2LM7654\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(10, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Deepa Nair\",\"address\":\"Mysore, Karnataka\",\"contact\":\"9876509876\",\"regDate\":\"30-03-2026\",\"regDateMonth\":\"March\",\"expiryDate\":\"30-03-2027\",\"vehicleMaker\":\"Volkswagen\",\"vehicleModel\":\"Polo\",\"fuelType\":\"Diesel\",\"saleAmt\":\"900000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"998\",\"prevInsuCompany\":\"Bajaj Allianz\",\"finance\":\"SBI Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"KA06UV1234\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(11, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Prakash Jadhav\",\"address\":\"Pune, Maharashtra\",\"contact\":\"9876543222\",\"regDate\":\"02-06-2026\",\"regDateMonth\":\"June\",\"expiryDate\":\"02-06-2027\",\"vehicleMaker\":\"Maruti\",\"vehicleModel\":\"Baleno\",\"fuelType\":\"Petrol\",\"saleAmt\":\"650000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"1197\",\"prevInsuCompany\":\"ICICI Lombard\",\"finance\":\"HDFC Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"MH15XY4321\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(12, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Neha Gupta\",\"address\":\"Delhi, India\",\"contact\":\"9123456799\",\"regDate\":\"12-05-2026\",\"regDateMonth\":\"May\",\"expiryDate\":\"12-05-2027\",\"vehicleMaker\":\"Hyundai\",\"vehicleModel\":\"i20\",\"fuelType\":\"Diesel\",\"saleAmt\":\"1100000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"998\",\"prevInsuCompany\":\"Bajaj Allianz\",\"finance\":\"Axis Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"DL10AB5678\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(13, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Suresh Mehta\",\"address\":\"Ahmedabad, Gujarat\",\"contact\":\"9988776622\",\"regDate\":\"25-04-2026\",\"regDateMonth\":\"April\",\"expiryDate\":\"25-04-2027\",\"vehicleMaker\":\"Honda\",\"vehicleModel\":\"Amaze\",\"fuelType\":\"Petrol\",\"saleAmt\":\"800000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"1197\",\"prevInsuCompany\":\"Tata AIG\",\"finance\":\"SBI Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"GJ3CD7890\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(14, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Anjali Menon\",\"address\":\"Bangalore, Karnataka\",\"contact\":\"9876501222\",\"regDate\":\"15-03-2026\",\"regDateMonth\":\"March\",\"expiryDate\":\"15-03-2027\",\"vehicleMaker\":\"Toyota\",\"vehicleModel\":\"Yaris\",\"fuelType\":\"Petrol\",\"saleAmt\":\"1200000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"998\",\"prevInsuCompany\":\"New India Assurance\",\"finance\":\"ICICI Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"KA07EF4321\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(15, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Manoj Kumar\",\"address\":\"Lucknow, Uttar Pradesh\",\"contact\":\"9123409888\",\"regDate\":\"08-02-2026\",\"regDateMonth\":\"February\",\"expiryDate\":\"08-02-2027\",\"vehicleMaker\":\"Ford\",\"vehicleModel\":\"Figo\",\"fuelType\":\"Diesel\",\"saleAmt\":\"600000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"1197\",\"prevInsuCompany\":\"HDFC Ergo\",\"finance\":\"HDFC Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"UP17GH8765\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(16, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Priyanka Sen\",\"address\":\"Kolkata, West Bengal\",\"contact\":\"9988701133\",\"regDate\":\"28-01-2026\",\"regDateMonth\":\"January\",\"expiryDate\":\"28-01-2027\",\"vehicleMaker\":\"Tata\",\"vehicleModel\":\"Altroz\",\"fuelType\":\"Petrol\",\"saleAmt\":\"750000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"998\",\"prevInsuCompany\":\"Oriental Insurance\",\"finance\":\"Axis Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"WB21JK3456\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(17, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Sachin Deshmukh\",\"address\":\"Pune, Maharashtra\",\"contact\":\"9876543233\",\"regDate\":\"12-06-2026\",\"regDateMonth\":\"June\",\"expiryDate\":\"12-06-2027\",\"vehicleMaker\":\"Mahindra\",\"vehicleModel\":\"XUV500\",\"fuelType\":\"Diesel\",\"saleAmt\":\"1700000\",\"seatCapacity\":\"7\",\"cubicCapacity\":\"1197\",\"prevInsuCompany\":\"United India Insurance\",\"finance\":\"SBI Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"MH16PQ9876\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34'),
(18, 1785576213, 'life', '207c49c0f2fc6493', 'pending', '{\"﻿customerName\":\"Rekha Sharma\",\"address\":\"Delhi, India\",\"contact\":\"9123456777\",\"regDate\":\"24-05-2026\",\"regDateMonth\":\"May\",\"expiryDate\":\"24-05-2027\",\"vehicleMaker\":\"Renault\",\"vehicleModel\":\"Duster\",\"fuelType\":\"Diesel\",\"saleAmt\":\"1000000\",\"seatCapacity\":\"5\",\"cubicCapacity\":\"998\",\"prevInsuCompany\":\"Future Generali\",\"finance\":\"HDFC Bank\",\"telecaller\":\"207c49c0f2fc6493\",\"registrationNumber\":\"DL11RS2345\",\"dddd\":\"ee\"}', '2026-08-01 03:53:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id_attendance_date` (`employee_id`,`attendance_date`);

--
-- Indexes for table `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`recordId`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employeeId`);

--
-- Indexes for table `employeeloginhistory`
--
ALTER TABLE `employeeloginhistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employeeId` (`employeeId`);

--
-- Indexes for table `employee_preferences`
--
ALTER TABLE `employee_preferences`
  ADD PRIMARY KEY (`employeeId`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`historyId`);

--
-- Indexes for table `id_map`
--
ALTER TABLE `id_map`
  ADD PRIMARY KEY (`old_id`),
  ADD UNIQUE KEY `new_id` (`new_id`);

--
-- Indexes for table `lead_history`
--
ALTER TABLE `lead_history`
  ADD PRIMARY KEY (`history_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`policy_id`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employeeId` (`employeeId`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`record_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `data`
--
ALTER TABLE `data`
  MODIFY `recordId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `employeeloginhistory`
--
ALTER TABLE `employeeloginhistory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `historyId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lead_history`
--
ALTER TABLE `lead_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `subscription`
--
ALTER TABLE `subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employeeloginhistory`
--
ALTER TABLE `employeeloginhistory`
  ADD CONSTRAINT `employeeloginhistory_ibfk_1` FOREIGN KEY (`employeeId`) REFERENCES `employee` (`employeeId`);

--
-- Constraints for table `employee_preferences`
--
ALTER TABLE `employee_preferences`
  ADD CONSTRAINT `employee_preferences_ibfk_1` FOREIGN KEY (`employeeId`) REFERENCES `employee` (`employeeId`);

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`employeeId`) REFERENCES `employee` (`employeeId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
