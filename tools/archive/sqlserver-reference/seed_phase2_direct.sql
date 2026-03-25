-- Phase 2 Test Data Seeding - Direct SQL Script
-- Run with: sqlcmd -S "DESKTOP-Q2001NS\SQLEXPRESS" -d porder_db -E -i seed_phase2_direct.sql

SET NOCOUNT ON;

PRINT '================================================';
PRINT '  Phase 2 Test Data Seeding';
PRINT '================================================';
PRINT '';

-- Get IDs we need
DECLARE @company_id INT;
DECLARE @project_id INT;
DECLARE @user_id INT;

SELECT TOP 1 @company_id = id FROM companies;
SELECT TOP 1 @project_id = proj_id FROM project_master;
SELECT TOP 1 @user_id = id FROM users;

IF @company_id IS NULL
BEGIN
    PRINT '❌ No companies found. Create companies first.';
    RETURN;
END

IF @project_id IS NULL
BEGIN
    PRINT '❌ No projects found. Create projects first.';
    RETURN;
END

PRINT 'Company ID: ' + CAST(@company_id AS VARCHAR);
PRINT 'Project ID: ' + CAST(@project_id AS VARCHAR);
PRINT 'User ID: ' + CAST(@user_id AS VARCHAR);
PRINT '';

-- ==================================================
-- SEED PROJECT ROLES
-- ==================================================
PRINT 'Seeding Project Roles...';

IF NOT EXISTS (SELECT 1 FROM project_roles WHERE project_id = @project_id AND role_name = 'Project Manager')
BEGIN
    INSERT INTO project_roles (company_id, project_id, user_id, role_name, approval_limit, is_active, created_at)
    VALUES (@company_id, @project_id, @user_id, 'Project Manager', 10000.00, 1, GETDATE());
    PRINT '  ✓ Created role: Project Manager ($10,000 limit)';
END

IF NOT EXISTS (SELECT 1 FROM project_roles WHERE project_id = @project_id AND role_name = 'Finance Manager')
BEGIN
    INSERT INTO project_roles (company_id, project_id, user_id, role_name, approval_limit, is_active, created_at)
    VALUES (@company_id, @project_id, @user_id, 'Finance Manager', 50000.00, 1, GETDATE());
    PRINT '  ✓ Created role: Finance Manager ($50,000 limit)';
END

IF NOT EXISTS (SELECT 1 FROM project_roles WHERE project_id = @project_id AND role_name = 'Director')
BEGIN
    INSERT INTO project_roles (company_id, project_id, user_id, role_name, approval_limit, is_active, created_at)
    VALUES (@company_id, @project_id, @user_id, 'Director', 100000.00, 1, GETDATE());
    PRINT '  ✓ Created role: Director ($100,000 limit)';
END

IF NOT EXISTS (SELECT 1 FROM project_roles WHERE project_id = @project_id AND role_name = 'Executive')
BEGIN
    INSERT INTO project_roles (company_id, project_id, user_id, role_name, approval_limit, is_active, created_at)
    VALUES (@company_id, @project_id, @user_id, 'Executive', NULL, 1, GETDATE());
    PRINT '  ✓ Created role: Executive (Unlimited)';
END

PRINT '✓ Project roles seeded';
PRINT '';

-- ==================================================
-- SEED APPROVAL WORKFLOWS
-- ==================================================
PRINT 'Seeding Approval Workflows...';

IF NOT EXISTS (SELECT 1 FROM approval_workflows WHERE company_id = @company_id AND name = 'Standard PO Approval')
BEGIN
    INSERT INTO approval_workflows (company_id, project_id, name, entity_type, threshold_amount, is_role_based, approval_role, approval_level, is_active, created_at)
    VALUES (@company_id, @project_id, 'Standard PO Approval', 'purchase_order', 5000.00, 1, 'Project Manager', 1, 1, GETDATE());
    PRINT '  ✓ Standard PO Approval ($5,000)';
END

