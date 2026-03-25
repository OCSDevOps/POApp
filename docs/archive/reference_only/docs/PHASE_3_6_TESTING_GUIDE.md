# Phase 3.6 Testing Guide

## Overview
This guide covers testing multi-tenancy data isolation and security in the POApp system.

## Prerequisites

### 1. Seed Test Companies
```bash
php artisan db:seed --class=CompanySeeder
```

This will create:
- **Default Company** (ID: 1) - All existing data assigned here
- **Acme Builders** (ID: 2) - Test company 1
- **Test Construction Co** (ID: 3) - Test company 2

### 2. Automated Testing

#### Feature Tests
```bash
# Run all company management tests
php artisan test --filter CompanyManagementTest

# Run all multi-tenancy isolation tests
php artisan test --filter MultiTenancyIsolationTest

# Run specific test
php artisan test --filter super_admin_can_view_companies_index
```

#### Command-Line Testing Tool
```bash
# Run comprehensive multi-tenancy test suite
php artisan test:multi-tenancy

# With verbose output
php artisan test:multi-tenancy --verbose
```

## Manual Testing Checklist

### A. Authorization Tests

**Super Admin Access (u_type = 1):**
- [ ] Can view `/admincontrol/companies` (company index page)
- [ ] Can create new companies
- [ ] Can edit any company
- [ ] Can delete empty companies (no users/projects/POs)
- [ ] Can switch between companies via dropdown
- [ ] Sees company switcher in topbar navigation
- [ ] Sees "Companies" menu in sidebar

**Regular User (u_type = 2):**
- [ ] Cannot access `/admincontrol/companies` (403 Forbidden)
- [ ] Cannot create companies (403 Forbidden)
- [ ] Cannot edit companies (403 Forbidden)
- [ ] Cannot switch companies (403 Forbidden)
- [ ] Does NOT see company switcher in navigation
- [ ] Does NOT see "Companies" menu in sidebar

### B. Data Isolation Tests

**Setup:**
1. Login as super admin
2. Create 2 test companies (Company A, Company B)
3. Add users, projects, POs to each company
4. Switch between companies and verify isolation

**Verification:**
- [ ] Company A context: Only sees Company A's data
- [ ] Company B context: Only sees Company B's data
- [ ] Purchase Orders filtered correctly
- [ ] Projects filtered correctly
- [ ] Suppliers filtered correctly
- [ ] Users filtered correctly
- [ ] Items filtered correctly
- [ ] Reports show only company-scoped data

**Example Test:**
```php
// As Super Admin
1. Switch to Company A
2. Go to Purchase Orders page
3. Note the count (e.g., 5 POs)
4. Switch to Company B
5. Go to Purchase Orders page
6. Verify different count (e.g., 0 or different number)
7. Switch back to Company A
8. Verify original count returns (5 POs)
```

### C. Security Tests

**URL Manipulation Prevention:**
- [ ] User in Company A cannot access Company B's PO by direct URL
  - Example: `/admincontrol/porder/123/edit` where PO 123 belongs to Company B
  - Expected: 404 Not Found or redirect
- [ ] User cannot bypass company scope via query parameters
- [ ] Direct database ID access blocked by CompanyScope

**Session Hijacking Prevention:**
- [ ] Changing `company_id` in session as regular user has no effect
- [ ] Only super admin can switch companies
- [ ] Company context persists across page loads
- [ ] Logout clears company context

### D. Company Management Tests

**Create Company:**
- [ ] Name is required
- [ ] Subdomain auto-generates if empty
- [ ] Subdomain must be unique
- [ ] Status defaults to active (1)
- [ ] Success message displays
- [ ] Redirects to company index

**Edit Company:**
- [ ] Can update name
- [ ] Can update subdomain (must be unique)
- [ ] Can change status (active/inactive)
- [ ] Changes persist
- [ ] Success message displays

**Delete Company:**
- [ ] Cannot delete company with users
- [ ] Cannot delete company with projects
- [ ] Cannot delete company with purchase orders
- [ ] CAN delete empty company
- [ ] Confirmation prompt appears
- [ ] Success message after deletion

