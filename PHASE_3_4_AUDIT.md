# Phase 3.4: Controller Security Audit - IN PROGRESS

## Objective
Audit all controllers for tenant isolation issues:
1. Direct `DB::table()` queries bypassing Eloquent scopes
2. Missing authorization checks (verify user's company matches resource)
3. Store/update methods that might override auto-injected company_id

## Discovery Phase - Complete

### DB::table() Usage Found: **70 instances** across 17 controllers

#### Critical Controllers (CRUD operations - HIGH PRIORITY)
1. **PurchaseOrderController** - 12 instances
   - Lines 74, 86, 87, 90, 147, 258, 259, 262, 299, 312, 384, 441
   - Issues: permission_master, item_package_master, taxgroup_master, unit_of_measure_tab, supplier_catalog_tab
   - Risk: Direct inserts to purchase_order_items bypassing model

2. **ReceiveOrderController** - 3 instances
   - Lines 51, 262, 285
   - Issues: vw_receiving_summary, vw_back_order_report views

3. **SupplierController** - 1 instance
   - Line 138: hasPO check
   
4. **ProjectController** - 1 instance
   - Line 132: hasPO check

5. **ItemController** - 3 instances
   - Lines 130, 196, 284
   - Issues: vw_item_pricing_summary view, porder_detail

6. **BudgetController** - 3 instances
   - Lines 39, 114, 248
   - Issues: vw_budget_summary view, porder_master

7. **CostCodeController** - 2 instances
   - Lines 71-72: Delete validation checks

#### Report Controllers (READ-ONLY - MEDIUM PRIORITY)
8. **BudgetReportController** - 10 instances
   - Critical: All queries missing company_id filter
   - Tables: project_master, budget_master, cost_code_master, purchase_order_master, receive_order_master

9. **CommittedActualReportController** - 13 instances
   - Critical: All queries missing company_id filter
   - Tables: project_master, purchase_order_master, receive_order_master, budget_master

10. **ChangeOrderReportController** - 3 instances
    - Critical: Missing company_id filter
    - Tables: project_master, budget_change_orders, po_change_orders

11. **AdminDashboardController** - 8 instances
    - Critical: Dashboard showing all companies' data
    - Tables: user_info, supplier_master, request_purchase_order, supplier_catalog_tab, receive_order_master

#### Lookup/Reference Tables (LOW PRIORITY)
12. **TaxGroupController** - 2 instances (in-use checks)
13. **UnitOfMeasureController** - 2 instances (in-use checks)
14. **SupplierCatalogController** - 2 instances (performance views)
15. **ProcoreController** - 7 instances (integration mappings - needs company_id)

## Audit Strategy

### Phase 1: Critical CRUD Controllers (Priority 1)
- [ ] PurchaseOrderController - Fix direct DB queries, add authorization
- [ ] ReceiveOrderController - Convert views to use Eloquent with scopes
- [ ] SupplierController - Add authorization checks
- [ ] ProjectController - Add authorization checks
- [ ] ItemController - Fix view queries
- [ ] BudgetController - Fix view queries

### Phase 2: Report Controllers (Priority 2)
- [ ] BudgetReportController - Add company_id to ALL queries
- [ ] CommittedActualReportController - Add company_id to ALL queries
- [ ] ChangeOrderReportController - Add company_id to ALL queries
- [ ] AdminDashboardController - Scope to current company

### Phase 3: Supporting Controllers (Priority 3)
- [ ] CostCodeController - Ensure scoped delete checks
- [ ] TaxGroupController - Verify in-use checks scoped
- [ ] UnitOfMeasureController - Verify in-use checks scoped
- [ ] ProcoreController - Add company_id to mapping tables

## Fix Patterns

### Pattern 1: Convert DB::table() to Eloquent
**Before:**
```php
$projects = DB::table('project_master')
    ->where('proj_status', 1)
    ->get();
```

**After:**
```php
$projects = Project::active()->get(); 
// Automatically scoped to session('company_id')
```

### Pattern 2: Add Authorization Check
**Before:**
```php
public function edit($id)
{
    $po = PurchaseOrder::findOrFail($id);
    // ... edit logic
}
```

**After:**
```php
public function edit($id)
{
    $po = PurchaseOrder::findOrFail($id);
    
    // Automatic scope ensures we can only find our company's POs
    // But add explicit check for clarity and logging:
    if (!$po->isOwnedByCurrentCompany()) {
        abort(403, 'Unauthorized access to another company\'s purchase order');
    }
    
    // ... edit logic
}
```

### Pattern 3: Manual company_id Filter for DB::table()
**When Eloquent conversion not possible (views, complex joins):**
```php
// Before
$summary = DB::table('vw_budget_summary')
    ->where('project_id', $projectId)
    ->first();

// After
$summary = DB::table('vw_budget_summary')
    ->where('company_id', session('company_id'))
    ->where('project_id', $projectId)
    ->first();
```

### Pattern 4: Direct Inserts (Avoid)
**Before:**
```php
DB::table('purchase_order_items')->insert([
    'po_detail_porder_ms' => $orderId,
    'po_detail_item' => $itemId,
    // ... other fields
]);
```

**After:**
```php
PurchaseOrderItem::create([
    'po_detail_porder_ms' => $orderId,
    'po_detail_item' => $itemId,
    // ... other fields
    // company_id auto-injected by CompanyScope trait
]);
```

## Tables Needing company_id (Discovery)

### Currently Have company_id ✅
- project_master
- supplier_master  
- purchase_order_master
- receive_order_master
- item_master
- budget_master
- budget_change_orders
- po_change_orders
- cost_code_master
- item_category_tab
- item_package_master (added)
- purchase_order_details (added)
- receive_order_details (added)

### May Need company_id ⚠️
- permission_master (user-specific, not company)
- taxgroup_master (global lookup?)
- unit_of_measure_tab (global lookup?)
- procore_project_mapping (integration mapping)
- procore_cost_code_mapping (integration mapping)
- request_purchase_order (RFQ table - check if has company_id)
- supplier_catalog_tab (supplier catalog items)

### Views Needing Recreation 🔍
- vw_budget_summary
- vw_receiving_summary
- vw_back_order_report
- vw_item_pricing_summary
- vw_supplier_performance

## Progress Tracker

### Critical Controllers (6)
- [ ] PurchaseOrderController (0%)
- [ ] ReceiveOrderController (0%)
- [ ] SupplierController (0%)
- [ ] ProjectController (0%)
- [ ] ItemController (0%)
- [ ] BudgetController (0%)

### Report Controllers (4)
- [ ] BudgetReportController (0%)
- [ ] CommittedActualReportController (0%)
- [ ] ChangeOrderReportController (0%)
- [ ] AdminDashboardController (0%)

### Supporting Controllers (4)
- [ ] CostCodeController (0%)
- [ ] TaxGroupController (0%)
- [ ] UnitOfMeasureController (0%)
- [ ] ProcoreController (0%)

## Security Risks Identified

### HIGH RISK 🔴
1. **Report Controllers** - Showing ALL companies' data in financial reports
2. **AdminDashboardController** - Dashboard stats aggregating all companies
3. **Direct DB inserts** - Bypassing auto-injection of company_id

### MEDIUM RISK 🟡
1. **Missing authorization checks** - Users could manipulate URLs to access other companies' resources
2. **View queries** - Database views may not filter by company_id

### LOW RISK 🟢
1. **Lookup tables** - Global reference data (UOM, tax groups) likely company-agnostic
2. **In-use checks** - Need scoping but don't leak data directly

## Testing Plan

### Per Controller Testing
1. Login as Test Company user (john.smith@testconstruction.com, Company 2)
2. Verify CRUD operations only show/affect Company 2 data
3. Attempt URL manipulation to access Company 1 or 3 resources (should fail)
4. Check SQL queries in log for proper company_id filtering

### Report Testing
1. Create sample data for each test company
2. Login as each company user
3. Verify reports show ONLY that company's data
4. Check dashboard stats are company-specific

### Authorization Testing
1. Login as Company 2 user
2. Get valid Company 1 resource ID
3. Attempt to edit/delete via URL: `/admin/purchase-orders/{company1_id}/edit`
4. Should receive 403 Forbidden or 404 Not Found

## Next Steps
1. Start with PurchaseOrderController (highest DB::table() usage)
2. Fix direct inserts and add authorization
3. Move to report controllers (highest security risk)
4. Create helper method for common authorization pattern
5. Document all changes in PHASE_3_4_STATUS.md
