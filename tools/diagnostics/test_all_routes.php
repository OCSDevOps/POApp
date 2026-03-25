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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

// Login as admin
$user = \App\Models\User::where('email', 'admin@demo.com')->first();
if (!$user) {
    echo "ERROR: admin@demo.com user not found!\n";
    exit(1);
}
Auth::login($user);
Session::put('company_id', 1);

echo "Logged in as: {$user->email} (company_id=1)\n\n";

// Test views by rendering them with required data
$tests = [];
$passed = 0;
$failed = 0;

function testView($name, $viewName, $data = []) {
    global $passed, $failed;
    try {
        $html = view($viewName, $data)->render();
        $len = strlen($html);
        echo "  PASS: {$name} ({$viewName}) - {$len} bytes\n";
        $passed++;
        return true;
    } catch (\Throwable $e) {
        $msg = $e->getMessage();
        // Truncate long messages
        if (strlen($msg) > 120) $msg = substr($msg, 0, 120) . '...';
        echo "  FAIL: {$name} ({$viewName}) - {$msg}\n";
        $failed++;
        return false;
    }
}

// Get sample data for testing
$projects = \App\Models\Project::active()->orderByName()->take(5)->get();
$suppliers = \App\Models\Supplier::active()->orderByName()->take(5)->get();
$costCodes = \App\Models\CostCode::active()->orderById()->take(5)->get();
$items = \App\Models\Item::with(['category', 'costCode', 'unitOfMeasure'])->take(5)->get();
$categories = \App\Models\ItemCategory::active()->orderByName()->get();
$uoms = \App\Models\UnitOfMeasure::active()->orderByName()->get();
$taxGroups = \App\Models\TaxGroup::all();

echo "=== TESTING ADMIN VIEWS ===\n\n";

// Dashboard
echo "--- Dashboard ---\n";
testView('Dashboard', 'admin.main', [
    'poCount' => 5, 'supplierCount' => 3, 'projectCount' => 4, 'itemCount' => 15,
    'recentPOs' => collect(), 'pendingApprovals' => 0, 'budgetAlerts' => collect(),
    'monthlySpend' => collect(), 'topSuppliers' => collect(),
]);

// Projects
echo "\n--- Projects ---\n";
testView('Project List', 'admin.project.project_list_view', ['projects' => $projects]);
testView('Add Project', 'admin.project.add_project', []);
if ($projects->count()) {
    $proj = $projects->first();
    testView('View Project', 'admin.project.view_project', ['project' => $proj]);
    testView('Edit Project', 'admin.project.edit_project', ['project' => $proj]);
}

// Suppliers
echo "\n--- Suppliers ---\n";
testView('Supplier List', 'admin.supplier.supplier_list_view', ['suppliers' => $suppliers]);
testView('Add Supplier', 'admin.supplier.add_supplier', []);
if ($suppliers->count()) {
    $sup = $suppliers->first();
    testView('View Supplier', 'admin.supplier.view_supplier', ['supplier' => $sup]);
    testView('Edit Supplier', 'admin.supplier.edit_supplier', ['supplier' => $sup]);
}

// Purchase Orders
echo "\n--- Purchase Orders ---\n";
$pos = \App\Models\PurchaseOrder::with(['project', 'supplier', 'items'])->take(5)->get();
testView('PO List', 'admin.porder.porder_list_view', ['purchaseOrders' => $pos]);
testView('Add PO', 'admin.porder.add_pur_order', [
    'items' => $items, 'projects' => $projects, 'suppliers' => $suppliers,
    'packages' => collect(), 'taxGroups' => $taxGroups, 'costCodes' => $costCodes,
    'categories' => $categories, 'uoms' => $uoms, 'budgetInfo' => null,
]);
if ($pos->count()) {
    $po = $pos->first();
    testView('View PO', 'admin.porder.view_pur_order', ['purchaseOrder' => $po]);
    testView('Edit PO', 'admin.porder.edit_pur_order', [
        'purchaseOrder' => $po, 'items' => $items, 'projects' => $projects,
        'suppliers' => $suppliers, 'packages' => collect(), 'taxGroups' => $taxGroups,
        'costCodes' => $costCodes, 'categories' => $categories, 'uoms' => $uoms,
    ]);
}

// PDF View
echo "\n--- PDF ---\n";
if ($pos->count()) {
    testView('PO PDF', 'admin.pdf_view.purchase_order', ['purchaseOrder' => $pos->first()]);
}

