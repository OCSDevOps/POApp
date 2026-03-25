<?php
/**
 * Test Laravel Database Connection
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing Laravel Database Connection...\n\n";

try {
    // Test connection
    $pdo = DB::connection()->getPdo();
    echo "SUCCESS: Laravel connected to database!\n\n";
    
    // Test query
    $count = DB::table('purchase_order_master')->count();
    echo "Purchase Orders Count: $count\n\n";
    
    // List some tables
    $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME");
    echo "Tables in database:\n";
    foreach (array_slice($tables, 0, 10) as $table) {
        echo "  - " . $table->TABLE_NAME . "\n";
    }
    echo "  ... and more\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
