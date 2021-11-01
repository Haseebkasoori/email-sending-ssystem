-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2021 at 02:00 PM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 7.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `email_sending_service`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(255) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `forgot_otp` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `user_ion_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `user_name`, `email`, `first_name`, `last_name`, `password`, `phone_number`, `address`, `forgot_otp`, `profile_image`, `user_ion_id`, `created_at`, `updated_at`) VALUES
(1, 'haseeb', 'm.h.kasoori@gmail.com', 'Muhammad', 'Haseeb', '$2y$10$1qy24m.KezVsie/FZhbfx.1fdrQL0Eooz/hBzo6naxJIoh7BgZrCC', '032060818160', '', '74842', '', '617448da3d212', '2021-10-22 12:42:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `merchants`
--

CREATE TABLE `merchants` (
  `id` int(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `user_ion_id` varchar(255) NOT NULL,
  `forgot_otp` varchar(255) NOT NULL,
  `stripe_customer_id` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` varchar(252) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `merchants`
--

INSERT INTO `merchants` (`id`, `user_name`, `email`, `password`, `first_name`, `last_name`, `phone_number`, `address`, `user_ion_id`, `forgot_otp`, `stripe_customer_id`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 'haseeb', 'm.h.kasoori@gmail.com', '$2y$10$Yp0ynys1XAdjhDISyh.Oeujxy5AvPZmdjhbtRQ6SQV3YG4FoNRpsW', 'Muhammad', 'Haseeb', '3206081816', '', '617fbffa4f863', '', '', NULL, '2021-11-01 10:22:50', '');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `cc_email` varchar(255) DEFAULT NULL,
  `bcc_email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `body` varchar(255) NOT NULL,
  `request_id` int(255) NOT NULL,
  `merchant_id` varchar(255) DEFAULT NULL,
  `sec_user_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `from_name`, `from_email`, `to_email`, `cc_email`, `bcc_email`, `subject`, `body`, `request_id`, `merchant_id`, `sec_user_id`, `status`, `created_at`, `updated_at`) VALUES
(6, 'Haseeb', 'hasee@gmail.com', 'haseebkasoori6081@gmail.com,m.h.kasoori@gmail.com', 'haseebkasoori6081@gmail.com,m.h.kasoori@gmail.com', 'haseebkasoori6081@gmail.com,m.h.kasoori@gmail.com', 'Mail from API', 'This is mail body', 617, '', NULL, 'not send', '2021-11-01 10:04:14', '');

-- --------------------------------------------------------

--
-- Table structure for table `request_response`
--

CREATE TABLE `request_response` (
  `id` int(255) NOT NULL,
  `request_id` int(255) NOT NULL,
  `response_message` varchar(255) NOT NULL,
  `status_code` int(255) NOT NULL,
  `data` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `secondary_user`
--

CREATE TABLE `secondary_user` (
  `id` int(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `user_ion_id` varchar(255) NOT NULL,
  `forgot_otp` varchar(255) DEFAULT NULL,
  `merchant_id` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email_sending` int(10) DEFAULT 0,
  `cradit_recharge` int(10) DEFAULT 0,
  `status` enum('Active','Disable') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` varchar(252) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `secondary_user`
--

INSERT INTO `secondary_user` (`id`, `user_name`, `email`, `password`, `first_name`, `last_name`, `phone_number`, `address`, `user_ion_id`, `forgot_otp`, `merchant_id`, `profile_image`, `email_sending`, `cradit_recharge`, `status`, `created_at`, `updated_at`) VALUES
(12, 'haseeb', 'abc@gmail.com', '$2y$10$0wD8PdM1GzVJ1zqa//clI.IaI5OpzOKjIzuvD668dIjczldGKn21m', 'abc', 'abc', '123456789', '', '617f9b93c0ae4', NULL, '617f8607e81b4', '../uploads/secondary_user/617fc4ab243be.jpeg', 0, 1, 'Active', '2021-11-01 07:47:31', '2021-11-01 12:46:40'),
(13, 'haseeb', 'haseeb1@gmail.com', '$2y$10$tpQIVgiBwOuN9L/V/krYqe.DBW9dWQJDH/SEcIoVhIjOuXRcTUczi', 'ali', 'ali', '000000000', '', '617f9c8da82a7', NULL, '617f8607e81b4', '../uploads/secondary_user/617fc4cd88fdf.jpeg', 0, 1, 'Active', '2021-11-01 07:51:41', '2021-11-01 11:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(255) NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `customer_id` varchar(255) NOT NULL,
  `merchant_id` varchar(11) NOT NULL,
  `product` int(255) NOT NULL,
  `amount` int(255) NOT NULL,
  `currency` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`id`, `transaction_id`, `customer_id`, `merchant_id`, `product`, `amount`, `currency`, `status`, `created_at`) VALUES
(4, 'ch_3JqvWgKav7v2b6Ix0qCH0VZK', 'cus_KVxRcGThIn4eGB', '617f8607e81', 0, 10000, 'usd', 'succeeded', '2021-11-01 08:05:54');

-- --------------------------------------------------------

--
-- Table structure for table `user_cradit_card`
--

CREATE TABLE `user_cradit_card` (
  `id` int(255) NOT NULL,
  `merchant_ion_id` varchar(255) NOT NULL,
  `card_number` varchar(255) NOT NULL,
  `exp_month` varchar(255) NOT NULL,
  `exp_year` varchar(255) NOT NULL,
  `cvc` varchar(255) NOT NULL,
  `stripe_customer_id` varchar(255) NOT NULL,
  `status` enum('successful','failed') NOT NULL DEFAULT 'successful'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_cradit_card`
--

INSERT INTO `user_cradit_card` (`id`, `merchant_ion_id`, `card_number`, `exp_month`, `exp_year`, `cvc`, `stripe_customer_id`, `status`) VALUES
(2, '617f8607e81b4', '4242424242424242', '12', '2021', '321', 'cus_KVxRcGThIn4eGB', 'successful');

-- --------------------------------------------------------

--
-- Table structure for table `user_credit`
--

CREATE TABLE `user_credit` (
  `id` int(255) NOT NULL,
  `merchant_ion_id` varchar(255) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT '$',
  `credit` double(7,4) NOT NULL DEFAULT 100.0000,
  `day_limit` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_credit`
--

INSERT INTO `user_credit` (`id`, `merchant_ion_id`, `currency`, `credit`, `day_limit`, `created_at`, `updated_at`) VALUES
(17, '617f8607e81b4', '$', 98.8264, '2021-12-01 11:22:03', '2021-11-01 10:22:03', '2021-11-01 13:47:17'),
(18, '617fbffa4f863', '$', 99.7066, '2021-12-01 11:22:50', '2021-11-01 10:22:50', '2021-11-01 11:28:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merchants`
--
ALTER TABLE `merchants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_merchants_email` (`email`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `request_id` (`request_id`);

--
-- Indexes for table `request_response`
--
ALTER TABLE `request_response`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_request_id` (`request_id`);

--
-- Indexes for table `secondary_user`
--
ALTER TABLE `secondary_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_cradit_card`
--
ALTER TABLE `user_cradit_card`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_credit`
--
ALTER TABLE `user_credit`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `merchants`
--
ALTER TABLE `merchants`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `request_response`
--
ALTER TABLE `request_response`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `secondary_user`
--
ALTER TABLE `secondary_user`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_cradit_card`
--
ALTER TABLE `user_cradit_card`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_credit`
--
ALTER TABLE `user_credit`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `request_response`
--
ALTER TABLE `request_response`
  ADD CONSTRAINT `fk_request_id` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
