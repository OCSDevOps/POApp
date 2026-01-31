-- Phase 3.1: Update existing companies table for multi-tenancy
USE porder_db;
GO

-- Add missing columns to existing companies table
ALTER TABLE companies ADD company_id AS id; -- Computed column for compatibility
ALTER TABLE companies ADD company_name AS name; -- Computed column for compatibility

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('companies') AND name = 'company_code')
    ALTER TABLE companies ADD company_code NVARCHAR(50) NULL;

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('companies') AND name = 'email')
    ALTER TABLE companies ADD email NVARCHAR(255) NULL;

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('companies') AND name = 'phone')
    ALTER TABLE companies ADD phone NVARCHAR(50) NULL;

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('companies') AND name = 'address')
    ALTER TABLE companies ADD address NVARCHAR(MAX) NULL;

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('companies') AND name = 'subscription_tier')
    ALTER TABLE companies ADD subscription_tier NVARCHAR(50) NOT NULL DEFAULT 'free';

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('companies') AND name = 'subscription_expires')
    ALTER TABLE companies ADD subscription_expires DATETIME2 NULL;

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('companies') AND name = 'logo_path')
    ALTER TABLE companies ADD logo_path NVARCHAR(255) NULL;

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('companies') AND name = 'created_by')
    ALTER TABLE companies ADD created_by INT NULL;

IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('companies') AND name = 'updated_by')
    ALTER TABLE companies ADD updated_by INT NULL;

-- Update existing company with default values
UPDATE companies 
SET 
    company_code = ISNULL(company_code, 'DEFAULT'),
    subscription_tier = ISNULL(subscription_tier, 'enterprise')
WHERE id = 1;

-- Create test companies if they don't exist
IF NOT EXISTS (SELECT * FROM companies WHERE company_code = 'ACME')
BEGIN
    INSERT INTO companies (name, subdomain, status, company_code, email, phone, subscription_tier, created_at, updated_at)
    VALUES 
        ('Acme Construction Co.', 'acme', 1, 'ACME', 'info@acmeconstruction.com', '555-0100', 'pro', GETDATE(), GETDATE()),
        ('BuildRight LLC', 'buildright', 1, 'BUILDRIGHT', 'contact@buildright.com', '555-0200', 'enterprise', GETDATE(), GETDATE());
    
    PRINT 'Created test companies';
END
ELSE
BEGIN
    PRINT 'Test companies already exist';
END
GO

-- Get default company ID
DECLARE @default_company_id BIGINT = (SELECT TOP 1 id FROM companies ORDER BY id);

-- Assign all existing data to default company
UPDATE users SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE project_master SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE supplier_master SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE purchase_order_master SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE receive_order_master SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE budget_master SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE cost_code_master SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE item_master SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE item_package_master SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE purchase_order_details SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE receive_order_details SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE project_cost_codes SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE budget_change_orders SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE po_change_orders SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE approval_workflows SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE approval_requests SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE project_roles SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE accounting_integrations SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE integration_sync_logs SET company_id = @default_company_id WHERE company_id IS NULL;
UPDATE integration_field_mappings SET company_id = @default_company_id WHERE company_id IS NULL;

PRINT 'Assigned all existing data to default company (ID: ' + CAST(@default_company_id AS NVARCHAR) + ')';
GO

-- Verification
PRINT '========================================';
PRINT 'Companies in system:';
SELECT id, name, company_code, subscription_tier, status FROM companies;

PRINT '';
PRINT 'Sample data counts assigned to company:';
SELECT 
    'users' AS [Table], 
    COUNT(*) AS total_records,
    COUNT(company_id) AS records_with_company,
    (SELECT TOP 1 company_id FROM users WHERE company_id IS NOT NULL) AS sample_company_id
FROM users
UNION ALL
SELECT 'projects', COUNT(*), COUNT(company_id), (SELECT TOP 1 company_id FROM project_master WHERE company_id IS NOT NULL) FROM project_master
UNION ALL
SELECT 'purchase_orders', COUNT(*), COUNT(company_id), (SELECT TOP 1 company_id FROM purchase_order_master WHERE company_id IS NOT NULL) FROM purchase_order_master
UNION ALL
SELECT 'budgets', COUNT(*), COUNT(company_id), (SELECT TOP 1 company_id FROM budget_master WHERE company_id IS NOT NULL) FROM budget_master;

PRINT '';
PRINT '========================================';
PRINT 'Phase 3.1 Complete - Multi-Tenancy Foundation Ready!';
PRINT '========================================';
GO