IF NOT EXISTS (SELECT 1 FROM approval_workflows WHERE company_id = @company_id AND name = 'Large PO Approval')
BEGIN
    INSERT INTO approval_workflows (company_id, project_id, name, entity_type, threshold_amount, is_role_based, approval_role, approval_level, is_active, created_at)
    VALUES (@company_id, @project_id, 'Large PO Approval', 'purchase_order', 25000.00, 1, 'Finance Manager', 2, 1, GETDATE());
    PRINT '  ✓ Large PO Approval ($25,000)';
END

IF NOT EXISTS (SELECT 1 FROM approval_workflows WHERE company_id = @company_id AND name = 'Executive PO Approval')
BEGIN
    INSERT INTO approval_workflows (company_id, project_id, name, entity_type, threshold_amount, is_role_based, approval_role, approval_level, is_active, created_at)
    VALUES (@company_id, @project_id, 'Executive PO Approval', 'purchase_order', 75000.00, 1, 'Executive', 3, 1, GETDATE());
    PRINT '  ✓ Executive PO Approval ($75,000)';
END

IF NOT EXISTS (SELECT 1 FROM approval_workflows WHERE company_id = @company_id AND name = 'Budget Change Order - Standard')
BEGIN
    INSERT INTO approval_workflows (company_id, project_id, name, entity_type, threshold_amount, is_role_based, approval_role, approval_level, is_active, created_at)
    VALUES (@company_id, @project_id, 'Budget Change Order - Standard', 'budget_change_order', 10000.00, 1, 'Finance Manager', 1, 1, GETDATE());
    PRINT '  ✓ BCO Standard ($10,000)';
END

IF NOT EXISTS (SELECT 1 FROM approval_workflows WHERE company_id = @company_id AND name = 'Budget Change Order - Large')
BEGIN
    INSERT INTO approval_workflows (company_id, project_id, name, entity_type, threshold_amount, is_role_based, approval_role, approval_level, is_active, created_at)
    VALUES (@company_id, @project_id, 'Budget Change Order - Large', 'budget_change_order', 50000.00, 1, 'Director', 2, 1, GETDATE());
    PRINT '  ✓ BCO Large ($50,000)';
END

IF NOT EXISTS (SELECT 1 FROM approval_workflows WHERE company_id = @company_id AND name = 'PO Change Order - Standard')
BEGIN
    INSERT INTO approval_workflows (company_id, project_id, name, entity_type, threshold_amount, is_role_based, approval_role, approval_level, is_active, created_at)
    VALUES (@company_id, @project_id, 'PO Change Order - Standard', 'po_change_order', 5000.00, 1, 'Project Manager', 1, 1, GETDATE());
    PRINT '  ✓ PCO Standard ($5,000)';
END

PRINT '✓ Approval workflows seeded';
PRINT '';

-- ==================================================
-- SEED SAMPLE BUDGETS
-- ==================================================
PRINT 'Seeding Sample Budgets...';

-- Get cost code IDs for budgets
DECLARE @cc1 INT, @cc2 INT, @cc3 INT, @cc4 INT, @cc5 INT;
SELECT @cc1 = cc_id FROM cost_code_master WHERE cc_no = '02-10-01';
SELECT @cc2 = cc_id FROM cost_code_master WHERE cc_no = '02-20-01';
SELECT @cc3 = cc_id FROM cost_code_master WHERE cc_no = '03-30-01';
SELECT @cc4 = cc_id FROM cost_code_master WHERE cc_no = '15-20-02';
SELECT @cc5 = cc_id FROM cost_code_master WHERE cc_no = '16-00-00';

-- Budget 1: Demolition (85% utilized - warning sent)
IF @cc1 IS NOT NULL AND NOT EXISTS (SELECT 1 FROM budget_master WHERE budget_project_id = @project_id AND budget_cost_code_id = @cc1)
BEGIN
    INSERT INTO budget_master (budget_project_id, budget_cost_code_id, budget_original_amount, budget_revised_amount, 
        original_amount, committed, actual, variance, warning_notification_sent, critical_notification_sent, budget_created_at)
    VALUES (@project_id, @cc1, 25000.00, 25000.00, 25000.00, 12000.00, 9250.00, 3750.00, 1, 0, GETDATE());
    PRINT '  ✓ Budget: Demolition - $25,000 (🟡 85% utilized)';
