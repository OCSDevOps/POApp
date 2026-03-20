-- Missing Schema Updates for POApp SQL Server Database
-- Run this with: sqlcmd -S DESKTOP-Q2001NS\SQLEXPRESS -d porder_db -E -i apply_missing_schema.sql

SET NOCOUNT ON;

PRINT '===========================================';
PRINT '  Applying Missing Schema Updates';
PRINT '===========================================';
PRINT '';

BEGIN TRANSACTION;

BEGIN TRY

    -- ======================================
    -- Phase 1.4: Create rfqs table
    -- ======================================
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
    PRINT '';

    -- ======================================
    -- Phase 1.5: Add backorder fields to purchase_order_details
    -- ======================================
    PRINT 'Adding backorder fields to purchase_order_details...';
    
    IF COL_LENGTH('purchase_order_details', 'backorder_qty') IS NULL
    BEGIN
        ALTER TABLE purchase_order_details ADD backorder_qty DECIMAL(10,2) NULL DEFAULT 0;
        PRINT '✓ backorder_qty added';
    END
    ELSE
        PRINT '- backorder_qty already exists';
    
    IF COL_LENGTH('purchase_order_details', 'backorder_status') IS NULL
    BEGIN
        ALTER TABLE purchase_order_details ADD backorder_status NVARCHAR(20) NULL;
        PRINT '✓ backorder_status added';
    END
    ELSE
        PRINT '- backorder_status already exists';
    
    IF COL_LENGTH('purchase_order_details', 'backorder_notes') IS NULL
    BEGIN
        ALTER TABLE purchase_order_details ADD backorder_notes NVARCHAR(MAX) NULL;
        PRINT '✓ backorder_notes added';
    END
    ELSE
        PRINT '- backorder_notes already exists';
    PRINT '';

    -- ======================================
    -- Phase 2.2: Enhance budget_master table
    -- ======================================
    PRINT 'Adding enhanced columns to budget_master...';
    
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
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
END CATCH;
GO

BEGIN TRANSACTION;
BEGIN TRY
    
    IF COL_LENGTH('budget_master', 'committed') IS NULL
    BEGIN
        ALTER TABLE budget_master ADD committed DECIMAL(15,2) NOT NULL DEFAULT 0;
        PRINT '✓ committed added';
    END
    ELSE
        PRINT '- committed already exists';
    
    IF COL_LENGTH('budget_master', 'actual') IS NULL
    BEGIN
        ALTER TABLE budget_master ADD actual DECIMAL(15,2) NOT NULL DEFAULT 0;
        PRINT '✓ actual added';
    END
    ELSE
        PRINT '- actual already exists';
    
    -- Note: variance is a computed column - will be added later if needed
    -- or handle it in the application layer
    
    IF COL_LENGTH('budget_master', 'warning_notification_sent') IS NULL
    BEGIN
        ALTER TABLE budget_master ADD warning_notification_sent BIT NOT NULL DEFAULT 0;
        PRINT '✓ warning_notification_sent added';
    END
    ELSE
        PRINT '- warning_notification_sent already exists';
    
    IF COL_LENGTH('budget_master', 'critical_notification_sent') IS NULL
    BEGIN
        ALTER TABLE budget_master ADD critical_notification_sent BIT NOT NULL DEFAULT 0;
        PRINT '✓ critical_notification_sent added';
    END
    ELSE
        PRINT '- critical_notification_sent already exists';
    PRINT '';

    -- ======================================
    -- Phase 2.2: Enhance cost_code_master table
    -- ======================================
    PRINT 'Adding hierarchy columns to cost_code_master...';
    
    IF COL_LENGTH('cost_code_master', 'parent_code') IS NULL
    BEGIN
        ALTER TABLE cost_code_master ADD parent_code NVARCHAR(50) NULL;
        PRINT '✓ parent_code added';
    END
    ELSE
        PRINT '- parent_code already exists';
    
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

-- Populate full_code from existing cc_code values
BEGIN TRANSACTION;
BEGIN TRY
    UPDATE cost_code_master SET full_code = cc_code WHERE full_code IS NULL;
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
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
    
    IF COL_LENGTH('cost_code_master', 'sortorder') IS NULL
    BEGIN
        ALTER TABLE cost_code_master ADD sortorder INT NULL;
        PRINT '✓ sortorder added';
    END
    ELSE
        PRINT '- sortorder already exists';
    
    IF COL_LENGTH('cost_code_master', 'is_active') IS NULL
    BEGIN
        ALTER TABLE cost_code_master ADD is_active BIT NOT NULL DEFAULT 1;
        PRINT '✓ is_active added';
    END
    ELSE
        PRINT '- is_active already exists';
    PRINT '';

    -- Create indexes for better query performance
    PRINT 'Creating indexes...';
    
    IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_cost_code_master_parent' AND object_id = OBJECT_ID('cost_code_master'))
    BEGIN
        CREATE INDEX IX_cost_code_master_parent ON cost_code_master(parent_code);
        PRINT '✓ Index on cost_code_master.parent_code created';
    END
    
    IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_cost_code_master_full_code' AND object_id = OBJECT_ID('cost_code_master'))
    BEGIN
        CREATE INDEX IX_cost_code_master_full_code ON cost_code_master(full_code);
        PRINT '✓ Index on cost_code_master.full_code created';
    END
    
    IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'IX_purchase_order_details_backorder' AND object_id = OBJECT_ID('purchase_order_details'))
    BEGIN
        CREATE INDEX IX_purchase_order_details_backorder ON purchase_order_details(backorder_status);
        PRINT '✓ Index on purchase_order_details.backorder_status created';
    END
    PRINT '';

    COMMIT TRANSACTION;
    
    PRINT '===========================================';
    PRINT '  Schema Updates Completed Successfully!';
    PRINT '===========================================';

END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    
    PRINT '';
    PRINT '===========================================';
    PRINT '  ERROR OCCURRED!';
    PRINT '===========================================';
    PRINT 'Error Message: ' + ERROR_MESSAGE();
    PRINT 'Error Line: ' + CAST(ERROR_LINE() AS NVARCHAR);
    
END CATCH;
