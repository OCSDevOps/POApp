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
    Route::get('generate_hash','generate_hash')->name('auth.generatehash');
    Route::get('logout','logout')->name('auth.logout');
});

// Reports Routes
Route::controller(ReportsController::class)->group(function(){
    Route::get('ccsummary','getCcSummary')->name('ccsummary');
    Route::post('ccsummary','getCcSummary')->name('ccsummary');
});

// Dashboard Routes (Legacy)
Route::controller(DashboardController::class)->group(function(){
    Route::get('dashboard','index')->name('dashboard');
    Route::get('dashboard-analytics','dashboard_analytics')->name('dashboard.analytics');
    Route::get('dashboard-ecommerce','dashboard_ecommerce')->name('dashboard.ecommerce');
    Route::get('dashboard-projects','dashboard_projects')->name('dashboard.projects');
});

// Admin Routes (Protected by auth middleware)
Route::middleware(['auth'])->prefix('admincontrol')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/chart-data', [AdminDashboardController::class, 'getPODataForChart'])->name('dashboard.chartdata');
    Route::get('logout', [AdminDashboardController::class, 'logout'])->name('logout');
    
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
        
        // AJAX Routes
        Route::post('/get-item-master-list', [PurchaseOrderController::class, 'getItemMasterList'])->name('itemlist');
        Route::post('/get-supplier-catalog-list', [PurchaseOrderController::class, 'getSupplierCatalogList'])->name('cataloglist');
        Route::post('/get-project-address', [PurchaseOrderController::class, 'getProjectAddress'])->name('projectaddress');
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

    // Cost Codes
    Route::prefix('costcodes')->name('costcodes.')->group(function () {
        Route::get('/', [CostCodeController::class, 'index'])->name('index');
        Route::post('/', [CostCodeController::class, 'store'])->name('store');
        Route::put('/{costcode}', [CostCodeController::class, 'update'])->name('update');
        Route::delete('/{costcode}', [CostCodeController::class, 'destroy'])->name('destroy');
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
});

// Procore Webhook (No auth required)
Route::post('procore/webhook', [ProcoreController::class, 'webhook'])->name('procore.webhook');

// 404 Error Route
Route::get('default404', function () {
    return view('errors.404');
})->name('error.404');
