-- Phase 3.1: Multi-Tenancy Foundation - SQL Server Direct Execution
-- Execute this script manually via sqlcmd or SSMS

USE porder_db;
GO

-- 1. Create companies table
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'companies')
BEGIN
    CREATE TABLE companies (
        company_id BIGINT IDENTITY(1,1) PRIMARY KEY,
        company_name NVARCHAR(255) NOT NULL UNIQUE,
        company_code NVARCHAR(50) NOT NULL UNIQUE,
        email NVARCHAR(255) NULL,
        phone NVARCHAR(50) NULL,
        address NVARCHAR(MAX) NULL,
        status TINYINT NOT NULL DEFAULT 1, -- 1=active, 0=inactive
        settings NVARCHAR(MAX) NULL, -- JSON: timezone, currency, date_format
        subscription_tier NVARCHAR(50) NOT NULL DEFAULT 'free', -- free, pro, enterprise
        subscription_expires DATETIME2 NULL,
        logo_path NVARCHAR(255) NULL,
        created_by INT NULL,
        updated_by INT NULL,
        created_at DATETIME2 NOT NULL DEFAULT GETDATE(),
        updated_at DATETIME2 NOT NULL DEFAULT GETDATE()
    );
    
    CREATE INDEX idx_companies_status ON companies(status);
    CREATE INDEX idx_companies_tier ON companies(subscription_tier);
    
    PRINT 'Created companies table';
END
ELSE
BEGIN
    PRINT 'companies table already exists';
END
GO

-- 2. Add company_id to all tenant-scoped tables
DECLARE @tables TABLE (table_name NVARCHAR(255));
INSERT INTO @tables VALUES
    ('users'),
    ('project_master'),
    ('supplier_master'),
    ('purchase_order_master'),
    ('receive_order_master'),
    ('budget_master'),
    ('cost_code_master'),
    ('item_master'),
    ('item_category_master'),
    ('item_package_master'),
    ('purchase_order_details'),
    ('receive_order_details'),
    ('project_cost_codes'),
    ('budget_change_orders'),
    ('po_change_orders'),
    ('approval_workflows'),
    ('approval_requests'),
    ('project_roles'),
    ('accounting_integrations'),
    ('integration_sync_logs'),
    ('integration_field_mappings'),
    ('checklist_master'),
    ('checklist_performance'),
    ('procore_auth'),
    ('procore_sync_logs');

DECLARE @table_name NVARCHAR(255);
DECLARE @sql NVARCHAR(MAX);

DECLARE table_cursor CURSOR FOR SELECT table_name FROM @tables;
OPEN table_cursor;
FETCH NEXT FROM table_cursor INTO @table_name;

WHILE @@FETCH_STATUS = 0
BEGIN
    -- Check if table exists
    IF EXISTS (SELECT * FROM sys.tables WHERE name = @table_name)
    BEGIN
        -- Check if column doesn't already exist
        IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID(@table_name) AND name = 'company_id')
        BEGIN
            SET @sql = 'ALTER TABLE ' + @table_name + ' ADD company_id BIGINT NULL';
            EXEC sp_executesql @sql;
            
            SET @sql = 'CREATE INDEX idx_' + @table_name + '_company_id ON ' + @table_name + '(company_id)';
            EXEC sp_executesql @sql;
            
            PRINT 'Added company_id to ' + @table_name;
        END
        ELSE
        BEGIN
            PRINT 'company_id already exists in ' + @table_name;
        END
    END
    ELSE
    BEGIN
        PRINT 'Table ' + @table_name + ' does not exist - skipping';
    END
    
    FETCH NEXT FROM table_cursor INTO @table_name;
END

CLOSE table_cursor;
DEALLOCATE table_cursor;
GO

