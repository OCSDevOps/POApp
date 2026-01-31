<?php
/**
 * Direct SQL Server Migration Runner
 * 
 * Applies all Phase 1.3-1.5 and Phase 2.2 migrations directly to SQL Server
 * Use this when artisan migrate has memory issues
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "===========================================\n";
echo "  POApp Direct SQL Server Migration Runner\n";
echo "===========================================\n\n";

echo "Database: " . env('DB_DATABASE') . "\n";
echo "Server: " . env('DB_HOST') . "\n\n";

// Migration files to check and run
$migrations = [
    '2026_01_30_020000_create_item_pricing_table' => 'Phase 1.3: Item Pricing',
    '2026_01_30_021000_create_rfqs_tables' => 'Phase 1.4: RFQ System',
    '2026_01_30_030000_add_backorder_fields_to_po_items' => 'Phase 1.5: Backorders',
    '2026_01_30_100000_create_accounting_integrations_tables' => 'Phase 2.1: Accounting',
    '2026_01_30_110000_create_budget_management_tables' => 'Phase 2.2 Batch 5: Budget Core',
    '2026_01_30_111000_add_project_roles_costcode_hierarchy' => 'Phase 2.2 Batch 6: Roles & Hierarchy',
];

echo "Checking migration status...\n";
echo "-----------------------------\n";

foreach ($migrations as $migrationFile => $description) {
    // Check if migration was already run
    $exists = DB::table('migrations')
        ->where('migration', $migrationFile)
        ->exists();
    
    if ($exists) {
        echo "✓ $description - ALREADY RUN\n";
    } else {
        echo "✗ $description - NOT RUN (will attempt to run)\n";
        
        // Load and run the migration
        $migrationPath = __DIR__ . "/database/migrations/{$migrationFile}.php";
        if (file_exists($migrationPath)) {
            try {
                require_once $migrationPath;
                
                // Extract class name from file
                $className = studly_case(str_replace('.php', '', $migrationFile));
                $className = preg_replace('/^\d+_\d+_\d+_\d+_/', '', $className);
                $className = str_replace('_', '', ucwords($className, '_'));
                
                if (class_exists($className)) {
                    $migration = new $className;
                    
                    echo "  Running migration...\n";
                    DB::beginTransaction();
                    
                    try {
                        $migration->up();
                        
                        // Record in migrations table
                        DB::table('migrations')->insert([
                            'migration' => $migrationFile,
                            'batch' => DB::table('migrations')->max('batch') + 1,
                        ]);
                        
                        DB::commit();
                        echo "  ✓ Migration completed successfully!\n";
                    } catch (\Exception $e) {
                        DB::rollBack();
                        echo "  ✗ Migration failed: " . $e->getMessage() . "\n";
                    }
                } else {
                    echo "  ✗ Migration class not found: $className\n";
                }
            } catch (\Exception $e) {
                echo "  ✗ Error loading migration: " . $e->getMessage() . "\n";
            }
        } else {
            echo "  ✗ Migration file not found\n";
        }
    }
    echo "\n";
}

echo "\n===========================================\n";
echo "  Migration Check Complete\n";
echo "===========================================\n\n";

echo "Now checking table existence...\n\n";

// Verify critical tables exist
$tables = [
    'item_pricing' => 'Phase 1.3',
    'rfqs' => 'Phase 1.4',
    'rfq_items' => 'Phase 1.4',
    'rfq_suppliers' => 'Phase 1.4',
    'rfq_quotes' => 'Phase 1.4',
    'accounting_integrations' => 'Phase 2.1',
    'project_cost_codes' => 'Phase 2.2',
    'budget_change_orders' => 'Phase 2.2',
    'po_change_orders' => 'Phase 2.2',
    'approval_workflows' => 'Phase 2.2',
    'approval_requests' => 'Phase 2.2',
    'project_roles' => 'Phase 2.2',
];

foreach ($tables as $table => $phase) {
    if (Schema::hasTable($table)) {
        $count = DB::table($table)->count();
        echo "✓ $table ($phase) - Records: $count\n";
    } else {
        echo "✗ $table ($phase) - NOT FOUND\n";
    }
}

// Check enhanced columns
echo "\nChecking enhanced columns...\n";
echo "----------------------------\n";

$columnChecks = [
    ['purchase_order_items', 'backorder_qty', 'Phase 1.5'],
    ['purchase_order_items', 'backorder_status', 'Phase 1.5'],
    ['budget_master', 'original_amount', 'Phase 2.2'],
    ['budget_master', 'committed', 'Phase 2.2'],
    ['budget_master', 'actual', 'Phase 2.2'],
    ['cost_code_master', 'parent_code', 'Phase 2.2'],
    ['cost_code_master', 'full_code', 'Phase 2.2'],
    ['cost_code_master', 'level', 'Phase 2.2'],
];

foreach ($columnChecks as [$table, $column, $phase]) {
    if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
        echo "✓ $table.$column ($phase)\n";
    } else {
        echo "✗ $table.$column ($phase) - NOT FOUND\n";
    }
}

echo "\n===========================================\n";
echo "  Database Verification Complete\n";
echo "===========================================\n";

function studly_case($value) {
    $value = ucwords(str_replace(['-', '_'], ' ', $value));
    return str_replace(' ', '', $value);
}
