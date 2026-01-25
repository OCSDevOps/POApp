-- =============================================
-- Database Optimization Script for POApp
-- Adds indexes, constraints, additional tables for business logic
-- and views for reporting
-- =============================================

-- =============================================
-- PART 1: INDEXES FOR PERFORMANCE
-- =============================================

-- Indexes for item_master
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_item_master_cat' AND object_id = OBJECT_ID('item_master'))
    CREATE INDEX IX_item_master_cat ON item_master(item_cat_ms);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_item_master_ccode' AND object_id = OBJECT_ID('item_master'))
    CREATE INDEX IX_item_master_ccode ON item_master(item_ccode_ms);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_item_master_unit' AND object_id = OBJECT_ID('item_master'))
    CREATE INDEX IX_item_master_unit ON item_master(item_unit_ms);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_item_master_status' AND object_id = OBJECT_ID('item_master'))
    CREATE INDEX IX_item_master_status ON item_master(item_status);
GO

-- Indexes for supplier_master
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_supplier_master_status' AND object_id = OBJECT_ID('supplier_master'))
    CREATE INDEX IX_supplier_master_status ON supplier_master(sup_status);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_supplier_master_email' AND object_id = OBJECT_ID('supplier_master'))
    CREATE INDEX IX_supplier_master_email ON supplier_master(sup_email);
GO

-- Indexes for supplier_catalog_tab
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_supplier_catalog_supplier' AND object_id = OBJECT_ID('supplier_catalog_tab'))
    CREATE INDEX IX_supplier_catalog_supplier ON supplier_catalog_tab(supcat_supplier);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_supplier_catalog_item' AND object_id = OBJECT_ID('supplier_catalog_tab'))
    CREATE INDEX IX_supplier_catalog_item ON supplier_catalog_tab(supcat_item_code);
GO

-- Indexes for project_master
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_project_master_status' AND object_id = OBJECT_ID('project_master'))
    CREATE INDEX IX_project_master_status ON project_master(proj_status);
GO

-- Indexes for project_details
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_project_details_proj' AND object_id = OBJECT_ID('project_details'))
    CREATE INDEX IX_project_details_proj ON project_details(pdetail_proj_ms);
GO

-- Indexes for purchase_order_master
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_porder_project' AND object_id = OBJECT_ID('purchase_order_master'))
    CREATE INDEX IX_porder_project ON purchase_order_master(porder_project_ms);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_porder_supplier' AND object_id = OBJECT_ID('purchase_order_master'))
    CREATE INDEX IX_porder_supplier ON purchase_order_master(porder_supplier_ms);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_porder_status' AND object_id = OBJECT_ID('purchase_order_master'))
    CREATE INDEX IX_porder_status ON purchase_order_master(porder_status);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_porder_delivery_status' AND object_id = OBJECT_ID('purchase_order_master'))
    CREATE INDEX IX_porder_delivery_status ON purchase_order_master(porder_delivery_status);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_porder_createdate' AND object_id = OBJECT_ID('purchase_order_master'))
    CREATE INDEX IX_porder_createdate ON purchase_order_master(porder_createdate);
GO

-- Indexes for purchase_order_details
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_po_detail_porder' AND object_id = OBJECT_ID('purchase_order_details'))
    CREATE INDEX IX_po_detail_porder ON purchase_order_details(po_detail_porder_ms);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_po_detail_item' AND object_id = OBJECT_ID('purchase_order_details'))
    CREATE INDEX IX_po_detail_item ON purchase_order_details(po_detail_item);
GO

-- Indexes for receive_order_master
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_rorder_porder' AND object_id = OBJECT_ID('receive_order_master'))
    CREATE INDEX IX_rorder_porder ON receive_order_master(rorder_porder_ms);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_rorder_date' AND object_id = OBJECT_ID('receive_order_master'))
    CREATE INDEX IX_rorder_date ON receive_order_master(rorder_date);
GO

-- Indexes for receive_order_details
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_ro_detail_rorder' AND object_id = OBJECT_ID('receive_order_details'))
    CREATE INDEX IX_ro_detail_rorder ON receive_order_details(ro_detail_rorder_ms);
GO

-- Indexes for item_category_tab
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_item_category_parent' AND object_id = OBJECT_ID('item_category_tab'))
    CREATE INDEX IX_item_category_parent ON item_category_tab(icat_parent);
