-- POApp Database Verification Script for SQL Server
-- Database: porder_db on DESKTOP-Q2001NS\SQLEXPRESS
-- Run this to verify all Phase 1.1 through Phase 2.2 tables exist

USE porder_db;
GO

PRINT 'POApp Database Table Verification';
PRINT '=================================';
PRINT '';

-- Phase 1.1: Multi-Tenancy Foundation
PRINT 'Phase 1.1: Multi-Tenancy Tables';
PRINT '--------------------------------';
IF OBJECT_ID('companies', 'U') IS NOT NULL
    SELECT 'companies' AS TableName, COUNT(*) AS RecordCount FROM companies
ELSE
    PRINT '✗ companies table NOT FOUND';

IF OBJECT_ID('supplier_users', 'U') IS NOT NULL
    SELECT 'supplier_users' AS TableName, COUNT(*) AS RecordCount FROM supplier_users
ELSE
    PRINT '✗ supplier_users table NOT FOUND';
PRINT '';

-- Phase 1.3: Item Pricing
PRINT 'Phase 1.3: Item Pricing Tables';
PRINT '-------------------------------';
IF OBJECT_ID('item_pricing', 'U') IS NOT NULL
    SELECT 'item_pricing' AS TableName, COUNT(*) AS RecordCount FROM item_pricing
ELSE
    PRINT '✗ item_pricing table NOT FOUND';
PRINT '';

-- Phase 1.4: RFQ System
PRINT 'Phase 1.4: RFQ System Tables';
PRINT '-----------------------------';
IF OBJECT_ID('rfqs', 'U') IS NOT NULL
    SELECT 'rfqs' AS TableName, COUNT(*) AS RecordCount FROM rfqs
ELSE
    PRINT '✗ rfqs table NOT FOUND';

IF OBJECT_ID('rfq_items', 'U') IS NOT NULL
    SELECT 'rfq_items' AS TableName, COUNT(*) AS RecordCount FROM rfq_items
ELSE
    PRINT '✗ rfq_items table NOT FOUND';

IF OBJECT_ID('rfq_suppliers', 'U') IS NOT NULL
    SELECT 'rfq_suppliers' AS TableName, COUNT(*) AS RecordCount FROM rfq_suppliers
ELSE
    PRINT '✗ rfq_suppliers table NOT FOUND';

IF OBJECT_ID('rfq_quotes', 'U') IS NOT NULL
    SELECT 'rfq_quotes' AS TableName, COUNT(*) AS RecordCount FROM rfq_quotes
ELSE
    PRINT '✗ rfq_quotes table NOT FOUND';
PRINT '';

-- Phase 1.5: Backorder Fields
PRINT 'Phase 1.5: Backorder Fields in purchase_order_details';
PRINT '-----------------------------------------------------';
IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('purchase_order_details') AND name = 'backorder_qty')
    PRINT '✓ backorder_qty column EXISTS'
ELSE
    PRINT '✗ backorder_qty column NOT FOUND';

IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('purchase_order_details') AND name = 'backorder_status')
    PRINT '✓ backorder_status column EXISTS'
ELSE
    PRINT '✗ backorder_status column NOT FOUND';

IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('purchase_order_details') AND name = 'backorder_notes')
    PRINT '✓ backorder_notes column EXISTS'
ELSE
    PRINT '✗ backorder_notes column NOT FOUND';
PRINT '';

-- Phase 2.1: Accounting Integrations
PRINT 'Phase 2.1: Accounting Integration Tables';
PRINT '-----------------------------------------';
IF OBJECT_ID('accounting_integrations', 'U') IS NOT NULL
    SELECT 'accounting_integrations' AS TableName, COUNT(*) AS RecordCount FROM accounting_integrations
ELSE
    PRINT '✗ accounting_integrations table NOT FOUND';

IF OBJECT_ID('integration_sync_logs', 'U') IS NOT NULL
    SELECT 'integration_sync_logs' AS TableName, COUNT(*) AS RecordCount FROM integration_sync_logs