**Switch Company:**
- [ ] Session `company_id` updates
- [ ] Dashboard reflects new company context
- [ ] Alert banner shows current company
- [ ] Data changes immediately (no cache issues)
- [ ] Current company marked in dropdown

### E. Navigation Tests

**Topbar Company Switcher:**
- [ ] Displays current company name
- [ ] Lists all active companies
- [ ] Current company marked with checkmark
- [ ] "Manage Companies" link present
- [ ] Only visible to super admins

**Sidebar Menu:**
- [ ] "Companies" link present for super admins
- [ ] "Companies" link absent for regular users
- [ ] Links to `/admincontrol/companies`
- [ ] Active state highlights correctly

**Dashboard Indicator:**
- [ ] Alert banner shows current company
- [ ] Only visible to super admins
- [ ] Dismissible
- [ ] Explains data scoping

### F. Report Scoping Tests

**All Reports Must Respect company_id:**
- [ ] Budget vs Actual Report
- [ ] Variance Analysis Report
- [ ] Committed Actual Report
- [ ] Change Order Report
- [ ] Any custom reports

**Test Process:**
1. Create test data in Company A (projects, POs, budgets)
2. Create different test data in Company B
3. Switch to Company A, run reports
4. Verify only Company A data appears
5. Switch to Company B, run same reports
6. Verify only Company B data appears

### G. Database Query Tests

**Verify DB::table() Queries Have company_id Filter:**

Check these controllers manually:
- [ ] PurchaseOrderController
- [ ] ReceiveOrderController
- [ ] AdminDashboardController
- [ ] BudgetReportController
- [ ] CommittedActualReportController
- [ ] ChangeOrderReportController
- [ ] CostCodeController
- [ ] TaxGroupController
- [ ] UnitOfMeasureController
- [ ] ProcoreController
- [ ] SupplierCatalogController

**Example Query Pattern:**
```php
// CORRECT (with company_id filter)
DB::table('purchase_order_master')
    ->where('company_id', session('company_id'))
    ->get();

// INCORRECT (missing company_id filter)
DB::table('purchase_order_master')->get();
```

## Performance Tests

- [ ] Company switching is fast (<1 second)
- [ ] Dashboard loads with company scope in reasonable time
- [ ] Large datasets don't cause issues (test with 100+ POs per company)
- [ ] Company scope doesn't significantly slow queries

## Edge Cases

- [ ] User with no company_id cannot login
- [ ] Inactive company users cannot login
- [ ] Super admin can switch to inactive company (for management)
- [ ] Creating company with duplicate name succeeds (only subdomain must be unique)
- [ ] Company with ID 0 or null handled gracefully
- [ ] Session expires, company context clears

## Known Limitations (MVP Phase)

- **No tenant-level domains**: All companies use same domain
- **Shared authentication**: Users login to main app, then switch companies
- **Single database**: All tenants in same DB, separated by company_id
- **No data portability**: Cannot move data between companies (future feature)

## Test Data Cleanup

After testing, reset to clean state:
```bash
# WARNING: This drops all data!
php artisan migrate:fresh
php artisan db:seed --class=CompanySeeder
```

## Troubleshooting

### Memory Issues
If you encounter "memory exhausted" errors:
1. Check `php.ini`: `memory_limit = 4096M`
2. Run with explicit memory: `php -d memory_limit=4G artisan ...`
3. Clear Laravel cache: `php artisan optimize:clear`

### Tests Not Running
1. Ensure database is configured: `.env` DB settings
2. Run migrations: `php artisan migrate`
3. Check test database: May need separate test DB

### Company Scope Not Working
1. Verify CompanyScope trait on model
2. Check session has `company_id`
3. Verify middleware is active
4. Check query uses Eloquent (not raw DB)

## Success Criteria

Phase 3.6 is complete when:
- ✅ All automated tests pass
- ✅ Manual testing checklist completed
- ✅ No data leakage between companies
- ✅ Authorization properly restricts access
- ✅ Reports respect company scope
- ✅ URL manipulation blocked
- ✅ Documentation complete

## Next Phase

After successful testing, proceed to **Phase 3.7: Security Audit & Documentation**.
