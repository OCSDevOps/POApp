<?php
/**
 * Create Takeoff & AI Settings tables for Project Takeoffs & Estimates feature
 * Uses direct PDO (no Laravel bootstrap) to avoid OOM.
 * Run: php create_takeoff_tables.php
 */

$server = 'DESKTOP-Q2001NS\SQLEXPRESS';
$database = 'porder_db';

try {
    $pdo = new PDO(
        "sqlsrv:Server=$server;Database=$database;TrustServerCertificate=true",
        '', '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to $database\n\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// Check if table exists
function tableExists(PDO $pdo, string $table): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?");
    $stmt->execute([$table]);
    return $stmt->fetchColumn() > 0;
}

$tables = [
    'ai_settings' => [
        "CREATE TABLE ai_settings (
            ai_setting_id BIGINT IDENTITY(1,1) PRIMARY KEY,
            company_id BIGINT NOT NULL,
            ai_provider VARCHAR(50) NOT NULL DEFAULT 'openai',
            api_key VARCHAR(500) NULL,
            model_name VARCHAR(100) NOT NULL DEFAULT 'gpt-4o',
            max_tokens INT NOT NULL DEFAULT 4096,
            temperature DECIMAL(3,2) NOT NULL DEFAULT 0.20,
            is_active TINYINT NOT NULL DEFAULT 0,
            ai_createby BIGINT NULL,
            ai_createdate DATETIME NULL,
            ai_modifyby BIGINT NULL,
            ai_modifydate DATETIME NULL,
            CONSTRAINT fk_ai_settings_company FOREIGN KEY (company_id) REFERENCES companies(id)
        )",
        "CREATE INDEX idx_ai_settings_company ON ai_settings(company_id)",
    ],

    'takeoff_master' => [
        "CREATE TABLE takeoff_master (
            to_id BIGINT IDENTITY(1,1) PRIMARY KEY,
            to_number VARCHAR(50) NOT NULL,
            to_project_id BIGINT NOT NULL,
            to_title VARCHAR(250) NOT NULL,
            to_description TEXT NULL,
            to_status TINYINT NOT NULL DEFAULT 1,
            to_total_items INT NOT NULL DEFAULT 0,
            to_subtotal DECIMAL(18,2) NOT NULL DEFAULT 0,
            to_tax_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
            to_total_amount DECIMAL(18,2) NOT NULL DEFAULT 0,
            to_notes TEXT NULL,
            to_finalized_by BIGINT NULL,
            to_finalized_at DATETIME NULL,
            to_createby BIGINT NULL,
            to_createdate DATETIME NULL,
            to_modifyby BIGINT NULL,
            to_modifydate DATETIME NULL,
            company_id BIGINT NOT NULL,
            CONSTRAINT uq_takeoff_number UNIQUE (to_number),
            CONSTRAINT fk_takeoff_project FOREIGN KEY (to_project_id) REFERENCES project_master(proj_id),
            CONSTRAINT fk_takeoff_company FOREIGN KEY (company_id) REFERENCES companies(id)
        )",
        "CREATE INDEX idx_takeoff_project ON takeoff_master(to_project_id)",
        "CREATE INDEX idx_takeoff_company ON takeoff_master(company_id)",
        "CREATE INDEX idx_takeoff_status ON takeoff_master(to_status)",
    ],

    'takeoff_details' => [
        "CREATE TABLE takeoff_details (
            tod_id BIGINT IDENTITY(1,1) PRIMARY KEY,
            tod_takeoff_id BIGINT NOT NULL,
            tod_item_code VARCHAR(50) NULL,
            tod_description NVARCHAR(500) NOT NULL,
            tod_quantity DECIMAL(18,4) NOT NULL DEFAULT 0,
            tod_uom_id BIGINT NULL,
            tod_unit_price DECIMAL(18,2) NOT NULL DEFAULT 0,
            tod_subtotal DECIMAL(18,2) NOT NULL DEFAULT 0,
            tod_cost_code_id BIGINT NULL,
            tod_source VARCHAR(20) NOT NULL DEFAULT 'manual',
            tod_ai_confidence DECIMAL(5,2) NULL,
            tod_notes TEXT NULL,
            tod_status TINYINT NOT NULL DEFAULT 1,
            tod_createdate DATETIME NULL,
            company_id BIGINT NOT NULL,
            CONSTRAINT fk_tod_takeoff FOREIGN KEY (tod_takeoff_id) REFERENCES takeoff_master(to_id),
            CONSTRAINT fk_tod_company FOREIGN KEY (company_id) REFERENCES companies(id)
        )",
        "CREATE INDEX idx_tod_takeoff ON takeoff_details(tod_takeoff_id)",
        "CREATE INDEX idx_tod_company ON takeoff_details(company_id)",
    ],

    'takeoff_drawings' => [
        "CREATE TABLE takeoff_drawings (
            tdr_id BIGINT IDENTITY(1,1) PRIMARY KEY,
            tdr_takeoff_id BIGINT NOT NULL,
            tdr_original_name VARCHAR(500) NOT NULL,
            tdr_path VARCHAR(500) NOT NULL,
            tdr_mime VARCHAR(100) NOT NULL,
            tdr_size BIGINT NOT NULL DEFAULT 0,
            tdr_page_count INT NULL,
            tdr_ai_status VARCHAR(20) NOT NULL DEFAULT 'pending',
            tdr_ai_processed_at DATETIME NULL,
            tdr_ai_raw_response TEXT NULL,
            tdr_ai_error VARCHAR(1000) NULL,
            tdr_createby BIGINT NULL,
            tdr_createdate DATETIME NULL,
            tdr_status TINYINT NOT NULL DEFAULT 1,
            company_id BIGINT NOT NULL,
            CONSTRAINT fk_tdr_takeoff FOREIGN KEY (tdr_takeoff_id) REFERENCES takeoff_master(to_id),
            CONSTRAINT fk_tdr_company FOREIGN KEY (company_id) REFERENCES companies(id)
        )",
        "CREATE INDEX idx_tdr_takeoff ON takeoff_drawings(tdr_takeoff_id)",
        "CREATE INDEX idx_tdr_company ON takeoff_drawings(company_id)",
    ],
];

$created = 0;
$skipped = 0;

foreach ($tables as $tableName => $statements) {
    if (tableExists($pdo, $tableName)) {
        echo "SKIP: '$tableName' already exists.\n";
        $skipped++;
        continue;
    }

    try {
        foreach ($statements as $sql) {
            $pdo->exec($sql);
        }
        echo "OK: Created '$tableName'\n";
        $created++;
    } catch (PDOException $e) {
        echo "ERROR: '$tableName' - " . $e->getMessage() . "\n";
    }
}

echo "\nDone: $created created, $skipped skipped.\n";
