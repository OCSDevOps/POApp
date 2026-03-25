<?php
/**
 * Verify all routes are registered correctly by bootstrapping Laravel's route system.
 * Uses memory limit override to avoid OOM.
 */
ini_set('memory_limit', '2048M');

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Boot the application (registers routes, service providers)
$kernel->handle($request = Illuminate\Http\Request::capture());

$router = $app->make('router');
$routes = $router->getRoutes();

echo "Total routes registered: " . count($routes) . "\n\n";

// Routes we want to verify exist
$expectedRoutes = [
    // New Takeoff routes
    'admin.takeoffs.index' => 'GET /admincontrol/takeoffs',
    'admin.takeoffs.create' => 'GET /admincontrol/takeoffs/create',
    'admin.takeoffs.store' => 'POST /admincontrol/takeoffs',
    'admin.takeoffs.show' => 'GET /admincontrol/takeoffs/{id}',
    'admin.takeoffs.edit' => 'GET /admincontrol/takeoffs/{id}/edit',
    'admin.takeoffs.update' => 'PUT /admincontrol/takeoffs/{id}',
    'admin.takeoffs.destroy' => 'DELETE /admincontrol/takeoffs/{id}',
    'admin.takeoffs.upload-drawings' => 'POST /admincontrol/takeoffs/{id}/drawings',
    'admin.takeoffs.process-drawing' => 'POST /admincontrol/takeoffs/{id}/drawings/{drawingId}/process',
    'admin.takeoffs.delete-drawing' => 'DELETE /admincontrol/takeoffs/{id}/drawings/{drawingId}',
    'admin.takeoffs.download-drawing' => 'GET /admincontrol/takeoffs/{id}/drawings/{drawingId}/download',
    'admin.takeoffs.processing-status' => 'GET /admincontrol/takeoffs/{id}/processing-status',
    'admin.takeoffs.finalize' => 'POST /admincontrol/takeoffs/{id}/finalize',
    'admin.takeoffs.convert-to-po' => 'POST /admincontrol/takeoffs/{id}/convert-to-po',
    'admin.takeoffs.item-suggestions' => 'POST /admincontrol/takeoffs/item-suggestions',

    // New AI Settings routes
    'admin.ai-settings.index' => 'GET /admincontrol/ai-settings',
    'admin.ai-settings.update' => 'POST /admincontrol/ai-settings',
    'admin.ai-settings.test' => 'POST /admincontrol/ai-settings/test',

    // Scheduling Engine routes
    'admin.schedule.index' => 'GET /admincontrol/schedule',
    'admin.schedule.calendars' => 'GET /admincontrol/schedule/calendars',
    'admin.schedule.storeCalendar' => 'POST /admincontrol/schedule/calendars',
    'admin.schedule.show' => 'GET /admincontrol/schedule/{projectId}',
    'admin.schedule.calculate' => 'POST /admincontrol/schedule/{projectId}/calculate',
    'admin.schedule.ganttData' => 'GET /admincontrol/schedule/{projectId}/gantt-data',
    'admin.schedule.criticalPath' => 'GET /admincontrol/schedule/{projectId}/critical-path',
    'admin.schedule.health' => 'GET /admincontrol/schedule/{projectId}/health',
    'admin.schedule.lookahead' => 'GET /admincontrol/schedule/{projectId}/lookahead',
    'admin.schedule.updateSettings' => 'PUT /admincontrol/schedule/{projectId}/settings',
    'admin.schedule.storeActivity' => 'POST /admincontrol/schedule/{projectId}/activities',
    'admin.schedule.updateActivity' => 'PUT /admincontrol/schedule/{projectId}/activities/{activityId}',
    'admin.schedule.deleteActivity' => 'DELETE /admincontrol/schedule/{projectId}/activities/{activityId}',
    'admin.schedule.reorderActivities' => 'POST /admincontrol/schedule/{projectId}/activities/reorder',
    'admin.schedule.updateActuals' => 'POST /admincontrol/schedule/{projectId}/activities/{activityId}/actuals',
    'admin.schedule.storeDependency' => 'POST /admincontrol/schedule/{projectId}/dependencies',
    'admin.schedule.deleteDependency' => 'DELETE /admincontrol/schedule/{projectId}/dependencies/{depId}',
    'admin.schedule.storeDriver' => 'POST /admincontrol/schedule/{projectId}/drivers',
    'admin.schedule.updateDriver' => 'PUT /admincontrol/schedule/{projectId}/drivers/{driverId}',
    'admin.schedule.deleteDriver' => 'DELETE /admincontrol/schedule/{projectId}/drivers/{driverId}',
    'admin.schedule.createBaseline' => 'POST /admincontrol/schedule/{projectId}/baselines',
    'admin.schedule.baselineVariance' => 'GET /admincontrol/schedule/{projectId}/baselines/{baselineId}/variance',

    // Contract Management routes
    'admin.contracts.index' => 'GET /admincontrol/contracts',
    'admin.contracts.create' => 'GET /admincontrol/contracts/create',
    'admin.contracts.store' => 'POST /admincontrol/contracts/store',
    'admin.contracts.show' => 'GET /admincontrol/contracts/view/{id}',
    'admin.contracts.edit' => 'GET /admincontrol/contracts/edit/{id}',
    'admin.contracts.update' => 'PUT /admincontrol/contracts/update/{id}',
    'admin.contracts.destroy' => 'DELETE /admincontrol/contracts/delete/{id}',
    'admin.contracts.updatestatus' => 'POST /admincontrol/contracts/update-status/{id}',
    'admin.contracts.upload-documents' => 'POST /admincontrol/contracts/{id}/upload-documents',
    'admin.contracts.download-document' => 'GET /admincontrol/contracts/{id}/documents/{docId}/download',
    'admin.contracts.delete-document' => 'DELETE /admincontrol/contracts/{id}/documents/{docId}',
    'admin.contracts.release-retention' => 'POST /admincontrol/contracts/{id}/release-retention',

    // Contract Invoices
    'admin.contracts.invoices.index' => 'GET /admincontrol/contracts/{contractId}/invoices',
    'admin.contracts.invoices.create' => 'GET /admincontrol/contracts/{contractId}/invoices/create',
    'admin.contracts.invoices.store' => 'POST /admincontrol/contracts/{contractId}/invoices/store',
    'admin.contracts.invoices.show' => 'GET /admincontrol/contracts/{contractId}/invoices/{id}',
    'admin.contracts.invoices.pay' => 'POST /admincontrol/contracts/{contractId}/invoices/{id}/pay',

    // Contract Change Orders
    'admin.contract-change-orders.index' => 'GET /admincontrol/contract-change-orders',
    'admin.contract-change-orders.create' => 'GET /admincontrol/contract-change-orders/contracts/{contractId}/create',
    'admin.contract-change-orders.store' => 'POST /admincontrol/contract-change-orders/contracts/{contractId}',
    'admin.contract-change-orders.show' => 'GET /admincontrol/contract-change-orders/{id}',
    'admin.contract-change-orders.submit' => 'POST /admincontrol/contract-change-orders/{id}/submit',
    'admin.contract-change-orders.approve' => 'POST /admincontrol/contract-change-orders/{id}/approve',
    'admin.contract-change-orders.reject' => 'POST /admincontrol/contract-change-orders/{id}/reject',
    'admin.contract-change-orders.cancel' => 'POST /admincontrol/contract-change-orders/{id}/cancel',

    // Supplier Compliance
    'admin.supplier-compliance.index' => 'GET /admincontrol/suppliers/{supplierId}/compliance',
    'admin.supplier-compliance.store' => 'POST /admincontrol/suppliers/{supplierId}/compliance',
    'admin.supplier-compliance.update' => 'PUT /admincontrol/suppliers/{supplierId}/compliance/{id}',
    'admin.supplier-compliance.destroy' => 'DELETE /admincontrol/suppliers/{supplierId}/compliance/{id}',
    'admin.compliance.upload' => 'POST /admincontrol/compliance/{id}/upload',
    'admin.compliance.download' => 'GET /admincontrol/compliance/{id}/download',
    'admin.compliance.dashboard' => 'GET /admincontrol/compliance/dashboard',

    // Some existing routes to confirm nothing broke
    'admin.dashboard' => 'GET /admincontrol/dashboard',
    'admin.porder.index' => 'GET /admincontrol/porder',
    'admin.projects.index' => 'GET /admincontrol/projects',
    'admin.budget.index' => 'GET /admincontrol/budgets',
    'admin.profile.index' => 'GET /admincontrol/profile',
];

$passed = 0;
$failed = 0;

foreach ($expectedRoutes as $name => $expected) {
    $route = $routes->getByName($name);
    if ($route) {
        $methods = implode('|', $route->methods());
        $uri = $route->uri();
        echo "PASS: $name => $methods /$uri\n";
        $passed++;
    } else {
        echo "FAIL: $name => Route not found!\n";
        $failed++;
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "RESULTS: $passed passed, $failed failed out of " . count($expectedRoutes) . " expected routes\n";
echo "Total routes in application: " . count($routes) . "\n";
echo str_repeat('=', 60) . "\n";
