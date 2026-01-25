<?php
/**
 * Test Users Table and Authentication
 */

$serverName = "DESKTOP-Q2001NS\\SQLEXPRESS";
$connectionOptions = array(
    "Database" => "porder_db",
    "TrustServerCertificate" => true
);

echo "Testing Users Table...\n\n";

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn) {
    echo "SUCCESS: Connected to database!\n\n";
    
    // Check if users table exists
    $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'users'";
    $stmt = sqlsrv_query($conn, $sql);
    
    if ($stmt && sqlsrv_fetch_array($stmt)) {
        echo "Users table exists!\n\n";
        sqlsrv_free_stmt($stmt);
        
        // Get all users
        $sql = "SELECT id, name, email, username, created_at FROM users";
        $stmt = sqlsrv_query($conn, $sql);
        
        if ($stmt) {
            echo "Users in database:\n";
            echo str_repeat("-", 80) . "\n";
            printf("%-5s %-20s %-30s %-15s\n", "ID", "Name", "Email", "Username");
            echo str_repeat("-", 80) . "\n";
            
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                printf("%-5s %-20s %-30s %-15s\n", 
                    $row['id'], 
                    $row['name'] ?? 'N/A', 
                    $row['email'] ?? 'N/A',
                    $row['username'] ?? 'N/A'
                );
            }
            sqlsrv_free_stmt($stmt);
        }
    } else {
        echo "Users table does NOT exist!\n";
        
        // Check for user_info table (legacy)
        $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'user_info'";
        $stmt = sqlsrv_query($conn, $sql);
        
        if ($stmt && sqlsrv_fetch_array($stmt)) {
            echo "Found legacy user_info table!\n\n";
            sqlsrv_free_stmt($stmt);
            
            // Get all users from user_info
            $sql = "SELECT u_id, firstname, lastname, email, username FROM user_info";
            $stmt = sqlsrv_query($conn, $sql);
            
            if ($stmt) {
                echo "Users in user_info table:\n";
                echo str_repeat("-", 80) . "\n";
                printf("%-5s %-20s %-30s %-15s\n", "ID", "Name", "Email", "Username");
                echo str_repeat("-", 80) . "\n";
                
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    printf("%-5s %-20s %-30s %-15s\n", 
                        $row['u_id'], 
                        ($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''), 
                        $row['email'] ?? 'N/A',
                        $row['username'] ?? 'N/A'
                    );
                }
                sqlsrv_free_stmt($stmt);
            }
        }
    }
    
    sqlsrv_close($conn);
} else {
