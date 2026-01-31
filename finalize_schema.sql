-- Final fixes for schema updates
-- Run this with: sqlcmd -S DESKTOP-Q2001NS\SQLEXPRESS -d porder_db -E -i finalize_schema.sql

SET NOCOUNT ON;

PRINT '===========================================';
PRINT '  Final Schema Updates';
PRINT '===========================================';
PRINT '';

-- Update original_amount from budget_revised_amount
BEGIN TRANSACTION;
BEGIN TRY
    UPDATE budget_master 
    SET original_amount = ISNULL(budget_revised_amount, budget_original_amount) 
    WHERE original_amount IS NULL;
    
    PRINT '✓ Populated budget_master.original_amount from budget_revised_amount';
    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    ROLLBACK TRANSACTION;
    PRINT 'Error: ' + ERROR_MESSAGE();
END CATCH;
GO

-- Try creating rfqs table without foreign keys (can add later if needed)
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
            updated_at DATETIME NULL
        );
        
        CREATE INDEX IX_rfqs_company ON rfqs(company_id);
        CREATE INDEX IX_rfqs_project ON rfqs(project_id);
        CREATE INDEX IX_rfqs_status ON rfqs(status);
        
        PRINT '✓ rfqs table created (without FK constraints for now)';
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

PRINT '';
PRINT '===========================================';
PRINT '  Final Updates Complete';
PRINT '===========================================';
