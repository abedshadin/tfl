-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 23, 2025 at 10:40 AM
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
-- Database: `od`
--

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `bank_acc_no` varchar(100) DEFAULT NULL,
  `ref_prefix` varchar(50) DEFAULT NULL COMMENT 'e.g., TFL/SCM/BBL/'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banks`
--

INSERT INTO `banks` (`id`, `bank_name`, `address1`, `address2`, `address3`, `bank_acc_no`, `ref_prefix`) VALUES
(1, 'BRAC BANK LIMITED', '1 Gulshan Avenue', 'Gulshan-1', 'Dhaka-1212.', '1501201914712001', 'TFL/SCM/BBL/'),
(2, 'Dutch-Bangla Bank', 'DBL Bank Address 1', 'DBL Bank Address 2', 'Dhaka', 'YOUR_DBL_ACC_NO', 'TFL/SCM/DBL/'),
(3, 'Standard Chartered Bank', 'SCB Bank Address 1', 'SCB Bank Address 2', 'Dhaka', 'YOUR_SCB_ACC_NO', 'TFL/SCM/SCB/');

-- --------------------------------------------------------

--
-- Table structure for table `cnf_agents`
--

CREATE TABLE `cnf_agents` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `attn_person` varchar(100) DEFAULT NULL,
  `ref_prefix` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cnf_agents`
--

INSERT INTO `cnf_agents` (`id`, `name`, `address1`, `address2`, `attn_person`, `ref_prefix`) VALUES
(1, 'SHAROTHI ENTERPRISE', '123 Sharothi Lane', 'Dhaka', 'Mr. Sharothi Contact', 'TFL/SCM/SE/'),
(2, 'Tea Holdings Limited', '10, Agrabad L/A,', 'Chattogram.', 'Mr. Biswas Mujibur Rahman.', 'TFL/SCM/THL/');

-- --------------------------------------------------------

--
-- Table structure for table `proforma_invoices`
--

CREATE TABLE `proforma_invoices` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `pi_number` varchar(100) NOT NULL,
  `pi_date` date DEFAULT NULL,
  `lc_number` varchar(100) DEFAULT NULL,
  `lc_date` date DEFAULT NULL,
  `freight_cost` decimal(10,2) DEFAULT NULL,
  `lc_tolerance_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `lc_tolerance_percentage` int(11) NOT NULL DEFAULT 10,
  `cnf_agent_id` int(11) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `cnf_reference_no` varchar(100) DEFAULT NULL,
  `subject_line` varchar(255) DEFAULT NULL,
  `amount_in_words` text DEFAULT NULL,
  `commercial_invoice_no` varchar(100) DEFAULT NULL,
  `commercial_invoice_date` date DEFAULT NULL,
  `bl_number` varchar(100) DEFAULT NULL,
  `bl_date` date DEFAULT NULL,
  `chk_bill_of_exchange` tinyint(1) NOT NULL DEFAULT 1,
  `chk_packing_list` tinyint(1) NOT NULL DEFAULT 1,
  `chk_coo` tinyint(1) NOT NULL DEFAULT 1,
  `chk_health_cert` tinyint(1) NOT NULL DEFAULT 1,
  `chk_radioactivity_cert` tinyint(1) NOT NULL DEFAULT 1,
  `chk_lc_copy` tinyint(1) NOT NULL DEFAULT 1,
  `chk_pi_copy` tinyint(1) NOT NULL DEFAULT 1,
  `chk_insurance_cert` tinyint(1) NOT NULL DEFAULT 1,
  `chk_form_ga` tinyint(1) NOT NULL DEFAULT 1,
  `od_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `chk_others_cert_text` varchar(255) DEFAULT NULL,
  `chk_noc` tinyint(1) NOT NULL DEFAULT 0,
  `chk_undertaking` tinyint(1) NOT NULL DEFAULT 0,
  `chk_declaration` tinyint(1) NOT NULL DEFAULT 0,
  `chk_lca_cad` tinyint(1) NOT NULL DEFAULT 0,
  `document_status` varchar(20) NOT NULL DEFAULT 'Original',
  `tolerance_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `tolerance_percentage` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proforma_invoices`
--