// Items
echo "\n--- Items ---\n";
testView('Item List', 'admin.item.index', [
    'items' => $items, 'categories' => $categories, 'costCodes' => $costCodes,
]);
testView('Create Item', 'admin.item.create', [
    'categories' => $categories, 'costCodes' => $costCodes, 'uoms' => $uoms,
]);
if ($items->count()) {
    $item = $items->first();
    testView('Show Item', 'admin.item.show', [
        'item' => $item, 'supplierCatalog' => collect(), 'priceHistory' => collect(),
        'pricingSummary' => null,
    ]);
    testView('Edit Item', 'admin.item.edit', [
        'item' => $item, 'categories' => $categories, 'costCodes' => $costCodes, 'uoms' => $uoms,
    ]);
}
testView('Price Comparison', 'admin.item.price_comparison', [
    'item' => $items->first() ?? new \App\Models\Item(), 'comparison' => collect(),
]);
testView('Price History', 'admin.item.price_history', [
    'item' => $items->first() ?? new \App\Models\Item(), 'history' => collect(),
]);
testView('Pricing Summary', 'admin.item.pricing_summary', [
    'summary' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20), 'categories' => $categories,
]);
testView('Item Import', 'admin.item.import', []);

// Receive Orders
echo "\n--- Receive Orders ---\n";
$ros = \App\Models\ReceiveOrder::with(['purchaseOrder.project', 'purchaseOrder.supplier'])->take(5)->get();
testView('RO List', 'admin.receive.index', ['receiveOrders' => $ros, 'summary' => collect()]);
testView('Select PO', 'admin.receive.select_po', ['purchaseOrders' => $pos]);
if ($pos->count()) {
    $receivedQtys = [];
    testView('Create RO', 'admin.receive.create', [
        'purchaseOrder' => $pos->first(), 'receivedQtys' => $receivedQtys,
    ]);
}
if ($ros->count()) {
    $ro = $ros->first();
    $ro->load(['purchaseOrder.project', 'purchaseOrder.supplier', 'items.item']);
    testView('Show RO', 'admin.receive.show', ['receiveOrder' => $ro]);
    $ro->load(['purchaseOrder.items', 'items']);
    testView('Edit RO', 'admin.receive.edit', ['receiveOrder' => $ro]);
}
testView('Back Order Report', 'admin.receive.back_order_report', [
    'backOrders' => collect(), 'supplierSummary' => collect(),
]);
testView('Receiving Summary', 'admin.receive.summary', ['summary' => collect()]);

// Budget
echo "\n--- Budget ---\n";
$budgets = \App\Models\Budget::with(['project', 'costCode'])->take(5)->get();
testView('Budget List', 'admin.budget.index', [
    'budgets' => new \Illuminate\Pagination\LengthAwarePaginator($budgets->toArray(), $budgets->count(), 15),
    'projects' => $projects, 'costCodes' => $costCodes, 'summary' => collect(),
]);
testView('Create Budget', 'admin.budget.create', ['projects' => $projects, 'costCodes' => $costCodes]);
if ($budgets->count()) {
    $budget = $budgets->first();
    testView('Show Budget', 'admin.budget.show', ['budget' => $budget, 'purchaseOrders' => collect()]);
    testView('Edit Budget', 'admin.budget.edit', [
        'budget' => $budget, 'projects' => $projects, 'costCodes' => $costCodes,
    ]);
}
testView('Budget Summary', 'admin.budget.summary', ['summary' => collect(), 'projects' => $projects]);

// Templates
echo "\n--- Templates ---\n";
$templates = \App\Models\PoTemplate::with(['supplier', 'project', 'items'])->take(5)->get();
testView('Template List', 'admin.template.index', ['templates' => $templates]);
testView('Create Template', 'admin.template.create', [
    'projects' => $projects, 'suppliers' => $suppliers, 'items' => $items,
    'costCodes' => $costCodes, 'uoms' => $uoms,
]);
if ($templates->count()) {
    $tmpl = $templates->first();
    testView('Show Template', 'admin.template.show', ['template' => $tmpl]);
    testView('Edit Template', 'admin.template.edit', [
        'template' => $tmpl, 'projects' => $projects, 'suppliers' => $suppliers,
        'items' => $items, 'costCodes' => $costCodes, 'uoms' => $uoms,
    ]);
    testView('Create PO from Template', 'admin.template.create_po', [
        'template' => $tmpl, 'projects' => $projects, 'suppliers' => $suppliers,
    ]);
}

