<?php
// Direct SQL Server connection to create supplier_users table

$serverName = "DESKTOP-Q2001NS\\SQLEXPRESS";
$database = "porder_db";

try {
    // Connect using Windows Authentication (no username/password needed)
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", "", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to database\n\n";

    // Drop table if exists
    try {
        $conn->exec("DROP TABLE IF EXISTS supplier_users");
        echo "✓ Dropped existing supplier_users table (if any)\n";
    } catch (PDOException $e) {
        echo "Note: " . $e->getMessage() . "\n";
    }

    // Create table
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
    echo "✓ supplier_users table created successfully!\n";

    // Create indexes
    $conn->exec("CREATE INDEX idx_supplier_users_supplier_id ON supplier_users(supplier_id);");
    echo "✓ Created index on supplier_id\n";
    
    $conn->exec("CREATE INDEX idx_supplier_users_company_id ON supplier_users(company_id);");
    echo "✓ Created index on company_id\n";
    
    $conn->exec("CREATE UNIQUE INDEX idx_supplier_users_email ON supplier_users(email);");
    echo "✓ Created unique index on email\n";

    // Verify table exists
    $stmt = $conn->query("SELECT COUNT(*) FROM supplier_users");
    $count = $stmt->fetchColumn();
    echo "\n✓ Table verified. Current record count: $count\n";

    echo "\n✓✓✓ Migration completed successfully! ✓✓✓\n";

} catch (PDOException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