INSERT INTO `proforma_invoices` (`id`, `vendor_id`, `pi_number`, `pi_date`, `lc_number`, `lc_date`, `freight_cost`, `lc_tolerance_enabled`, `lc_tolerance_percentage`, `cnf_agent_id`, `bank_id`, `reference_no`, `cnf_reference_no`, `subject_line`, `amount_in_words`, `commercial_invoice_no`, `commercial_invoice_date`, `bl_number`, `bl_date`, `chk_bill_of_exchange`, `chk_packing_list`, `chk_coo`, `chk_health_cert`, `chk_radioactivity_cert`, `chk_lc_copy`, `chk_pi_copy`, `chk_insurance_cert`, `chk_form_ga`, `od_enabled`, `chk_others_cert_text`, `chk_noc`, `chk_undertaking`, `chk_declaration`, `chk_lca_cad`, `document_status`, `tolerance_enabled`, `tolerance_percentage`) VALUES
(1, 1, '', '0000-00-00', '', NULL, 0.00, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(2, 1, '', '2025-10-17', '', NULL, 0.00, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(3, 1, '', '0000-00-00', '', NULL, 100.00, 1, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(4, 1, '5000001935', '2025-10-17', 'fghfg', NULL, 546546.00, 1, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 0),
(5, 2, '', '2025-10-17', '', NULL, 0.00, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(6, 2, '', '2025-10-17', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(7, 1, '', '2025-10-17', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(8, 1, '', '2025-10-17', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(9, 5, '455', '2025-10-07', '308525000000000', '2025-10-15', 2850.00, 1, 10, 1, NULL, 'TFL/SCM/DBL/2025/2', 'TFL/SCM/SE/2025/5', 'Opening L/C for Import of Raw Items', 'Twenty four thousand two hundred and forty and sixty eight cents', '123', '2025-10-17', 'BL1256', NULL, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 'Phytosanitary', 0, 0, 0, 0, '0', 0, 10),
(10, 1, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(11, 1, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(12, 1, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(13, 1, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(14, 1, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(15, 4, 'tryhrth', '2025-10-18', '1234564', '2025-10-15', 500.35, 0, 10, 1, 1, 'TFL/SCM/BBL/2025/1', 'TFL/SCM/SE/2025/1', 'Opening L/C for Import for International Cha', 'Five hundred and', '', NULL, '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, 0, 'Original', 0, 10),
(16, 4, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(17, 4, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(18, 4, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(19, 4, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(20, 4, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(21, 4, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(22, 4, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(23, 1, '', '2025-10-18', 'fghfg', NULL, 0.00, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(24, 4, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(25, 5, '456', '2025-10-07', '308525000000000', '2025-10-19', 2850.00, 1, 10, 1, NULL, '', 'TFL/SCM/SE/2025/3', 'Opening L/C for Import of Kitchen Equipment for International Chain Restaurant', '', '123', '2025-10-19', '126', NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 1, 1, 1, 1, 'Original', 0, 10),
(26, 4, '', '2025-10-18', NULL, NULL, NULL, 0, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(27, 3, 'aaa', '2025-10-20', '', NULL, 0.00, 0, 10, 1, NULL, 'TFL/SCM/SE/102', 'TFL/SCM/SE/2025/2', 'Opening L/C for Import of Kitchen Equipment for International Chain Restaurant', 'Zero', '', NULL, '', NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Copy', 0, 10),
(28, 3, 'bbbb', '2025-10-20', '', NULL, 0.00, 0, 10, 1, NULL, 'TFL/SCM/SE/102', NULL, NULL, NULL, '', NULL, '', NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(29, 3, '', '2025-10-20', '', NULL, 0.00, 0, 10, NULL, NULL, '', NULL, NULL, NULL, '', NULL, '', NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1', 0, 0, 0, 0, 'Original', 0, 10),
(30, 3, '', '2025-10-20', '', NULL, 541.00, 0, 0, NULL, NULL, '', NULL, NULL, NULL, '', NULL, '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, 0, 'copy', 0, 10);

-- --------------------------------------------------------

--
-- Table structure for table `proforma_products`
--

CREATE TABLE `proforma_products` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `unit` varchar(20) DEFAULT 'Case',
  `net_weight` decimal(10,2) DEFAULT NULL,
  `hs_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proforma_products`
--

INSERT INTO `proforma_products` (`id`, `invoice_id`, `description`, `quantity`, `unit_price`, `unit`, `net_weight`, `hs_code`) VALUES
(47, 5, 'Grade A Green Peas 10kg Bulk', 1, 15.00, 'Case', NULL, '0710.21.00'),
(48, 5, 'Grade A Green Peas 10kg Bulk', 1, 15.00, 'Case', NULL, '0710.21.01'),
(90, 4, 'Grade A Green Peas 10kg Bulk', 2, 55.00, 'Carton', NULL, '2004.10.00'),
(91, 4, 'Savoury Wedges 5 x 2.5 Kg = 12.5 Kg', 1, 17.88, 'Case', NULL, '2004.10.00'),
(92, 4, 'Sure Crisp 6MM Coated Fries 6 x 2.5 Kg = 15 Kg', 1, 21.34, 'Case', NULL, '2004.10.00'),
(119, 1, '1450 Case Sure Crisp 6MM Coated Fries 6 x 2.5 Kg = 15 Kg', 1450, 21.34, 'Case', NULL, '2004.10.00'),
(120, 1, '90 Case Savoury Wedges 5 x 2.5 Kg = 12.5 Kg', 90, 17.88, 'Case', NULL, '2004.10.00'),
(121, 1, 'Savoury Wedges 5 x 2.5 Kg = 12.5 Kg', 1, 17.88, 'Case', NULL, '2004.10.00'),
(132, 3, 'Sure Crisp 6MM Coated Frie', 1, 50.00, 'None', NULL, '2004.10.00'),
(133, 23, '1450 Case Sure Crisp 6MM Coated Fries 6 x 2.5 Kg = 15 Kg', 1, 21.34, 'Case', NULL, '2004.10.00'),
(230, 30, 'Sure Crisp 6MM Coated Frie', 1, 456.75, 'Case', 348.00, '555'),
(231, 30, 'new', 1, 371.20, 'Case', 300.00, '55'),
(241, 15, 'Sure Crisp 6MM Coated Fries 6 x 2.5 Kg = 15 Kg', 1, 1.00, 'Case', 0.00, '5'),
(243, 27, 'Sure Crisp 6MM Coated Frie', 15, 456.75, 'Case', 348.00, '555'),
(279, 25, 'Veg Mayonnaise 1 Kg ', 1, 18.00, 'Case', 13200.00, '2103.90.90'),
(280, 25, 'Nashville Sauce 500g ', 1, 15.75, 'Case', 2760.00, '2103.90.90'),
(281, 25, 'Masala Salsa 1 Kg ', 1, 18.56, 'Case', 420.00, '2103.90.90'),
(282, 25, 'Three Chilli Mayonnaise ', 1, 29.00, 'Case', 225.00, '2103.90.90'),
(283, 25, 'Tandoori Sauce Fast 1 Kg', 1, 17.70, 'Case', 825.00, '2103.90.90'),
(324, 9, 'Veg Mayonnaise 1 Kg', 880, 18.00, 'Case', 15.00, '2103.90.90'),
(325, 9, 'Nashville Sauce 500g', 230, 15.75, 'Case', 12.00, '2103.90.90'),
(326, 9, 'Masala Salsa 1 Kg', 28, 18.56, 'Case', 15.00, '2103.90.90'),
(327, 9, 'Three Chilli Mayonnaise', 15, 29.00, 'Case', 15.00, '2103.90.90'),
(328, 9, 'Tandoori Sauce Fast 1 Kg', 55, 17.70, 'Case', 15.00, '2103.90.90');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`) VALUES
(2, 'admin', '$2y$10$1QInx/1z2VIkcnf55i4BKO5wBRhIdyPbyJA3SK92Qrv2BwTf6HeF6');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `vendor_address` text DEFAULT NULL,
  `default_currency` varchar(10) DEFAULT 'US$',
  `beneficiary_bank` varchar(255) DEFAULT NULL,
  `beneficiary_swift` varchar(50) DEFAULT NULL,
  `beneficiary_ac_no` varchar(50) DEFAULT NULL,
  `advising_bank_name` varchar(255) DEFAULT NULL,
  `advising_bank_swift` varchar(50) DEFAULT NULL,
  `advising_bank_ac_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `vendor_address`, `default_currency`, `beneficiary_bank`, `beneficiary_swift`, `beneficiary_ac_no`, `advising_bank_name`, `advising_bank_swift`, `advising_bank_ac_no`) VALUES
(1, 'MCCAIN FOODS (INDIA) PVT. LTD.', NULL, 'US$', 'CITIBANK NA', 'CITIINBX', '0520263012', 'CITIBANK NY', 'CITIUS33', '10990896'),
(2, 'Global Food Suppliers Inc.', NULL, 'US$', 'Bank of America', 'BOFAUS3N', '9876543210', NULL, NULL, NULL),
(3, 'Premium Frozen Goods Ltd.', NULL, 'EUR€', 'HSBC Hong Kong', 'HSBCHKHH', '123-456789-001', '', '', ''),
(4, 'AgroFresh Exports', 'Sherpur', 'US$', 'State Bank of India', 'SBININBB', '1122334455', '', '', ''),
(5, 'Dr. Oetkar India Private Limited', '', 'US$', 'HSBC Bank, 25, Barakhamba Road, New Delhi-110001, India', 'HSBCINBB', '0520263012', '', '', ''),
(6, 'yj', NULL, 'US$', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_products`
--

CREATE TABLE `vendor_products` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `default_unit_price` decimal(10,2) DEFAULT NULL,
  `default_unit` varchar(20) DEFAULT 'Case',
  `default_net_weight` decimal(10,2) DEFAULT NULL,
  `default_hs_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_products`
--

INSERT INTO `vendor_products` (`id`, `vendor_id`, `description`, `default_unit_price`, `default_unit`, `default_net_weight`, `default_hs_code`) VALUES
(1, 1, 'Sure Crisp 6MM Coated Fries 6 x 2.5 Kg = 15 Kg', 50.00, 'None', 15.00, '2004.10.00'),
(4, 2, 'Grade A Green Peas 10kg Bulk', 15.00, 'Case', NULL, '0710.21.01'),
(5, 2, 'Sweet Corn Kernels 10kg Bulk', 14.25, 'Case', NULL, '0710.40.00'),
(85, 1, 'Savoury Wedges 5 x 2.5 Kg = 12.5 Kg', 17.88, 'Case', 12.50, '2004.10.00'),
(86, 5, 'Tandoori Sauce Fast 1 Kg', 17.70, '0', 15.00, '2103.90.90'),
(87, 5, 'Veg Mayonnaise 1 Kg ', 18.00, '0', 15.00, '2103.90.90'),
(88, 5, 'Nashville Sauce 500g ', 15.75, '0', 12.00, '2103.90.90'),
(89, 5, 'Masala Salsa 1 Kg ', 18.56, '0', 15.00, '2103.90.90'),
(90, 5, 'Three Chilli Mayonnaise ', 29.00, '0', 15.00, '2103.90.90'),
(107, 1, '1450 Case Sure Crisp 6MM Coated Fries 6 x 2.5 Kg = 15 Kg', 21.34, 'Case', 15.00, '2004.10.00'),
(108, 1, '90 Case Savoury Wedges 5 x 2.5 Kg = 12.5 Kg', 17.88, 'Case', 12.50, '2004.10.00'),
(122, 5, 'Small wear', 5000.00, 'None', NULL, '55'),
(124, 1, 'Sure Crisp 6MM Coated Frie', 50.00, 'None', 15.00, '2004.10.00'),
(169, 3, 'Sure Crisp 6MM Coated Frie', 456.75, '0', 348.00, '555'),
(187, 3, 'new', 371.20, '0', 300.00, '55'),
(226, 4, 'Sure Crisp 6MM Coated Fries 6 x 2.5 Kg = 15 Kg', 1.00, '0', 0.00, '5');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cnf_agents`
--
ALTER TABLE `cnf_agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `proforma_invoices`
--
ALTER TABLE `proforma_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `fk_cnf_agent` (`cnf_agent_id`),
  ADD KEY `fk_bank` (`bank_id`);

--
-- Indexes for table `proforma_products`
--
ALTER TABLE `proforma_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_products`
--
ALTER TABLE `vendor_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vendor_product` (`vendor_id`,`description`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cnf_agents`
--
ALTER TABLE `cnf_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `proforma_invoices`
--
ALTER TABLE `proforma_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `proforma_products`
--
ALTER TABLE `proforma_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=329;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vendor_products`
--
ALTER TABLE `vendor_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=323;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `proforma_invoices`
--
ALTER TABLE `proforma_invoices`
  ADD CONSTRAINT `fk_bank` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cnf_agent` FOREIGN KEY (`cnf_agent_id`) REFERENCES `cnf_agents` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `proforma_invoices_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `proforma_products`
--
ALTER TABLE `proforma_products`
  ADD CONSTRAINT `proforma_products_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `proforma_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_products`
--
ALTER TABLE `vendor_products`
  ADD CONSTRAINT `vendor_products_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
