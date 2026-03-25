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
    'purchase_order_master', 'purchase_order_details',
    'project_master', 'supplier_master', 'item_master',
    'receive_order_master', 'receive_order_details',
    'cost_code_master', 'supplier_catalog_tab',
    'item_category_tab', 'unit_of_measure_tab', 'taxgroup_master',
    'users', 'companies', 'supplier_users', 'budgets',
    'po_templates', 'po_template_items',
    'budget_change_orders', 'po_change_orders',
    'approval_workflows', 'approval_workflow_steps', 'approval_requests',
    'procore_sync_logs', 'procore_project_mappings', 'procore_cost_code_mappings',
    'integrations', 'integration_sync_logs',
    'rfqs', 'rfq_items', 'rfq_suppliers', 'rfq_quotes',
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
