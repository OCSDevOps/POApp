<?php
// Create supplier_users table directly
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get database connection
$db = DB::connection();

// Drop table if exists
try {
    $db->statement('DROP TABLE IF EXISTS supplier_users');
    echo "Dropped existing supplier_users table (if any)\n";
} catch (Exception $e) {
    echo "Note: " . $e->getMessage() . "\n";
}

// Create table
$sql = "
CREATE TABLE supplier_users (
    id BIGINT IDENTITY(1,1) PRIMARY KEY,
    supplier_id BIGINT NULL,
    company_id BIGINT NULL,
    name NVARCHAR(255) NOT NULL,
    email NVARCHAR(255) NOT NULL UNIQUE,
    phone NVARCHAR(255) NULL,
    password NVARCHAR(255) NOT NULL,
    status TINYINT NOT NULL DEFAULT 1,
    email_verified_at DATETIME2 NULL,
    remember_token NVARCHAR(100) NULL,
    created_at DATETIME2 NULL,
    updated_at DATETIME2 NULL,
    CONSTRAINT FK_supplier_users_company_id FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

CREATE INDEX idx_supplier_users_supplier_id ON supplier_users(supplier_id);
CREATE INDEX idx_supplier_users_company_id ON supplier_users(company_id);
";

try {
    $db->unprepared($sql);
    echo "✓ supplier_users table created successfully!\n";
    
    // Verify table exists
    $count = $db->table('supplier_users')->count();
    echo "✓ Table verified. Current record count: $count\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✓ Migration completed successfully!\n";
