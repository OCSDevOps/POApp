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

$tables = [
    'po_template_items',
    'rfq_items', 'rfq_suppliers', 'rfq_quotes', 'rfqs',
    'integration_field_mappings', 'integration_sync_logs',
    'budget_change_orders', 'po_change_orders',
    'approval_workflows', 'approval_requests',
    'rfq_master', 'commitment_master', 'project_roles', 'project_cost_codes',
    'item_price_history', 'accounting_integrations',
];

foreach ($tables as $table) {
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
