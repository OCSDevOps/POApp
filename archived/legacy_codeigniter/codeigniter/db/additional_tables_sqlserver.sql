-- Additional Tables for POApp - SQL Server Version
-- Converted from MySQL syntax

-- =============================================
-- Table: company_tab
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[company_tab]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[company_tab] (
    [company_id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [company_name] NVARCHAR(300) NOT NULL,
    [company_address] NVARCHAR(MAX) NOT NULL,
    [company_logo] NVARCHAR(250) NULL,
    [company_createdate] DATETIME NOT NULL,
    [company_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for company_tab
SET IDENTITY_INSERT [dbo].[company_tab] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[company_tab] WHERE [company_id] = 1)
INSERT INTO [dbo].[company_tab] ([company_id], [company_name], [company_address], [company_logo], [company_createdate], [company_status]) VALUES
(1, '213123', 'ffff', '0931451200x600wa.png', '2022-04-30 09:31:45', 1);
SET IDENTITY_INSERT [dbo].[company_tab] OFF;
GO

-- =============================================
-- Table: cost_code_master
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[cost_code_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[cost_code_master] (
    [cc_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [cc_no] NVARCHAR(100) NOT NULL UNIQUE,
    [cc_description] NVARCHAR(300) NOT NULL,
    [cc_details] NVARCHAR(250) NULL,
    [cc_createdate] DATETIME NOT NULL,
    [cc_createby] BIGINT NOT NULL,
    [cc_modifydate] DATETIME NULL,
    [cc_modifyby] BIGINT NULL,
    [cc_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for cost_code_master
SET IDENTITY_INSERT [dbo].[cost_code_master] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[cost_code_master] WHERE [cc_id] = 1)
BEGIN
INSERT INTO [dbo].[cost_code_master] ([cc_id], [cc_no], [cc_description], [cc_details], [cc_createdate], [cc_createby], [cc_modifydate], [cc_modifyby], [cc_status]) VALUES
(1, 'x2444', 'sada', NULL, '2022-04-17 20:07:58', 1, '2022-04-17 20:29:44', 1, 1),
(2, 'x24', 'dfsdf', NULL, '2022-04-17 20:08:14', 1, NULL, NULL, 1),
(3, 'ADx24', 'dfsdf', NULL, '2022-04-17 20:08:14', 1, NULL, NULL, 1),
(4, 'CC1', '', NULL, '2022-05-01 15:00:16', 1, NULL, NULL, 1),
(5, 'CC2', 'adssd', NULL, '2022-05-01 15:00:16', 1, NULL, NULL, 1),
(6, 'CC3', 'asdddd', NULL, '2022-05-01 15:00:16', 1, NULL, NULL, 1),
(7, 'CC4', '', NULL, '2022-05-01 15:00:16', 1, NULL, NULL, 1),
(8, 'CC5', 'asd', NULL, '2022-05-01 15:00:16', 1, NULL, NULL, 1);
END
SET IDENTITY_INSERT [dbo].[cost_code_master] OFF;
GO

-- =============================================
-- Table: item_category_tab
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[item_category_tab]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[item_category_tab] (
    [icat_id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [icat_name] NVARCHAR(200) NOT NULL UNIQUE,
    [icat_details] NVARCHAR(250) NULL,
    [icat_parent] INT NULL,
    [icat_createdate] DATETIME NOT NULL,
    [icat_createby] BIGINT NOT NULL,
    [icat_modifydate] DATETIME NULL,
    [icat_modifyby] BIGINT NULL,
    [icat_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for item_category_tab
SET IDENTITY_INSERT [dbo].[item_category_tab] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[item_category_tab] WHERE [icat_id] = 1)
BEGIN
INSERT INTO [dbo].[item_category_tab] ([icat_id], [icat_name], [icat_details], [icat_parent], [icat_createdate], [icat_createby], [icat_modifydate], [icat_modifyby], [icat_status]) VALUES
(1, 'FADS', NULL, NULL, '2022-04-17 20:52:40', 1, NULL, NULL, 1),
(2, 'FAD', NULL, NULL, '2022-04-17 20:53:42', 1, '2022-04-17 21:01:41', 1, 0),
(3, 'ARd', NULL, NULL, '2022-04-17 20:54:06', 1, NULL, NULL, 1),
(4, 'Cat-A', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1),
(5, 'Cat-B', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1),
(6, 'Cat-C', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1),
(7, 'Cat-D', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1),
(8, 'Cat-E', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1),
(9, 'Cat-F', NULL, NULL, '2022-05-01 14:49:25', 1, NULL, NULL, 1);
END
SET IDENTITY_INSERT [dbo].[item_category_tab] OFF;
GO

-- =============================================
-- Table: item_master
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[item_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[item_master] (
    [item_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [item_code] NVARCHAR(50) NOT NULL UNIQUE,
    [item_name] NVARCHAR(250) NOT NULL,
    [item_description] NVARCHAR(MAX) NULL,
    [item_ccode_ms] BIGINT NOT NULL,
    [item_cat_ms] INT NOT NULL,
    [item_unit_ms] INT NOT NULL,
    [item_createdate] DATETIME NOT NULL,
    [item_createby] BIGINT NOT NULL,
    [item_modifydate] DATETIME NULL,
    [item_modifyby] BIGINT NULL,
    [item_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for item_master
SET IDENTITY_INSERT [dbo].[item_master] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[item_master] WHERE [item_id] = 1)
BEGIN
INSERT INTO [dbo].[item_master] ([item_id], [item_code], [item_name], [item_description], [item_ccode_ms], [item_cat_ms], [item_unit_ms], [item_createdate], [item_createby], [item_modifydate], [item_modifyby], [item_status]) VALUES
(1, 'U001', 'DAS', 'sada sda sdddd', 3, 3, 1, '2022-04-20 18:31:53', 1, '2022-04-20 19:46:31', 1, 1),
(2, 'U0011', 'SA SD dd', 'asd ddddd', 1, 1, 2, '2022-04-20 18:32:21', 1, '2022-04-20 19:46:15', 1, 1),
(3, 'A998', 'ADASD', 'sadasd sadasdsd', 3, 3, 2, '2022-04-24 18:37:22', 1, '2022-04-26 08:39:20', 1, 1),
(4, 'B3242', 'SADASD', 'sadd', 1, 1, 1, '2022-04-24 18:37:43', 1, '2022-04-26 08:39:30', 1, 1),
(5, 'ITM003', 'sdf sdfffd f', 'fddf', 2, 3, 3, '2022-04-26 08:39:06', 1, NULL, NULL, 1);
END
SET IDENTITY_INSERT [dbo].[item_master] OFF;
GO

-- =============================================
-- Table: item_package_master
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[item_package_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[item_package_master] (
    [ipack_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [ipack_name] NVARCHAR(250) NOT NULL,
    [ipack_details] NVARCHAR(300) NULL,
    [ipack_totalitem] INT NOT NULL DEFAULT 0,
    [ipack_total_qty] BIGINT NOT NULL DEFAULT 0,
    [ipack_createdate] DATETIME NOT NULL,
    [ipack_createby] BIGINT NOT NULL,
    [ipack_modifydate] DATETIME NULL,
    [ipack_modifyby] BIGINT NULL,
    [ipack_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for item_package_master
SET IDENTITY_INSERT [dbo].[item_package_master] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[item_package_master] WHERE [ipack_id] = 1)
BEGIN
INSERT INTO [dbo].[item_package_master] ([ipack_id], [ipack_name], [ipack_details], [ipack_totalitem], [ipack_total_qty], [ipack_createdate], [ipack_createby], [ipack_modifydate], [ipack_modifyby], [ipack_status]) VALUES
(1, 'AEW11', 'ZXZX3', 2, 900, '2022-04-24 17:51:14', 1, '2022-04-24 18:45:24', 1, 1);
END
SET IDENTITY_INSERT [dbo].[item_package_master] OFF;
GO

-- =============================================
-- Table: item_package_details
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[item_package_details]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[item_package_details] (
    [ipdetail_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [ipdetail_autogen] NVARCHAR(20) NOT NULL,
    [ipdetail_ipack_ms] BIGINT NULL,
    [ipdetail_item_ms] NVARCHAR(50) NOT NULL,
    [ipdetail_quantity] BIGINT NOT NULL,
    [ipdetail_info] NVARCHAR(250) NULL,
    [ipdetail_createdate] DATETIME NOT NULL,
    [ipdetail_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for item_package_details
SET IDENTITY_INSERT [dbo].[item_package_details] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[item_package_details] WHERE [ipdetail_id] = 14)
BEGIN
INSERT INTO [dbo].[item_package_details] ([ipdetail_id], [ipdetail_autogen], [ipdetail_ipack_ms], [ipdetail_item_ms], [ipdetail_quantity], [ipdetail_info], [ipdetail_createdate], [ipdetail_status]) VALUES
(14, '240422175057', 1, 'U0011', 400, NULL, '2022-04-24 18:44:12', 1),
(15, '240422175057', 1, 'U001', 500, NULL, '2022-04-24 18:45:16', 1);
END
SET IDENTITY_INSERT [dbo].[item_package_details] OFF;
GO

-- =============================================
-- Table: master_user_type
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[master_user_type]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[master_user_type] (
    [mu_id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [mu_name] NVARCHAR(150) NOT NULL UNIQUE,
    [mu_createdate] DATETIME NOT NULL,
    [mu_createby] INT NOT NULL,
    [mu_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for master_user_type
SET IDENTITY_INSERT [dbo].[master_user_type] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[master_user_type] WHERE [mu_id] = 1)
BEGIN
INSERT INTO [dbo].[master_user_type] ([mu_id], [mu_name], [mu_createdate], [mu_createby], [mu_status]) VALUES
(1, 'Super-Admin', '2020-06-22 00:00:00', 1, 1),
(2, 'Employee', '2020-06-22 00:00:00', 1, 1);
END
SET IDENTITY_INSERT [dbo].[master_user_type] OFF;
GO

-- =============================================
-- Table: project_master
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[project_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[project_master] (
    [proj_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [proj_number] NVARCHAR(50) NOT NULL,
    [proj_name] NVARCHAR(250) NOT NULL,
    [proj_address] NVARCHAR(MAX) NOT NULL,
    [proj_description] NVARCHAR(MAX) NULL,
    [proj_contact] INT NOT NULL DEFAULT 0,
    [proj_createdate] DATETIME NOT NULL,
    [proj_createby] BIGINT NOT NULL,
    [proj_modifydate] DATETIME NULL,
    [proj_modifyby] BIGINT NULL,
    [proj_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for project_master
SET IDENTITY_INSERT [dbo].[project_master] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[project_master] WHERE [proj_id] = 1)
BEGIN
INSERT INTO [dbo].[project_master] ([proj_id], [proj_number], [proj_name], [proj_address], [proj_description], [proj_contact], [proj_createdate], [proj_createby], [proj_modifydate], [proj_modifyby], [proj_status]) VALUES
(1, 'P001', 'Project namesets 123', '22sajdkha as kjdhas dsah adaksas , asdasdashdkashd', '11a sda fdgfdg dgdfgfg', 3, '2022-04-23 14:24:19', 1, '2022-04-23 15:06:06', 1, 1);
END
SET IDENTITY_INSERT [dbo].[project_master] OFF;
GO

-- =============================================
-- Table: project_details
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[project_details]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[project_details] (
    [pdetail_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [pdetail_proj_ms] BIGINT NOT NULL,
    [pdetail_user] BIGINT NOT NULL,
    [pdetail_info] NVARCHAR(250) NULL,
    [pdetail_createdate] DATETIME NOT NULL,
    [pdetail_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for project_details
SET IDENTITY_INSERT [dbo].[project_details] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[project_details] WHERE [pdetail_id] = 3)
BEGIN
INSERT INTO [dbo].[project_details] ([pdetail_id], [pdetail_proj_ms], [pdetail_user], [pdetail_info], [pdetail_createdate], [pdetail_status]) VALUES
(3, 1, 4, NULL, '2022-04-23 15:06:06', 1),
(4, 1, 5, NULL, '2022-04-23 15:06:06', 1),
(5, 1, 6, NULL, '2022-04-23 15:06:06', 1);
END
SET IDENTITY_INSERT [dbo].[project_details] OFF;
GO

-- =============================================
-- Table: supplier_master
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[supplier_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[supplier_master] (
    [sup_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [sup_name] NVARCHAR(250) NOT NULL,
    [sup_address] NVARCHAR(MAX) NOT NULL,
    [sup_contact_person] NVARCHAR(250) NOT NULL,
    [sup_phone] NVARCHAR(20) NOT NULL,
    [sup_email] NVARCHAR(250) NOT NULL,
    [sup_details] NVARCHAR(250) NULL,
    [sup_createdate] DATETIME NOT NULL,
    [sup_createby] BIGINT NOT NULL,
    [sup_modifydate] DATETIME NULL,
    [sup_modifyby] BIGINT NULL,
    [sup_status] TINYINT NOT NULL DEFAULT 1 -- 0 = Inactive, 1 = Active
);
END
GO

-- Insert data for supplier_master
SET IDENTITY_INSERT [dbo].[supplier_master] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[supplier_master] WHERE [sup_id] = 1)
BEGIN
INSERT INTO [dbo].[supplier_master] ([sup_id], [sup_name], [sup_address], [sup_contact_person], [sup_phone], [sup_email], [sup_details], [sup_createdate], [sup_createby], [sup_modifydate], [sup_modifyby], [sup_status]) VALUES
(1, 'SAD12', 'sadsdsd', 'ddddasas', '9830260404', 'amasd@fdsf.com', NULL, '2022-04-17 21:58:49', 1, '2022-04-17 22:22:30', 1, 1),
(2, 'SAD1', 'gv dg hghgfh 454', 'yyyyzzz', '8745120215222', 'asddkdd@dsfsf.ccc', NULL, '2022-04-17 21:59:44', 1, '2022-04-17 22:22:10', 1, 1);
END
SET IDENTITY_INSERT [dbo].[supplier_master] OFF;
GO

-- =============================================
-- Table: supplier_catalog_tab
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[supplier_catalog_tab]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[supplier_catalog_tab] (
    [supcat_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [supcat_supplier] BIGINT NOT NULL,
    [supcat_item_code] NVARCHAR(50) NOT NULL,
    [supcat_sku_no] NVARCHAR(50) NOT NULL,
    [supcat_uom] INT NOT NULL,
    [supcat_price] DECIMAL(10,2) NOT NULL,
    [supcat_lastdate] DATE NOT NULL,
    [supcat_details] NVARCHAR(MAX) NULL,
    [supcat_createdate] DATETIME NOT NULL,
    [supcat_createby] BIGINT NOT NULL,
    [supcat_modifydate] DATETIME NULL,
    [supcat_modifyby] BIGINT NULL,
    [supcat_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for supplier_catalog_tab
SET IDENTITY_INSERT [dbo].[supplier_catalog_tab] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[supplier_catalog_tab] WHERE [supcat_id] = 1)
BEGIN
INSERT INTO [dbo].[supplier_catalog_tab] ([supcat_id], [supcat_supplier], [supcat_item_code], [supcat_sku_no], [supcat_uom], [supcat_price], [supcat_lastdate], [supcat_details], [supcat_createdate], [supcat_createby], [supcat_modifydate], [supcat_modifyby], [supcat_status]) VALUES
(1, 2, 'U001', 'S3423', 1, 3.44, '2022-04-02', NULL, '2022-04-21 19:26:09', 1, NULL, NULL, 1),
(2, 2, 'U001', 'S341111', 2, 4.22, '2022-04-15', NULL, '2022-04-21 19:48:31', 1, '2022-04-23 07:13:50', 1, 1),
(3, 1, 'A998', 'SADFFF', 1, 33.00, '2022-05-10', NULL, '2022-05-01 15:14:43', 1, NULL, NULL, 1),
(4, 2, 'ITM003', 'SA', 3, 232.00, '2022-05-30', NULL, '2022-05-01 15:17:40', 1, NULL, NULL, 1),
(5, 2, 'ITM003', 'PPP', 1, 666.00, '2022-05-27', NULL, '2022-05-01 15:19:29', 1, NULL, NULL, 1),
(6, 2, 'B3242', 'TT', 2, 33.00, '2022-05-11', NULL, '2022-05-01 15:29:24', 1, NULL, NULL, 1);
END
SET IDENTITY_INSERT [dbo].[supplier_catalog_tab] OFF;
GO

-- =============================================
-- Table: taxcode_master
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[taxcode_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[taxcode_master] (
    [tc_id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [tc_tax_value] DECIMAL(5,2) NOT NULL,
    [tc_createdate] DATETIME NOT NULL,
    [tc_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for taxcode_master
SET IDENTITY_INSERT [dbo].[taxcode_master] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[taxcode_master] WHERE [tc_id] = 1)
BEGIN
INSERT INTO [dbo].[taxcode_master] ([tc_id], [tc_tax_value], [tc_createdate], [tc_status]) VALUES
(1, 10.00, '2022-04-26 00:00:00', 1);
END
SET IDENTITY_INSERT [dbo].[taxcode_master] OFF;
GO

-- =============================================
-- Table: taxgroup_master
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[taxgroup_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[taxgroup_master] (
    [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [name] NVARCHAR(60) NOT NULL,
    [percentage] INT NULL,
    [created_at] DATETIME NOT NULL
);
END
GO

-- =============================================
-- Table: unit_of_measure_tab
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[unit_of_measure_tab]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[unit_of_measure_tab] (
    [uom_id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [uom_name] NVARCHAR(200) NOT NULL UNIQUE,
    [uom_detail] NVARCHAR(250) NULL,
    [uom_createdate] DATETIME NOT NULL,
    [uom_createby] BIGINT NOT NULL,
    [uom_modifydate] DATETIME NULL,
    [uom_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for unit_of_measure_tab
SET IDENTITY_INSERT [dbo].[unit_of_measure_tab] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[unit_of_measure_tab] WHERE [uom_id] = 1)
BEGIN
INSERT INTO [dbo].[unit_of_measure_tab] ([uom_id], [uom_name], [uom_detail], [uom_createdate], [uom_createby], [uom_modifydate], [uom_status]) VALUES
(1, 'REWRW', NULL, '2022-04-17 18:16:35', 1, '2022-04-17 18:56:09', 1),
(2, 'REWRW23', NULL, '2022-04-17 18:16:50', 1, '2022-04-17 18:55:32', 1),
(3, 'REWRW2342', NULL, '2022-04-17 18:18:06', 1, NULL, 1);
END
SET IDENTITY_INSERT [dbo].[unit_of_measure_tab] OFF;
GO

-- =============================================
-- Table: purchase_order_master
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[purchase_order_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[purchase_order_master] (
    [porder_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [porder_project_ms] BIGINT NOT NULL,
    [porder_no] NVARCHAR(50) NOT NULL UNIQUE,
    [porder_supplier_ms] BIGINT NOT NULL,
    [porder_address] NVARCHAR(MAX) NOT NULL,
    [porder_delivery_note] NVARCHAR(MAX) NULL,
    [porder_description] NVARCHAR(MAX) NULL,
    [porder_total_item] INT NOT NULL,
    [porder_total_amount] DECIMAL(10,2) NOT NULL,
    [porder_delivery_status] TINYINT NOT NULL DEFAULT 0, -- 1 = Full, 2 = Partial
    [porder_createdate] DATETIME NOT NULL,
    [porder_createby] BIGINT NOT NULL,
    [porder_modifydate] DATETIME NULL,
    [porder_modifyby] BIGINT NULL,
    [porder_status] TINYINT NOT NULL DEFAULT 1,
    [porder_total_tax] DECIMAL(10,2) NOT NULL DEFAULT 0
);
END
GO

-- Insert data for purchase_order_master
SET IDENTITY_INSERT [dbo].[purchase_order_master] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[purchase_order_master] WHERE [porder_id] = 2)
BEGIN
INSERT INTO [dbo].[purchase_order_master] ([porder_id], [porder_project_ms], [porder_no], [porder_supplier_ms], [porder_address], [porder_delivery_note], [porder_total_item], [porder_total_amount], [porder_delivery_status], [porder_createdate], [porder_createby], [porder_modifydate], [porder_modifyby], [porder_status], [porder_total_tax]) VALUES
(2, 1, 'P00111', 2, '22sajdkha as kjdhas dsah adaksas , asdasdashdkashdM', 'xxxxx xxxx xxxxx 112233', 1, 378.40, 0, '2022-04-30 06:18:18', 1, '2022-04-30 06:38:00', 1, 1, 0);
END
SET IDENTITY_INSERT [dbo].[purchase_order_master] OFF;
GO

-- =============================================
-- Table: purchase_order_details
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[purchase_order_details]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[purchase_order_details] (
    [po_detail_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [po_detail_autogen] NVARCHAR(30) NOT NULL,
    [po_detail_porder_ms] BIGINT NOT NULL,
    [po_detail_item] NVARCHAR(30) NOT NULL,
    [po_detail_sku] NVARCHAR(30) NOT NULL,
    [po_detail_taxcode] NVARCHAR(50) NOT NULL,
    [po_detail_quantity] INT NOT NULL,
    [po_detail_unitprice] DECIMAL(10,2) NOT NULL,
    [po_detail_subtotal] DECIMAL(10,2) NOT NULL,
    [po_detail_taxamount] DECIMAL(10,2) NOT NULL,
    [po_detail_total] DECIMAL(10,2) NOT NULL,
    [po_detail_createdate] DATETIME NOT NULL,
    [po_detail_status] TINYINT NOT NULL DEFAULT 1,
    [po_detail_tax_group] NVARCHAR(50) NULL
);
END
GO

-- Insert data for purchase_order_details
SET IDENTITY_INSERT [dbo].[purchase_order_details] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[purchase_order_details] WHERE [po_detail_id] = 13)
BEGIN
INSERT INTO [dbo].[purchase_order_details] ([po_detail_id], [po_detail_autogen], [po_detail_porder_ms], [po_detail_item], [po_detail_sku], [po_detail_taxcode], [po_detail_quantity], [po_detail_unitprice], [po_detail_subtotal], [po_detail_taxamount], [po_detail_total], [po_detail_createdate], [po_detail_status]) VALUES
(13, '300422061745', 2, 'U001', 'S3423', '10.00', 100, 3.44, 344.00, 34.40, 378.40, '2022-04-30 06:18:15', 1);
END
SET IDENTITY_INSERT [dbo].[purchase_order_details] OFF;
GO

-- =============================================
-- Table: receive_order_master
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[receive_order_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[receive_order_master] (
    [rorder_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [rorder_porder_ms] BIGINT NOT NULL,
    [rorder_slip_no] NVARCHAR(100) NOT NULL,
    [rorder_infoset] NVARCHAR(200) NULL,
    [rorder_date] DATE NOT NULL,
    [rorder_totalitem] INT NULL,
    [rorder_totalamount] DECIMAL(10,2) NULL,
    [rorder_createdate] DATETIME NOT NULL,
    [rorder_createby] BIGINT NOT NULL,
    [rorder_modifydate] DATETIME NULL,
    [rorder_modifyby] BIGINT NULL,
    [rorder_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for receive_order_master
SET IDENTITY_INSERT [dbo].[receive_order_master] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[receive_order_master] WHERE [rorder_id] = 3)
BEGIN
INSERT INTO [dbo].[receive_order_master] ([rorder_id], [rorder_porder_ms], [rorder_slip_no], [rorder_infoset], [rorder_date], [rorder_totalitem], [rorder_totalamount], [rorder_createdate], [rorder_createby], [rorder_modifydate], [rorder_modifyby], [rorder_status]) VALUES
(3, 2, 'FDSF', NULL, '2022-02-05', 1, NULL, '2022-05-01 07:24:25', 1, NULL, NULL, 1),
(4, 2, 'SDFSDF', NULL, '2022-02-05', 1, NULL, '2022-05-01 07:27:05', 1, NULL, NULL, 1);
END
SET IDENTITY_INSERT [dbo].[receive_order_master] OFF;
GO

-- =============================================
-- Table: receive_order_details
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[receive_order_details]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[receive_order_details] (
    [ro_detail_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [ro_detail_rorder_ms] BIGINT NOT NULL,
    [ro_detail_item] NVARCHAR(50) NOT NULL,
    [ro_detail_quantity] BIGINT NOT NULL,
    [ro_detail_createdate] DATETIME NOT NULL,
    [ro_detail_status] TINYINT NOT NULL DEFAULT 1
);
END
GO

-- Insert data for receive_order_details
SET IDENTITY_INSERT [dbo].[receive_order_details] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[receive_order_details] WHERE [ro_detail_id] = 2)
BEGIN
INSERT INTO [dbo].[receive_order_details] ([ro_detail_id], [ro_detail_rorder_ms], [ro_detail_item], [ro_detail_quantity], [ro_detail_createdate], [ro_detail_status]) VALUES
(2, 3, 'U001', 20, '2022-05-01 07:24:25', 1),
(3, 4, 'U001', 80, '2022-05-01 07:27:05', 1);
END
SET IDENTITY_INSERT [dbo].[receive_order_details] OFF;
GO

-- =============================================
-- Table: user_info (Legacy user table)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[user_info]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[user_info] (
    [u_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [u_type] INT NOT NULL,
    [u_access] INT NOT NULL DEFAULT 1,
    [username] NVARCHAR(200) NOT NULL UNIQUE,
    [password] NVARCHAR(250) NOT NULL,
    [phone] NVARCHAR(255) NOT NULL,
    [email] NVARCHAR(255) NOT NULL,
    [firstname] NVARCHAR(200) NOT NULL,
    [lastname] NVARCHAR(200) NOT NULL,
    [u_masteruser] INT NULL,
    [create_date] DATETIME NOT NULL,
    [modify_date] DATETIME NULL,
    [access_ip] NVARCHAR(200) NOT NULL,
    [status] NVARCHAR(1) NOT NULL DEFAULT '1'
);
END
GO

-- Insert data for user_info
SET IDENTITY_INSERT [dbo].[user_info] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[user_info] WHERE [u_id] = 1)
BEGIN
INSERT INTO [dbo].[user_info] ([u_id], [u_type], [u_access], [username], [password], [phone], [email], [firstname], [lastname], [u_masteruser], [create_date], [modify_date], [access_ip], [status]) VALUES
(1, 1, 1, 'admin', '8d1af614908958e76b9c89d78d2f296fd2ca9ef3adcb95392610df20ea106b24e48551ef2964b1c58fdb262932476511939d208364b073c13966f0e164328df0', '9830260404', 'amit@albatrossoft.com', 'Super', 'Admin', NULL, '2015-04-22 07:39:18', '2022-05-01 19:30:29', '127.0.0.1', '1'),
(2, 2, 1, 'admin_2', '8d1af614908958e76b9c89d78d2f296fd2ca9ef3adcb95392610df20ea106b24e48551ef2964b1c58fdb262932476511939d208364b073c13966f0e164328df0', '9830260405', 'amit222@albatrossoft.com', 'Sub', 'Admin', NULL, '2015-04-22 07:39:18', '2022-04-16 08:42:57', '127.0.0.1', '1'),
(4, 2, 1, 'ddd', '2467c27760e1d8f4af75a1933d5f518daa4ad07df13595cd37d3c267fedde7e2b08db5a6da811c11ac3e21cb85dc8c946d3e516b013631312d668a6d4d631b17', '25487845120', 'dasdasd@fdf.gg', 'Adasd', 'ASDASD', NULL, '2022-04-23 10:42:20', '2022-04-23 10:42:20', '127.0.0.1', '1'),
(5, 2, 1, 'sff', '6b65cb040835879e1a4bb41c42abbd791235392efc4b8247e2b37034e8df4905177683569ab017705c6e83ca5b64fb55db0227ab98c47c8d88723d76c383f6bb', '4432124555', 'sdfgfgfd.@fdf.gg', 'RRRR', 'ass', NULL, '2022-04-23 10:42:55', '2022-04-23 10:42:55', '127.0.0.1', '1'),
(6, 2, 1, 'ggg', 'f288a8d9cbe997fc6dea75b724c0cfe9f4c5f341bd3c8bfe99dae2cb937621beba43a7783fe796804dcfec08d8ffdadae807e131e74cb32273b6b08edc2488fc', '34236456456', 'ghyjghj@fdfd.cc', 'TTTT', 'sd sd fdsfdf fdff', NULL, '2022-04-23 10:43:27', '2022-04-23 10:43:27', '127.0.0.1', '1'),
(7, 2, 1, 'vvv', '2467c27760e1d8f4af75a1933d5f518daa4ad07df13595cd37d3c267fedde7e2b08db5a6da811c11ac3e21cb85dc8c946d3e516b013631312d668a6d4d631b17', '342343244456', 'sadsadas@fssaa.cb', '111sdjfgsd sdjhds', 'sdjfhdsf sdhf', NULL, '2022-04-23 10:44:04', '2022-04-23 10:44:04', '127.0.0.1', '1');
END
SET IDENTITY_INSERT [dbo].[user_info] OFF;
GO

-- =============================================
-- Table: user_details
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[user_details]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[user_details] (
    [id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [uid] BIGINT NOT NULL UNIQUE,
    [address] NVARCHAR(255) NULL,
    [country] NVARCHAR(200) NULL,
    [state] NVARCHAR(200) NULL,
    [city] NVARCHAR(200) NULL,
    [pincode] NVARCHAR(100) NULL
);
END
GO

-- Insert data for user_details
SET IDENTITY_INSERT [dbo].[user_details] ON;
IF NOT EXISTS (SELECT 1 FROM [dbo].[user_details] WHERE [id] = 1)
BEGIN
INSERT INTO [dbo].[user_details] ([id], [uid], [address], [country], [state], [city], [pincode]) VALUES
(1, 1, '', NULL, NULL, NULL, NULL),
(2, 2, '', NULL, NULL, NULL, NULL),
(4, 4, '', NULL, NULL, NULL, NULL),
(5, 5, 'sss', NULL, NULL, NULL, NULL),
(6, 6, 'dfdsf', NULL, NULL, NULL, NULL),
(7, 7, 'sdfds', NULL, NULL, NULL, NULL);
END
SET IDENTITY_INSERT [dbo].[user_details] OFF;
GO

-- =============================================
-- View: user_views
-- =============================================
IF EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[user_views]'))
    DROP VIEW [dbo].[user_views];
GO

CREATE VIEW [dbo].[user_views] AS
SELECT 
    ui.[u_id],
    ui.[u_type],
    ui.[u_access],
    ui.[username],
    ui.[password],
    ui.[phone],
    ui.[email],
    ui.[firstname],
    ui.[lastname],
    ui.[u_masteruser],
    ui.[create_date],
    ui.[modify_date],
    ui.[access_ip],
    ui.[status],
    ud.[id],
    ud.[uid],
    ud.[address],
    ud.[country],
    ud.[state],
    ud.[city],
    ud.[pincode],
    mut.[mu_name]
FROM [dbo].[user_info] ui
INNER JOIN [dbo].[user_details] ud ON ui.[u_id] = ud.[uid]
INNER JOIN [dbo].[master_user_type] mut ON ui.[u_type] = mut.[mu_id];
GO

PRINT 'All tables and data have been created successfully!';
GO
