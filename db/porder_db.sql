CREATE TABLE `company_tab` (
  `company_id` int(5) NOT NULL,
  `company_name` varchar(300) NOT NULL,
  `company_address` text NOT NULL,
  `company_logo` varchar(250) DEFAULT NULL,
  `company_createdate` datetime NOT NULL,
  `company_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `company_tab`
--

INSERT INTO `company_tab` (`company_id`, `company_name`, `company_address`, `company_logo`, `company_createdate`, `company_status`) VALUES
(1, '213123', 'ffff', '0931451200x600wa.png', '2022-04-30 09:31:45', 1);

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
  `cc_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cost_code_master`
--

INSERT INTO `cost_code_master` (`cc_id`, `cc_no`, `cc_description`, `cc_details`, `cc_createdate`, `cc_createby`, `cc_modifydate`, `cc_modifyby`, `cc_status`) VALUES
(1, 'x2444', 'sada', NULL, '2022-04-17 20:07:58', 1, '2022-04-17 20:29:44', 1, 1),
(2, 'x24', 'dfsdf', NULL, '2022-04-17 20:08:14', 1, NULL, NULL, 1),
(3, 'ADx24', 'dfsdf', NULL, '2022-04-17 20:08:14', 1, NULL, NULL, 1),
(4, 'CC1', '', NULL, '2022-05-01 15:00:16', 1, NULL, NULL, 1),
(5, 'CC2', 'adssd', NULL, '2022-05-01 15:00:16', 1, NULL, NULL, 1),
(6, 'CC3', 'asdddd', NULL, '2022-05-01 15:00:16', 1, NULL, NULL, 1),
(7, 'CC4', '', NULL, '2022-05-01 15:00:16', 1, NULL, NULL, 1),
(8, 'CC5', 'asd', NULL, '2022-05-01 15:00:16', 1, NULL, NULL, 1);

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
  `icat_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_category_tab`
--

INSERT INTO `item_category_tab` (`icat_id`, `icat_name`, `icat_details`, `icat_parent`, `icat_createdate`, `icat_createby`, `icat_modifydate`, `icat_modifyby`, `icat_status`) VALUES
(1, 'FADS', NULL, NULL, '2022-04-17 20:52:40', 1, NULL, NULL, 1),
(2, 'FAD', NULL, NULL, '2022-04-17 20:53:42', 1, '2022-04-17 21:01:41', 1, 0),
(3, 'ARd', NULL, NULL, '2022-04-17 20:54:06', 1, NULL, NULL, 1),
(4, 'Cat-A', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1),
(5, 'Cat-B', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1),
(6, 'Cat-C', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1),
(7, 'Cat-D', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1),
(8, 'Cat-E', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1),
(9, 'Cat-F', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `item_master`
--

CREATE TABLE `item_master` (
  `item_id` bigint(20) NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `item_name` varchar(250) NOT NULL,
  `item_description` text,
  `item_ccode_ms` bigint(10) NOT NULL,
  `item_cat_ms` int(5) NOT NULL,
  `item_unit_ms` int(5) NOT NULL,
  `item_createdate` datetime NOT NULL,
  `item_createby` bigint(10) NOT NULL,
  `item_modifydate` datetime DEFAULT NULL,
  `item_modifyby` bigint(10) DEFAULT NULL,
  `item_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_master`
--

INSERT INTO `item_master` (`item_id`, `item_code`, `item_name`, `item_description`, `item_ccode_ms`, `item_cat_ms`, `item_unit_ms`, `item_createdate`, `item_createby`, `item_modifydate`, `item_modifyby`, `item_status`) VALUES
(1, 'U001', 'DAS', 'sada sda sdddd', 3, 3, 1, '2022-04-20 18:31:53', 1, '2022-04-20 19:46:31', 1, 1),
(2, 'U0011', 'SA SD dd', 'asd ddddd', 1, 1, 2, '2022-04-20 18:32:21', 1, '2022-04-20 19:46:15', 1, 1),
(3, 'A998', 'ADASD', 'sadasd sadasdsd', 3, 3, 2, '2022-04-24 18:37:22', 1, '2022-04-26 08:39:20', 1, 1),
(4, 'B3242', 'SADASD', 'sadd', 1, 1, 1, '2022-04-24 18:37:43', 1, '2022-04-26 08:39:30', 1, 1),
(5, 'ITM003', 'sdf sdfffd f', 'fddf', 2, 3, 3, '2022-04-26 08:39:06', 1, NULL, NULL, 1);

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
  `ipdetail_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_package_details`
--

INSERT INTO `item_package_details` (`ipdetail_id`, `ipdetail_autogen`, `ipdetail_ipack_ms`, `ipdetail_item_ms`, `ipdetail_quantity`, `ipdetail_info`, `ipdetail_createdate`, `ipdetail_status`) VALUES
(14, '240422175057', 1, 'U0011', 400, NULL, '2022-04-24 18:44:12', 1),
(15, '240422175057', 1, 'U001', 500, NULL, '2022-04-24 18:45:16', 1);

-- --------------------------------------------------------

--
-- Table structure for table `item_package_master`
--

CREATE TABLE `item_package_master` (
  `ipack_id` bigint(10) NOT NULL,
  `ipack_name` varchar(250) NOT NULL,
  `ipack_details` varchar(300) DEFAULT NULL,
  `ipack_totalitem` int(5) NOT NULL DEFAULT '0',
  `ipack_total_qty` bigint(8) NOT NULL DEFAULT '0',
  `ipack_createdate` datetime NOT NULL,
  `ipack_createby` bigint(10) NOT NULL,
  `ipack_modifydate` datetime DEFAULT NULL,
  `ipack_modifyby` bigint(10) DEFAULT NULL,
  `ipack_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_package_master`
--

INSERT INTO `item_package_master` (`ipack_id`, `ipack_name`, `ipack_details`, `ipack_totalitem`, `ipack_total_qty`, `ipack_createdate`, `ipack_createby`, `ipack_modifydate`, `ipack_modifyby`, `ipack_status`) VALUES
(1, 'AEW11', 'ZXZX3', 2, 900, '2022-04-24 17:51:14', 1, '2022-04-24 18:45:24', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `master_user_type`
--

CREATE TABLE `master_user_type` (
  `mu_id` int(11) NOT NULL,
  `mu_name` varchar(150) NOT NULL,
  `mu_createdate` datetime NOT NULL,
  `mu_createby` int(11) NOT NULL,
  `mu_status` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_user_type`
--

INSERT INTO `master_user_type` (`mu_id`, `mu_name`, `mu_createdate`, `mu_createby`, `mu_status`) VALUES
(1, 'Super-Admin', '2020-06-22 00:00:00', 1, 1),
(2, 'Employee', '2020-06-22 00:00:00', 1, 1);

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
  `pdetail_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `project_details`
--

INSERT INTO `project_details` (`pdetail_id`, `pdetail_proj_ms`, `pdetail_user`, `pdetail_info`, `pdetail_createdate`, `pdetail_status`) VALUES
(3, 1, 4, NULL, '2022-04-23 15:06:06', 1),
(4, 1, 5, NULL, '2022-04-23 15:06:06', 1),
(5, 1, 6, NULL, '2022-04-23 15:06:06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `project_master`
--

CREATE TABLE `project_master` (
  `proj_id` bigint(10) NOT NULL,
  `proj_number` varchar(50) NOT NULL,
  `proj_name` varchar(250) NOT NULL,
  `proj_address` text NOT NULL,
  `proj_description` text,
  `proj_contact` int(5) NOT NULL DEFAULT '0',
  `proj_createdate` datetime NOT NULL,
  `proj_createby` bigint(10) NOT NULL,
  `proj_modifydate` datetime DEFAULT NULL,
  `proj_modifyby` bigint(10) DEFAULT NULL,
  `proj_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `project_master`
--

INSERT INTO `project_master` (`proj_id`, `proj_number`, `proj_name`, `proj_address`, `proj_description`, `proj_contact`, `proj_createdate`, `proj_createby`, `proj_modifydate`, `proj_modifyby`, `proj_status`) VALUES
(1, 'P001', 'Project namesets 123', '22sajdkha as kjdhas dsah adaksas , asdasdashdkashd', '11a sda fdgfdg dgdfgfg', 3, '2022-04-23 14:24:19', 1, '2022-04-23 15:06:06', 1, 1);

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
  `po_detail_status` tinyint(2) NOT NULL DEFAULT '1',
  `po_detail_tax_group` varchar(50)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Table structure for table `taxgroup_master`
--

CREATE TABLE `taxgroup_master` (
  `id` int(11) AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `percentage` int(10) NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Dumping data for table `purchase_order_details`
--

INSERT INTO `purchase_order_details` (`po_detail_id`, `po_detail_autogen`, `po_detail_porder_ms`, `po_detail_item`, `po_detail_sku`, `po_detail_taxcode`, `po_detail_quantity`, `po_detail_unitprice`, `po_detail_subtotal`, `po_detail_taxamount`, `po_detail_total`, `po_detail_createdate`, `po_detail_status`) VALUES
(13, '300422061745', 2, 'U001', 'S3423', '10.00', 100, 3.44, 344.00, 34.40, 378.40, '2022-04-30 06:18:15', 1);

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
  `porder_delivery_note` text,
  `porder_description` text,
  `porder_total_item` int(5) NOT NULL,
  `porder_total_amount` decimal(10,2) NOT NULL,
  `porder_delivery_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1 = Full, 2 = Partial',
  `porder_createdate` datetime NOT NULL,
  `porder_createby` bigint(10) NOT NULL,
  `porder_modifydate` datetime DEFAULT NULL,
  `porder_modifyby` bigint(10) DEFAULT NULL,
  `porder_status` tinyint(2) NOT NULL DEFAULT '1',
  `porder_total_tax` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `purchase_order_master`
--

INSERT INTO `purchase_order_master` (`porder_id`, `porder_project_ms`, `porder_no`, `porder_supplier_ms`, `porder_address`, `porder_delivery_note`, `porder_total_item`, `porder_total_amount`, `porder_delivery_status`, `porder_createdate`, `porder_createby`, `porder_modifydate`, `porder_modifyby`, `porder_status`) VALUES
(2, 1, 'P00111', 2, '22sajdkha as kjdhas dsah adaksas , asdasdashdkashdM', 'xxxxx xxxx xxxxx 112233', 1, '378.40', 0, '2022-04-30 06:18:18', 1, '2022-04-30 06:38:00', 1, 1);

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
  `ro_detail_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `receive_order_details`
--

INSERT INTO `receive_order_details` (`ro_detail_id`, `ro_detail_rorder_ms`, `ro_detail_item`, `ro_detail_quantity`, `ro_detail_createdate`, `ro_detail_status`) VALUES
(2, 3, 'U001', 20, '2022-05-01 07:24:25', 1),
(3, 4, 'U001', 80, '2022-05-01 07:27:05', 1);

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
  `rorder_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `receive_order_master`
--

INSERT INTO `receive_order_master` (`rorder_id`, `rorder_porder_ms`, `rorder_slip_no`, `rorder_infoset`, `rorder_date`, `rorder_totalitem`, `rorder_totalamount`, `rorder_createdate`, `rorder_createby`, `rorder_modifydate`, `rorder_modifyby`, `rorder_status`) VALUES
(3, 2, 'FDSF', NULL, '2022-02-05', 1, NULL, '2022-05-01 07:24:25', 1, NULL, NULL, 1),
(4, 2, 'SDFSDF', NULL, '2022-02-05', 1, NULL, '2022-05-01 07:27:05', 1, NULL, NULL, 1);

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
  `supcat_details` text,
  `supcat_createdate` datetime NOT NULL,
  `supcat_createby` bigint(10) NOT NULL,
  `supcat_modifydate` datetime NOT NULL,
  `supcat_modifyby` bigint(10) NOT NULL,
  `supcat_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `supplier_catalog_tab`
--

INSERT INTO `supplier_catalog_tab` (`supcat_id`, `supcat_supplier`, `supcat_item_code`, `supcat_sku_no`, `supcat_uom`, `supcat_price`, `supcat_lastdate`, `supcat_details`, `supcat_createdate`, `supcat_createby`, `supcat_modifydate`, `supcat_modifyby`, `supcat_status`) VALUES
(1, 2, 'U001', 'S3423', 1, 3.44, '2022-04-02', NULL, '2022-04-21 19:26:09', 1, '0000-00-00 00:00:00', 0, 1),
(2, 2, 'U001', 'S341111', 2, 4.22, '2022-04-15', NULL, '2022-04-21 19:48:31', 1, '2022-04-23 07:13:50', 1, 1),
(3, 1, 'A998', 'SADFFF', 1, 33.00, '2022-05-10', NULL, '2022-05-01 15:14:43', 1, '0000-00-00 00:00:00', 0, 1),
(4, 2, 'ITM003', 'SA', 3, 232.00, '2022-05-30', NULL, '2022-05-01 15:17:40', 1, '0000-00-00 00:00:00', 0, 1),
(5, 2, 'ITM003', 'PPP', 1, 666.00, '2022-05-27', NULL, '2022-05-01 15:19:29', 1, '0000-00-00 00:00:00', 0, 1),
(6, 2, 'B3242', 'TT', 2, 33.00, '2022-05-11', NULL, '2022-05-01 15:29:24', 1, '0000-00-00 00:00:00', 0, 1);

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
  `sup_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0 = Inactive, 1 = Active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `supplier_master`
--

INSERT INTO `supplier_master` (`sup_id`, `sup_name`, `sup_address`, `sup_contact_person`, `sup_phone`, `sup_email`, `sup_details`, `sup_createdate`, `sup_createby`, `sup_modifydate`, `sup_modifyby`, `sup_status`) VALUES
(1, 'SAD12', 'sadsdsd', 'ddddasas', '9830260404', 'amasd@fdsf.com', NULL, '2022-04-17 21:58:49', 1, '2022-04-17 22:22:30', 1, 1),
(2, 'SAD1', 'gv dg hghgfh 454', 'yyyyzzz', '8745120215222', 'asddkdd@dsfsf.ccc', NULL, '2022-04-17 21:59:44', 1, '2022-04-17 22:22:10', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `taxcode_master`
--

CREATE TABLE `taxcode_master` (
  `tc_id` int(5) NOT NULL,
  `tc_tax_value` float(5,2) NOT NULL,
  `tc_createdate` datetime NOT NULL,
  `tc_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `taxcode_master`
--

INSERT INTO `taxcode_master` (`tc_id`, `tc_tax_value`, `tc_createdate`, `tc_status`) VALUES
(1, 10.00, '2022-04-26 00:00:00', 1);

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
  `uom_status` tinyint(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `unit_of_measure_tab`
--

INSERT INTO `unit_of_measure_tab` (`uom_id`, `uom_name`, `uom_detail`, `uom_createdate`, `uom_createby`, `uom_modifydate`, `uom_status`) VALUES
(1, 'REWRW', NULL, '2022-04-17 18:16:35', 1, '2022-04-17 18:56:09', 1),
(2, 'REWRW23', NULL, '2022-04-17 18:16:50', 1, '2022-04-17 18:55:32', 1),
(3, 'REWRW2342', NULL, '2022-04-17 18:18:06', 1, NULL, 1);

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
(7, 7, 'sdfds', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `u_id` bigint(20) NOT NULL,
  `u_type` int(11) NOT NULL,
  `u_access` int(11) NOT NULL DEFAULT '1',
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
(1, 1, 1, 'admin', '8d1af614908958e76b9c89d78d2f296fd2ca9ef3adcb95392610df20ea106b24e48551ef2964b1c58fdb262932476511939d208364b073c13966f0e164328df0', '9830260404', 'amit@albatrossoft.com', 'Super', 'Admin', NULL, '2015-04-22 07:39:18', '2022-05-01 19:30:29', '127.0.0.1', '1'),
(2, 2, 1, 'admin_2', '8d1af614908958e76b9c89d78d2f296fd2ca9ef3adcb95392610df20ea106b24e48551ef2964b1c58fdb262932476511939d208364b073c13966f0e164328df0', '9830260405', 'amit222@albatrossoft.com', 'Sub', 'Admin', NULL, '2015-04-22 07:39:18', '2022-04-16 08:42:57', '127.0.0.1', '1'),
(4, 2, 1, 'ddd', '2467c27760e1d8f4af75a1933d5f518daa4ad07df13595cd37d3c267fedde7e2b08db5a6da811c11ac3e21cb85dc8c946d3e516b013631312d668a6d4d631b17', '25487845120', 'dasdasd@fdf.gg', 'Adasd', 'ASDASD', NULL, '2022-04-23 10:42:20', '2022-04-23 10:42:20', '127.0.0.1', '1'),
(5, 2, 1, 'sff', '6b65cb040835879e1a4bb41c42abbd791235392efc4b8247e2b37034e8df4905177683569ab017705c6e83ca5b64fb55db0227ab98c47c8d88723d76c383f6bb', '4432124555', 'sdfgfgfd.@fdf.gg', 'RRRR', 'ass', NULL, '2022-04-23 10:42:55', '2022-04-23 10:42:55', '127.0.0.1', '1'),
(6, 2, 1, 'ggg', 'f288a8d9cbe997fc6dea75b724c0cfe9f4c5f341bd3c8bfe99dae2cb937621beba43a7783fe796804dcfec08d8ffdadae807e131e74cb32273b6b08edc2488fc', '34236456456', 'ghyjghj@fdfd.cc', 'TTTT', 'sd sd fdsfdf fdff', NULL, '2022-04-23 10:43:27', '2022-04-23 10:43:27', '127.0.0.1', '1'),
(7, 2, 1, 'vvv', '2467c27760e1d8f4af75a1933d5f518daa4ad07df13595cd37d3c267fedde7e2b08db5a6da811c11ac3e21cb85dc8c946d3e516b013631312d668a6d4d631b17', '342343244456', 'sadsadas@fssaa.cb', '111sdjfgsd sdjhds', 'sdjfhdsf sdhf', NULL, '2022-04-23 10:44:04', '2022-04-23 10:44:04', '127.0.0.1', '1');

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

CREATE VIEW `user_views`  AS  select `user_info`.`u_id` AS `u_id`,`user_info`.`u_type` AS `u_type`,`user_info`.`u_access` AS `u_access`,`user_info`.`username` AS `username`,`user_info`.`password` AS `password`,`user_info`.`phone` AS `phone`,`user_info`.`email` AS `email`,`user_info`.`firstname` AS `firstname`,`user_info`.`lastname` AS `lastname`,`user_info`.`u_masteruser` AS `u_masteruser`,`user_info`.`create_date` AS `create_date`,`user_info`.`modify_date` AS `modify_date`,`user_info`.`access_ip` AS `access_ip`,`user_info`.`status` AS `status`,`user_details`.`id` AS `id`,`user_details`.`uid` AS `uid`,`user_details`.`address` AS `address`,`user_details`.`country` AS `country`,`user_details`.`state` AS `state`,`user_details`.`city` AS `city`,`user_details`.`pincode` AS `pincode`,`master_user_type`.`mu_name` AS `mu_name` from ((`user_info` join `user_details` on((`user_info`.`u_id` = `user_details`.`uid`))) join `master_user_type` on((`user_info`.`u_type` = `master_user_type`.`mu_id`))) ;

--
-- Indexes for dumped tables
--

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
-- Indexes for table `taxcode_master`
--
ALTER TABLE `taxcode_master`
  ADD PRIMARY KEY (`tc_id`);

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
-- AUTO_INCREMENT for table `company_tab`
--
ALTER TABLE `company_tab`
  MODIFY `company_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `cost_code_master`
--
ALTER TABLE `cost_code_master`
  MODIFY `cc_id` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `item_category_tab`
--
ALTER TABLE `item_category_tab`
  MODIFY `icat_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `item_master`
--
ALTER TABLE `item_master`
  MODIFY `item_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `item_package_details`
--
ALTER TABLE `item_package_details`
  MODIFY `ipdetail_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `item_package_master`
--
ALTER TABLE `item_package_master`
  MODIFY `ipack_id` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `master_user_type`
--
ALTER TABLE `master_user_type`
  MODIFY `mu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `project_details`
--
ALTER TABLE `project_details`
  MODIFY `pdetail_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `project_master`
--
ALTER TABLE `project_master`
  MODIFY `proj_id` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  MODIFY `po_detail_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `purchase_order_master`
--
ALTER TABLE `purchase_order_master`
  MODIFY `porder_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `receive_order_details`
--
ALTER TABLE `receive_order_details`
  MODIFY `ro_detail_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `receive_order_master`
--
ALTER TABLE `receive_order_master`
  MODIFY `rorder_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `supplier_catalog_tab`
--
ALTER TABLE `supplier_catalog_tab`
  MODIFY `supcat_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `supplier_master`
--
ALTER TABLE `supplier_master`
  MODIFY `sup_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `taxcode_master`
--
ALTER TABLE `taxcode_master`
  MODIFY `tc_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `unit_of_measure_tab`
--
ALTER TABLE `unit_of_measure_tab`
  MODIFY `uom_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `u_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details`
  ADD CONSTRAINT `user_details_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `user_info` (`u_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

