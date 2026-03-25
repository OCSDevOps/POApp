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

$views = [
    'vw_budget_summary',
    'vw_receiving_summary',
    'vw_back_order_report',
    'vw_item_pricing_summary',
    'vw_supplier_performance',
];

foreach ($views as $view) {
    try {
        $result = DB::select("SELECT TOP 0 * FROM {$view}");
        echo "{$view}: EXISTS\n";
    } catch (\Exception $e) {
        echo "{$view}: MISSING - {$e->getMessage()}\n";
    }
}
