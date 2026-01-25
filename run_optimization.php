<?php
/**
 * Run Database Optimization Script
 */

try {
    $conn = new PDO('sqlsrv:Server=DESKTOP-Q2001NS\SQLEXPRESS;Database=porder_db', '', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n\n";
    
    $sql = file_get_contents(__DIR__ . '/db/database_optimization.sql');
    
    // Split by GO statements
    $batches = preg_split('/^GO\s*$/m', $sql);
    
    $success = 0;
    $errors = [];
    
    foreach ($batches as $batch) {
        $batch = trim($batch);
        if (empty($batch) || strpos($batch, 'PRINT') === 0) continue;
        
        try {
            $conn->exec($batch);
            $success++;
        } catch (PDOException $e) {
            $errors[] = substr($e->getMessage(), 0, 300);
        }
    }
    
    echo "Executed $success batches successfully\n";
    if (count($errors) > 0) {
        echo "Errors (" . count($errors) . "):\n";
        foreach (array_slice($errors, 0, 15) as $err) {
            echo "- $err\n\n";
        }
    }
    
    echo "\n--- Database Status ---\n";
    
    // Check tables
    $tables = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables (" . count($tables) . "):\n";
    foreach ($tables as $t) echo "  - $t\n";
    
    // Check views
    $views = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.VIEWS ORDER BY TABLE_NAME")->fetchAll(PDO::FETCH_COLUMN);
    echo "\nViews (" . count($views) . "):\n";
    foreach ($views as $v) echo "  - $v\n";
    
    // Check indexes count
    $indexes = $conn->query("SELECT COUNT(*) FROM sys.indexes WHERE is_primary_key = 0 AND is_unique_constraint = 0 AND type > 0")->fetchColumn();
    echo "\nNon-PK Indexes: $indexes\n";
    
    // Check foreign keys
    $fks = $conn->query("SELECT COUNT(*) FROM sys.foreign_keys")->fetchColumn();
    echo "Foreign Keys: $fks\n";
    
} catch (PDOException $e) {
    echo "Connection Error: " . $e->getMessage() . "\n";
}
