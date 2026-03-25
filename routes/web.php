<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\RfqController;
use App\Http\Controllers\Admin\BudgetController;
use App\Http\Controllers\Admin\ReceiveOrderController;
use App\Http\Controllers\Admin\PoTemplateController;
use App\Http\Controllers\Admin\SupplierCatalogController;
use App\Http\Controllers\Admin\ProcoreController;
use App\Http\Controllers\Admin\CostCodeController;
use App\Http\Controllers\Admin\UnitOfMeasureController;
use App\Http\Controllers\Admin\TaxGroupController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\PermissionTemplateController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ItemPackageController;
use App\Http\Controllers\Admin\ChecklistController;
use App\Http\Controllers\Admin\PerformChecklistController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\AttachmentController;
use App\Http\Controllers\Admin\TakeoffController;
use App\Http\Controllers\Admin\AiSettingsController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\ContractChangeOrderController;
use App\Http\Controllers\Admin\ContractInvoiceController;
use App\Http\Controllers\Admin\SupplierComplianceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Route::controller(AuthController::class)->group(function(){
    Route::get('/','index')->name('login');
    Route::post('validate_login','validate_login')->name('auth.validatelogin');
    Route::get('two-factor-challenge', 'showTwoFactorChallenge')->name('auth.2fa.challenge');
    Route::post('two-factor-challenge', 'verifyTwoFactorChallenge')->name('auth.2fa.verify');
    Route::get('generate_hash','generate_hash')->name('auth.generatehash');
    Route::get('logout','logout')->name('auth.logout');
});

// Reports Routes
Route::controller(ReportsController::class)->group(function(){
    Route::get('ccsummary','getCcSummary')->name('ccsummary');
    Route::post('ccsummary','getCcSummary')->name('ccsummary');
});

// Dashboard Routes (Legacy - DISABLED to prevent theme confusion)
// These routes used the old HYPER theme. Use admin.dashboard instead.
// Route::controller(DashboardController::class)->group(function(){
//     Route::get('dashboard','index')->name('dashboard');
//     Route::get('dashboard-analytics','dashboard_analytics')->name('dashboard.analytics');
//     Route::get('dashboard-ecommerce','dashboard_ecommerce')->name('dashboard.ecommerce');
//     Route::get('dashboard-projects','dashboard_projects')->name('dashboard.projects');
// });

// Legacy admin dashboard alias kept for backwards compatibility.
Route::middleware(['auth'])->get('/admin/dashboard', function () {
    return redirect()->route('admin.dashboard');
});

