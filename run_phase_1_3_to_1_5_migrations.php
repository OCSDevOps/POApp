<?php
// Run Phase 1.3, 1.4, 1.5 migrations directly

$serverName = "DESKTOP-Q2001NS\\SQLEXPRESS";
$database = "porder_db";

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", "", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to database: $database\n\n";

    // ============================================
    // PHASE 1.3: Item Pricing Management
    // ============================================
    echo "PHASE 1.3: Creating item_pricing table...\n";
    echo "==========================================\n";
    
    // Check if table exists
    $stmt = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'item_pricing'");
    if ($stmt->fetch()) {
        echo "⊘ item_pricing table already exists, skipping\n";
    } else {
        $sql = "
        CREATE TABLE item_pricing (
            pricing_id BIGINT IDENTITY(1,1) PRIMARY KEY,
            item_id BIGINT NOT NULL,
            supplier_id BIGINT NOT NULL,
            project_id BIGINT NULL,
            company_id BIGINT NULL,
            unit_price DECIMAL(15,2) NOT NULL,
            effective_from DATE NOT NULL,
            effective_to DATE NULL,
            status TINYINT NOT NULL DEFAULT 1,
            created_at DATETIME2 NULL,
            updated_at DATETIME2 NULL
        );
        ";
        $conn->exec($sql);
        echo "✓ item_pricing table created\n";
        
        // Create indexes
        $conn->exec("CREATE INDEX idx_item_pricing_item_id ON item_pricing(item_id)");
        $conn->exec("CREATE INDEX idx_item_pricing_supplier_id ON item_pricing(supplier_id)");
        $conn->exec("CREATE INDEX idx_item_pricing_project_id ON item_pricing(project_id)");
        $conn->exec("CREATE INDEX idx_item_pricing_company_id ON item_pricing(company_id)");
        echo "✓ Created indexes\n";
    }

    // ============================================
    // PHASE 1.4: RFQ System
    // ============================================
    echo "\nPHASE 1.4: Creating RFQ tables...\n";
    echo "==========================================\n";
    
    // rfq_master
    $stmt = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'rfq_master'");
    if ($stmt->fetch()) {
        echo "⊘ rfq_master table already exists, skipping\n";
    } else {
        $sql = "
        CREATE TABLE rfq_master (
            rfq_id BIGINT IDENTITY(1,1) PRIMARY KEY,
            rfq_no NVARCHAR(255) NOT NULL UNIQUE,
            rfq_project_id BIGINT NULL,
            company_id BIGINT NULL,
            rfq_title NVARCHAR(250) NOT NULL,
            rfq_description NVARCHAR(MAX) NULL,
            rfq_due_date DATE NULL,
            rfq_status TINYINT NOT NULL DEFAULT 1,
            rfq_created_by BIGINT NULL,
            rfq_created_at DATETIME2 NULL,
            rfq_modified_by BIGINT NULL,
            rfq_modified_at DATETIME2 NULL
        );
        ";
        $conn->exec($sql);
        echo "✓ rfq_master table created\n";
        $conn->exec("CREATE INDEX idx_rfq_master_project_id ON rfq_master(rfq_project_id)");
        $conn->exec("CREATE INDEX idx_rfq_master_company_id ON rfq_master(company_id)");
    }

    // rfq_items
    $stmt = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'rfq_items'");
    if ($stmt->fetch()) {
        echo "⊘ rfq_items table already exists, skipping\n";
    } else {
        $sql = "
        CREATE TABLE rfq_items (
            rfqi_id BIGINT IDENTITY(1,1) PRIMARY KEY,
            rfqi_rfq_id BIGINT NOT NULL,
            rfqi_item_id BIGINT NOT NULL,
            rfqi_uom_id BIGINT NULL,
            project_id BIGINT NULL,
            company_id BIGINT NULL,
            rfqi_quantity INT NOT NULL,
            rfqi_target_price DECIMAL(15,2) NULL,
            rfqi_notes NVARCHAR(MAX) NULL,
            rfqi_created_at DATETIME2 NULL
        );
        ";
        $conn->exec($sql);
        echo "✓ rfq_items table created\n";
        $conn->exec("CREATE INDEX idx_rfq_items_rfq_id ON rfq_items(rfqi_rfq_id)");
        $conn->exec("CREATE INDEX idx_rfq_items_item_id ON rfq_items(rfqi_item_id)");
        $conn->exec("CREATE INDEX idx_rfq_items_company_id ON rfq_items(company_id)");
    }

    // rfq_suppliers
    $stmt = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'rfq_suppliers'");
    if ($stmt->fetch()) {
        echo "⊘ rfq_suppliers table already exists, skipping\n";
    } else {
        $sql = "
        CREATE TABLE rfq_suppliers (
            rfqs_id BIGINT IDENTITY(1,1) PRIMARY KEY,
            rfqs_rfq_id BIGINT NOT NULL,
            rfqs_supplier_id BIGINT NOT NULL,
            company_id BIGINT NULL,
            rfqs_sent_date DATETIME2 NULL,
            rfqs_response_date DATETIME2 NULL,
            rfqs_status TINYINT NOT NULL DEFAULT 1,
            rfqs_notes NVARCHAR(MAX) NULL,
            rfqs_created_at DATETIME2 NULL
        );
        ";
        $conn->exec($sql);
        echo "✓ rfq_suppliers table created\n";
        $conn->exec("CREATE INDEX idx_rfq_suppliers_rfq_id ON rfq_suppliers(rfqs_rfq_id)");
        $conn->exec("CREATE INDEX idx_rfq_suppliers_supplier_id ON rfq_suppliers(rfqs_supplier_id)");
        $conn->exec("CREATE INDEX idx_rfq_suppliers_company_id ON rfq_suppliers(company_id)");
    }

    // rfq_quotes
    $stmt = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'rfq_quotes'");
    if ($stmt->fetch()) {
        echo "⊘ rfq_quotes table already exists, skipping\n";
    } else {
        $sql = "
        CREATE TABLE rfq_quotes (
            rfqq_id BIGINT IDENTITY(1,1) PRIMARY KEY,
            rfqq_rfqs_id BIGINT NOT NULL,
            rfqq_rfqi_id BIGINT NOT NULL,
            company_id BIGINT NULL,
            rfqq_quoted_price DECIMAL(15,2) NOT NULL,
            rfqq_lead_time_days INT NULL,
            rfqq_valid_until DATE NULL,
            rfqq_notes NVARCHAR(MAX) NULL,
            rfqq_created_at DATETIME2 NULL
        );
        ";
        $conn->exec($sql);
        echo "✓ rfq_quotes table created\n";
        $conn->exec("CREATE INDEX idx_rfq_quotes_rfqs_id ON rfq_quotes(rfqq_rfqs_id)");
        $conn->exec("CREATE INDEX idx_rfq_quotes_rfqi_id ON rfq_quotes(rfqq_rfqi_id)");
        $conn->exec("CREATE INDEX idx_rfq_quotes_company_id ON rfq_quotes(company_id)");
    }

    // ============================================
    // PHASE 1.5: Backorder Tracking
    // ============================================
    echo "\nPHASE 1.5: Adding backorder fields to purchase_order_details...\n";
    echo "==========================================\n";
    
    // Check if columns exist
    $stmt = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'purchase_order_details' AND COLUMN_NAME IN ('backordered_qty', 'expected_backorder_date', 'backorder_status')");
    $existingCols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($existingCols) === 3) {
        echo "⊘ Backorder fields already exist, skipping\n";
    } else {
        if (!in_array('backordered_qty', $existingCols)) {
            $conn->exec("ALTER TABLE purchase_order_details ADD backordered_qty INT NOT NULL DEFAULT 0");
            echo "✓ Added backordered_qty column\n";
        }
        if (!in_array('expected_backorder_date', $existingCols)) {
            $conn->exec("ALTER TABLE purchase_order_details ADD expected_backorder_date DATE NULL");
            echo "✓ Added expected_backorder_date column\n";
        }
        if (!in_array('backorder_status', $existingCols)) {
            $conn->exec("ALTER TABLE purchase_order_details ADD backorder_status TINYINT NOT NULL DEFAULT 0");
            echo "✓ Added backorder_status column\n";
        }
    }

    // ============================================
    // Update migrations table
    // ============================================
    echo "\nUpdating migrations table...\n";
    echo "==========================================\n";
    
    $nextBatch = $conn->query("SELECT ISNULL(MAX(batch), 0) + 1 FROM migrations")->fetchColumn();
    
    $migrations = [
        '2026_01_30_020000_create_item_pricing_table',
        '2026_01_30_021000_create_rfqs_tables',
        '2026_01_30_030000_add_backorder_fields_to_po_items'
    ];
    
    foreach ($migrations as $migration) {
        try {
            $conn->exec("INSERT INTO migrations (migration, batch) VALUES ('$migration', $nextBatch)");
            echo "✓ Recorded migration: $migration\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'duplicate') !== false) {
                echo "⊘ Migration already recorded: $migration\n";
            } else {
                throw $e;
            }
        }
    }

    // ============================================
    // Summary
    // ============================================
    echo "\n";
    echo "==========================================\n";
    echo "✓✓✓ PHASE 1.3-1.5 MIGRATIONS COMPLETE! ✓✓✓\n";
    echo "==========================================\n";
    
    $tables = ['item_pricing', 'rfq_master', 'rfq_items', 'rfq_suppliers', 'rfq_quotes'];
    foreach ($tables as $table) {
        $count = $conn->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "- $table: $count records\n";
    }
    
    echo "\nNext steps:\n";
    echo "1. Test item pricing CRUD operations\n";
    echo "2. Test RFQ workflow (create, assign suppliers, submit quotes)\n";
    echo "3. Test backorder tracking on PO items\n";
    echo "4. Run smoke tests for supplier portal\n";
    echo "5. Commit Phase 1.3-1.5\n";

} catch (PDOException $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