END

-- Budget 2: Excavation (80% utilized - warning sent)
IF @cc2 IS NOT NULL AND NOT EXISTS (SELECT 1 FROM budget_master WHERE budget_project_id = @project_id AND budget_cost_code_id = @cc2)
BEGIN
    INSERT INTO budget_master (budget_project_id, budget_cost_code_id, budget_original_amount, budget_revised_amount, 
        original_amount, committed, actual, variance, warning_notification_sent, critical_notification_sent, budget_created_at)
    VALUES (@project_id, @cc2, 50000.00, 55000.00, 50000.00, 28000.00, 16000.00, 11000.00, 1, 0, GETDATE());
    PRINT '  ✓ Budget: Excavation - $55,000 (🟡 80% utilized)';
END

-- Budget 3: Foundation Concrete (65% utilized - ok)
IF @cc3 IS NOT NULL AND NOT EXISTS (SELECT 1 FROM budget_master WHERE budget_project_id = @project_id AND budget_cost_code_id = @cc3)
BEGIN
    INSERT INTO budget_master (budget_project_id, budget_cost_code_id, budget_original_amount, budget_revised_amount, 
        original_amount, committed, actual, variance, warning_notification_sent, critical_notification_sent, budget_created_at)
    VALUES (@project_id, @cc3, 150000.00, 150000.00, 150000.00, 62000.00, 35500.00, 52500.00, 0, 0, GETDATE());
    PRINT '  ✓ Budget: Foundation Concrete - $150,000 (🟢 65% utilized)';
END

-- Budget 4: HVAC Equipment (91% utilized - critical sent)
IF @cc4 IS NOT NULL AND NOT EXISTS (SELECT 1 FROM budget_master WHERE budget_project_id = @project_id AND budget_cost_code_id = @cc4)
BEGIN
    INSERT INTO budget_master (budget_project_id, budget_cost_code_id, budget_original_amount, budget_revised_amount, 
        original_amount, committed, actual, variance, warning_notification_sent, critical_notification_sent, budget_created_at)
    VALUES (@project_id, @cc4, 75000.00, 80000.00, 75000.00, 52000.00, 21000.00, 7000.00, 1, 1, GETDATE());
    PRINT '  ✓ Budget: HVAC Equipment - $80,000 (🔴 91% utilized)';
END

-- Budget 5: Electrical (60% utilized - ok)
IF @cc5 IS NOT NULL AND NOT EXISTS (SELECT 1 FROM budget_master WHERE budget_project_id = @project_id AND budget_cost_code_id = @cc5)
BEGIN
    INSERT INTO budget_master (budget_project_id, budget_cost_code_id, budget_original_amount, budget_revised_amount, 
        original_amount, committed, actual, variance, warning_notification_sent, critical_notification_sent, budget_created_at)
    VALUES (@project_id, @cc5, 100000.00, 100000.00, 100000.00, 38000.00, 22000.00, 40000.00, 0, 0, GETDATE());
    PRINT '  ✓ Budget: Electrical - $100,000 (🟢 60% utilized)';
END

PRINT '✓ Sample budgets seeded';
PRINT '';

PRINT '================================================';
PRINT '✓ Phase 2 Test Data Seeding Complete!';
PRINT '================================================';
PRINT '';

-- Summary
DECLARE @role_count INT, @wf_count INT, @budget_count INT;
SELECT @role_count = COUNT(*) FROM project_roles WHERE project_id = @project_id;
SELECT @wf_count = COUNT(*) FROM approval_workflows WHERE company_id = @company_id;
SELECT @budget_count = COUNT(*) FROM budget_master WHERE budget_project_id = @project_id;

PRINT 'Summary:';
PRINT '  - Project Roles: ' + CAST(@role_count AS VARCHAR);
PRINT '  - Approval Workflows: ' + CAST(@wf_count AS VARCHAR);
PRINT '  - Sample Budgets: ' + CAST(@budget_count AS VARCHAR);
PRINT '';
PRINT 'Ready for testing!';
