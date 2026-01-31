-- Phase 3.2: Seed test data for multi-tenancy validation
USE porder_db;
GO

PRINT '========================================';
PRINT 'Phase 3.2: Multi-Tenancy Test Data Seeder';
PRINT '========================================';
PRINT '';

-- Get company IDs
DECLARE @testCompanyId BIGINT = (SELECT id FROM companies WHERE subdomain = 'test');
DECLARE @acmeCompanyId BIGINT = (SELECT id FROM companies WHERE subdomain = 'acme');

IF @testCompanyId IS NULL OR @acmeCompanyId IS NULL
BEGIN
    PRINT 'Error: Test companies not found. Run Phase 3.1 SQL script first.';
    RETURN;
END

PRINT 'Companies found:';
PRINT '  Test Construction Co: ID = ' + CAST(@testCompanyId AS NVARCHAR);
PRINT '  Acme Builders Inc: ID = ' + CAST(@acmeCompanyId AS NVARCHAR);
PRINT '';

-- Seed test users
PRINT '========================================';
PRINT 'Seeding test users...';
PRINT '========================================';

-- Test Construction Co users
IF NOT EXISTS (SELECT * FROM users WHERE email = 'john.smith@testconstruction.com')
BEGIN
    INSERT INTO users (name, username, email, password, company_id, created_at, updated_at)
    VALUES ('John Smith', 'john.smith', 'john.smith@testconstruction.com', 
            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
            @testCompanyId, GETDATE(), GETDATE());
    PRINT '  ✓ Created user: John Smith';
END

IF NOT EXISTS (SELECT * FROM users WHERE email = 'sarah.johnson@testconstruction.com')
BEGIN
    INSERT INTO users (name, username, email, password, company_id, created_at, updated_at)
    VALUES ('Sarah Johnson', 'sarah.johnson', 'sarah.johnson@testconstruction.com',
            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            @testCompanyId, GETDATE(), GETDATE());
    PRINT '  ✓ Created user: Sarah Johnson';
END

-- Acme Builders users
IF NOT EXISTS (SELECT * FROM users WHERE email = 'mike.davis@acmebuilders.com')
BEGIN
    INSERT INTO users (name, username, email, password, company_id, created_at, updated_at)
    VALUES ('Mike Davis', 'mike.davis', 'mike.davis@acmebuilders.com',
            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            @acmeCompanyId, GETDATE(), GETDATE());
    PRINT '  ✓ Created user: Mike Davis';
END