// Admin Routes (Protected by auth middleware)
Route::middleware(['auth'])->prefix('admincontrol')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/chart-data', [AdminDashboardController::class, 'getPODataForChart'])->name('dashboard.chartdata');
    Route::get('logout', [AdminDashboardController::class, 'logout'])->name('logout');
    
    // Company Management (Super Admin Only)
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CompaniesController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\CompaniesController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\CompaniesController::class, 'store'])->name('store');
        Route::get('/{company}', [\App\Http\Controllers\Admin\CompaniesController::class, 'show'])->name('show');
        Route::get('/{company}/edit', [\App\Http\Controllers\Admin\CompaniesController::class, 'edit'])->name('edit');
        Route::put('/{company}', [\App\Http\Controllers\Admin\CompaniesController::class, 'update'])->name('update');
        Route::delete('/{company}', [\App\Http\Controllers\Admin\CompaniesController::class, 'destroy'])->name('destroy');
        Route::post('/{company}/switch', [\App\Http\Controllers\Admin\CompaniesController::class, 'switch'])->name('switch');
    });
    
    // Purchase Orders
    Route::prefix('porder')->name('porder.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/all_purchase_order_list', [PurchaseOrderController::class, 'index'])->name('list');
        Route::get('/add_new_purchase_order', [PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/store', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/view/{id}', [PurchaseOrderController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [PurchaseOrderController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PurchaseOrderController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
        Route::post('/update-status/{id}', [PurchaseOrderController::class, 'updateStatus'])->name('updatestatus');
        Route::get('/pdf/{id}', [PurchaseOrderController::class, 'generatePdf'])->name('pdf');
        Route::get('/view/{id}/attachments/{attachmentId}/download', [PurchaseOrderController::class, 'downloadAttachment'])
            ->name('attachments.download');
        
        // AJAX Routes
        Route::post('/get-item-master-list', [PurchaseOrderController::class, 'getItemMasterList'])->name('itemlist');
        Route::post('/get-supplier-catalog-list', [PurchaseOrderController::class, 'getSupplierCatalogList'])->name('cataloglist');
        Route::post('/get-project-address', [PurchaseOrderController::class, 'getProjectAddress'])->name('projectaddress');
        Route::post('/check-budget-availability', [PurchaseOrderController::class, 'checkBudgetAvailability'])->name('check-budget');
    });
    
    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/create', [ProjectController::class, 'create'])->name('create');
        Route::post('/store', [ProjectController::class, 'store'])->name('store');
        Route::get('/view/{id}', [ProjectController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [ProjectController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ProjectController::class, 'destroy'])->name('destroy');
        Route::post('/update-status/{id}', [ProjectController::class, 'updateStatus'])->name('updatestatus');
    });
    
    // Suppliers
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/store', [SupplierController::class, 'store'])->name('store');
        Route::get('/view/{id}', [SupplierController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SupplierController::class, 'destroy'])->name('destroy');
        Route::post('/update-status/{id}', [SupplierController::class, 'updateStatus'])->name('updatestatus');
    });

    // Items
    Route::prefix('items')->name('item.')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('index');
        Route::get('/create', [ItemController::class, 'create'])->name('create');
        Route::post('/store', [ItemController::class, 'store'])->name('store');
        Route::get('/view/{id}', [ItemController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [ItemController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ItemController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ItemController::class, 'destroy'])->name('destroy');
        Route::get('/price-comparison/{id}', [ItemController::class, 'priceComparison'])->name('pricecomparison');
        Route::get('/price-history/{id}', [ItemController::class, 'priceHistory'])->name('pricehistory');
        Route::post('/update-price/{id}', [ItemController::class, 'updatePrice'])->name('updateprice');
        Route::get('/pricing-summary', [ItemController::class, 'pricingSummary'])->name('pricingsummary');
        Route::get('/import', [ItemController::class, 'import'])->name('import');
        Route::post('/import', [ItemController::class, 'import'])->name('import.store');
        Route::get('/export', [ItemController::class, 'export'])->name('export');
    });

    // Item Pricing (read-only admin view)
    Route::prefix('pricing')->name('pricing.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ItemPricingController::class, 'index'])->name('index');
    });

    // RFQ (Request for Quote)
    Route::prefix('rfq')->name('rfq.')->group(function () {
        Route::get('/', [RfqController::class, 'index'])->name('index');
        Route::get('/create', [RfqController::class, 'create'])->name('create');
        Route::post('/store', [RfqController::class, 'store'])->name('store');
        Route::get('/view/{id}', [RfqController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [RfqController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [RfqController::class, 'update'])->name('update');
        Route::post('/send/{id}', [RfqController::class, 'send'])->name('send');
        Route::post('/record-quote/{id}/{supplierId}', [RfqController::class, 'recordQuote'])->name('recordquote');
        Route::post('/convert-to-po/{id}', [RfqController::class, 'convertToPo'])->name('converttopo');
        Route::post('/cancel/{id}', [RfqController::class, 'cancel'])->name('cancel');
        Route::get('/compare-quotes/{id}', [RfqController::class, 'compareQuotes'])->name('comparequotes');
    });

    // Budgets
    Route::prefix('budgets')->name('budget.')->group(function () {
        Route::get('/', [BudgetController::class, 'index'])->name('index');
        Route::get('/create', [BudgetController::class, 'create'])->name('create');
        Route::post('/store', [BudgetController::class, 'store'])->name('store');
        Route::get('/view/{id}', [BudgetController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [BudgetController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [BudgetController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [BudgetController::class, 'destroy'])->name('destroy');
        Route::post('/transfer', [BudgetController::class, 'transfer'])->name('transfer');
        Route::get('/summary', [BudgetController::class, 'summary'])->name('summary');
        Route::post('/import-procore', [BudgetController::class, 'importFromProcore'])->name('importprocore');
    });

    // Receive Orders
    Route::prefix('receive')->name('receive.')->group(function () {
        Route::get('/', [ReceiveOrderController::class, 'index'])->name('index');
        Route::get('/create', [ReceiveOrderController::class, 'create'])->name('create');
        Route::post('/store', [ReceiveOrderController::class, 'store'])->name('store');
        Route::get('/view/{id}', [ReceiveOrderController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [ReceiveOrderController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ReceiveOrderController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ReceiveOrderController::class, 'destroy'])->name('destroy');
        Route::get('/back-order-report', [ReceiveOrderController::class, 'backOrderReport'])->name('backorderreport');
        Route::get('/summary', [ReceiveOrderController::class, 'receivingSummary'])->name('summary');
    });

    // Backorders dashboard
    Route::prefix('backorders')->name('backorders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BackorderController::class, 'index'])->name('index');
    });

    // PO Templates
    Route::prefix('templates')->name('template.')->group(function () {
        Route::get('/', [PoTemplateController::class, 'index'])->name('index');
        Route::get('/create', [PoTemplateController::class, 'create'])->name('create');
        Route::post('/store', [PoTemplateController::class, 'store'])->name('store');
        Route::get('/view/{id}', [PoTemplateController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [PoTemplateController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PoTemplateController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PoTemplateController::class, 'destroy'])->name('destroy');
        Route::post('/duplicate/{id}', [PoTemplateController::class, 'duplicate'])->name('duplicate');
        Route::get('/create-po/{id}', [PoTemplateController::class, 'createPo'])->name('createpo');
        Route::post('/store-po/{id}', [PoTemplateController::class, 'storePo'])->name('storepo');
    });

    // Supplier Portal / Catalog
    Route::prefix('supplier/{supplierId}')->name('supplier.')->group(function () {
        Route::get('/dashboard', [SupplierCatalogController::class, 'dashboard'])->name('dashboard');
        Route::get('/catalog', [SupplierCatalogController::class, 'index'])->name('catalog.index');
        Route::get('/catalog/create', [SupplierCatalogController::class, 'create'])->name('catalog.create');
        Route::post('/catalog/store', [SupplierCatalogController::class, 'store'])->name('catalog.store');
        Route::get('/catalog/edit/{catalogId}', [SupplierCatalogController::class, 'edit'])->name('catalog.edit');
        Route::put('/catalog/update/{catalogId}', [SupplierCatalogController::class, 'update'])->name('catalog.update');
        Route::delete('/catalog/delete/{catalogId}', [SupplierCatalogController::class, 'destroy'])->name('catalog.destroy');
        Route::post('/catalog/bulk-update-prices', [SupplierCatalogController::class, 'bulkUpdatePrices'])->name('catalog.bulkupdate');
        Route::get('/catalog/import', [SupplierCatalogController::class, 'import'])->name('catalog.import');
        Route::post('/catalog/import', [SupplierCatalogController::class, 'import'])->name('catalog.import.store');
        Route::get('/catalog/export', [SupplierCatalogController::class, 'export'])->name('catalog.export');
        Route::get('/performance', [SupplierCatalogController::class, 'performance'])->name('performance');
    });

    // Procore Integration
    Route::prefix('procore')->name('procore.')->group(function () {
        Route::get('/', [ProcoreController::class, 'index'])->name('index');
        Route::post('/sync-all', [ProcoreController::class, 'syncAll'])->name('syncall');
        Route::post('/sync-projects', [ProcoreController::class, 'syncProjects'])->name('syncprojects');
        Route::post('/sync-vendors', [ProcoreController::class, 'syncVendors'])->name('syncvendors');
        Route::post('/sync-cost-codes', [ProcoreController::class, 'syncCostCodes'])->name('synccostcodes');
        Route::post('/sync-budgets', [ProcoreController::class, 'syncBudgets'])->name('syncbudgets');
        Route::post('/sync-commitments', [ProcoreController::class, 'syncCommitments'])->name('synccommitments');
        Route::post('/push-po', [ProcoreController::class, 'pushPurchaseOrder'])->name('pushpo');
        Route::get('/sync-log/{id}', [ProcoreController::class, 'syncLog'])->name('synclog');
        Route::get('/project-mappings', [ProcoreController::class, 'projectMappings'])->name('projectmappings');
        Route::put('/project-mappings/{procoreProjectId}', [ProcoreController::class, 'updateProjectMapping'])->name('updateprojectmapping');
        Route::get('/cost-code-mappings', [ProcoreController::class, 'costCodeMappings'])->name('costcodemappings');
        Route::get('/settings', [ProcoreController::class, 'settings'])->name('settings');
        Route::post('/settings', [ProcoreController::class, 'updateSettings'])->name('updatesettings');
        Route::post('/test-connection', [ProcoreController::class, 'testConnection'])->name('testconnection');
    });

    // Accounting Integrations (Sage, QuickBooks, etc.)
    Route::prefix('integrations')->name('integrations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\IntegrationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\IntegrationController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Admin\IntegrationController::class, 'store'])->name('store');
        Route::get('/oauth/{type}/callback', [\App\Http\Controllers\Admin\IntegrationController::class, 'oauthCallback'])->name('oauth.callback');
        Route::post('/test-connection/{id}', [\App\Http\Controllers\Admin\IntegrationController::class, 'testConnection'])->name('testconnection');
        Route::post('/sync-purchase-orders/{id}', [\App\Http\Controllers\Admin\IntegrationController::class, 'syncPurchaseOrders'])->name('syncpurchaseorders');
        Route::post('/sync-vendors/{id}', [\App\Http\Controllers\Admin\IntegrationController::class, 'syncVendors'])->name('syncvendors');
        Route::get('/logs/{id}', [\App\Http\Controllers\Admin\IntegrationController::class, 'logs'])->name('logs');
        Route::put('/update/{id}', [\App\Http\Controllers\Admin\IntegrationController::class, 'update'])->name('update');
        Route::post('/toggle-active/{id}', [\App\Http\Controllers\Admin\IntegrationController::class, 'toggleActive'])->name('toggleactive');
        Route::delete('/delete/{id}', [\App\Http\Controllers\Admin\IntegrationController::class, 'destroy'])->name('destroy');
    });

    // Cost Codes
    Route::prefix('costcodes')->name('costcodes.')->group(function () {
        Route::get('/', [CostCodeController::class, 'index'])->name('index');
        Route::post('/', [CostCodeController::class, 'store'])->name('store');
        Route::put('/{costcode}', [CostCodeController::class, 'update'])->name('update');
        Route::delete('/{costcode}', [CostCodeController::class, 'destroy'])->name('destroy');
        
        // Hierarchical cost code management
        Route::get('/hierarchy', [CostCodeController::class, 'hierarchy'])->name('hierarchy');
        Route::post('/hierarchical', [CostCodeController::class, 'storeHierarchical'])->name('store-hierarchical');
        Route::put('/hierarchical/{costcode}', [CostCodeController::class, 'updateHierarchical'])->name('update-hierarchical');
        Route::get('/{parentCode}/children', [CostCodeController::class, 'getChildCodes'])->name('children');
    });

    // Cost Code Templates
    Route::prefix('costcode-templates')->name('costcode-templates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CostCodeTemplateController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\CostCodeTemplateController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\CostCodeTemplateController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\CostCodeTemplateController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\CostCodeTemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\CostCodeTemplateController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\CostCodeTemplateController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/cost-codes', [\App\Http\Controllers\Admin\CostCodeTemplateController::class, 'getCostCodes'])->name('cost-codes');
    });

    // Units of Measure
    Route::prefix('uom')->name('uom.')->group(function () {
        Route::get('/', [UnitOfMeasureController::class, 'index'])->name('index');
        Route::post('/', [UnitOfMeasureController::class, 'store'])->name('store');
        Route::put('/{uom}', [UnitOfMeasureController::class, 'update'])->name('update');
        Route::delete('/{uom}', [UnitOfMeasureController::class, 'destroy'])->name('destroy');
    });

    // Tax Groups
    Route::prefix('taxgroups')->name('taxgroups.')->group(function () {
        Route::get('/', [TaxGroupController::class, 'index'])->name('index');
        Route::post('/', [TaxGroupController::class, 'store'])->name('store');
        Route::put('/{taxgroup}', [TaxGroupController::class, 'update'])->name('update');
        Route::delete('/{taxgroup}', [TaxGroupController::class, 'destroy'])->name('destroy');
    });

    // Equipment
    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::post('/', [EquipmentController::class, 'store'])->name('store');
        Route::put('/{equipment}', [EquipmentController::class, 'update'])->name('update');
        Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');
    });

    // Permission Templates
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionTemplateController::class, 'index'])->name('index');
        Route::post('/', [PermissionTemplateController::class, 'store'])->name('store');
        Route::put('/{permission}', [PermissionTemplateController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionTemplateController::class, 'destroy'])->name('destroy');
    });

    // Checklists
    Route::prefix('checklists')->name('checklists.')->group(function () {
        Route::get('/', [ChecklistController::class, 'index'])->name('index');
        Route::get('/create', [ChecklistController::class, 'create'])->name('create');
        Route::post('/', [ChecklistController::class, 'store'])->name('store');
        Route::get('/{checklist}/edit', [ChecklistController::class, 'edit'])->name('edit');
        Route::put('/{checklist}', [ChecklistController::class, 'update'])->name('update');
        Route::delete('/{checklist}', [ChecklistController::class, 'destroy'])->name('destroy');
    });

    // Perform Checklists
    Route::prefix('perform-checklists')->name('performchecklists.')->group(function () {
        Route::get('/', [PerformChecklistController::class, 'index'])->name('index');
        Route::get('/create', [PerformChecklistController::class, 'create'])->name('create');
        Route::post('/', [PerformChecklistController::class, 'store'])->name('store');
        Route::get('/{performchecklist}', [PerformChecklistController::class, 'show'])->name('show');
    });

    // Support
    Route::get('support', [SupportController::class, 'index'])->name('support.index');

    // User Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('index');
        Route::put('/', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('update');
        Route::put('/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('password');
    });

    // Company Settings
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::post('/profile', [CompanyController::class, 'updateCompany'])->name('update');
        Route::post('/smtp', [CompanyController::class, 'updateSmtp'])->name('smtp');
    });

    // Phase 3: Multi-Tenancy Management (Super Admin)
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TenantManagementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\TenantManagementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\TenantManagementController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\TenantManagementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\TenantManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\TenantManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\TenantManagementController::class, 'destroy'])->name('destroy');
        Route::get('/switch/{id}', [\App\Http\Controllers\Admin\TenantManagementController::class, 'switch'])->name('switch');
        Route::get('/{id}/settings', [\App\Http\Controllers\Admin\TenantManagementController::class, 'settings'])->name('settings');
        Route::post('/{id}/settings', [\App\Http\Controllers\Admin\TenantManagementController::class, 'updateSettings'])->name('update-settings');
    });

    // Item Packages
    Route::prefix('packages')->name('packages.')->group(function () {
        Route::get('/', [ItemPackageController::class, 'index'])->name('index');
        Route::post('/', [ItemPackageController::class, 'store'])->name('store');
        Route::put('/{package}', [ItemPackageController::class, 'update'])->name('update');
        Route::delete('/{package}', [ItemPackageController::class, 'destroy'])->name('destroy');
    });

    // Phase 2.2: Project Budget Management
    Route::prefix('budgets')->name('budgets.')->group(function () {
        // Cost code assignment
        Route::get('/projects/{projectId}/assign-cost-codes', [\App\Http\Controllers\Admin\ProjectBudgetController::class, 'assignCostCodes'])->name('assign-cost-codes');
        Route::post('/projects/{projectId}/save-cost-codes', [\App\Http\Controllers\Admin\ProjectBudgetController::class, 'saveCostCodeAssignments'])->name('save-cost-codes');
        
        // Budget setup
        Route::get('/projects/{projectId}/setup', [\App\Http\Controllers\Admin\ProjectBudgetController::class, 'setupBudgets'])->name('setup');
        Route::post('/projects/{projectId}/save', [\App\Http\Controllers\Admin\ProjectBudgetController::class, 'saveBudgets'])->name('save');
        
        // Budget summary
        Route::get('/projects/{projectId}', [\App\Http\Controllers\Admin\ProjectBudgetController::class, 'viewBudgetSummary'])->name('view');
        Route::get('/projects/{projectId}/cost-codes/{costCodeId}/details', [\App\Http\Controllers\Admin\ProjectBudgetController::class, 'getBudgetDetails'])->name('details');
        
        // Budget availability check (AJAX)
        Route::post('/projects/{projectId}/check-availability', [\App\Http\Controllers\Admin\ProjectBudgetController::class, 'checkBudgetAvailability'])->name('check-availability');
    });

    // Budget Change Orders
    Route::prefix('budget-change-orders')->name('budget-change-orders.')->group(function () {
        Route::get('/projects/{projectId}', [\App\Http\Controllers\Admin\BudgetChangeOrderController::class, 'index'])->name('index');
        Route::get('/projects/{projectId}/create', [\App\Http\Controllers\Admin\BudgetChangeOrderController::class, 'create'])->name('create');
        Route::post('/projects/{projectId}', [\App\Http\Controllers\Admin\BudgetChangeOrderController::class, 'store'])->name('store');
        Route::get('/projects/{projectId}/{id}', [\App\Http\Controllers\Admin\BudgetChangeOrderController::class, 'show'])->name('show');
        Route::post('/projects/{projectId}/{id}/submit', [\App\Http\Controllers\Admin\BudgetChangeOrderController::class, 'submit'])->name('submit');
        Route::post('/projects/{projectId}/{id}/approve', [\App\Http\Controllers\Admin\BudgetChangeOrderController::class, 'approve'])->name('approve');
        Route::post('/projects/{projectId}/{id}/reject', [\App\Http\Controllers\Admin\BudgetChangeOrderController::class, 'reject'])->name('reject');
        Route::post('/projects/{projectId}/{id}/cancel', [\App\Http\Controllers\Admin\BudgetChangeOrderController::class, 'cancel'])->name('cancel');
        
        // AJAX: Get budget details
        Route::post('/projects/{projectId}/budget-details', [\App\Http\Controllers\Admin\BudgetChangeOrderController::class, 'getBudgetDetails'])->name('budget-details');
    });

    // PO Change Orders
    Route::prefix('po-change-orders')->name('po-change-orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PoChangeOrderController::class, 'index'])->name('index');
        Route::get('/purchase-orders/{poId}/create', [\App\Http\Controllers\Admin\PoChangeOrderController::class, 'create'])->name('create');
        Route::post('/purchase-orders/{poId}', [\App\Http\Controllers\Admin\PoChangeOrderController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\PoChangeOrderController::class, 'show'])->name('show');
        Route::post('/{id}/submit', [\App\Http\Controllers\Admin\PoChangeOrderController::class, 'submit'])->name('submit');
        Route::post('/{id}/approve', [\App\Http\Controllers\Admin\PoChangeOrderController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\Admin\PoChangeOrderController::class, 'reject'])->name('reject');
        Route::post('/{id}/cancel', [\App\Http\Controllers\Admin\PoChangeOrderController::class, 'cancel'])->name('cancel');
        
        // AJAX: Check budget availability
        Route::get('/{id}/check-budget', [\App\Http\Controllers\Admin\PoChangeOrderController::class, 'checkBudgetAvailability'])->name('check-budget');
    });

    // Approvals Dashboard
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ApprovalController::class, 'dashboard'])->name('dashboard');

        // Static routes MUST come before {id} wildcard
        Route::get('/history/entity', [\App\Http\Controllers\Admin\ApprovalController::class, 'getHistory'])->name('history');
        Route::get('/statistics', [\App\Http\Controllers\Admin\ApprovalController::class, 'getStatistics'])->name('statistics');

        Route::get('/{id}', [\App\Http\Controllers\Admin\ApprovalController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [\App\Http\Controllers\Admin\ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\Admin\ApprovalController::class, 'reject'])->name('reject');
        Route::post('/{id}/override', [\App\Http\Controllers\Admin\ApprovalController::class, 'override'])->name('override');
    });

    // Project Role Management
    Route::prefix('project-roles')->name('project-roles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ProjectRoleController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\ProjectRoleController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\ProjectRoleController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ProjectRoleController::class, 'destroy'])->name('destroy');
        
        // AJAX: Get users by role
        Route::get('/users-by-role', [\App\Http\Controllers\Admin\ProjectRoleController::class, 'getUsersByRole'])->name('users-by-role');
    });

    // Approval Workflow Management
    Route::prefix('approval-workflows')->name('approval-workflows.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ApprovalWorkflowController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ApprovalWorkflowController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ApprovalWorkflowController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\ApprovalWorkflowController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\ApprovalWorkflowController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\Admin\ApprovalWorkflowController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\ApprovalWorkflowController::class, 'destroy'])->name('destroy');
    });
    
    // Budget Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        // Budget vs Actual Report
        Route::get('/budget-vs-actual', [\App\Http\Controllers\Admin\BudgetReportController::class, 'index'])->name('budget-vs-actual');
        Route::post('/budget-vs-actual/queue-export', [\App\Http\Controllers\Admin\BudgetReportController::class, 'queueExport'])->name('budget-vs-actual.queue-export');
        Route::get('/budget-vs-actual/exports/{exportId}/download', [\App\Http\Controllers\Admin\BudgetReportController::class, 'downloadQueuedExport'])->name('budget-vs-actual.exports.download');
        Route::get('/budget-vs-actual/export', [\App\Http\Controllers\Admin\BudgetReportController::class, 'export'])->name('budget-vs-actual.export');
        Route::get('/budget-drilldown/{projectId}/{costCodeId}', [\App\Http\Controllers\Admin\BudgetReportController::class, 'drilldown'])->name('budget-drilldown');
        
        // Variance Analysis Dashboard
        Route::get('/variance-analysis', [\App\Http\Controllers\Admin\BudgetReportController::class, 'varianceAnalysis'])->name('variance-analysis');
        
        // Change Order Reports
        Route::get('/change-orders', [\App\Http\Controllers\Admin\ChangeOrderReportController::class, 'index'])->name('change-orders');
        Route::get('/change-orders/export', [\App\Http\Controllers\Admin\ChangeOrderReportController::class, 'export'])->name('change-orders.export');
        
        // Committed vs Actual Tracking
        Route::get('/committed-vs-actual', [\App\Http\Controllers\Admin\CommittedActualReportController::class, 'index'])->name('committed-vs-actual');
        Route::get('/committed-vs-actual/export', [\App\Http\Controllers\Admin\CommittedActualReportController::class, 'export'])->name('committed-vs-actual.export');
    });

    // Project Takeoffs & Estimates
    Route::prefix('takeoffs')->name('takeoffs.')->group(function () {
        Route::get('/', [TakeoffController::class, 'index'])->name('index');
        Route::get('/create', [TakeoffController::class, 'create'])->name('create');
        Route::post('/', [TakeoffController::class, 'store'])->name('store');
        Route::get('/{id}', [TakeoffController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [TakeoffController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TakeoffController::class, 'update'])->name('update');
        Route::delete('/{id}', [TakeoffController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/drawings', [TakeoffController::class, 'uploadDrawings'])->name('upload-drawings');
        Route::post('/{id}/drawings/{drawingId}/process', [TakeoffController::class, 'processDrawing'])->name('process-drawing');
        Route::delete('/{id}/drawings/{drawingId}', [TakeoffController::class, 'deleteDrawing'])->name('delete-drawing');
        Route::get('/{id}/drawings/{drawingId}/download', [TakeoffController::class, 'downloadDrawing'])->name('download-drawing');
        Route::get('/{id}/processing-status', [TakeoffController::class, 'checkProcessingStatus'])->name('processing-status');
        Route::post('/{id}/finalize', [TakeoffController::class, 'finalize'])->name('finalize');
        Route::post('/{id}/convert-to-po', [TakeoffController::class, 'convertToPo'])->name('convert-to-po');
        Route::post('/item-suggestions', [TakeoffController::class, 'getItemSuggestions'])->name('item-suggestions');
    });

    // Project Scheduling (CPM Engine)
    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/calendars', [ScheduleController::class, 'calendars'])->name('calendars');
        Route::post('/calendars', [ScheduleController::class, 'storeCalendar'])->name('storeCalendar');
        Route::put('/calendars/{calId}', [ScheduleController::class, 'updateCalendar'])->name('updateCalendar');
        Route::delete('/calendars/{calId}', [ScheduleController::class, 'deleteCalendar'])->name('deleteCalendar');
        Route::post('/calendars/{calId}/exceptions', [ScheduleController::class, 'storeCalendarException'])->name('storeCalendarException');
        Route::delete('/calendars/{calId}/exceptions/{exId}', [ScheduleController::class, 'deleteCalendarException'])->name('deleteCalendarException');

        // Project-scoped schedule routes
        Route::get('/{projectId}', [ScheduleController::class, 'show'])->name('show');
        Route::post('/{projectId}/calculate', [ScheduleController::class, 'calculate'])->name('calculate');
        Route::get('/{projectId}/gantt-data', [ScheduleController::class, 'ganttData'])->name('ganttData');
        Route::get('/{projectId}/critical-path', [ScheduleController::class, 'criticalPath'])->name('criticalPath');
        Route::get('/{projectId}/health', [ScheduleController::class, 'health'])->name('health');
        Route::get('/{projectId}/lookahead', [ScheduleController::class, 'lookahead'])->name('lookahead');
        Route::put('/{projectId}/settings', [ScheduleController::class, 'updateSettings'])->name('updateSettings');

        // Activities
        Route::post('/{projectId}/activities', [ScheduleController::class, 'storeActivity'])->name('storeActivity');
        Route::put('/{projectId}/activities/{activityId}', [ScheduleController::class, 'updateActivity'])->name('updateActivity');
        Route::delete('/{projectId}/activities/{activityId}', [ScheduleController::class, 'deleteActivity'])->name('deleteActivity');
        Route::post('/{projectId}/activities/reorder', [ScheduleController::class, 'reorderActivities'])->name('reorderActivities');
        Route::post('/{projectId}/activities/{activityId}/actuals', [ScheduleController::class, 'updateActuals'])->name('updateActuals');

        // Dependencies
        Route::post('/{projectId}/dependencies', [ScheduleController::class, 'storeDependency'])->name('storeDependency');
        Route::delete('/{projectId}/dependencies/{depId}', [ScheduleController::class, 'deleteDependency'])->name('deleteDependency');

        // Drivers
        Route::post('/{projectId}/drivers', [ScheduleController::class, 'storeDriver'])->name('storeDriver');
        Route::put('/{projectId}/drivers/{driverId}', [ScheduleController::class, 'updateDriver'])->name('updateDriver');
        Route::delete('/{projectId}/drivers/{driverId}', [ScheduleController::class, 'deleteDriver'])->name('deleteDriver');

        // Baselines
        Route::post('/{projectId}/baselines', [ScheduleController::class, 'createBaseline'])->name('createBaseline');
        Route::get('/{projectId}/baselines/{baselineId}/variance', [ScheduleController::class, 'baselineVariance'])->name('baselineVariance');
    });

    // AI Settings
    Route::prefix('ai-settings')->name('ai-settings.')->group(function () {
        Route::get('/', [AiSettingsController::class, 'index'])->name('index');
        Route::post('/', [AiSettingsController::class, 'update'])->name('update');
        Route::post('/test', [AiSettingsController::class, 'testConnection'])->name('test');
    });

    // ── Subcontractor & Contract Management ──

    // Contracts
    Route::prefix('contracts')->name('contracts.')->group(function () {
        Route::get('/', [ContractController::class, 'index'])->name('index');
        Route::get('/create', [ContractController::class, 'create'])->name('create');
        Route::post('/store', [ContractController::class, 'store'])->name('store');
        Route::get('/view/{id}', [ContractController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [ContractController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ContractController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ContractController::class, 'destroy'])->name('destroy');
        Route::post('/update-status/{id}', [ContractController::class, 'updateStatus'])->name('updatestatus');
        Route::post('/{id}/upload-documents', [ContractController::class, 'uploadDocuments'])->name('upload-documents');
        Route::get('/{id}/documents/{docId}/download', [ContractController::class, 'downloadDocument'])->name('download-document');
        Route::delete('/{id}/documents/{docId}', [ContractController::class, 'deleteDocument'])->name('delete-document');
        Route::post('/{id}/release-retention', [ContractController::class, 'releaseRetention'])->name('release-retention');

        // Contract Invoices (nested)
        Route::get('/{contractId}/invoices', [ContractInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/{contractId}/invoices/create', [ContractInvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/{contractId}/invoices/store', [ContractInvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/{contractId}/invoices/{id}', [ContractInvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/{contractId}/invoices/{id}/pay', [ContractInvoiceController::class, 'recordPayment'])->name('invoices.pay');
    });

    // Contract Change Orders
    Route::prefix('contract-change-orders')->name('contract-change-orders.')->group(function () {
        Route::get('/', [ContractChangeOrderController::class, 'index'])->name('index');
        Route::get('/contracts/{contractId}/create', [ContractChangeOrderController::class, 'create'])->name('create');
        Route::post('/contracts/{contractId}', [ContractChangeOrderController::class, 'store'])->name('store');
        Route::get('/{id}', [ContractChangeOrderController::class, 'show'])->name('show');
        Route::post('/{id}/submit', [ContractChangeOrderController::class, 'submit'])->name('submit');
        Route::post('/{id}/approve', [ContractChangeOrderController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [ContractChangeOrderController::class, 'reject'])->name('reject');
        Route::post('/{id}/cancel', [ContractChangeOrderController::class, 'cancel'])->name('cancel');
    });

    // Supplier Compliance
    Route::prefix('suppliers/{supplierId}/compliance')->name('supplier-compliance.')->group(function () {
        Route::get('/', [SupplierComplianceController::class, 'index'])->name('index');
        Route::post('/', [SupplierComplianceController::class, 'store'])->name('store');
        Route::put('/{id}', [SupplierComplianceController::class, 'update'])->name('update');
        Route::delete('/{id}', [SupplierComplianceController::class, 'destroy'])->name('destroy');
    });

    // Compliance documents & dashboard
    Route::post('compliance/{id}/upload', [SupplierComplianceController::class, 'uploadDocument'])->name('compliance.upload');
    Route::get('compliance/{id}/download', [SupplierComplianceController::class, 'downloadDocument'])->name('compliance.download');
    Route::get('compliance/dashboard', [SupplierComplianceController::class, 'dashboard'])->name('compliance.dashboard');

    // Security
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/2fa', [SecurityController::class, 'twoFactorSettings'])->name('2fa');
        Route::post('/2fa/setup', [SecurityController::class, 'generateTwoFactorSecret'])->name('2fa.setup');
        Route::post('/2fa/confirm', [SecurityController::class, 'confirmTwoFactor'])->name('2fa.confirm');
        Route::post('/2fa/disable', [SecurityController::class, 'disableTwoFactor'])->name('2fa.disable');
        Route::get('/audit-logs', [SecurityController::class, 'auditLogs'])->name('audit-logs');
    });

    // Attachments (Polymorphic File Upload System)
    Route::prefix('attachments')->name('attachments.')->group(function () {
        Route::post('/upload', [AttachmentController::class, 'upload'])->name('upload');
        Route::post('/list', [AttachmentController::class, 'list'])->name('list');
        Route::post('/reorder', [AttachmentController::class, 'reorder'])->name('reorder');
        Route::post('/delete-multiple', [AttachmentController::class, 'destroyMultiple'])->name('destroy-multiple');
        Route::get('/{id}/download', [AttachmentController::class, 'download'])->name('download');
        Route::get('/{id}/view', [AttachmentController::class, 'view'])->name('view');
        Route::delete('/{id}', [AttachmentController::class, 'destroy'])->name('destroy');
    });
});

// Procore Webhook (No auth required)
Route::post('procore/webhook', [ProcoreController::class, 'webhook'])->name('procore.webhook');

// Debug-only: Seed database via HTTP (since artisan hangs on this project)
if (app()->environment('local') || config('app.debug')) {
    Route::get('_dev/seed', [\App\Http\Controllers\DevSeedController::class, 'run'])->name('dev.seed');
    Route::get('_dev/seedext', [\App\Http\Controllers\DevSeedController::class, 'seedExtended'])->name('dev.seed-extended');
    Route::get('_dev/createtables', [\App\Http\Controllers\DevSeedController::class, 'createTables'])->name('dev.create-tables');
    Route::get('_dev/debug-auth', [\App\Http\Controllers\DevSeedController::class, 'debugAuth'])->name('dev.debug-auth');
}

// 404 Error Route
Route::get('default404', function () {
    return view('errors.404');
})->name('error.404');

// Supplier Portal Routes
require __DIR__.'/supplier.php';
