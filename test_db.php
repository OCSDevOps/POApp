<?php
/**
 * Test SQL Server Connection
 */

// Test 1: Using sqlsrv extension directly
echo "Testing SQL Server Connection...\n\n";

$serverName = "DESKTOP-Q2001NS\\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "porder_db",
    "TrustServerCertificate" => true
);

echo "Server: $serverName\n";
echo "Database: porder_db\n\n";

// Try sqlsrv_connect
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn) {
    echo "SUCCESS: Connected using sqlsrv_connect!\n\n";
    
    // Test query
    $sql = "SELECT COUNT(*) as cnt FROM purchase_order_master";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo "Purchase Orders Count: " . $row['cnt'] . "\n";
        sqlsrv_free_stmt($stmt);
    }
    
    // List tables
    $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt) {
        echo "\nTables in database:\n";
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo "  - " . $row['TABLE_NAME'] . "\n";
        }
        sqlsrv_free_stmt($stmt);
    }
    
    sqlsrv_close($conn);
} else {
    echo "FAILED: Could not connect using sqlsrv_connect\n";
    echo "Errors:\n";
    print_r(sqlsrv_errors());
}

echo "\n\n";

// Test 2: Using PDO
echo "Testing PDO Connection...\n\n";

try {
    $dsn = "sqlsrv:Server=$serverName;Database=porder_db;TrustServerCertificate=true";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "SUCCESS: Connected using PDO!\n\n";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM purchase_order_master");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Purchase Orders Count: " . $row['cnt'] . "\n";
    
} catch (PDOException $e) {
    echo "FAILED: Could not connect using PDO\n";
    echo "Error: " . $e->getMessage() . "\n";
}