IF NOT EXISTS (SELECT * FROM users WHERE email = 'emily.chen@acmebuilders.com')
BEGIN
    INSERT INTO users (name, username, email, password, company_id, created_at, updated_at)
    VALUES ('Emily Chen', 'emily.chen', 'emily.chen@acmebuilders.com',
            '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            @acmeCompanyId, GETDATE(), GETDATE());
    PRINT '  ✓ Created user: Emily Chen';
END

PRINT '';

-- Seed test suppliers
PRINT '========================================';
PRINT 'Seeding test suppliers...';
PRINT '========================================';

-- Test Construction Co suppliers
IF NOT EXISTS (SELECT * FROM supplier_master WHERE sup_email = 'sales@abcsupply.com')
BEGIN
    INSERT INTO supplier_master (sup_name, sup_address, sup_email, sup_phone, sup_status, company_id)
    VALUES ('ABC Supply Co', '123 Supply Street', 'sales@abcsupply.com', '555-1000', 1, @testCompanyId);
    PRINT '  ✓ Created supplier: ABC Supply Co';
END

IF NOT EXISTS (SELECT * FROM supplier_master WHERE sup_email = 'orders@buildmart.com')
BEGIN
    INSERT INTO supplier_master (sup_name, sup_address, sup_email, sup_phone, sup_status, company_id)
    VALUES ('BuildMart Wholesale', '456 Warehouse Blvd', 'orders@buildmart.com', '555-2000', 1, @testCompanyId);
    PRINT '  ✓ Created supplier: BuildMart Wholesale';
END

-- Acme Builders suppliers
IF NOT EXISTS (SELECT * FROM supplier_master WHERE sup_email = 'info@premiermaterials.com')
BEGIN
    INSERT INTO supplier_master (sup_name, sup_address, sup_email, sup_phone, sup_status, company_id)
    VALUES ('Premier Materials Inc', '789 Industrial Pkwy', 'info@premiermaterials.com', '555-3000', 1, @acmeCompanyId);
    PRINT '  ✓ Created supplier: Premier Materials Inc';
END

IF NOT EXISTS (SELECT * FROM supplier_master WHERE sup_email = 'sales@qualitylumber.com')
BEGIN
    INSERT INTO supplier_master (sup_name, sup_address, sup_email, sup_phone, sup_status, company_id)
    VALUES ('Quality Lumber & Hardware', '321 Lumber Lane', 'sales@qualitylumber.com', '555-4000', 1, @acmeCompanyId);
    PRINT '  ✓ Created supplier: Quality Lumber & Hardware';
END

PRINT '';

-- Seed test projects
PRINT '========================================';
PRINT 'Seeding test projects...';
PRINT '========================================';

-- Test Construction Co projects
IF NOT EXISTS (SELECT * FROM project_master WHERE proj_name = 'Downtown Office Building' AND company_id = @testCompanyId)
BEGIN
    INSERT INTO project_master (proj_name, proj_address, proj_status, company_id)
    VALUES ('Downtown Office Building', '100 Main Street', 1, @testCompanyId);
    PRINT '  ✓ Created project: Downtown Office Building';
END

IF NOT EXISTS (SELECT * FROM project_master WHERE proj_name = 'Riverside Shopping Center' AND company_id = @testCompanyId)
BEGIN
    INSERT INTO project_master (proj_name, proj_address, proj_status, company_id)
    VALUES ('Riverside Shopping Center', '200 River Road', 1, @testCompanyId);
    PRINT '  ✓ Created project: Riverside Shopping Center';
END

-- Acme Builders projects
IF NOT EXISTS (SELECT * FROM project_master WHERE proj_name = 'Parkview Residential Complex' AND company_id = @acmeCompanyId)
BEGIN
    INSERT INTO project_master (proj_name, proj_address, proj_status, company_id)
    VALUES ('Parkview Residential Complex', '300 Park Avenue', 1, @acmeCompanyId);
    PRINT '  ✓ Created project: Parkview Residential Complex';
END

IF NOT EXISTS (SELECT * FROM project_master WHERE proj_name = 'Tech Campus Phase 2' AND company_id = @acmeCompanyId)
BEGIN
    INSERT INTO project_master (proj_name, proj_address, proj_status, company_id)
    VALUES ('Tech Campus Phase 2', '400 Innovation Drive', 1, @acmeCompanyId);
    PRINT '  ✓ Created project: Tech Campus Phase 2';
END

PRINT '';

-- Show summary
PRINT '========================================';
PRINT 'Data Summary by Company';
PRINT '========================================';
PRINT '';

SELECT 
    c.id AS company_id,
    c.name AS company_name,
    c.subdomain,
    (SELECT COUNT(*) FROM users WHERE company_id = c.id) AS users,
    (SELECT COUNT(*) FROM supplier_master WHERE company_id = c.id) AS suppliers,
    (SELECT COUNT(*) FROM project_master WHERE company_id = c.id) AS projects,
    (SELECT COUNT(*) FROM purchase_order_master WHERE company_id = c.id) AS purchase_orders
FROM companies c
ORDER BY c.id;

PRINT '';
PRINT '========================================';
PRINT 'Phase 3.2 Test Data Seeding Complete!';
PRINT '========================================';
PRINT '';
PRINT 'Test credentials (all passwords: password):';
PRINT '  - john.smith@testconstruction.com (Test Construction Co)';
PRINT '  - mike.davis@acmebuilders.com (Acme Builders Inc)';
PRINT '';
GO
