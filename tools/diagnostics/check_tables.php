<?php
// Check which tables exist in the database

$serverName = "DESKTOP-Q2001NS\\SQLEXPRESS";
$database = "porder_db";

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", "", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connected to database: $database\n\n";

    // Check for companies table
    $stmt = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_NAME IN ('companies', 'supplier_users', 'users', 'supplier_master', 'migrations') ORDER BY TABLE_NAME");
    
    echo "Checking for migration-related tables:\n";
    echo "==========================================\n";
    
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (in_array('companies', $tables)) {
        echo "✓ companies table EXISTS\n";
        $count = $conn->query("SELECT COUNT(*) FROM companies")->fetchColumn();
        echo "  - Record count: $count\n";
    } else {
        echo "✗ companies table MISSING\n";
    }
    
    if (in_array('supplier_users', $tables)) {
        echo "✓ supplier_users table EXISTS\n";
    } else {
        echo "✗ supplier_users table MISSING\n";
    }
    
    if (in_array('users', $tables)) {
        echo "✓ users table EXISTS\n";
        $count = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "  - Record count: $count\n";
        
        // Check if users table has company_id
        $stmt = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'company_id'");
        if ($stmt->fetch()) {
            echo "  - ✓ company_id column EXISTS in users\n";
        } else {
            echo "  - ✗ company_id column MISSING in users\n";
        }
    } else {
        echo "✗ users table MISSING\n";
    }
    
    if (in_array('supplier_master', $tables)) {
        echo "✓ supplier_master table EXISTS\n";
    } else {
        echo "✗ supplier_master table MISSING\n";
    }
    
    if (in_array('migrations', $tables)) {
        echo "✓ migrations table EXISTS\n";
        $stmt = $conn->query("SELECT migration, batch FROM migrations ORDER BY id DESC");
        $migrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($migrations) {
            echo "  - Last 5 migrations:\n";
            foreach (array_slice($migrations, 0, 5) as $migration) {
                echo "    - {$migration['migration']} (batch {$migration['batch']})\n";
            }
        }
    } else {
        echo "✗ migrations table MISSING\n";
    }

} catch (PDOException $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
