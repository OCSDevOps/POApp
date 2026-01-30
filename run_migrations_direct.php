<?php
// Create Phase 1.1 (companies + company_id columns) and Phase 1.2 (supplier_users) tables

$serverName = "DESKTOP-Q2001NS\\SQLEXPRESS";
$database = "porder_db";

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", "", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to database: $database\n\n";

    // ============================================
    // PHASE 1.1: Create companies table
    // ============================================
    echo "PHASE 1.1: Creating companies table...\n";
    echo "==========================================\n";
    
    $conn->exec("DROP TABLE IF EXISTS supplier_users"); // Drop if exists (FK constraint)
    $conn->exec("DROP TABLE IF EXISTS companies");
    
    $sql = "
    CREATE TABLE companies (
        id BIGINT IDENTITY(1,1) PRIMARY KEY,
        name NVARCHAR(255) NOT NULL,
        subdomain NVARCHAR(255) NULL UNIQUE,
        status TINYINT NOT NULL DEFAULT 1,
        settings NVARCHAR(MAX) NULL,
        created_at DATETIME2 NULL,
        updated_at DATETIME2 NULL
    );
    ";
    $conn->exec($sql);
    echo "✓ companies table created\n";

    // Insert default companies
    $conn->exec("
        INSERT INTO companies (name, subdomain, status, settings, created_at) VALUES
        ('Default Company', 'default', 1, NULL, GETDATE()),
        ('Test Construction Co', 'test', 1, NULL, GETDATE()),
        ('Acme Builders Inc', 'acme', 1, NULL, GETDATE())
    ");
    echo "✓ Seeded 3 companies\n";

    // ============================================
    // PHASE 1.1: Add company_id to existing tables
    // ============================================
    echo "\nAdding company_id columns to tables...\n";
    echo "==========================================\n";

    $tables = [
        'users' => 'id',
        'project_master' => 'proj_id',
        'supplier_master' => 'sup_id',
        'purchase_order_master' => 'porder_id',
        'item_master' => 'item_id',
        'budget_master' => 'bg_id',
        'receive_order_master' => 'rcv_id',
        'item_categories' => 'category_id',
        'cost_codes' => 'id',
        'checklists' => 'id',
        'equipment' => 'equip_id'
    ];

    $defaultCompanyId = $conn->query("SELECT id FROM companies WHERE subdomain = 'default'")->fetchColumn();

    foreach ($tables as $table => $pk) {
        try {
            // Check if table exists
            $stmt = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '$table'");
            if (!$stmt->fetch()) {
                echo "  ⊘ $table - table doesn't exist, skipping\n";
                continue;
            }

            // Check if company_id already exists
            $stmt = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMN_NAME = 'company_id'");
            if ($stmt->fetch()) {
                echo "  ✓ $table - company_id already exists\n";
                continue;
            }

            // Add company_id column
            $conn->exec("ALTER TABLE $table ADD company_id BIGINT NULL");
            echo "  ✓ $table - added company_id column\n";

            // Set default value for existing records
            $conn->exec("UPDATE $table SET company_id = $defaultCompanyId WHERE company_id IS NULL");
            echo "    - migrated existing records to Default Company\n";

            // Create index
            $conn->exec("CREATE INDEX idx_{$table}_company_id ON $table(company_id)");

        } catch (PDOException $e) {
            echo "  ✗ $table - Error: " . $e->getMessage() . "\n";
        }
    }

    // ============================================
    // PHASE 1.2: Create supplier_users table
    // ============================================
    echo "\nPHASE 1.2: Creating supplier_users table...\n";
    echo "==========================================\n";

    $sql = "
    CREATE TABLE supplier_users (
        id BIGINT IDENTITY(1,1) PRIMARY KEY,
        supplier_id BIGINT NULL,
        company_id BIGINT NULL,
        name NVARCHAR(255) NOT NULL,
        email NVARCHAR(255) NOT NULL,
        phone NVARCHAR(255) NULL,
        password NVARCHAR(255) NOT NULL,
        status TINYINT NOT NULL DEFAULT 1,
        email_verified_at DATETIME2 NULL,
        remember_token NVARCHAR(100) NULL,
        created_at DATETIME2 NULL,
        updated_at DATETIME2 NULL,
        CONSTRAINT FK_supplier_users_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
    );
    ";
    $conn->exec($sql);
    echo "✓ supplier_users table created\n";

    // Create indexes
    $conn->exec("CREATE INDEX idx_supplier_users_supplier_id ON supplier_users(supplier_id);");
    $conn->exec("CREATE INDEX idx_supplier_users_company_id ON supplier_users(company_id);");
    $conn->exec("CREATE UNIQUE INDEX idx_supplier_users_email ON supplier_users(email);");
    echo "✓ Created indexes\n";

    // ============================================
    // Update migrations table
    // ============================================
    echo "\nUpdating migrations table...\n";
    echo "==========================================\n";
    
    $nextBatch = $conn->query("SELECT ISNULL(MAX(batch), 0) + 1 FROM migrations")->fetchColumn();
    
    $migrations = [
        '2026_01_30_000001_create_companies_table',
        '2026_01_30_000002_add_company_id_to_tables',
        '2026_01_30_010000_create_supplier_users_table'
    ];
    
    foreach ($migrations as $migration) {
        try {
            $conn->exec("INSERT INTO migrations (migration, batch) VALUES ('$migration', $nextBatch)");
            echo "✓ Recorded migration: $migration\n";
        } catch (PDOException $e) {
            // Ignore duplicates
        }
    }

    // ============================================
    // Summary
    // ============================================
    echo "\n";
    echo "==========================================\n";
    echo "✓✓✓ MIGRATION COMPLETED SUCCESSFULLY! ✓✓✓\n";
    echo "==========================================\n";
    
    $companyCount = $conn->query("SELECT COUNT(*) FROM companies")->fetchColumn();
    $supplierUserCount = $conn->query("SELECT COUNT(*) FROM supplier_users")->fetchColumn();
    
    echo "- Companies: $companyCount records\n";
    echo "- Supplier Users: $supplierUserCount records\n";
    echo "\nYou can now register supplier users!\n";

} catch (PDOException $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
