-- Fixed Missing Schema Updates for POApp SQL Server Database
-- Correct table names: purchase_order_details, cost_code_master (with cc_no not cc_code)
-- Run this with: sqlcmd -S DESKTOP-Q2001NS\SQLEXPRESS -d porder_db -E -i apply_missing_schema_fixed.sql

SET NOCOUNT ON;

PRINT '===========================================';
PRINT '  Applying Missing Schema Updates';
PRINT '  (Fixed table and column names)';
PRINT '===========================================';
PRINT '';

-- ======================================
-- Phase 1.4: Create rfqs table
-- ======================================
BEGIN TRANSACTION;
BEGIN TRY
    IF OBJECT_ID('rfqs', 'U') IS NULL
    BEGIN
        PRINT 'Creating rfqs table...';
        
        CREATE TABLE rfqs (
            id INT IDENTITY(1,1) PRIMARY KEY,
            company_id INT NOT NULL,
            rfq_number NVARCHAR(50) NOT NULL,
            project_id INT NOT NULL,
            title NVARCHAR(255) NOT NULL,
            description NVARCHAR(MAX) NULL,
            due_date DATE NULL,
            status NVARCHAR(20) NOT NULL DEFAULT 'draft',
            created_by INT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT GETDATE(),
            updated_at DATETIME NULL,
            CONSTRAINT FK_rfqs_company FOREIGN KEY (company_id) REFERENCES companies(id),
            CONSTRAINT FK_rfqs_project FOREIGN KEY (project_id) REFERENCES project_master(proj_id),
            CONSTRAINT FK_rfqs_created_by FOREIGN KEY (created_by) REFERENCES user_master(u_id)
        );
        
        CREATE INDEX IX_rfqs_company ON rfqs(company_id);
        CREATE INDEX IX_rfqs_project ON rfqs(project_id);
        CREATE INDEX IX_rfqs_status ON rfqs(status);
        
        PRINT '✓ rfqs table created';
    END
    ELSE
        PRINT '- rfqs table already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error creating rfqs: ' + ERROR_MESSAGE();
END CATCH;
GO

-- ======================================
-- Phase 1.5: Add backorder fields to purchase_order_details
-- ======================================
PRINT '';
PRINT 'Adding backorder fields to purchase_order_details...';

BEGIN TRANSACTION;
BEGIN TRY
    IF COL_LENGTH('purchase_order_details', 'backorder_qty') IS NULL
    BEGIN
        ALTER TABLE purchase_order_details ADD backorder_qty DECIMAL(10,2) NULL DEFAULT 0;
        PRINT '✓ backorder_qty added';
    END
    ELSE
        PRINT '- backorder_qty already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error adding backorder_qty: ' + ERROR_MESSAGE();
END CATCH;
GO

BEGIN TRANSACTION;
BEGIN TRY
    IF COL_LENGTH('purchase_order_details', 'backorder_status') IS NULL
    BEGIN
        ALTER TABLE purchase_order_details ADD backorder_status NVARCHAR(20) NULL;
        PRINT '✓ backorder_status added';
    END
    ELSE
        PRINT '- backorder_status already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error adding backorder_status: ' + ERROR_MESSAGE();
END CATCH;
GO

BEGIN TRANSACTION;
BEGIN TRY
    IF COL_LENGTH('purchase_order_details', 'backorder_notes') IS NULL
    BEGIN
        ALTER TABLE purchase_order_details ADD backorder_notes NVARCHAR(MAX) NULL;
        PRINT '✓ backorder_notes added';
    END
    ELSE
        PRINT '- backorder_notes already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error adding backorder_notes: ' + ERROR_MESSAGE();
END CATCH;
GO

-- ======================================
-- Phase 2.2: Enhance budget_master table
-- ======================================
PRINT '';
PRINT 'Adding enhanced columns to budget_master...';

BEGIN TRANSACTION;
BEGIN TRY
    IF COL_LENGTH('budget_master', 'original_amount') IS NULL
    BEGIN
        ALTER TABLE budget_master ADD original_amount DECIMAL(15,2) NULL;
        PRINT '✓ original_amount added';
    END
    ELSE
        PRINT '- original_amount already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error adding original_amount: ' + ERROR_MESSAGE();
END CATCH;
GO

-- Update existing records with original amounts
BEGIN TRANSACTION;
BEGIN TRY
    UPDATE budget_master SET original_amount = ISNULL(budgeted_amount, 0) WHERE original_amount IS NULL;
    PRINT '✓ Populated original_amount from budgeted_amount';
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error updating original_amount: ' + ERROR_MESSAGE();
END CATCH;
GO