GO

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_item_category_status' AND object_id = OBJECT_ID('item_category_tab'))
    CREATE INDEX IX_item_category_status ON item_category_tab(icat_status);
GO

-- Indexes for cost_code_master
IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_cost_code_status' AND object_id = OBJECT_ID('cost_code_master'))
    CREATE INDEX IX_cost_code_status ON cost_code_master(cc_status);
GO

-- =============================================
-- PART 2: FOREIGN KEY CONSTRAINTS
-- =============================================

-- item_master foreign keys
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_item_category')
    ALTER TABLE item_master ADD CONSTRAINT FK_item_category 
    FOREIGN KEY (item_cat_ms) REFERENCES item_category_tab(icat_id);
GO

IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_item_costcode')
    ALTER TABLE item_master ADD CONSTRAINT FK_item_costcode 
    FOREIGN KEY (item_ccode_ms) REFERENCES cost_code_master(cc_id);
GO

IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_item_uom')
    ALTER TABLE item_master ADD CONSTRAINT FK_item_uom 
    FOREIGN KEY (item_unit_ms) REFERENCES unit_of_measure_tab(uom_id);
GO

-- supplier_catalog_tab foreign keys
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_supcat_supplier')
    ALTER TABLE supplier_catalog_tab ADD CONSTRAINT FK_supcat_supplier 
    FOREIGN KEY (supcat_supplier) REFERENCES supplier_master(sup_id);
GO

IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_supcat_uom')
    ALTER TABLE supplier_catalog_tab ADD CONSTRAINT FK_supcat_uom 
    FOREIGN KEY (supcat_uom) REFERENCES unit_of_measure_tab(uom_id);
GO

-- project_details foreign keys
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_pdetail_project')
    ALTER TABLE project_details ADD CONSTRAINT FK_pdetail_project 
    FOREIGN KEY (pdetail_proj_ms) REFERENCES project_master(proj_id);
GO

-- purchase_order_master foreign keys
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_porder_project')
    ALTER TABLE purchase_order_master ADD CONSTRAINT FK_porder_project 
    FOREIGN KEY (porder_project_ms) REFERENCES project_master(proj_id);
GO

IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_porder_supplier')
    ALTER TABLE purchase_order_master ADD CONSTRAINT FK_porder_supplier 
    FOREIGN KEY (porder_supplier_ms) REFERENCES supplier_master(sup_id);
GO

-- purchase_order_details foreign keys
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_po_detail_porder')
    ALTER TABLE purchase_order_details ADD CONSTRAINT FK_po_detail_porder 
    FOREIGN KEY (po_detail_porder_ms) REFERENCES purchase_order_master(porder_id);
GO

-- receive_order_master foreign keys
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_rorder_porder')
    ALTER TABLE receive_order_master ADD CONSTRAINT FK_rorder_porder 
    FOREIGN KEY (rorder_porder_ms) REFERENCES purchase_order_master(porder_id);
GO

-- receive_order_details foreign keys
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_ro_detail_rorder')
    ALTER TABLE receive_order_details ADD CONSTRAINT FK_ro_detail_rorder 
    FOREIGN KEY (ro_detail_rorder_ms) REFERENCES receive_order_master(rorder_id);
GO

-- item_package_details foreign keys
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_ipdetail_package')
    ALTER TABLE item_package_details ADD CONSTRAINT FK_ipdetail_package 
    FOREIGN KEY (ipdetail_ipack_ms) REFERENCES item_package_master(ipack_id);
GO

-- item_category_tab self-referencing foreign key
IF NOT EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_icat_parent')
    ALTER TABLE item_category_tab ADD CONSTRAINT FK_icat_parent 
    FOREIGN KEY (icat_parent) REFERENCES item_category_tab(icat_id);
GO

-- =============================================
-- PART 3: ADDITIONAL TABLES FOR BUSINESS LOGIC
-- =============================================

