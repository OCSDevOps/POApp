-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2022 at 09:25 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `porder_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `company_setting_master`
--

CREATE TABLE `company_setting_master` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `smtp_host` varchar(70) NOT NULL,
  `smtp_username` varchar(70) NOT NULL,
  `smtp_password` int(70) NOT NULL,
  `smtp_port` varchar(70) NOT NULL,
  `smtp_encryption` varchar(70) NOT NULL,
  `smtp_from_address` varchar(70) NOT NULL,
  `smtp_from_name` varchar(70) NOT NULL,
  `smtp_cc` varchar(255) DEFAULT NULL,
  `smtp_bcc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `company_tab`
--

CREATE TABLE `company_tab` (
  `company_id` int(5) NOT NULL,
  `company_name` varchar(300) NOT NULL,
  `company_address` text NOT NULL,
  `company_logo` varchar(250) DEFAULT NULL,
  `company_createdate` datetime NOT NULL,
  `company_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cost_code_master`
--

CREATE TABLE `cost_code_master` (
  `cc_id` bigint(10) NOT NULL,
  `cc_no` varchar(100) NOT NULL,
  `cc_description` varchar(300) NOT NULL,
  `cc_details` varchar(250) DEFAULT NULL,
  `cc_createdate` datetime NOT NULL,
  `cc_createby` bigint(10) NOT NULL,
  `cc_modifydate` datetime DEFAULT NULL,
  `cc_modifyby` bigint(10) DEFAULT NULL,
  `cc_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_category_tab`
--

CREATE TABLE `item_category_tab` (
  `icat_id` int(5) NOT NULL,
  `icat_name` varchar(200) NOT NULL,
  `icat_details` varchar(250) DEFAULT NULL,
  `icat_parent` int(5) DEFAULT NULL,
  `icat_createdate` datetime NOT NULL,
  `icat_createby` bigint(10) NOT NULL,
  `icat_modifydate` datetime DEFAULT NULL,
  `icat_modifyby` bigint(10) DEFAULT NULL,
  `icat_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_master`
--

CREATE TABLE `item_master` (
  `item_id` bigint(20) NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `item_name` varchar(250) NOT NULL,
  `item_description` text DEFAULT NULL,
  `item_ccode_ms` bigint(10) NOT NULL,
  `item_cat_ms` int(5) NOT NULL,
  `item_unit_ms` int(5) NOT NULL,
  `item_createdate` datetime NOT NULL,
  `item_createby` bigint(10) NOT NULL,
  `item_modifydate` datetime DEFAULT NULL,
  `item_modifyby` bigint(10) DEFAULT NULL,
  `item_status` tinyint(2) NOT NULL DEFAULT 1,
  `item_is_rentable` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_package_details`
--

CREATE TABLE `item_package_details` (
  `ipdetail_id` bigint(11) NOT NULL,
  `ipdetail_autogen` varchar(20) NOT NULL,
  `ipdetail_ipack_ms` bigint(10) DEFAULT NULL,
  `ipdetail_item_ms` varchar(50) NOT NULL,
  `ipdetail_quantity` bigint(8) NOT NULL,
  `ipdetail_info` varchar(250) DEFAULT NULL,
  `ipdetail_createdate` datetime NOT NULL,
  `ipdetail_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_package_master`
--

CREATE TABLE `item_package_master` (
  `ipack_id` bigint(10) NOT NULL,
  `ipack_name` varchar(250) NOT NULL,
  `ipack_details` varchar(300) DEFAULT NULL,
  `ipack_totalitem` int(5) NOT NULL DEFAULT 0,
  `ipack_total_qty` bigint(8) NOT NULL DEFAULT 0,
  `ipack_createdate` datetime NOT NULL,
  `ipack_createby` bigint(10) NOT NULL,
  `ipack_modifydate` datetime DEFAULT NULL,
  `ipack_modifyby` bigint(10) DEFAULT NULL,
  `ipack_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `master_user_type`
--

CREATE TABLE `master_user_type` (
  `mu_id` int(11) NOT NULL,
  `mu_name` varchar(150) NOT NULL,
  `mu_createdate` datetime NOT NULL,
  `mu_createby` int(11) NOT NULL,
  `mu_status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_details`
--

CREATE TABLE `project_details` (
  `pdetail_id` bigint(11) NOT NULL,
  `pdetail_proj_ms` bigint(10) NOT NULL,
  `pdetail_user` bigint(10) NOT NULL,
  `pdetail_info` varchar(250) DEFAULT NULL,
  `pdetail_createdate` datetime NOT NULL,
  `pdetail_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_master`
--

CREATE TABLE `project_master` (
  `proj_id` bigint(10) NOT NULL,
  `proj_number` varchar(50) NOT NULL,
  `proj_name` varchar(250) NOT NULL,
  `proj_address` text NOT NULL,
  `proj_description` text DEFAULT NULL,
  `proj_contact` int(5) NOT NULL DEFAULT 0,
  `proj_createdate` datetime NOT NULL,
  `proj_createby` bigint(10) NOT NULL,
  `proj_modifydate` datetime DEFAULT NULL,
  `proj_modifyby` bigint(10) DEFAULT NULL,
  `proj_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_details`
--

CREATE TABLE `purchase_order_details` (
  `po_detail_id` bigint(11) NOT NULL,
  `po_detail_autogen` varchar(30) NOT NULL,
  `po_detail_porder_ms` bigint(10) NOT NULL,
  `po_detail_item` varchar(30) NOT NULL,
  `po_detail_sku` varchar(30) NOT NULL,
  `po_detail_taxcode` varchar(50) NOT NULL,
  `po_detail_quantity` int(5) NOT NULL,
  `po_detail_unitprice` double(10,2) NOT NULL,
  `po_detail_subtotal` double(10,2) NOT NULL,
  `po_detail_taxamount` double(10,2) NOT NULL,
  `po_detail_total` double(10,2) NOT NULL,
  `po_detail_createdate` datetime NOT NULL,
  `po_detail_status` tinyint(2) NOT NULL DEFAULT 1,
  `po_detail_tax_group` varchar(50) DEFAULT NULL,
  `po_detail_duration` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_master`
--

CREATE TABLE `purchase_order_master` (
  `porder_id` bigint(11) NOT NULL,
  `porder_project_ms` bigint(10) NOT NULL,
  `porder_no` varchar(50) NOT NULL,
  `porder_supplier_ms` bigint(10) NOT NULL,
  `porder_address` text NOT NULL,
  `porder_delivery_note` text DEFAULT NULL,
  `porder_total_item` int(5) NOT NULL,
  `porder_total_amount` decimal(10,2) NOT NULL,
  `porder_delivery_status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '1 = Full, 2 = Partial',
  `porder_createdate` datetime NOT NULL,
  `porder_createby` bigint(10) NOT NULL,
  `porder_modifydate` datetime DEFAULT NULL,
  `porder_modifyby` bigint(10) DEFAULT NULL,
  `porder_status` tinyint(2) NOT NULL DEFAULT 1,
  `porder_description` text DEFAULT NULL,
  `porder_total_tax` decimal(10,2) DEFAULT NULL,
  `porder_type` enum('Material PO','Rental PO') DEFAULT 'Material PO',
  `porder_delivery_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `receive_order_details`
--

CREATE TABLE `receive_order_details` (
  `ro_detail_id` bigint(11) NOT NULL,
  `ro_detail_rorder_ms` bigint(10) NOT NULL,
  `ro_detail_item` varchar(50) NOT NULL,
  `ro_detail_quantity` bigint(8) NOT NULL,
  `ro_detail_createdate` datetime NOT NULL,
  `ro_detail_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `receive_order_master`
--

CREATE TABLE `receive_order_master` (
  `rorder_id` bigint(11) NOT NULL,
  `rorder_porder_ms` bigint(11) NOT NULL,
  `rorder_slip_no` varchar(100) NOT NULL,
  `rorder_infoset` varchar(200) DEFAULT NULL,
  `rorder_date` date NOT NULL,
  `rorder_totalitem` int(5) DEFAULT NULL,
  `rorder_totalamount` double(10,2) DEFAULT NULL,
  `rorder_createdate` datetime NOT NULL,
  `rorder_createby` bigint(10) NOT NULL,
  `rorder_modifydate` datetime DEFAULT NULL,
  `rorder_modifyby` bigint(10) DEFAULT NULL,
  `rorder_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_catalog_tab`
--

CREATE TABLE `supplier_catalog_tab` (
  `supcat_id` bigint(20) NOT NULL,
  `supcat_supplier` bigint(11) NOT NULL,
  `supcat_item_code` varchar(50) NOT NULL,
  `supcat_sku_no` varchar(50) NOT NULL,
  `supcat_uom` int(5) NOT NULL,
  `supcat_price` double(10,2) NOT NULL,
  `supcat_lastdate` date NOT NULL,
  `supcat_details` text DEFAULT NULL,
  `supcat_createdate` datetime NOT NULL,
  `supcat_createby` bigint(10) NOT NULL,
  `supcat_modifydate` datetime NOT NULL,
  `supcat_modifyby` bigint(10) NOT NULL,
  `supcat_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_master`
--

CREATE TABLE `supplier_master` (
  `sup_id` bigint(11) NOT NULL,
  `sup_name` varchar(250) NOT NULL,
  `sup_address` text NOT NULL,
  `sup_contact_person` varchar(250) NOT NULL,
  `sup_phone` varchar(20) NOT NULL,
  `sup_email` varchar(250) NOT NULL,
  `sup_details` varchar(250) DEFAULT NULL,
  `sup_createdate` datetime NOT NULL,
  `sup_createby` bigint(10) NOT NULL,
  `sup_modifydate` datetime DEFAULT NULL,
  `sup_modifyby` bigint(10) DEFAULT NULL,
  `sup_status` tinyint(2) NOT NULL DEFAULT 1 COMMENT '0 = Inactive, 1 = Active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `taxcode_master`
--

CREATE TABLE `taxcode_master` (
  `tc_id` int(5) NOT NULL,
  `tc_tax_value` float(5,2) NOT NULL,
  `tc_createdate` datetime NOT NULL,
  `tc_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `taxgroup_master`
--

CREATE TABLE `taxgroup_master` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `percentage` int(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `template_master`
--

CREATE TABLE `template_master` (
  `id` int(11) NOT NULL,
  `email_name` varchar(255) DEFAULT NULL,
  `email_body` text NOT NULL,
  `email_key` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `unit_of_measure_tab`
--

CREATE TABLE `unit_of_measure_tab` (
  `uom_id` int(5) NOT NULL,
  `uom_name` varchar(200) NOT NULL,
  `uom_detail` varchar(250) DEFAULT NULL,
  `uom_createdate` datetime NOT NULL,
  `uom_createby` bigint(10) NOT NULL,
  `uom_modifydate` datetime DEFAULT NULL,
  `uom_status` tinyint(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `id` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `country` varchar(200) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `pincode` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`id`, `uid`, `address`, `country`, `state`, `city`, `pincode`) VALUES
(1, 1, '', NULL, NULL, NULL, NULL),
(2, 2, '', NULL, NULL, NULL, NULL),
(4, 4, '', NULL, NULL, NULL, NULL),
(5, 5, 'sss', NULL, NULL, NULL, NULL),
(6, 6, 'dfdsf', NULL, NULL, NULL, NULL),
(7, 7, 'sdfds', NULL, NULL, NULL, NULL),
(8, 8, '123456 191 street', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `u_id` bigint(20) NOT NULL,
  `u_type` int(11) NOT NULL,
  `u_access` int(11) NOT NULL DEFAULT 1,
  `username` varchar(200) NOT NULL,
  `password` varchar(250) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `u_masteruser` int(8) DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `modify_date` datetime DEFAULT NULL,
  `access_ip` varchar(200) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`u_id`, `u_type`, `u_access`, `username`, `password`, `phone`, `email`, `firstname`, `lastname`, `u_masteruser`, `create_date`, `modify_date`, `access_ip`, `status`) VALUES
(1, 1, 1, 'admin', '8d1af614908958e76b9c89d78d2f296fd2ca9ef3adcb95392610df20ea106b24e48551ef2964b1c58fdb262932476511939d208364b073c13966f0e164328df0', '9830260404', 'amit@albatrossoft.com', 'Super', 'Admin', NULL, '2015-04-22 07:39:18', '2022-05-18 20:59:45', '::1', '1'),
(2, 2, 1, 'admin_2', '8d1af614908958e76b9c89d78d2f296fd2ca9ef3adcb95392610df20ea106b24e48551ef2964b1c58fdb262932476511939d208364b073c13966f0e164328df0', '9830260405', 'amit222@albatrossoft.com', 'Sub', 'Admin', NULL, '2015-04-22 07:39:18', '2022-04-16 08:42:57', '127.0.0.1', '1'),
(4, 2, 1, 'ddd', '2467c27760e1d8f4af75a1933d5f518daa4ad07df13595cd37d3c267fedde7e2b08db5a6da811c11ac3e21cb85dc8c946d3e516b013631312d668a6d4d631b17', '25487845120', 'dasdasd@fdf.gg', 'Adasd', 'ASDASD', NULL, '2022-04-23 10:42:20', '2022-04-23 10:42:20', '127.0.0.1', '1'),
(5, 2, 1, 'sff', '6b65cb040835879e1a4bb41c42abbd791235392efc4b8247e2b37034e8df4905177683569ab017705c6e83ca5b64fb55db0227ab98c47c8d88723d76c383f6bb', '4432124555', 'sdfgfgfd.@fdf.gg', 'RRRR', 'ass', NULL, '2022-04-23 10:42:55', '2022-04-23 10:42:55', '127.0.0.1', '1'),
(6, 2, 1, 'ggg', 'f288a8d9cbe997fc6dea75b724c0cfe9f4c5f341bd3c8bfe99dae2cb937621beba43a7783fe796804dcfec08d8ffdadae807e131e74cb32273b6b08edc2488fc', '34236456456', 'ghyjghj@fdfd.cc', 'TTTT', 'sd sd fdsfdf fdff', NULL, '2022-04-23 10:43:27', '2022-04-23 10:43:27', '127.0.0.1', '1'),
(7, 2, 1, 'vvv', '2467c27760e1d8f4af75a1933d5f518daa4ad07df13595cd37d3c267fedde7e2b08db5a6da811c11ac3e21cb85dc8c946d3e516b013631312d668a6d4d631b17', '342343244456', 'sadsadas@fssaa.cb', '111sdjfgsd sdjhds', 'sdjfhdsf sdhf', NULL, '2022-04-23 10:44:04', '2022-04-23 10:44:04', '127.0.0.1', '1'),
(8, 2, 1, 'User1', 'ecd26c902d06672c7b5246bbf04a604e20db34184254da64cef047a63f7e6bc215d95bd86ccd4fbdbb873c0d46561f068278790dac004b7fbccdd6eda1dfc160', '7789899999', 'test@essenceliving.com', 'User 1', 'UL', NULL, '2022-05-13 21:19:06', '2022-05-13 21:19:06', '::1', '1');

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_views`
-- (See below for the actual view)
--
CREATE TABLE `user_views` (
`u_id` bigint(20)
,`u_type` int(11)
,`u_access` int(11)
,`username` varchar(200)
,`password` varchar(250)
,`phone` varchar(255)
,`email` varchar(255)
,`firstname` varchar(200)
,`lastname` varchar(200)
,`u_masteruser` int(8)
,`create_date` datetime
,`modify_date` datetime
,`access_ip` varchar(200)
,`status` enum('0','1')
,`id` bigint(20)
,`uid` bigint(20)
,`address` varchar(255)
,`country` varchar(200)
,`state` varchar(200)
,`city` varchar(200)
,`pincode` varchar(100)
,`mu_name` varchar(150)
);

-- --------------------------------------------------------

--
-- Structure for view `user_views`
--
DROP TABLE IF EXISTS `user_views`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_views`  AS SELECT `user_info`.`u_id` AS `u_id`, `user_info`.`u_type` AS `u_type`, `user_info`.`u_access` AS `u_access`, `user_info`.`username` AS `username`, `user_info`.`password` AS `password`, `user_info`.`phone` AS `phone`, `user_info`.`email` AS `email`, `user_info`.`firstname` AS `firstname`, `user_info`.`lastname` AS `lastname`, `user_info`.`u_masteruser` AS `u_masteruser`, `user_info`.`create_date` AS `create_date`, `user_info`.`modify_date` AS `modify_date`, `user_info`.`access_ip` AS `access_ip`, `user_info`.`status` AS `status`, `user_details`.`id` AS `id`, `user_details`.`uid` AS `uid`, `user_details`.`address` AS `address`, `user_details`.`country` AS `country`, `user_details`.`state` AS `state`, `user_details`.`city` AS `city`, `user_details`.`pincode` AS `pincode`, `master_user_type`.`mu_name` AS `mu_name` FROM ((`user_info` join `user_details` on(`user_info`.`u_id` = `user_details`.`uid`)) join `master_user_type` on(`user_info`.`u_type` = `master_user_type`.`mu_id`))  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company_setting_master`
--
ALTER TABLE `company_setting_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_tab`
--
ALTER TABLE `company_tab`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `cost_code_master`
--
ALTER TABLE `cost_code_master`
  ADD PRIMARY KEY (`cc_id`),
  ADD UNIQUE KEY `cc_no` (`cc_no`);

--
-- Indexes for table `item_category_tab`
--
ALTER TABLE `item_category_tab`
  ADD PRIMARY KEY (`icat_id`),
  ADD UNIQUE KEY `icat_name` (`icat_name`);

--
-- Indexes for table `item_master`
--
ALTER TABLE `item_master`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `item_code` (`item_code`);

--
-- Indexes for table `item_package_details`
--
ALTER TABLE `item_package_details`
  ADD PRIMARY KEY (`ipdetail_id`);

--
-- Indexes for table `item_package_master`
--
ALTER TABLE `item_package_master`
  ADD PRIMARY KEY (`ipack_id`);

--
-- Indexes for table `master_user_type`
--
ALTER TABLE `master_user_type`
  ADD PRIMARY KEY (`mu_id`),
  ADD UNIQUE KEY `mu_name` (`mu_name`);

--
-- Indexes for table `project_details`
--
ALTER TABLE `project_details`
  ADD PRIMARY KEY (`pdetail_id`);

--
-- Indexes for table `project_master`
--
ALTER TABLE `project_master`
  ADD PRIMARY KEY (`proj_id`);

--
-- Indexes for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD PRIMARY KEY (`po_detail_id`);

--
-- Indexes for table `purchase_order_master`
--
ALTER TABLE `purchase_order_master`
  ADD PRIMARY KEY (`porder_id`),
  ADD UNIQUE KEY `porder_no` (`porder_no`);

--
-- Indexes for table `receive_order_details`
--
ALTER TABLE `receive_order_details`
  ADD PRIMARY KEY (`ro_detail_id`);

--
-- Indexes for table `receive_order_master`
--
ALTER TABLE `receive_order_master`
  ADD PRIMARY KEY (`rorder_id`);

--
-- Indexes for table `supplier_catalog_tab`
--
ALTER TABLE `supplier_catalog_tab`
  ADD PRIMARY KEY (`supcat_id`);

--
-- Indexes for table `supplier_master`
--
ALTER TABLE `supplier_master`
  ADD PRIMARY KEY (`sup_id`);

--
-- Indexes for table `taxgroup_master`
--
ALTER TABLE `taxgroup_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template_master`
--
ALTER TABLE `template_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unit_of_measure_tab`
--
ALTER TABLE `unit_of_measure_tab`
  ADD PRIMARY KEY (`uom_id`),
  ADD UNIQUE KEY `uom_name` (`uom_name`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`u_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company_setting_master`
--
ALTER TABLE `company_setting_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_tab`
--
ALTER TABLE `company_tab`
  MODIFY `company_id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost_code_master`
--
ALTER TABLE `cost_code_master`
  MODIFY `cc_id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_category_tab`
--
ALTER TABLE `item_category_tab`
  MODIFY `icat_id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_master`
--
ALTER TABLE `item_master`
  MODIFY `item_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_package_details`
--
ALTER TABLE `item_package_details`
  MODIFY `ipdetail_id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_package_master`
--
ALTER TABLE `item_package_master`
  MODIFY `ipack_id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_user_type`
--
ALTER TABLE `master_user_type`
  MODIFY `mu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_details`
--
ALTER TABLE `project_details`
  MODIFY `pdetail_id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_master`
--
ALTER TABLE `project_master`
  MODIFY `proj_id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  MODIFY `po_detail_id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_master`
--
ALTER TABLE `purchase_order_master`
  MODIFY `porder_id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receive_order_details`
--
ALTER TABLE `receive_order_details`
  MODIFY `ro_detail_id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receive_order_master`
--
ALTER TABLE `receive_order_master`
  MODIFY `rorder_id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_catalog_tab`
--
ALTER TABLE `supplier_catalog_tab`
  MODIFY `supcat_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_master`
--
ALTER TABLE `supplier_master`
  MODIFY `sup_id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxgroup_master`
--
ALTER TABLE `taxgroup_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `template_master`
--
ALTER TABLE `template_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unit_of_measure_tab`
--
ALTER TABLE `unit_of_measure_tab`
  MODIFY `uom_id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `u_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `user_info` (`u_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