-- 3. Create default company and assign existing data
IF NOT EXISTS (SELECT * FROM companies WHERE company_code = 'DEFAULT')
BEGIN
    INSERT INTO companies (company_name, company_code, status, subscription_tier, created_at, updated_at)
    VALUES ('Default Company', 'DEFAULT', 1, 'enterprise', GETDATE(), GETDATE());
    
    DECLARE @default_company_id BIGINT = SCOPE_IDENTITY();
    
    PRINT 'Created default company with ID: ' + CAST(@default_company_id AS NVARCHAR);
    
    -- Assign all existing data to default company
    UPDATE users SET company_id = @default_company_id WHERE company_id IS NULL;
    UPDATE project_master SET company_id = @default_company_id WHERE company_id IS NULL;
    UPDATE supplier_master SET company_id = @default_company_id WHERE company_id IS NULL;
    UPDATE purchase_order_master SET company_id = @default_company_id WHERE company_id IS NULL;
    UPDATE receive_order_master SET company_id = @default_company_id WHERE company_id IS NULL;
    UPDATE budget_master SET company_id = @default_company_id WHERE company_id IS NULL;
    UPDATE cost_code_master SET company_id = @default_company_id WHERE company_id IS NULL;
    UPDATE item_master SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'item_category_master')
        UPDATE item_category_master SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'item_package_master')
        UPDATE item_package_master SET company_id = @default_company_id WHERE company_id IS NULL;
    
    UPDATE purchase_order_details SET company_id = @default_company_id WHERE company_id IS NULL;
    UPDATE receive_order_details SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'project_cost_codes')
        UPDATE project_cost_codes SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'budget_change_orders')
        UPDATE budget_change_orders SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'po_change_orders')
        UPDATE po_change_orders SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'approval_workflows')
        UPDATE approval_workflows SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'approval_requests')
        UPDATE approval_requests SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'project_roles')
        UPDATE project_roles SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'accounting_integrations')
        UPDATE accounting_integrations SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'integration_sync_logs')
        UPDATE integration_sync_logs SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'checklist_master')
        UPDATE checklist_master SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'checklist_performance')
        UPDATE checklist_performance SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'procore_auth')
        UPDATE procore_auth SET company_id = @default_company_id WHERE company_id IS NULL;
    
    IF EXISTS (SELECT * FROM sys.tables WHERE name = 'procore_sync_logs')
        UPDATE procore_sync_logs SET company_id = @default_company_id WHERE company_id IS NULL;
    
    PRINT 'Assigned all existing data to default company';
END
ELSE
BEGIN
    PRINT 'Default company already exists';
END
GO

-- 4. Create 2 test companies for multi-tenancy testing
IF NOT EXISTS (SELECT * FROM companies WHERE company_code = 'ACME')
BEGIN
    INSERT INTO companies (company_name, company_code, email, phone, status, subscription_tier, created_at, updated_at)
    VALUES 
        ('Acme Construction Co.', 'ACME', 'info@acmeconstruction.com', '555-0100', 1, 'pro', GETDATE(), GETDATE()),
        ('BuildRight LLC', 'BUILDRIGHT', 'contact@buildright.com', '555-0200', 1, 'enterprise', GETDATE(), GETDATE());
    
    PRINT 'Created test companies: Acme Construction Co. and BuildRight LLC';
END
ELSE
BEGIN
    PRINT 'Test companies already exist';
END
GO

-- Verification queries
PRINT '========================================';
PRINT 'VERIFICATION RESULTS';
PRINT '========================================';

SELECT 'Companies Created:' AS Info, COUNT(*) AS Count FROM companies;
SELECT company_id, company_name, company_code, subscription_tier, status FROM companies;

PRINT '';
PRINT 'Tables with company_id column:';
SELECT 
    t.name AS table_name,
    c.name AS column_name,
    ty.name AS data_type
FROM sys.tables t
INNER JOIN sys.columns c ON t.object_id = c.object_id
INNER JOIN sys.types ty ON c.user_type_id = ty.user_type_id
WHERE c.name = 'company_id'
ORDER BY t.name;

PRINT '';
PRINT 'Data assigned to default company:';
SELECT 'users' AS [Table], COUNT(*) AS records_with_company FROM users WHERE company_id IS NOT NULL
UNION ALL
SELECT 'project_master', COUNT(*) FROM project_master WHERE company_id IS NOT NULL
UNION ALL
SELECT 'supplier_master', COUNT(*) FROM supplier_master WHERE company_id IS NOT NULL
UNION ALL
SELECT 'purchase_order_master', COUNT(*) FROM purchase_order_master WHERE company_id IS NOT NULL
UNION ALL
SELECT 'cost_code_master', COUNT(*) FROM cost_code_master WHERE company_id IS NOT NULL
UNION ALL
SELECT 'budget_master', COUNT(*) FROM budget_master WHERE company_id IS NOT NULL;

PRINT '';
PRINT '========================================';
PRINT 'Phase 3.1 Migration Complete!';
PRINT '========================================';
GO
