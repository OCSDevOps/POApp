<?php
/**
 * Create subcontractor & contract management tables for POApp.
 * Uses direct PDO to avoid Laravel OOM issues.
 *
 * Tables: 5 new tables + 1 ALTER on supplier_master
 */
ini_set('memory_limit', '512M');

$serverName = 'DESKTOP-Q2001NS\SQLEXPRESS';
$database = 'porder_db';

try {
    $pdo = new PDO(
        "sqlsrv:Server=$serverName;Database=$database;TrustServerCertificate=yes",
        null,
        null,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to database.\n\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

$tables = [];

// ─────────────────────────────────────────────────────────────
// 0. ALTER supplier_master — add sup_type column
// ─────────────────────────────────────────────────────────────
echo "=== ALTER supplier_master ===\n";
try {
    // Check if column already exists
    $stmt = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'supplier_master' AND COLUMN_NAME = 'sup_type'");
    if ($stmt->fetch()) {
        echo "  Column sup_type already exists, skipping.\n";
    } else {
        $pdo->exec("ALTER TABLE supplier_master ADD sup_type TINYINT NOT NULL DEFAULT 1");
        echo "  Added sup_type column (1=Supplier, 2=Subcontractor, 3=Both).\n";
    }
} catch (PDOException $e) {
    echo "  ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// ─────────────────────────────────────────────────────────────
// 1. contract_master
// ─────────────────────────────────────────────────────────────
$tables['contract_master'] = "
CREATE TABLE contract_master (
    contract_id          BIGINT IDENTITY(1,1) PRIMARY KEY,
    contract_number      NVARCHAR(50) NOT NULL,
    contract_title       NVARCHAR(255) NOT NULL,
    contract_description NVARCHAR(MAX) NULL,

    contract_project_id  BIGINT NOT NULL,
    contract_supplier_id BIGINT NOT NULL,
    contract_cost_code_id BIGINT NULL,

    contract_original_value   DECIMAL(18,2) NOT NULL DEFAULT 0,
    contract_approved_cos     DECIMAL(18,2) NOT NULL DEFAULT 0,
    contract_pending_cos      DECIMAL(18,2) NOT NULL DEFAULT 0,
    contract_invoiced_amount  DECIMAL(18,2) NOT NULL DEFAULT 0,
    contract_paid_amount      DECIMAL(18,2) NOT NULL DEFAULT 0,
    contract_retention_pct    DECIMAL(5,2) NOT NULL DEFAULT 0,
    contract_retention_held   DECIMAL(18,2) NOT NULL DEFAULT 0,
    contract_retention_released DECIMAL(18,2) NOT NULL DEFAULT 0,

    contract_start_date  DATE NULL,
    contract_end_date    DATE NULL,

    contract_status      TINYINT NOT NULL DEFAULT 1,

    contract_scope       NVARCHAR(MAX) NULL,
    contract_terms       NVARCHAR(MAX) NULL,

    contract_created_by  BIGINT NULL,
    contract_created_at  DATETIME2 NOT NULL DEFAULT GETDATE(),
    contract_modified_by BIGINT NULL,
    contract_modified_at DATETIME2 NULL,

    company_id           BIGINT NOT NULL,
    procore_contract_id  BIGINT NULL,

    CONSTRAINT FK_contract_project FOREIGN KEY (contract_project_id) REFERENCES project_master(proj_id),
    CONSTRAINT FK_contract_supplier FOREIGN KEY (contract_supplier_id) REFERENCES supplier_master(sup_id),
    CONSTRAINT FK_contract_costcode FOREIGN KEY (contract_cost_code_id) REFERENCES cost_code_master(cc_id),
    CONSTRAINT UQ_contract_number_company UNIQUE (contract_number, company_id)
)";

// ─────────────────────────────────────────────────────────────
// 2. contract_change_orders
// ─────────────────────────────────────────────────────────────
$tables['contract_change_orders'] = "
CREATE TABLE contract_change_orders (
    cco_id               BIGINT IDENTITY(1,1) PRIMARY KEY,
    cco_number           NVARCHAR(50) NOT NULL,
    contract_id          BIGINT NOT NULL,
    cco_amount           DECIMAL(18,2) NOT NULL,
    cco_description      NVARCHAR(MAX) NOT NULL,
    cco_reason           NVARCHAR(500) NULL,

    cco_status           NVARCHAR(30) NOT NULL DEFAULT 'draft',

    submitted_at         DATETIME2 NULL,
    approved_by          BIGINT NULL,
    approved_at          DATETIME2 NULL,
    rejection_reason     NVARCHAR(500) NULL,

    created_by           BIGINT NULL,
    created_at           DATETIME2 NOT NULL DEFAULT GETDATE(),
    updated_at           DATETIME2 NULL,

    company_id           BIGINT NOT NULL,

    CONSTRAINT FK_cco_contract FOREIGN KEY (contract_id) REFERENCES contract_master(contract_id)
)";

// ─────────────────────────────────────────────────────────────
// 3. contract_documents
// ─────────────────────────────────────────────────────────────
$tables['contract_documents'] = "
CREATE TABLE contract_documents (
    cdoc_id              BIGINT IDENTITY(1,1) PRIMARY KEY,
    cdoc_contract_id     BIGINT NOT NULL,
    cdoc_original_name   NVARCHAR(255) NOT NULL,
    cdoc_path            NVARCHAR(500) NOT NULL,
    cdoc_mime            NVARCHAR(100) NULL,
    cdoc_size            BIGINT NULL,

    cdoc_type            NVARCHAR(30) NOT NULL DEFAULT 'other',
    cdoc_description     NVARCHAR(500) NULL,

    cdoc_createby        BIGINT NULL,
    cdoc_createdate      DATETIME2 NOT NULL DEFAULT GETDATE(),
    cdoc_status          TINYINT NOT NULL DEFAULT 1,

    company_id           BIGINT NOT NULL,

    CONSTRAINT FK_cdoc_contract FOREIGN KEY (cdoc_contract_id) REFERENCES contract_master(contract_id)
)";

// ─────────────────────────────────────────────────────────────
// 4. supplier_compliance
// ─────────────────────────────────────────────────────────────
$tables['supplier_compliance'] = "
CREATE TABLE supplier_compliance (
    compliance_id        BIGINT IDENTITY(1,1) PRIMARY KEY,
    compliance_supplier_id BIGINT NOT NULL,

    compliance_type      NVARCHAR(50) NOT NULL,
    compliance_name      NVARCHAR(255) NOT NULL,
    compliance_number    NVARCHAR(100) NULL,
    compliance_issuer    NVARCHAR(255) NULL,
    compliance_amount    DECIMAL(18,2) NULL,

    compliance_issue_date    DATE NULL,
    compliance_expiry_date   DATE NULL,
    compliance_warning_days  INT NOT NULL DEFAULT 30,

    compliance_document_path NVARCHAR(500) NULL,
    compliance_document_name NVARCHAR(255) NULL,

    compliance_status    TINYINT NOT NULL DEFAULT 1,

    compliance_required  BIT NOT NULL DEFAULT 1,

    compliance_contract_id BIGINT NULL,

    compliance_notes     NVARCHAR(MAX) NULL,

    compliance_created_by  BIGINT NULL,
    compliance_created_at  DATETIME2 NOT NULL DEFAULT GETDATE(),
    compliance_modified_by BIGINT NULL,
    compliance_modified_at DATETIME2 NULL,

    company_id           BIGINT NOT NULL,

    CONSTRAINT FK_compliance_supplier FOREIGN KEY (compliance_supplier_id) REFERENCES supplier_master(sup_id),
    CONSTRAINT FK_compliance_contract FOREIGN KEY (compliance_contract_id) REFERENCES contract_master(contract_id)
)";

// ─────────────────────────────────────────────────────────────
// 5. contract_invoices
// ─────────────────────────────────────────────────────────────
$tables['contract_invoices'] = "
CREATE TABLE contract_invoices (
    cinv_id              BIGINT IDENTITY(1,1) PRIMARY KEY,
    cinv_contract_id     BIGINT NOT NULL,
    cinv_number          NVARCHAR(50) NOT NULL,
    cinv_description     NVARCHAR(500) NULL,

    cinv_gross_amount    DECIMAL(18,2) NOT NULL,
    cinv_retention_held  DECIMAL(18,2) NOT NULL DEFAULT 0,
    cinv_net_amount      DECIMAL(18,2) NOT NULL,
    cinv_paid_amount     DECIMAL(18,2) NOT NULL DEFAULT 0,

    cinv_invoice_date    DATE NOT NULL,
    cinv_due_date        DATE NULL,
    cinv_paid_date       DATE NULL,

    cinv_period_from     DATE NULL,
    cinv_period_to       DATE NULL,

    cinv_status          TINYINT NOT NULL DEFAULT 1,

    cinv_created_by      BIGINT NULL,
    cinv_created_at      DATETIME2 NOT NULL DEFAULT GETDATE(),
    cinv_modified_by     BIGINT NULL,
    cinv_modified_at     DATETIME2 NULL,

    company_id           BIGINT NOT NULL,

    CONSTRAINT FK_cinv_contract FOREIGN KEY (cinv_contract_id) REFERENCES contract_master(contract_id)
)";

// ─────────────────────────────────────────────────────────────
// Create all tables
// ─────────────────────────────────────────────────────────────
$created = 0;
$skipped = 0;
$errors = 0;

foreach ($tables as $name => $ddl) {
    echo "=== $name ===\n";
    try {
        // Check if table already exists
        $check = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '$name'");
        if ($check->fetch()) {
            echo "  Already exists, skipping.\n";
            $skipped++;
            continue;
        }
        $pdo->exec($ddl);
        echo "  Created successfully.\n";
        $created++;
    } catch (PDOException $e) {
        echo "  ERROR: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n";

// ─────────────────────────────────────────────────────────────
// Create indexes
// ─────────────────────────────────────────────────────────────
echo "=== Creating Indexes ===\n";

$indexes = [
    // supplier_master
    "CREATE NONCLUSTERED INDEX IX_supplier_master_sup_type ON supplier_master(sup_type)",

    // contract_master
    "CREATE NONCLUSTERED INDEX IX_contract_master_project ON contract_master(contract_project_id)",
    "CREATE NONCLUSTERED INDEX IX_contract_master_supplier ON contract_master(contract_supplier_id)",
    "CREATE NONCLUSTERED INDEX IX_contract_master_company ON contract_master(company_id)",
    "CREATE NONCLUSTERED INDEX IX_contract_master_status ON contract_master(contract_status)",
    "CREATE NONCLUSTERED INDEX IX_contract_master_costcode ON contract_master(contract_cost_code_id)",

    // contract_change_orders
    "CREATE NONCLUSTERED INDEX IX_cco_contract ON contract_change_orders(contract_id)",
    "CREATE NONCLUSTERED INDEX IX_cco_company ON contract_change_orders(company_id)",
    "CREATE NONCLUSTERED INDEX IX_cco_status ON contract_change_orders(cco_status)",

    // contract_documents
    "CREATE NONCLUSTERED INDEX IX_cdoc_contract ON contract_documents(cdoc_contract_id)",
    "CREATE NONCLUSTERED INDEX IX_cdoc_company ON contract_documents(company_id)",

    // supplier_compliance
    "CREATE NONCLUSTERED INDEX IX_compliance_supplier ON supplier_compliance(compliance_supplier_id)",
    "CREATE NONCLUSTERED INDEX IX_compliance_expiry ON supplier_compliance(compliance_expiry_date)",
    "CREATE NONCLUSTERED INDEX IX_compliance_company ON supplier_compliance(company_id)",
    "CREATE NONCLUSTERED INDEX IX_compliance_type ON supplier_compliance(compliance_type)",
    "CREATE NONCLUSTERED INDEX IX_compliance_contract ON supplier_compliance(compliance_contract_id)",

    // contract_invoices
    "CREATE NONCLUSTERED INDEX IX_cinv_contract ON contract_invoices(cinv_contract_id)",
    "CREATE NONCLUSTERED INDEX IX_cinv_company ON contract_invoices(company_id)",
    "CREATE NONCLUSTERED INDEX IX_cinv_status ON contract_invoices(cinv_status)",
];

$idxCreated = 0;
foreach ($indexes as $sql) {
    try {
        // Extract index name for checking
        preg_match('/INDEX\s+(\S+)\s+ON/i', $sql, $m);
        $idxName = $m[1] ?? 'unknown';

        // Check if index already exists
        $check = $pdo->query("SELECT name FROM sys.indexes WHERE name = '$idxName'");
        if ($check->fetch()) {
            echo "  Index $idxName already exists, skipping.\n";
            continue;
        }

        $pdo->exec($sql);
        echo "  Created index: $idxName\n";
        $idxCreated++;
    } catch (PDOException $e) {
        echo "  Index error: " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo str_repeat('=', 60) . "\n";
echo "RESULTS:\n";
echo "  Tables created: $created\n";
echo "  Tables skipped (already exist): $skipped\n";
echo "  Table errors: $errors\n";
echo "  Indexes created: $idxCreated\n";
echo str_repeat('=', 60) . "\n";