-- =============================================
-- Table: item_price_history (Price Tracking)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[item_price_history]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[item_price_history] (
    [iph_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [iph_item_id] BIGINT NOT NULL,
    [iph_supplier_id] BIGINT NOT NULL,
    [iph_old_price] DECIMAL(10,2) NOT NULL,
    [iph_new_price] DECIMAL(10,2) NOT NULL,
    [iph_effective_date] DATE NOT NULL,
    [iph_notes] NVARCHAR(500) NULL,
    [iph_created_by] BIGINT NOT NULL,
    [iph_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_iph_item FOREIGN KEY (iph_item_id) REFERENCES item_master(item_id),
    CONSTRAINT FK_iph_supplier FOREIGN KEY (iph_supplier_id) REFERENCES supplier_master(sup_id)
);
CREATE INDEX IX_iph_item ON item_price_history(iph_item_id);
CREATE INDEX IX_iph_supplier ON item_price_history(iph_supplier_id);
CREATE INDEX IX_iph_effective_date ON item_price_history(iph_effective_date);
END
GO

-- =============================================
-- Table: rfq_master (Request for Quote)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[rfq_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[rfq_master] (
    [rfq_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [rfq_no] NVARCHAR(50) NOT NULL UNIQUE,
    [rfq_project_id] BIGINT NOT NULL,
    [rfq_title] NVARCHAR(250) NOT NULL,
    [rfq_description] NVARCHAR(MAX) NULL,
    [rfq_due_date] DATE NOT NULL,
    [rfq_status] TINYINT NOT NULL DEFAULT 1, -- 1=Draft, 2=Sent, 3=Received, 4=Converted, 5=Cancelled
    [rfq_created_by] BIGINT NOT NULL,
    [rfq_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    [rfq_modified_by] BIGINT NULL,
    [rfq_modified_at] DATETIME NULL,
    CONSTRAINT FK_rfq_project FOREIGN KEY (rfq_project_id) REFERENCES project_master(proj_id)
);
CREATE INDEX IX_rfq_project ON rfq_master(rfq_project_id);
CREATE INDEX IX_rfq_status ON rfq_master(rfq_status);
CREATE INDEX IX_rfq_due_date ON rfq_master(rfq_due_date);
END
GO

-- =============================================
-- Table: rfq_suppliers (RFQ to Supplier mapping)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[rfq_suppliers]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[rfq_suppliers] (
    [rfqs_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [rfqs_rfq_id] BIGINT NOT NULL,
    [rfqs_supplier_id] BIGINT NOT NULL,
    [rfqs_sent_date] DATETIME NULL,
    [rfqs_response_date] DATETIME NULL,
    [rfqs_status] TINYINT NOT NULL DEFAULT 1, -- 1=Pending, 2=Sent, 3=Responded, 4=Selected, 5=Rejected
    [rfqs_notes] NVARCHAR(MAX) NULL,
    [rfqs_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_rfqs_rfq FOREIGN KEY (rfqs_rfq_id) REFERENCES rfq_master(rfq_id),
    CONSTRAINT FK_rfqs_supplier FOREIGN KEY (rfqs_supplier_id) REFERENCES supplier_master(sup_id)
);
CREATE INDEX IX_rfqs_rfq ON rfq_suppliers(rfqs_rfq_id);
CREATE INDEX IX_rfqs_supplier ON rfq_suppliers(rfqs_supplier_id);
END
GO

-- =============================================
-- Table: rfq_items (RFQ Line Items)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[rfq_items]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[rfq_items] (
    [rfqi_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [rfqi_rfq_id] BIGINT NOT NULL,
    [rfqi_item_id] BIGINT NOT NULL,
    [rfqi_quantity] INT NOT NULL,
    [rfqi_uom_id] INT NOT NULL,
    [rfqi_target_price] DECIMAL(10,2) NULL,
    [rfqi_notes] NVARCHAR(500) NULL,
    [rfqi_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_rfqi_rfq FOREIGN KEY (rfqi_rfq_id) REFERENCES rfq_master(rfq_id),
    CONSTRAINT FK_rfqi_item FOREIGN KEY (rfqi_item_id) REFERENCES item_master(item_id),
    CONSTRAINT FK_rfqi_uom FOREIGN KEY (rfqi_uom_id) REFERENCES unit_of_measure_tab(uom_id)
);
CREATE INDEX IX_rfqi_rfq ON rfq_items(rfqi_rfq_id);
CREATE INDEX IX_rfqi_item ON rfq_items(rfqi_item_id);
END
GO

-- =============================================
-- Table: rfq_quotes (Supplier Quotes for RFQ)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[rfq_quotes]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[rfq_quotes] (
    [rfqq_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [rfqq_rfqs_id] BIGINT NOT NULL, -- Links to rfq_suppliers
    [rfqq_rfqi_id] BIGINT NOT NULL, -- Links to rfq_items
    [rfqq_quoted_price] DECIMAL(10,2) NOT NULL,
    [rfqq_lead_time_days] INT NULL,
    [rfqq_valid_until] DATE NULL,
    [rfqq_notes] NVARCHAR(500) NULL,
    [rfqq_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_rfqq_rfqs FOREIGN KEY (rfqq_rfqs_id) REFERENCES rfq_suppliers(rfqs_id),
    CONSTRAINT FK_rfqq_rfqi FOREIGN KEY (rfqq_rfqi_id) REFERENCES rfq_items(rfqi_id)
);
CREATE INDEX IX_rfqq_rfqs ON rfq_quotes(rfqq_rfqs_id);
CREATE INDEX IX_rfqq_rfqi ON rfq_quotes(rfqq_rfqi_id);
END
GO

-- =============================================
-- Table: po_template_master (PO Templates)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[po_template_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[po_template_master] (
    [pot_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [pot_name] NVARCHAR(250) NOT NULL,
    [pot_description] NVARCHAR(MAX) NULL,
    [pot_supplier_id] BIGINT NULL,
    [pot_project_id] BIGINT NULL,
    [pot_terms] NVARCHAR(MAX) NULL,
    [pot_delivery_notes] NVARCHAR(MAX) NULL,
    [pot_is_active] BIT NOT NULL DEFAULT 1,
    [pot_created_by] BIGINT NOT NULL,
    [pot_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    [pot_modified_by] BIGINT NULL,
    [pot_modified_at] DATETIME NULL,
    CONSTRAINT FK_pot_supplier FOREIGN KEY (pot_supplier_id) REFERENCES supplier_master(sup_id),
    CONSTRAINT FK_pot_project FOREIGN KEY (pot_project_id) REFERENCES project_master(proj_id)
);
CREATE INDEX IX_pot_supplier ON po_template_master(pot_supplier_id);
CREATE INDEX IX_pot_project ON po_template_master(pot_project_id);
END
GO

-- =============================================
-- Table: po_template_items (PO Template Line Items)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[po_template_items]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[po_template_items] (
    [poti_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [poti_template_id] BIGINT NOT NULL,
    [poti_item_id] BIGINT NOT NULL,
    [poti_default_qty] INT NOT NULL DEFAULT 1,
    [poti_uom_id] INT NOT NULL,
    [poti_cost_code_id] BIGINT NULL,
    [poti_notes] NVARCHAR(500) NULL,
    [poti_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_poti_template FOREIGN KEY (poti_template_id) REFERENCES po_template_master(pot_id),
    CONSTRAINT FK_poti_item FOREIGN KEY (poti_item_id) REFERENCES item_master(item_id),
    CONSTRAINT FK_poti_uom FOREIGN KEY (poti_uom_id) REFERENCES unit_of_measure_tab(uom_id),
    CONSTRAINT FK_poti_costcode FOREIGN KEY (poti_cost_code_id) REFERENCES cost_code_master(cc_id)
);
CREATE INDEX IX_poti_template ON po_template_items(poti_template_id);
CREATE INDEX IX_poti_item ON po_template_items(poti_item_id);
END
GO

-- =============================================
-- Table: budget_master (Project Budgets)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[budget_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[budget_master] (
    [budget_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [budget_project_id] BIGINT NOT NULL,
    [budget_cost_code_id] BIGINT NOT NULL,
    [budget_original_amount] DECIMAL(15,2) NOT NULL DEFAULT 0,
    [budget_revised_amount] DECIMAL(15,2) NOT NULL DEFAULT 0,
    [budget_committed_amount] DECIMAL(15,2) NOT NULL DEFAULT 0,
    [budget_spent_amount] DECIMAL(15,2) NOT NULL DEFAULT 0,
    [budget_remaining_amount] AS (budget_revised_amount - budget_committed_amount - budget_spent_amount),
    [budget_fiscal_year] INT NOT NULL,
    [budget_notes] NVARCHAR(MAX) NULL,
    [budget_status] TINYINT NOT NULL DEFAULT 1, -- 1=Active, 0=Inactive
    [budget_created_by] BIGINT NOT NULL,
    [budget_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    [budget_modified_by] BIGINT NULL,
    [budget_modified_at] DATETIME NULL,
    [procore_budget_id] BIGINT NULL,
    CONSTRAINT FK_budget_project FOREIGN KEY (budget_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT FK_budget_costcode FOREIGN KEY (budget_cost_code_id) REFERENCES cost_code_master(cc_id)
);
CREATE INDEX IX_budget_project ON budget_master(budget_project_id);
CREATE INDEX IX_budget_costcode ON budget_master(budget_cost_code_id);
CREATE INDEX IX_budget_fiscal_year ON budget_master(budget_fiscal_year);
CREATE INDEX IX_budget_procore ON budget_master(procore_budget_id);
END
GO

-- =============================================
-- Table: commitment_master (Commitments/Contracts)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[commitment_master]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[commitment_master] (
    [commit_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [commit_project_id] BIGINT NOT NULL,
    [commit_supplier_id] BIGINT NOT NULL,
    [commit_cost_code_id] BIGINT NOT NULL,
    [commit_number] NVARCHAR(50) NOT NULL,
    [commit_title] NVARCHAR(250) NOT NULL,
    [commit_description] NVARCHAR(MAX) NULL,
    [commit_original_value] DECIMAL(15,2) NOT NULL DEFAULT 0,
    [commit_approved_cos] DECIMAL(15,2) NOT NULL DEFAULT 0, -- Change Orders
    [commit_pending_cos] DECIMAL(15,2) NOT NULL DEFAULT 0,
    [commit_revised_value] AS (commit_original_value + commit_approved_cos),
    [commit_invoiced_amount] DECIMAL(15,2) NOT NULL DEFAULT 0,
    [commit_paid_amount] DECIMAL(15,2) NOT NULL DEFAULT 0,
    [commit_start_date] DATE NULL,
    [commit_end_date] DATE NULL,
    [commit_status] TINYINT NOT NULL DEFAULT 1, -- 1=Draft, 2=Pending, 3=Approved, 4=Active, 5=Completed, 6=Cancelled
    [commit_created_by] BIGINT NOT NULL,
    [commit_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    [commit_modified_by] BIGINT NULL,
    [commit_modified_at] DATETIME NULL,
    [procore_commitment_id] BIGINT NULL,
    CONSTRAINT FK_commit_project FOREIGN KEY (commit_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT FK_commit_supplier FOREIGN KEY (commit_supplier_id) REFERENCES supplier_master(sup_id),
    CONSTRAINT FK_commit_costcode FOREIGN KEY (commit_cost_code_id) REFERENCES cost_code_master(cc_id)
);
CREATE INDEX IX_commit_project ON commitment_master(commit_project_id);
CREATE INDEX IX_commit_supplier ON commitment_master(commit_supplier_id);
CREATE INDEX IX_commit_costcode ON commitment_master(commit_cost_code_id);
CREATE INDEX IX_commit_status ON commitment_master(commit_status);
CREATE INDEX IX_commit_procore ON commitment_master(procore_commitment_id);
END
GO

-- =============================================
-- Table: procore_sync_log (Procore Integration Log)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[procore_sync_log]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[procore_sync_log] (
    [sync_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [sync_type] NVARCHAR(50) NOT NULL, -- projects, cost_codes, budgets, commitments, purchase_orders
    [sync_direction] NVARCHAR(10) NOT NULL, -- inbound, outbound
    [sync_entity_id] BIGINT NULL, -- Local entity ID
    [sync_procore_id] BIGINT NULL, -- Procore entity ID
    [sync_status] NVARCHAR(20) NOT NULL, -- success, failed, pending
    [sync_message] NVARCHAR(MAX) NULL,
    [sync_request_data] NVARCHAR(MAX) NULL,
    [sync_response_data] NVARCHAR(MAX) NULL,
    [sync_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    [sync_created_by] BIGINT NULL
);
CREATE INDEX IX_sync_type ON procore_sync_log(sync_type);
CREATE INDEX IX_sync_status ON procore_sync_log(sync_status);
CREATE INDEX IX_sync_created_at ON procore_sync_log(sync_created_at);
END
GO

-- =============================================
-- Table: procore_project_mapping (Procore Project Mapping)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[procore_project_mapping]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[procore_project_mapping] (
    [ppm_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [ppm_local_project_id] BIGINT NOT NULL,
    [ppm_procore_project_id] BIGINT NOT NULL,
    [ppm_procore_company_id] BIGINT NOT NULL,
    [ppm_last_sync_at] DATETIME NULL,
    [ppm_sync_enabled] BIT NOT NULL DEFAULT 1,
    [ppm_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_ppm_project FOREIGN KEY (ppm_local_project_id) REFERENCES project_master(proj_id)
);
CREATE UNIQUE INDEX IX_ppm_local_project ON procore_project_mapping(ppm_local_project_id);
CREATE INDEX IX_ppm_procore_project ON procore_project_mapping(ppm_procore_project_id);
END
GO

-- =============================================
-- Table: procore_cost_code_mapping (Procore Cost Code Mapping)
-- =============================================
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[procore_cost_code_mapping]') AND type in (N'U'))
BEGIN
CREATE TABLE [dbo].[procore_cost_code_mapping] (
    [pccm_id] BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    [pccm_local_cost_code_id] BIGINT NOT NULL,
    [pccm_procore_cost_code_id] BIGINT NOT NULL,
    [pccm_procore_project_id] BIGINT NOT NULL,
    [pccm_last_sync_at] DATETIME NULL,
    [pccm_created_at] DATETIME NOT NULL DEFAULT GETDATE(),
    CONSTRAINT FK_pccm_costcode FOREIGN KEY (pccm_local_cost_code_id) REFERENCES cost_code_master(cc_id)
);
CREATE INDEX IX_pccm_local_costcode ON procore_cost_code_mapping(pccm_local_cost_code_id);
CREATE INDEX IX_pccm_procore_costcode ON procore_cost_code_mapping(pccm_procore_cost_code_id);
END
GO

-- =============================================
-- Add Procore integration columns to existing tables
-- =============================================

-- Add procore_project_id to project_master if not exists
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('project_master') AND name = 'procore_project_id')
    ALTER TABLE project_master ADD procore_project_id BIGINT NULL;
GO

-- Add procore_supplier_id to supplier_master if not exists
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('supplier_master') AND name = 'procore_supplier_id')
    ALTER TABLE supplier_master ADD procore_supplier_id BIGINT NULL;
GO

-- Add procore_po_id to purchase_order_master if not exists
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('purchase_order_master') AND name = 'procore_po_id')
    ALTER TABLE purchase_order_master ADD procore_po_id BIGINT NULL;
GO

-- Add integration_status to purchase_order_master if not exists
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('purchase_order_master') AND name = 'integration_status')
    ALTER TABLE purchase_order_master ADD integration_status NVARCHAR(20) NULL DEFAULT 'pending';
GO

-- Add procore_cost_code_id to cost_code_master if not exists
IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('cost_code_master') AND name = 'procore_cost_code_id')
    ALTER TABLE cost_code_master ADD procore_cost_code_id BIGINT NULL;
GO

-- =============================================
-- PART 4: DATABASE VIEWS FOR REPORTING
-- =============================================

-- =============================================
-- View: vw_purchase_order_summary
-- =============================================
IF EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vw_purchase_order_summary]'))
    DROP VIEW [dbo].[vw_purchase_order_summary];
GO

CREATE VIEW [dbo].[vw_purchase_order_summary] AS
SELECT 
    po.porder_id,
    po.porder_no,
    po.porder_createdate,
    po.porder_total_amount,
    po.porder_total_tax,
    po.porder_delivery_status,
    po.porder_status,
    po.integration_status,
    p.proj_id,
    p.proj_number,
    p.proj_name,
    s.sup_id,
    s.sup_name,
    s.sup_email,
    CASE po.porder_delivery_status 
        WHEN 0 THEN 'Not Received'
        WHEN 1 THEN 'Fully Received'
        WHEN 2 THEN 'Partially Received'
        ELSE 'Unknown'
    END AS delivery_status_text,
    CASE po.porder_status 
        WHEN 0 THEN 'Cancelled'
        WHEN 1 THEN 'Active'
        WHEN 2 THEN 'Completed'
        ELSE 'Unknown'
    END AS status_text
FROM purchase_order_master po
INNER JOIN project_master p ON po.porder_project_ms = p.proj_id
INNER JOIN supplier_master s ON po.porder_supplier_ms = s.sup_id;
GO

-- =============================================
-- View: vw_back_order_report
-- =============================================
IF EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vw_back_order_report]'))
    DROP VIEW [dbo].[vw_back_order_report];
GO

CREATE VIEW [dbo].[vw_back_order_report] AS
SELECT 
    po.porder_id,
    po.porder_no,
    po.porder_createdate,
    pod.po_detail_item AS item_code,
    pod.po_detail_quantity AS ordered_qty,
    ISNULL(SUM(rod.ro_detail_quantity), 0) AS received_qty,
    pod.po_detail_quantity - ISNULL(SUM(rod.ro_detail_quantity), 0) AS back_order_qty,
    p.proj_name,
    s.sup_name
FROM purchase_order_master po
INNER JOIN purchase_order_details pod ON po.porder_id = pod.po_detail_porder_ms
INNER JOIN project_master p ON po.porder_project_ms = p.proj_id
INNER JOIN supplier_master s ON po.porder_supplier_ms = s.sup_id
LEFT JOIN receive_order_master rom ON po.porder_id = rom.rorder_porder_ms
LEFT JOIN receive_order_details rod ON rom.rorder_id = rod.ro_detail_rorder_ms 
    AND pod.po_detail_item = rod.ro_detail_item
WHERE po.porder_status = 1
GROUP BY 
    po.porder_id, po.porder_no, po.porder_createdate,
    pod.po_detail_item, pod.po_detail_quantity,
    p.proj_name, s.sup_name
HAVING pod.po_detail_quantity > ISNULL(SUM(rod.ro_detail_quantity), 0);
GO

-- =============================================
-- View: vw_item_pricing_summary
-- =============================================
IF EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vw_item_pricing_summary]'))
    DROP VIEW [dbo].[vw_item_pricing_summary];
GO

CREATE VIEW [dbo].[vw_item_pricing_summary] AS
SELECT 
    i.item_id,
    i.item_code,
    i.item_name,
    ic.icat_name AS category_name,
    cc.cc_no AS cost_code,
    uom.uom_name AS unit_of_measure,
    s.sup_id,
    s.sup_name AS supplier_name,
    sc.supcat_sku_no AS supplier_sku,
    sc.supcat_price AS current_price,
    sc.supcat_lastdate AS price_effective_date
FROM item_master i
INNER JOIN item_category_tab ic ON i.item_cat_ms = ic.icat_id
INNER JOIN cost_code_master cc ON i.item_ccode_ms = cc.cc_id
INNER JOIN unit_of_measure_tab uom ON i.item_unit_ms = uom.uom_id
LEFT JOIN supplier_catalog_tab sc ON i.item_code = sc.supcat_item_code
LEFT JOIN supplier_master s ON sc.supcat_supplier = s.sup_id
WHERE i.item_status = 1;
GO

-- =============================================
-- View: vw_budget_summary
-- =============================================
IF EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vw_budget_summary]'))
    DROP VIEW [dbo].[vw_budget_summary];
GO

CREATE VIEW [dbo].[vw_budget_summary] AS
SELECT 
    b.budget_id,
    p.proj_id,
    p.proj_number,
    p.proj_name,
    cc.cc_id,
    cc.cc_no AS cost_code,
    cc.cc_description AS cost_code_name,
    b.budget_original_amount,
    b.budget_revised_amount,
    b.budget_committed_amount,
    b.budget_spent_amount,
    b.budget_remaining_amount,
    b.budget_fiscal_year,
    CASE 
        WHEN b.budget_revised_amount > 0 
        THEN CAST((b.budget_committed_amount + b.budget_spent_amount) / b.budget_revised_amount * 100 AS DECIMAL(5,2))
        ELSE 0 
    END AS budget_utilization_pct
FROM budget_master b
INNER JOIN project_master p ON b.budget_project_id = p.proj_id
INNER JOIN cost_code_master cc ON b.budget_cost_code_id = cc.cc_id
WHERE b.budget_status = 1;
GO

-- =============================================
-- View: vw_commitment_summary
-- =============================================
IF EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vw_commitment_summary]'))
    DROP VIEW [dbo].[vw_commitment_summary];
GO

CREATE VIEW [dbo].[vw_commitment_summary] AS
SELECT 
    c.commit_id,
    c.commit_number,
    c.commit_title,
    p.proj_id,
    p.proj_number,
    p.proj_name,
    s.sup_id,
    s.sup_name AS supplier_name,
    cc.cc_no AS cost_code,
    c.commit_original_value,
    c.commit_approved_cos,
    c.commit_pending_cos,
    c.commit_revised_value,
    c.commit_invoiced_amount,
    c.commit_paid_amount,
    c.commit_revised_value - c.commit_invoiced_amount AS remaining_to_invoice,
    c.commit_start_date,
    c.commit_end_date,
    CASE c.commit_status 
        WHEN 1 THEN 'Draft'
        WHEN 2 THEN 'Pending'
        WHEN 3 THEN 'Approved'
        WHEN 4 THEN 'Active'
        WHEN 5 THEN 'Completed'
        WHEN 6 THEN 'Cancelled'
        ELSE 'Unknown'
    END AS status_text
FROM commitment_master c
INNER JOIN project_master p ON c.commit_project_id = p.proj_id
INNER JOIN supplier_master s ON c.commit_supplier_id = s.sup_id
INNER JOIN cost_code_master cc ON c.commit_cost_code_id = cc.cc_id;
GO

-- =============================================
-- View: vw_rfq_summary
-- =============================================
IF EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vw_rfq_summary]'))
    DROP VIEW [dbo].[vw_rfq_summary];
GO

CREATE VIEW [dbo].[vw_rfq_summary] AS
SELECT 
    r.rfq_id,
    r.rfq_no,
    r.rfq_title,
    r.rfq_due_date,
    p.proj_id,
    p.proj_number,
    p.proj_name,
    (SELECT COUNT(*) FROM rfq_items WHERE rfqi_rfq_id = r.rfq_id) AS item_count,
    (SELECT COUNT(*) FROM rfq_suppliers WHERE rfqs_rfq_id = r.rfq_id) AS supplier_count,
    (SELECT COUNT(*) FROM rfq_suppliers WHERE rfqs_rfq_id = r.rfq_id AND rfqs_status = 3) AS responses_received,
    CASE r.rfq_status 
        WHEN 1 THEN 'Draft'
        WHEN 2 THEN 'Sent'
        WHEN 3 THEN 'Received'
        WHEN 4 THEN 'Converted'
        WHEN 5 THEN 'Cancelled'
        ELSE 'Unknown'
    END AS status_text,
    r.rfq_created_at
FROM rfq_master r
INNER JOIN project_master p ON r.rfq_project_id = p.proj_id;
GO

-- =============================================
-- View: vw_receiving_summary
-- =============================================
IF EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vw_receiving_summary]'))
    DROP VIEW [dbo].[vw_receiving_summary];
GO

CREATE VIEW [dbo].[vw_receiving_summary] AS
SELECT 
    ro.rorder_id,
    ro.rorder_slip_no,
    ro.rorder_date,
    po.porder_id,
    po.porder_no,
    p.proj_name,
    s.sup_name,
    ro.rorder_totalitem,
    ro.rorder_totalamount,
    CASE ro.rorder_status 
        WHEN 0 THEN 'Cancelled'
        WHEN 1 THEN 'Active'
        ELSE 'Unknown'
    END AS status_text
FROM receive_order_master ro
INNER JOIN purchase_order_master po ON ro.rorder_porder_ms = po.porder_id
INNER JOIN project_master p ON po.porder_project_ms = p.proj_id
INNER JOIN supplier_master s ON po.porder_supplier_ms = s.sup_id;
GO

-- =============================================
-- View: vw_supplier_performance
-- =============================================
IF EXISTS (SELECT * FROM sys.views WHERE object_id = OBJECT_ID(N'[dbo].[vw_supplier_performance]'))
    DROP VIEW [dbo].[vw_supplier_performance];
GO

CREATE VIEW [dbo].[vw_supplier_performance] AS
SELECT 
    s.sup_id,
    s.sup_name,
    s.sup_email,
    COUNT(DISTINCT po.porder_id) AS total_orders,
    SUM(po.porder_total_amount) AS total_order_value,
    COUNT(DISTINCT CASE WHEN po.porder_delivery_status = 1 THEN po.porder_id END) AS fully_received_orders,
    COUNT(DISTINCT CASE WHEN po.porder_delivery_status = 2 THEN po.porder_id END) AS partially_received_orders,
    COUNT(DISTINCT CASE WHEN po.porder_delivery_status = 0 THEN po.porder_id END) AS pending_orders,
    (SELECT COUNT(*) FROM supplier_catalog_tab WHERE supcat_supplier = s.sup_id) AS catalog_items
FROM supplier_master s
LEFT JOIN purchase_order_master po ON s.sup_id = po.porder_supplier_ms AND po.porder_status = 1
WHERE s.sup_status = 1
GROUP BY s.sup_id, s.sup_name, s.sup_email;
GO

PRINT 'Database optimization completed successfully!';
GO
