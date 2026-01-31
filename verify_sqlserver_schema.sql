-- POApp SQL Server Schema Verification Script
-- Run this with: sqlcmd -S DESKTOP-Q2001NS\SQLEXPRESS -d porder_db -i verify_sqlserver_schema.sql

SET NOCOUNT ON;

DECLARE @count INT;
DECLARE @output VARCHAR(255);

PRINT '===========================================';
PRINT '  POApp Database Schema Verification';
PRINT '===========================================';
PRINT '';

-- Check Phase 1.3: Item Pricing
PRINT 'Phase 1.3: Item Pricing';
PRINT '------------------------';
IF OBJECT_ID('item_pricing', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM item_pricing;
    SET @output = '✓ item_pricing table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ item_pricing table NOT FOUND';
PRINT '';

-- Check Phase 1.4: RFQ System Tables
PRINT 'Phase 1.4: RFQ System';
PRINT '------------------------';

IF OBJECT_ID('rfqs', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM rfqs;
    SET @output = '✓ rfqs table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ rfqs table NOT FOUND';

IF OBJECT_ID('rfq_items', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM rfq_items;
    SET @output = '✓ rfq_items table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ rfq_items table NOT FOUND';

IF OBJECT_ID('rfq_suppliers', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM rfq_suppliers;
    SET @output = '✓ rfq_suppliers table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ rfq_suppliers table NOT FOUND';

IF OBJECT_ID('rfq_quotes', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM rfq_quotes;
    SET @output = '✓ rfq_quotes table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ rfq_quotes table NOT FOUND';
PRINT '';

-- Check Phase 1.5: Backorder Fields
PRINT 'Phase 1.5: Backorder Tracking';
PRINT '-------------------------------';
IF COL_LENGTH('purchase_order_details', 'backorder_qty') IS NOT NULL
    PRINT '✓ purchase_order_details.backorder_qty exists';
ELSE
    PRINT '✗ purchase_order_details.backorder_qty NOT FOUND';

IF COL_LENGTH('purchase_order_details', 'backorder_status') IS NOT NULL
    PRINT '✓ purchase_order_details.backorder_status exists';
ELSE
    PRINT '✗ purchase_order_details.backorder_status NOT FOUND';

IF COL_LENGTH('purchase_order_details', 'backorder_notes') IS NOT NULL
    PRINT '✓ purchase_order_details.backorder_notes exists';
ELSE
    PRINT '✗ purchase_order_details.backorder_notes NOT FOUND';
PRINT '';

-- Check Phase 2.1: Accounting Integration
PRINT 'Phase 2.1: Accounting Integration';
PRINT '----------------------------------';
IF OBJECT_ID('accounting_integrations', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM accounting_integrations;
    SET @output = '✓ accounting_integrations table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ accounting_integrations table NOT FOUND';

IF OBJECT_ID('integration_sync_logs', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM integration_sync_logs;
    SET @output = '✓ integration_sync_logs table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ integration_sync_logs table NOT FOUND';
PRINT '';

-- Check Phase 2.2: Budget Management Tables
PRINT 'Phase 2.2: Budget Management';
PRINT '-----------------------------';
IF OBJECT_ID('project_cost_codes', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM project_cost_codes;
    SET @output = '✓ project_cost_codes table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ project_cost_codes table NOT FOUND';

IF OBJECT_ID('budget_change_orders', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM budget_change_orders;
    SET @output = '✓ budget_change_orders table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ budget_change_orders table NOT FOUND';

IF OBJECT_ID('po_change_orders', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM po_change_orders;
    SET @output = '✓ po_change_orders table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ po_change_orders table NOT FOUND';

IF OBJECT_ID('approval_workflows', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM approval_workflows;
    SET @output = '✓ approval_workflows table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ approval_workflows table NOT FOUND';

IF OBJECT_ID('approval_requests', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM approval_requests;
    SET @output = '✓ approval_requests table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ approval_requests table NOT FOUND';

IF OBJECT_ID('project_roles', 'U') IS NOT NULL
BEGIN
    SELECT @count = COUNT(*) FROM project_roles;
    SET @output = '✓ project_roles table exists - Records: ' + CAST(@count AS VARCHAR);
    PRINT @output;
END
ELSE
    PRINT '✗ project_roles table NOT FOUND';
PRINT '';

-- Check Enhanced Budget Columns
PRINT 'Phase 2.2: Enhanced Budget Columns';
PRINT '------------------------------------';
IF COL_LENGTH('budget_master', 'original_amount') IS NOT NULL
    PRINT '✓ budget_master.original_amount exists';
ELSE
    PRINT '✗ budget_master.original_amount NOT FOUND';

IF COL_LENGTH('budget_master', 'committed') IS NOT NULL
    PRINT '✓ budget_master.committed exists';
ELSE
    PRINT '✗ budget_master.committed NOT FOUND';

IF COL_LENGTH('budget_master', 'actual') IS NOT NULL
    PRINT '✓ budget_master.actual exists';
ELSE
    PRINT '✗ budget_master.actual NOT FOUND';

IF COL_LENGTH('budget_master', 'variance') IS NOT NULL
    PRINT '✓ budget_master.variance exists';
ELSE
    PRINT '✗ budget_master.variance NOT FOUND';

IF COL_LENGTH('budget_master', 'warning_notification_sent') IS NOT NULL
    PRINT '✓ budget_master.warning_notification_sent exists';
ELSE
    PRINT '✗ budget_master.warning_notification_sent NOT FOUND';
PRINT '';

-- Check Cost Code Hierarchy Columns
PRINT 'Phase 2.2: Cost Code Hierarchy';
PRINT '--------------------------------';
IF COL_LENGTH('cost_code_master', 'parent_code') IS NOT NULL
    PRINT '✓ cost_code_master.parent_code exists';
ELSE
    PRINT '✗ cost_code_master.parent_code NOT FOUND';

IF COL_LENGTH('cost_code_master', 'full_code') IS NOT NULL
    PRINT '✓ cost_code_master.full_code exists';
ELSE
    PRINT '✗ cost_code_master.full_code NOT FOUND';

IF COL_LENGTH('cost_code_master', 'level') IS NOT NULL
    PRINT '✓ cost_code_master.level exists';
ELSE
    PRINT '✗ cost_code_master.level NOT FOUND';

IF COL_LENGTH('cost_code_master', 'sortorder') IS NOT NULL
    PRINT '✓ cost_code_master.sortorder exists';
ELSE
    PRINT '✗ cost_code_master.sortorder NOT FOUND';
PRINT '';

PRINT '===========================================';
PRINT '  Verification Complete';
PRINT '===========================================';