ELSE
    PRINT '✗ integration_sync_logs table NOT FOUND';

IF OBJECT_ID('integration_field_mappings', 'U') IS NOT NULL
    SELECT 'integration_field_mappings' AS TableName, COUNT(*) AS RecordCount FROM integration_field_mappings
ELSE
    PRINT '✗ integration_field_mappings table NOT FOUND';
PRINT '';

-- Phase 2.2: Budget Management
PRINT 'Phase 2.2: Budget Management Tables';
PRINT '------------------------------------';
IF OBJECT_ID('project_cost_codes', 'U') IS NOT NULL
    SELECT 'project_cost_codes' AS TableName, COUNT(*) AS RecordCount FROM project_cost_codes
ELSE
    PRINT '✗ project_cost_codes table NOT FOUND';

IF OBJECT_ID('budget_change_orders', 'U') IS NOT NULL
    SELECT 'budget_change_orders' AS TableName, COUNT(*) AS RecordCount FROM budget_change_orders
ELSE
    PRINT '✗ budget_change_orders table NOT FOUND';

IF OBJECT_ID('po_change_orders', 'U') IS NOT NULL
    SELECT 'po_change_orders' AS TableName, COUNT(*) AS RecordCount FROM po_change_orders
ELSE
    PRINT '✗ po_change_orders table NOT FOUND';

IF OBJECT_ID('approval_workflows', 'U') IS NOT NULL
    SELECT 'approval_workflows' AS TableName, COUNT(*) AS RecordCount FROM approval_workflows
ELSE
    PRINT '✗ approval_workflows table NOT FOUND';

IF OBJECT_ID('approval_requests', 'U') IS NOT NULL
    SELECT 'approval_requests' AS TableName, COUNT(*) AS RecordCount FROM approval_requests
ELSE
    PRINT '✗ approval_requests table NOT FOUND';

IF OBJECT_ID('project_roles', 'U') IS NOT NULL
    SELECT 'project_roles' AS TableName, COUNT(*) AS RecordCount FROM project_roles
ELSE
    PRINT '✗ project_roles table NOT FOUND';

-- Check budget_master enhancements
PRINT '';
PRINT 'Phase 2.2: Budget Master Enhancements';
PRINT '--------------------------------------';
IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('budget_master') AND name = 'original_amount')
    PRINT '✓ budget_master.original_amount column EXISTS'
ELSE
    PRINT '✗ budget_master.original_amount column NOT FOUND';

IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('budget_master') AND name = 'change_orders_total')
    PRINT '✓ budget_master.change_orders_total column EXISTS'
ELSE
    PRINT '✗ budget_master.change_orders_total column NOT FOUND';

IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('budget_master') AND name = 'committed')
    PRINT '✓ budget_master.committed column EXISTS'
ELSE
    PRINT '✗ budget_master.committed column NOT FOUND';

IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('budget_master') AND name = 'actual')
    PRINT '✓ budget_master.actual column EXISTS'
ELSE
    PRINT '✗ budget_master.actual column NOT FOUND';

-- Check cost_code_master hierarchical fields
PRINT '';
PRINT 'Phase 2.2: Cost Code Hierarchy Fields';
PRINT '--------------------------------------';
IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('cost_code_master') AND name = 'parent_code')
    PRINT '✓ cost_code_master.parent_code column EXISTS'
ELSE
    PRINT '✗ cost_code_master.parent_code column NOT FOUND';

IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('cost_code_master') AND name = 'full_code')
    PRINT '✓ cost_code_master.full_code column EXISTS'
ELSE
    PRINT '✗ cost_code_master.full_code column NOT FOUND';

IF EXISTS (SELECT 1 FROM sys.columns WHERE object_id = OBJECT_ID('cost_code_master') AND name = 'level')
    PRINT '✓ cost_code_master.level column EXISTS'
ELSE
    PRINT '✗ cost_code_master.level column NOT FOUND';

PRINT '';
PRINT 'Verification Complete!';
PRINT '=================================';
GO
