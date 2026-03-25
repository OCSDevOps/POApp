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

use Illuminate\Support\Facades\Route;

// Get all GET routes
$routes = Route::getRoutes();
$adminRoutes = [];

foreach ($routes as $route) {
    if (in_array('GET', $route->methods()) && str_starts_with($route->uri(), 'admin/')) {
        $name = $route->getName() ?? '';
        $uri = $route->uri();
        $action = $route->getActionName();

        // Get the view file it renders (from controller action)
        $adminRoutes[] = [
            'uri' => $uri,
            'name' => $name,
            'action' => $action,
        ];
    }
}

echo "=== ALL ADMIN GET ROUTES (" . count($adminRoutes) . " total) ===\n\n";

foreach ($adminRoutes as $r) {
    echo sprintf("%-55s %-45s %s\n", $r['uri'], $r['name'], $r['action']);
}

// Now simulate loading each view
echo "\n=== CHECKING VIEW FILES ===\n\n";

// Extract views from controller methods
$viewMappings = [
    'admin/dashboard' => 'admin.dashboard',
    'admin/projects' => 'admin.project.project_list_view',
    'admin/projects/create' => 'admin.project.add_project',
    'admin/suppliers' => 'admin.supplier.supplier_list_view',
    'admin/suppliers/create' => 'admin.supplier.add_supplier',
    'admin/porder' => 'admin.porder.porder_list_view',
    'admin/porder/create' => 'admin.porder.add_pur_order',
    'admin/items' => 'admin.item.index',
    'admin/items/create' => 'admin.item.create',
    'admin/items/import' => 'admin.item.import',
    'admin/items/pricing-summary' => 'admin.item.pricing_summary',
    'admin/receive' => 'admin.receive.index',
    'admin/receive/create' => 'admin.receive.create',
    'admin/receive/back-orders' => 'admin.receive.back_order_report',
    'admin/receive/summary' => 'admin.receive.summary',
    'admin/budget' => 'admin.budget.index',
    'admin/budget/create' => 'admin.budget.create',
    'admin/budget/summary' => 'admin.budget.summary',
    'admin/templates' => 'admin.template.index',
    'admin/templates/create' => 'admin.template.create',
    'admin/procore' => 'admin.procore.index',
    'admin/procore/settings' => 'admin.procore.settings',
    'admin/procore/project-mappings' => 'admin.procore.project_mappings',
    'admin/procore/cost-code-mappings' => 'admin.procore.cost_code_mappings',
    'admin/integrations' => 'admin.integrations.index',
    'admin/integrations/create' => 'admin.integrations.create',
    'admin/rfq' => 'admin.rfq.index',
    'admin/rfq/create' => 'admin.rfq.create',
    'admin/approvals' => 'admin.approvals.dashboard',
    'admin/budget-change-orders' => 'admin.budget-change-orders.index',
    'admin/budget-change-orders/create' => 'admin.budget-change-orders.create',
    'admin/po-change-orders' => 'admin.po-change-orders.index',
    'admin/po-change-orders/create' => 'admin.po-change-orders.create',
    'admin/backorders' => 'admin.backorders.index',
    'admin/pricing' => 'admin.pricing.index',
];

$viewFinder = app('view')->getFinder();
$missing = [];
$found = [];

foreach ($viewMappings as $route => $view) {
    try {
        $path = $viewFinder->find($view);
        $found[] = $view;
    } catch (\Exception $e) {
        $missing[] = $view;
    }
}

echo "Views FOUND: " . count($found) . "\n";
echo "Views MISSING: " . count($missing) . "\n\n";

if (!empty($missing)) {
    echo "MISSING VIEWS:\n";
    foreach ($missing as $v) {
        echo "  - $v\n";
    }
}