-- Add variance column as nullable (can't be computed if original_amount was just added)
BEGIN TRANSACTION;
BEGIN TRY
    IF COL_LENGTH('budget_master', 'variance') IS NULL
    BEGIN
        ALTER TABLE budget_master ADD variance DECIMAL(15,2) NULL;
        PRINT '✓ variance added (as nullable column)';
    END
    ELSE
        PRINT '- variance already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error adding variance: ' + ERROR_MESSAGE();
END CATCH;
GO

-- ======================================
-- Phase 2.2: Enhance cost_code_master table
-- ======================================
PRINT '';
PRINT 'Adding hierarchy columns to cost_code_master...';

BEGIN TRANSACTION;
BEGIN TRY
    IF COL_LENGTH('cost_code_master', 'parent_code') IS NULL
    BEGIN
        ALTER TABLE cost_code_master ADD parent_code NVARCHAR(50) NULL;
        PRINT '✓ parent_code added';
    END
    ELSE
        PRINT '- parent_code already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error adding parent_code: ' + ERROR_MESSAGE();
END CATCH;
GO

BEGIN TRANSACTION;
BEGIN TRY
    IF COL_LENGTH('cost_code_master', 'full_code') IS NULL
    BEGIN
        ALTER TABLE cost_code_master ADD full_code NVARCHAR(50) NULL;
        PRINT '✓ full_code added';
    END
    ELSE
        PRINT '- full_code already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error adding full_code: ' + ERROR_MESSAGE();
END CATCH;
GO

-- Populate full_code from existing cc_no values
BEGIN TRANSACTION;
BEGIN TRY
    UPDATE cost_code_master SET full_code = cc_no WHERE full_code IS NULL;
    PRINT '✓ Populated full_code from cc_no';
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error populating full_code: ' + ERROR_MESSAGE();
END CATCH;
GO

BEGIN TRANSACTION;
BEGIN TRY
    IF COL_LENGTH('cost_code_master', 'level') IS NULL
    BEGIN
        ALTER TABLE cost_code_master ADD level INT NOT NULL DEFAULT 1;
        PRINT '✓ level added';
    END
    ELSE
        PRINT '- level already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error adding level: ' + ERROR_MESSAGE();
END CATCH;
GO

BEGIN TRANSACTION;
BEGIN TRY
    IF COL_LENGTH('cost_code_master', 'sortorder') IS NULL
    BEGIN
        ALTER TABLE cost_code_master ADD sortorder INT NULL;
        PRINT '✓ sortorder added';
    END
    ELSE
        PRINT '- sortorder already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error adding sortorder: ' + ERROR_MESSAGE();
END CATCH;
GO

BEGIN TRANSACTION;
BEGIN TRY
    IF COL_LENGTH('cost_code_master', 'is_active') IS NULL
    BEGIN
        ALTER TABLE cost_code_master ADD is_active BIT NOT NULL DEFAULT 1;
        PRINT '✓ is_active added';
    END
    ELSE
        PRINT '- is_active already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error adding is_active: ' + ERROR_MESSAGE();
END CATCH;
GO

-- ======================================
-- Create indexes for performance
-- ======================================
PRINT '';
PRINT 'Creating indexes...';

BEGIN TRANSACTION;
BEGIN TRY
    IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_cost_code_master_parent' AND object_id = OBJECT_ID('cost_code_master'))
    BEGIN
        CREATE INDEX IX_cost_code_master_parent ON cost_code_master(parent_code);
        PRINT '✓ Index on cost_code_master.parent_code created';
    END
    ELSE
        PRINT '- Index IX_cost_code_master_parent already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error creating parent_code index: ' + ERROR_MESSAGE();
END CATCH;
GO

BEGIN TRANSACTION;
BEGIN TRY
    IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_cost_code_master_full_code' AND object_id = OBJECT_ID('cost_code_master'))
    BEGIN
        CREATE INDEX IX_cost_code_master_full_code ON cost_code_master(full_code);
        PRINT '✓ Index on cost_code_master.full_code created';
    END
    ELSE
        PRINT '- Index IX_cost_code_master_full_code already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error creating full_code index: ' + ERROR_MESSAGE();
END CATCH;
GO

BEGIN TRANSACTION;
BEGIN TRY
    IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_purchase_order_details_backorder' AND object_id = OBJECT_ID('purchase_order_details'))
    BEGIN
        CREATE INDEX IX_purchase_order_details_backorder ON purchase_order_details(backorder_status);
        PRINT '✓ Index on purchase_order_details.backorder_status created';
    END
    ELSE
        PRINT '- Index IX_purchase_order_details_backorder already exists';
    
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error creating backorder_status index: ' + ERROR_MESSAGE();
END CATCH;
GO

PRINT '';
PRINT '===========================================';
PRINT '  Schema Updates Completed Successfully!';
PRINT '===========================================';
