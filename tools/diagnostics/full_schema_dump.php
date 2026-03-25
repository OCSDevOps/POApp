<?php
ini_set('memory_limit', '512M');
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->instance('request', Illuminate\Http\Request::capture());
$app->bootstrapWith([
    Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    Illuminate\Foundation\Bootstrap\BootProviders::class,
]);

use Illuminate\Support\Facades\DB;

// Get ALL tables in the database
$allTables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_CATALOG = 'porder_db' ORDER BY TABLE_NAME");

echo "=== ALL TABLES IN porder_db ===\n";
foreach ($allTables as $t) {
    echo "  - " . $t->TABLE_NAME . "\n";
}
echo "\nTotal: " . count($allTables) . " tables\n";

// Now dump schema for tables we care about
$focusTables = ['budget_master', 'po_templates', 'procore_sync_logs', 'procore_project_mappings', 'procore_cost_code_mappings', 'integrations'];

echo "\n\n=== SCHEMA FOR PREVIOUSLY-MISSING TABLES ===\n";
foreach ($focusTables as $table) {
    $cols = DB::select("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH, COLUMN_DEFAULT
        FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? ORDER BY ORDINAL_POSITION", [$table]);
    if (empty($cols)) {
        echo "\n$table: TABLE NOT FOUND\n";
        continue;
    }
    echo "\n$table:\n";
    foreach ($cols as $c) {
        $type = $c->DATA_TYPE;
        if ($c->CHARACTER_MAXIMUM_LENGTH && $c->CHARACTER_MAXIMUM_LENGTH > 0) {
            $type .= "({$c->CHARACTER_MAXIMUM_LENGTH})";
        } elseif ($c->CHARACTER_MAXIMUM_LENGTH == -1) {
            $type .= "(max)";
        }
        $null = $c->IS_NULLABLE === 'YES' ? 'nullable' : 'required';
        $def = $c->COLUMN_DEFAULT ? " default={$c->COLUMN_DEFAULT}" : '';
        echo "  {$c->COLUMN_NAME} ({$type}) {$null}{$def}\n";
    }
}