// Procore
echo "\n--- Procore ---\n";
testView('Procore Index', 'admin.procore.index', [
    'lastSync' => null, 'syncHistory' => collect(), 'projectMappings' => collect(),
    'costCodeMappingsCount' => 0,
]);
testView('Procore Sync Log', 'admin.procore.sync_log', [
    'log' => new \App\Models\ProcoreSyncLog(),
]);
testView('Procore Project Mappings', 'admin.procore.project_mappings', [
    'mappings' => collect(), 'unmappedProjects' => collect(),
]);
testView('Procore Cost Code Mappings', 'admin.procore.cost_code_mappings', [
    'mappings' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20), 'projects' => $projects,
]);
testView('Procore Settings', 'admin.procore.settings', [
    'settings' => (object)['client_id' => '', 'base_url' => '', 'company_id' => ''],
]);

// Integrations
echo "\n--- Integrations ---\n";
testView('Integrations Index', 'admin.integrations.index', ['integrations' => collect()]);
testView('Create Integration', 'admin.integrations.create', []);
testView('Integration Logs', 'admin.integrations.logs', [
    'integration' => new \App\Models\AccountingIntegration(),
    'logs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
]);

// RFQ
echo "\n--- RFQ ---\n";
$rfqs = \App\Models\Rfq::with(['project', 'suppliers', 'items'])->take(5)->get();
testView('RFQ List', 'admin.rfq.index', ['rfqs' => $rfqs, 'projects' => $projects]);
testView('Create RFQ', 'admin.rfq.create', [
    'projects' => $projects, 'suppliers' => $suppliers, 'items' => $items, 'uoms' => $uoms,
]);
if ($rfqs->count()) {
    $rfq = $rfqs->first();
    testView('Show RFQ', 'admin.rfq.show', ['rfq' => $rfq]);
    testView('Edit RFQ', 'admin.rfq.edit', [
        'rfq' => $rfq, 'projects' => $projects, 'suppliers' => $suppliers,
        'items' => $items, 'uoms' => $uoms,
    ]);
    testView('Compare RFQ', 'admin.rfq.compare', ['rfq' => $rfq]);
}

// Approvals
echo "\n--- Approvals & Change Orders ---\n";
testView('Approvals Dashboard', 'admin.approvals.dashboard', [
    'pendingRequests' => collect(), 'myRequests' => collect(), 'recentActions' => collect(),
]);
testView('BCO Index', 'admin.budget-change-orders.index', [
    'changeOrders' => collect(), 'projects' => $projects,
]);
testView('BCO Create', 'admin.budget-change-orders.create', [
    'budgets' => $budgets, 'projects' => $projects, 'costCodes' => $costCodes,
]);
testView('PCO Index', 'admin.po-change-orders.index', [
    'changeOrders' => collect(), 'purchaseOrders' => $pos,
]);
testView('PCO Create', 'admin.po-change-orders.create', [
    'purchaseOrders' => $pos,
]);

// Backorders & Pricing
echo "\n--- Backorders & Pricing ---\n";
testView('Backorders', 'admin.backorders.index', [
    'backOrders' => collect(), 'projects' => $projects, 'suppliers' => $suppliers,
]);
testView('Pricing', 'admin.pricing.index', [
    'items' => $items, 'categories' => $categories,
]);

// Budgets (different from budget - these are setup views)
echo "\n--- Budget Setup ---\n";
testView('Budget Setup', 'admin.budgets.setup', [
    'projects' => $projects, 'budgets' => $budgets,
]);
testView('Budget View', 'admin.budgets.view', [
    'project' => $projects->first() ?? new \App\Models\Project(),
    'budgets' => $budgets, 'summary' => (object)['total' => 0, 'committed' => 0, 'spent' => 0, 'remaining' => 0],
]);
testView('Assign Cost Codes', 'admin.budgets.assign-cost-codes', [
    'project' => $projects->first() ?? new \App\Models\Project(),
    'costCodes' => $costCodes, 'assignedCostCodes' => collect(),
]);

echo "\n=== RESULTS ===\n";
echo "PASSED: {$passed}\n";
echo "FAILED: {$failed}\n";
echo "TOTAL:  " . ($passed + $failed) . "\n";
