# POApp Architecture Documentation

> **Purchase Order Management System** — Laravel 9, SQL Server, Bootstrap 5
> Last updated: 2026-02-06

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Request Lifecycle](#2-request-lifecycle)
3. [Authentication & Authorization](#3-authentication--authorization)
4. [Multi-Tenancy Architecture](#4-multi-tenancy-architecture)
5. [Data Flow Patterns](#5-data-flow-patterns)
6. [Service Layer](#6-service-layer)
7. [Database Schema](#7-database-schema)
8. [Frontend Architecture](#8-frontend-architecture)
9. [Route Map](#9-route-map)
10. [Key Workflows](#10-key-workflows)

---

## 1. System Overview

### Tech Stack

| Layer        | Technology                                  |
|--------------|---------------------------------------------|
| Framework    | Laravel 9 (PHP 8.1+)                       |
| Database     | SQL Server (DESKTOP-Q2001NS\SQLEXPRESS)     |
| Database     | `porder_db`                                 |
| CSS          | Bootstrap 5.3.0 (CDN)                       |
| JavaScript   | jQuery 3.7.0 (CDN)                          |
| Tables       | DataTables 1.13.4 (CDN)                     |
| Charts       | Chart.js (CDN)                              |
| Icons        | Font Awesome 6.4.0 (CDN)                    |
| Build        | Vite (Laravel default)                      |
| Platform     | Windows development server                  |

### Directory Structure

```
POApp/
└── html/                          ← Laravel app root
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   │   ├── Admin/         ← Admin panel controllers (~20 controllers)
    │   │   │   ├── Supplier/      ← Supplier portal controllers
    │   │   │   ├── AuthController.php
    │   │   │   └── DashboardController.php (legacy, disabled)
    │   │   ├── Kernel.php         ← Middleware stack definition
    │   │   └── Middleware/
    │   │       ├── Authenticate.php
    │   │       ├── SetTenantContext.php      ← Multi-tenancy middleware
    │   │       ├── SupplierAuthenticate.php
    │   │       └── EnsureSupplierEmailIsVerified.php
    │   ├── Models/                ← 46 Eloquent models
    │   ├── Providers/
    │   │   └── AppServiceProvider.php  ← View composers
    │   ├── Services/              ← 8 service classes
    │   ├── Traits/
    │   │   └── CompanyScope.php   ← Tenant isolation trait
    │   └── Notifications/         ← Email/DB notifications
    ├── config/
    │   └── auth.php               ← Dual-guard configuration
    ├── resources/views/
    │   ├── layouts/
    │   │   └── admin.blade.php    ← Master layout (sidebar, topbar, scripts)
    │   ├── admin/                 ← Admin view templates
    │   ├── supplier/              ← Supplier portal views
    │   └── auth/                  ← Login/register views
    ├── routes/
    │   ├── web.php                ← Admin routes (~460 lines)
    │   ├── supplier.php           ← Supplier portal routes
    │   └── api.php                ← API routes
    └── public/                    ← Web-accessible root
```

---

## 2. Request Lifecycle

Every HTTP request passes through a defined middleware pipeline before reaching a controller.

### Middleware Chain

```
HTTP Request
    │
    ├── GLOBAL MIDDLEWARE (every request)
    │   ├── 1. TrustProxies
    │   ├── 2. HandleCors
    │   ├── 3. PreventRequestsDuringMaintenance
    │   ├── 4. ValidatePostSize
    │   ├── 5. TrimStrings
    │   └── 6. ConvertEmptyStringsToNull
    │
    ├── WEB MIDDLEWARE GROUP (all web routes)
    │   ├── 7.  EncryptCookies
    │   ├── 8.  AddQueuedCookiesToResponse
    │   ├── 9.  StartSession
    │   ├── 10. ShareErrorsFromSession
    │   ├── 11. VerifyCsrfToken
    │   ├── 12. SubstituteBindings (route model binding)
    │   └── 13. SetTenantContext ← TENANT ISOLATION (sets company_id in session)
    │
    ├── ROUTE MIDDLEWARE (per-route)
    │   └── 14. auth / auth.supplier / guest / verified.supplier
    │
    └── Controller → Service → Model → View → Response
```

### SetTenantContext Middleware Details

Defined in [app/Http/Middleware/SetTenantContext.php](app/Http/Middleware/SetTenantContext.php):

1. Checks both `web` and `supplier` authentication guards
2. For the first authenticated guard found:
   - Reads `company_id` from the user model
   - Sets `session('company_id')` and `session('company_name')`
   - Merges `tenant_company_id` into the request object
   - Shares `current_company_id` and `current_company_name` with all views
3. Non-super-admin users without a `company_id` receive a `403` error
4. Super admins (`u_type == 1`) can proceed without company context
5. Stops after the first authenticated guard is found

### API Middleware Group

API routes use a separate, simpler pipeline:

```
API Request → throttle:api → SubstituteBindings → Controller
```

---

## 3. Authentication & Authorization

### Dual-Guard System

The application uses two separate Laravel authentication guards:

| Guard      | Model         | Table            | Session Driver | Purpose           |
|------------|---------------|------------------|----------------|-------------------|
| `web`      | `User`        | `users`          | Session        | Admin panel       |
| `supplier` | `SupplierUser`| `supplier_users` | Session        | Supplier portal   |

Configured in [config/auth.php](config/auth.php).

### Admin Login Flow

Handled by [app/Http/Controllers/AuthController.php](app/Http/Controllers/AuthController.php):

```
GET /                    → Show login form
POST /validate_login     → Validate credentials
    │
    ├── $req->validate(['email' => 'required', 'password' => 'required'])
    ├── Auth::attempt($credentials)
    │   ├── Success:
    │   │   ├── session(['name' => $user->name])
    │   │   ├── session(['company_id' => $user->company_id])
    │   │   ├── session(['company_name' => $user->company->name])
    │   │   └── redirect('dashboard')
    │   └── Failure:
    │       └── redirect('/')->with('invalid-user', 'true')
    │
GET /logout              → Session::flush() + Auth::logout()
```

### Supplier Login Flow

Handled by [app/Http/Controllers/Supplier/AuthController.php](app/Http/Controllers/Supplier/AuthController.php):

```
GET  /supplier/login     → Show supplier login form
POST /supplier/login     → Auth::guard('supplier')->attempt()
GET  /supplier/register  → Self-registration form
POST /supplier/register  → Create SupplierUser + email verification
```

Supplier routes additionally require email verification via `verified.supplier` middleware.

### Permission System

- Each user has a `pt_id` (permission template ID) referencing `permission_master`
- Controllers check `session('pt_id')` against permission templates to grant/deny access
- Super admin (`u_type == 1`) bypasses all permission checks
- Permission templates define CRUD access per module (PO, projects, items, etc.)

### User Type Hierarchy

| `u_type` | Role         | Capabilities                                    |
|----------|--------------|--------------------------------------------------|
| 1        | Super Admin  | Full system access, company switching, tenant CRUD |
| 2        | Admin        | Full access within assigned company               |
| 3        | Manager      | Access based on permission template               |
| 4        | User         | Access based on permission template               |

---

## 4. Multi-Tenancy Architecture

### Overview

POApp uses **session-based, shared-database multi-tenancy**. All tenant data lives in the same SQL Server database, isolated by a `company_id` foreign key on every tenant-scoped table.

### CompanyScope Trait

Defined in [app/Traits/CompanyScope.php](app/Traits/CompanyScope.php). Any model that `use CompanyScope` gets:

**1. Automatic query filtering (Global Scope):**
```php
// Every SELECT is filtered by session('company_id')
static::addGlobalScope('company', function (Builder $builder) {
    if (session()->has('company_id')) {
        $builder->where($table . '.company_id', session('company_id'));
    }
});
```

**2. Automatic company_id injection on INSERT:**
```php
static::creating(function (Model $model) {
    if (!$model->company_id && session()->has('company_id')) {
        $model->company_id = session('company_id');
    }
});
```

**3. Helper scopes and methods:**
- `Model::forCompany($id)` — scope to specific company
- `Model::allCompanies()` — bypass global scope (for super admin)
- `$model->company()` — belongsTo Company relationship
- `$model->isOwnedByCurrentCompany()` — ownership check
- `$model->isOwnedByCompany($id)` — ownership check for specific company

### Models Using CompanyScope (28 models)

| Model                | Table                        |
|----------------------|------------------------------|
| PurchaseOrder        | purchase_order_master        |
| PurchaseOrderItem    | purchase_order_details       |
| Project              | project_master               |
| Supplier             | supplier_master              |
| Item                 | item_master                  |
| ItemCategory         | item_category_tab            |
| ItemPackage          | item_packages                |
| ItemPricing          | item_pricing                 |
| Budget               | budget_master                |
| BudgetChangeOrder    | budget_change_orders         |
| CostCode             | cost_code_master             |
| ProjectCostCode      | project_cost_codes           |
| ProjectRole          | project_roles                |
| ReceiveOrder         | receive_order_master         |
| ReceiveOrderItem     | receive_order_details        |
| Rfq                  | rfq_master                   |
| RfqItem              | rfq_items                    |
| RfqSupplier          | rfq_suppliers                |
| RfqQuote             | rfq_quotes                   |
| PoChangeOrder        | po_change_orders             |
| ApprovalWorkflow     | approval_workflows           |
| ApprovalRequest      | approval_requests            |
| AccountingIntegration| accounting_integrations      |
| IntegrationSyncLog   | integration_sync_logs        |
| Equipment            | eq_master                    |
| Checklist            | checklist_master             |
| User                 | users                        |
| SupplierUser         | supplier_users               |

### Company Model

Defined in [app/Models/Company.php](app/Models/Company.php):

- **Table:** `companies`, **PK:** `id`
- **Does NOT** use CompanyScope (it IS the tenant table)
- Provides `getCompanyIdAttribute()` accessor returning `$this->id` for compatibility
- HasMany relationships to all tenant-scoped models
- Supports JSON `settings` column for per-company configuration
- `getSetting($key)` / `setSetting($key, $value)` for company-level config

### Super Admin Company Switching

Super admins (`u_type == 1`) can switch company context:

1. Admin visits company management page
2. Clicks "Switch" on a company row
3. POST to `admin.companies.switch` updates `session('company_id')`
4. All subsequent requests filter data by the new company_id
5. CompanyScope trait automatically picks up the new session value

---

## 5. Data Flow Patterns

### Pattern 1: List Pages (e.g., Purchase Order List)

```
Browser GET /admincontrol/porder
    │
    ├── Route: admin.porder.index
    ├── Middleware: auth
    │
    ├── PurchaseOrderController@index
    │   ├── PurchaseOrder::with(['project', 'supplier'])
    │   │   └── CompanyScope auto-adds: WHERE company_id = session('company_id')
    │   ├── Apply optional scopes: ->byProject(), ->byStatus(), etc.
    │   └── return view('admin.porder.index', compact('orders', 'projects', ...))
    │
    ├── Blade Template
    │   ├── @extends('layouts.admin')
    │   ├── @section('content') → table with @foreach($orders)
    │   └── @push('scripts') → DataTable initialization
    │
    └── HTML Response with DataTables-enhanced table
```

### Pattern 2: Create/Store (e.g., New Purchase Order)

```
Browser GET /admincontrol/porder/add_new_purchase_order
    │
    ├── PurchaseOrderController@create
    │   ├── Load form data: projects, suppliers, items, tax groups, UOMs
    │   │   └── Each query auto-filtered by CompanyScope
    │   └── return view('admin.porder.create', compact(...))
    │
Browser POST /admincontrol/porder/store
    │
    ├── PurchaseOrderController@store
    │   ├── $request->validate([...])
    │   ├── DB::beginTransaction()
    │   ├── PurchaseOrder::create([...])
    │   │   └── CompanyScope auto-injects company_id
    │   ├── foreach($items) → PurchaseOrderItem::create([...])
    │   ├── Budget validation → BudgetService::validatePoBudget()
    │   ├── Budget commitment → BudgetService::updateBudgetCommitment()
    │   ├── DB::commit()
    │   └── redirect()->route('admin.porder.show', $po->porder_id)
    │       ->with('success', 'Purchase Order created')
```

### Pattern 3: Dashboard (Server-rendered + AJAX refresh)

```
Browser GET /admincontrol/dashboard
    │
    ├── AdminDashboardController@index
    │   ├── PurchaseOrder::count()           → $total_po
    │   ├── PurchaseOrder::pending()->count() → $pending_po
    │   ├── PurchaseOrder::submitted()->count() → $submitted_po
    │   ├── PurchaseOrder::rte()->count()     → $rte_po
    │   ├── Delivery status counts           → $fully_received, etc.
    │   ├── Project::all()                   → $proj_list (for filter dropdown)
    │   └── return view('admin.main', compact(...))
    │
    ├── Initial render: stat cards + Chart.js (doughnut + bar)
    │
    ├── User changes project filter dropdown
    │   └── $.ajax GET /admincontrol/dashboard/chart-data?proj_id=X
    │       ├── AdminDashboardController@getPODataForChart
    │       │   └── Re-queries with project filter, returns JSON
    │       └── JavaScript updates:
    │           ├── $('#totalPO').text(data.total_po)
    │           ├── ... (all stat card values)
    │           └── initCharts(data) → destroys and recreates Chart.js instances
```

### Pattern 4: AJAX Data Endpoints

Used within forms for dynamic dropdowns and validation:

```
POST /admincontrol/porder/get-item-master-list
    → Returns JSON array of items for a given project/cost code

POST /admincontrol/porder/get-supplier-catalog-list
    → Returns JSON array of supplier catalog items

POST /admincontrol/porder/get-project-address
    → Returns project address for delivery address auto-fill

POST /admincontrol/porder/check-budget-availability
    → Returns budget availability status for PO creation
```

All AJAX requests include CSRF token via `$.ajaxSetup` in the master layout.

### Pattern 5: Detail / Show Pages

```
Browser GET /admincontrol/porder/view/{id}
    │
    ├── PurchaseOrderController@show
    │   ├── PurchaseOrder::with(['project', 'supplier', 'items', 'receiveOrders'])
    │   │   └── findOrFail($id) — CompanyScope ensures tenant isolation
    │   └── return view('admin.porder.show', compact('order'))
    │
    └── Blade renders status-dependent UI:
        ├── Status badges (pending, submitted, approved, etc.)
        ├── Action buttons (edit, submit, approve) based on current status
        ├── Line items table
        └── Receive order history
```

---

## 6. Service Layer

The application uses a service layer to encapsulate complex business logic. Services are resolved from the Laravel container.

### Service Classes

| Service                  | File                                        | Purpose                                   |
|--------------------------|---------------------------------------------|-------------------------------------------|
| `BudgetService`          | [app/Services/BudgetService.php](app/Services/BudgetService.php)           | Budget CRUD, change orders, validation, thresholds |
| `ApprovalService`        | [app/Services/ApprovalService.php](app/Services/ApprovalService.php)       | Workflow routing, approvals, rejections    |
| `PurchaseOrderService`   | [app/Services/PurchaseOrderService.php](app/Services/PurchaseOrderService.php) | PO creation, RFQ management, receiving, price tracking |
| `PoChangeOrderService`   | [app/Services/PoChangeOrderService.php](app/Services/PoChangeOrderService.php) | PO change order CRUD and approval          |
| `ProcoreService`         | [app/Services/ProcoreService.php](app/Services/ProcoreService.php)         | Procore API sync (projects, vendors, budgets) |
| `ItemPricingService`     | [app/Services/ItemPricingService.php](app/Services/ItemPricingService.php) | Supplier price comparison, history         |
| `BackorderService`       | [app/Services/BackorderService.php](app/Services/BackorderService.php)     | Backorder recalculation per PO             |
| `ReportExportService`    | [app/Services/ReportExportService.php](app/Services/ReportExportService.php) | Report data export                         |

### BudgetService

Manages budget lifecycle:

- **`assignCostCodesToProject($projectId, $costCodeIds)`** — Link cost codes to project
- **`setupBudget($projectId, $costCodeId, $amount, $userId)`** — Create or adjust budget via change order
- **`createBudgetChangeOrder($data)`** — Create draft BCO
- **`approveBudgetChangeOrder($bcoId, $userId)`** — Apply BCO to budget
- **`validatePoBudget($projectId, $costCodeId, $poAmount)`** — Check budget availability before PO
- **`updateBudgetCommitment($projectId, $costCodeId, $poAmount)`** — Commit budget when PO created
- **`updateJobCostActual($projectId, $costCodeId, $actualAmount)`** — Record spend on receive
- **`checkBudgetThresholds($budget)`** — Warning (75%) / Critical (90%) notifications
- **`getProjectBudgetSummary($projectId)`** — Summary: original, change orders, committed, actual, available

### ApprovalService

Routes entities through configurable approval workflows:

- **`submitForApproval($entityType, $entityId, $amount, $requestedBy, $projectId)`**
  - Finds matching workflows (project-specific take precedence over company-wide)
  - Creates `ApprovalRequest` record
  - Resolves approvers using role-based resolution (`ProjectRole` model)
  - Sends `ApprovalPendingNotification` to first-level approvers
  - Auto-approves if no matching workflow exists
- **`processApproval($requestId, $action, $userId, $userName, $comments)`**
  - Validates user is authorized approver
  - On approval: advances to next level or completes
  - On rejection: marks request rejected, updates entity status
  - Triggers entity-specific logic (`executeApproval`) on final approval
- **Supported entity types:** `po`, `budget_co`, `po_co`, `budget`

### PurchaseOrderService

Central orchestrator for PO-related operations:

- **PO Management:** Number generation (`PO000001` format), creation with line items, budget validation/commitment
- **RFQ Management:** Create, send to suppliers, record quotes, convert winning quote to PO
- **Receiving:** Create receive orders, update PO delivery status, update budget actuals
- **Back Orders:** Report generation from `vw_back_order_report` database view
- **Price Tracking:** Update supplier catalog prices, maintain price history, cross-supplier comparison

---

## 7. Database Schema

### Core Tables

#### `companies` (Tenant Table)

| Column     | Type     | Notes                      |
|------------|----------|----------------------------|
| `id`       | int (PK) | Auto-increment             |
| `name`     | string   | Unique company name        |
| `subdomain`| string   | URL-safe identifier        |
| `status`   | int      | 1=Active, 0=Inactive       |
| `settings` | JSON     | Per-company configuration  |
| timestamps | datetime | created_at, updated_at     |

#### `users`

| Column       | Type     | Notes                         |
|--------------|----------|-------------------------------|
| `id`         | int (PK) | Auto-increment               |
| `name`       | string   | Display name                 |
| `email`      | string   | Login credential             |
| `password`   | string   | Bcrypt hash                  |
| `u_type`     | int      | 1=Super, 2=Admin, 3=Mgr, 4=User |
| `pt_id`      | int (FK) | Permission template          |
| `company_id` | int (FK) | → companies.id               |

#### `purchase_order_master`

| Column                   | Type     | Notes                          |
|--------------------------|----------|--------------------------------|
| `porder_id`              | int (PK) | Auto-increment                |
| `porder_no`              | string   | Formatted: PO000001           |
| `porder_project_ms`      | int (FK) | → projects.proj_id            |
| `porder_supplier_ms`     | int (FK) | → suppliers.sup_id            |
| `porder_type`            | string   | PO type                       |
| `porder_date`            | date     | PO creation date               |
| `porder_delivery_date`   | date     | Expected delivery              |
| `porder_delivery_status` | int      | 0=Not, 1=Full, 2=Partial      |
| `porder_general_status`  | string   | pending/submitted/approved     |
| `porder_total`           | decimal  | Total amount                   |
| `porder_tax`             | decimal  | Tax amount                     |
| `porder_grand_total`     | decimal  | Including tax                  |
| `integration_status`     | string   | pending/rte/synced             |
| `procore_po_id`          | int      | External Procore ID            |
| `company_id`             | int (FK) | → companies.id                 |

#### `purchase_order_details`

| Column                    | Type     | Notes                        |
|---------------------------|----------|------------------------------|
| `po_detail_autogen`       | string   | Unique item identifier       |
| `po_detail_porder_ms`     | int (FK) | → purchase_order_master.porder_id |
| `po_detail_item`          | string   | Item code                    |
| `po_detail_quantity`      | decimal  | Ordered quantity             |
| `po_detail_unitprice`     | decimal  | Unit price                   |
| `po_detail_subtotal`      | decimal  | qty * price                  |
| `po_detail_taxamount`     | decimal  | Tax amount                   |
| `po_detail_total`         | decimal  | Subtotal + tax               |

#### `budget_master`

| Column                    | Type     | Notes                        |
|---------------------------|----------|------------------------------|
| `budget_id`               | int (PK) | Auto-increment              |
| `budget_project_id`       | int (FK) | → projects.proj_id          |
| `budget_cost_code_id`     | int (FK) | → cost_codes.cc_id          |
| `budget_original_amount`  | decimal  | Initial budget amount        |
| `budget_revised_amount`   | decimal  | After change orders          |
| `budget_committed_amount` | decimal  | Committed via POs            |
| `budget_spent_amount`     | decimal  | Actual spend (received)      |
| `budget_fiscal_year`      | int      | Fiscal year                  |
| `budget_status`           | int      | 1=Active                     |
| `company_id`              | int (FK) | → companies.id               |

#### `approval_workflows`

| Column               | Type     | Notes                           |
|----------------------|----------|---------------------------------|
| `workflow_id`        | int (PK) | Auto-increment                 |
| `workflow_type`      | string   | po, budget_co, po_co           |
| `approval_level`     | int      | 1, 2, 3...                    |
| `min_amount`         | decimal  | Threshold to trigger           |
| `max_amount`         | decimal  | Upper bound (nullable)         |
| `project_id`         | int (FK) | NULL=company-wide              |
| `approver_roles`     | JSON     | Role names for resolution      |
| `approver_user_ids`  | JSON     | Legacy: specific user IDs      |
| `is_active`          | boolean  | Enable/disable                 |
| `company_id`         | int (FK) | → companies.id                 |

#### `approval_requests`

| Column                | Type     | Notes                          |
|-----------------------|----------|--------------------------------|
| `ar_id`               | int (PK) | Auto-increment                |
| `workflow_id`         | int (FK) | → approval_workflows           |
| `request_type`        | string   | po, budget_co, po_co           |
| `entity_id`           | int      | PK of the entity being approved|
| `request_amount`      | decimal  | Amount under review            |
| `current_level`       | int      | Current approval level         |
| `required_levels`     | int      | Total levels needed            |
| `request_status`      | string   | pending/approved/rejected      |
| `requested_by`        | int (FK) | → users.id                     |
| `current_approver_id` | int (FK) | → users.id                     |
| `company_id`          | int (FK) | → companies.id                 |

### Relationship Diagram (Key Tables)

```
companies (1) ──┬── (N) users
                ├── (N) projects
                ├── (N) suppliers
                ├── (N) purchase_order_master
                ├── (N) budget_master
                ├── (N) cost_codes
                ├── (N) approval_workflows
                └── (N) items

projects (1) ──┬── (N) purchase_order_master
               ├── (N) budget_master (via budget_project_id)
               ├── (N) project_cost_codes
               ├── (N) project_roles
               └── (N) rfqs

purchase_order_master (1) ──┬── (N) purchase_order_items
                            ├── (N) receive_orders
                            └── (N) po_change_orders

budget_master (1) ──── (N) budget_change_orders

suppliers (1) ──┬── (N) purchase_order_master
                ├── (N) supplier_catalogs
                └── (N) supplier_users
```

### Additional Tables

| Table                       | Purpose                              |
|-----------------------------|--------------------------------------|
| `projects`                  | Project master (PK: `proj_id`)       |
| `suppliers`                 | Supplier master (PK: `sup_id`)       |
| `items`                     | Item master catalog                  |
| `item_categories`           | Item category grouping               |
| `cost_codes`                | Cost code definitions                |
| `project_cost_codes`        | Project ↔ cost code assignments      |
| `project_roles`             | User role assignments per project    |
| `receive_orders`            | Goods receipt headers                |
| `receive_order_details`       | Goods receipt line items             |
| `rfqs`                      | Request for quote headers            |
| `rfq_items`                 | RFQ line items                       |
| `rfq_suppliers`             | RFQ ↔ supplier assignments           |
| `rfq_quotes`                | Supplier quote responses             |
| `po_change_orders`          | PO change order records              |
| `budget_change_orders`      | Budget change order records          |
| `po_templates`              | Reusable PO templates                |
| `po_template_items`         | Template line items                  |
| `supplier_catalogs`         | Supplier item catalog with pricing   |
| `item_price_history`        | Historical price changes             |
| `item_pricing`              | Supplier-submitted pricing           |
| `accounting_integrations`   | Sage/QuickBooks connection config    |
| `integration_sync_logs`     | Integration sync history             |
| `integration_field_mappings`| Field mapping for integrations       |
| `procore_sync_logs`         | Procore sync history                 |
| `commitments`               | Procore commitment tracking          |
| `permission_templates`      | Permission template definitions      |
| `units_of_measure`          | UOM lookup table                     |
| `tax_groups`                | Tax rate definitions                 |
| `equipment`                 | Equipment master list                |
| `checklists`                | Checklist templates                  |
| `checklist_items`           | Checklist template items             |
| `checklist_performances`    | Performed checklist headers          |
| `checklist_performance_details` | Performed checklist item results |
| `settings`                  | Application settings                 |
| `supplier_users`            | Supplier portal user accounts        |
| `vw_back_order_report`      | Database view for backorder data     |

---

## 8. Frontend Architecture

### Master Layout

[resources/views/layouts/admin.blade.php](resources/views/layouts/admin.blade.php) defines the shell for all admin pages:

```
┌─────────────────────────────────────────────────┐
│ Topbar (navbar-dark bg-primary)                 │
│  [☰ Toggle]  [Company Dropdown]  [User Menu]    │
├──────────┬──────────────────────────────────────┤
│ Sidebar  │  Content Area                        │
│          │  ┌──────────────────────────────────┐ │
│ Dashboard│  │ @yield('content')                │ │
│ PO       │  │                                  │ │
│ Projects │  │  (page-specific content)         │ │
│ Suppliers│  │                                  │ │
│ Items    │  └──────────────────────────────────┘ │
│ RFQ      │                                      │
│ Budgets  │  ┌──────────────────────────────────┐ │
│ Receiving│  │ @stack('scripts')                │ │
│ ...      │  │  (page-specific JavaScript)      │ │
│ Settings▼│  └──────────────────────────────────┘ │
│  Company │                                      │
│  UOM     │                                      │
│  Tax     │                                      │
└──────────┴──────────────────────────────────────┘
```

### View Composition Pattern

Every admin view follows this pattern:

```blade
@extends('layouts.admin')

@section('title', 'Page Title')

@section('content')
    {{-- Page HTML --}}
@endsection

@push('scripts')
<script>
    // Page-specific JavaScript
</script>
@endpush
```

### View Composer

[app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php) shares company data with the layout:

```php
View::composer('layouts.admin', function ($view) {
    // For super admins: load current company + switchable companies
    if (session('u_type') == 1 && session('company_id')) {
        $currentCompany = Company::find(session('company_id'));
        $switchableCompanies = Company::where('status', 1)->orderBy('name')->get();
    }
    $view->with('currentCompany', $currentCompany);
    $view->with('switchableCompanies', $switchableCompanies);
});
```

### CDN Dependencies

Loaded in the master layout `<head>` and before `</body>`:

| Library          | Version | Purpose                           |
|------------------|---------|-----------------------------------|
| Bootstrap CSS    | 5.3.0   | Responsive grid, components       |
| Font Awesome     | 6.4.0   | Icons throughout the UI           |
| jQuery           | 3.7.0   | DOM manipulation, AJAX            |
| Bootstrap JS     | 5.3.0   | Modals, dropdowns, collapse       |
| DataTables       | 1.13.4  | Table sorting, searching, paging  |
| Chart.js         | (latest)| Dashboard charts                  |

### BS4 Compatibility CSS

The layout includes custom CSS classes for Bootstrap 4 → 5 migration compatibility:

```css
.font-weight-bold { font-weight: 700 !important; }
.text-gray-800   { color: #5a5c69 !important; }
.text-gray-300   { color: #dddfeb !important; }
.text-xs         { font-size: 0.7rem; }
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-info    { border-left: 4px solid #36b9cc !important; }
.border-left-danger  { border-left: 4px solid #e74a3b !important; }
```

### DataTable Initialization

The master layout auto-initializes DataTables on any table with class `.datatable`:

```javascript
$('.datatable').each(function() {
    if (!$.fn.dataTable.isDataTable(this)) {
        $(this).DataTable({ responsive: true, pageLength: 25 });
    }
});
```

Pages can override with custom initialization in their `@push('scripts')` block.

### CSRF Token Setup

Global AJAX CSRF protection configured in the layout:

```javascript
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});
```

---

## 9. Route Map

All routes are defined in [routes/web.php](routes/web.php) and [routes/supplier.php](routes/supplier.php).

### Authentication Routes (no middleware)

| Method | URI                | Controller               | Name                  |
|--------|--------------------|---------------------------|-----------------------|
| GET    | `/`                | AuthController@index      | login                 |
| POST   | `/validate_login`  | AuthController@validate_login | auth.validatelogin|
| GET    | `/logout`          | AuthController@logout     | auth.logout           |

### Admin Routes (prefix: `/admincontrol`, middleware: `auth`)

#### Dashboard
| Method | URI                          | Name                    |
|--------|------------------------------|-------------------------|
| GET    | `dashboard`                  | admin.dashboard         |
| GET    | `dashboard/chart-data`       | admin.dashboard.chartdata |

#### Purchase Orders (`porder/`)
| Method | URI                              | Name                      |
|--------|----------------------------------|---------------------------|
| GET    | `porder/`                        | admin.porder.index        |
| GET    | `porder/add_new_purchase_order`  | admin.porder.create       |
| POST   | `porder/store`                   | admin.porder.store        |
| GET    | `porder/view/{id}`               | admin.porder.show         |
| GET    | `porder/edit/{id}`               | admin.porder.edit         |
| PUT    | `porder/update/{id}`             | admin.porder.update       |
| DELETE | `porder/delete/{id}`             | admin.porder.destroy      |
| POST   | `porder/update-status/{id}`      | admin.porder.updatestatus |
| GET    | `porder/pdf/{id}`                | admin.porder.pdf          |
| POST   | `porder/get-item-master-list`    | admin.porder.itemlist     |
| POST   | `porder/get-supplier-catalog-list`| admin.porder.cataloglist |
| POST   | `porder/get-project-address`     | admin.porder.projectaddress |
| POST   | `porder/check-budget-availability`| admin.porder.check-budget |

#### Projects (`projects/`)
| Method | URI                    | Name                      |
|--------|------------------------|---------------------------|
| GET    | `projects/`            | admin.projects.index      |
| GET    | `projects/create`      | admin.projects.create     |
| POST   | `projects/store`       | admin.projects.store      |
| GET    | `projects/view/{id}`   | admin.projects.show       |
| GET    | `projects/edit/{id}`   | admin.projects.edit       |
| PUT    | `projects/update/{id}` | admin.projects.update     |
| DELETE | `projects/delete/{id}` | admin.projects.destroy    |

#### Suppliers (`suppliers/`)
Standard CRUD: index, create, store, show, edit, update, destroy, updatestatus

#### Items (`items/`)
CRUD + pricing: index, create, store, show, edit, update, destroy, priceComparison, priceHistory, updatePrice, pricingSummary, import, export

#### RFQ (`rfq/`)
| Method | URI                                | Name                     |
|--------|------------------------------------|--------------------------|
| GET    | `rfq/`                             | admin.rfq.index          |
| GET    | `rfq/create`                       | admin.rfq.create         |
| POST   | `rfq/store`                        | admin.rfq.store          |
| GET    | `rfq/view/{id}`                    | admin.rfq.show           |
| POST   | `rfq/send/{id}`                    | admin.rfq.send           |
| POST   | `rfq/record-quote/{id}/{supplierId}` | admin.rfq.recordquote |
| POST   | `rfq/convert-to-po/{id}`          | admin.rfq.converttopo    |
| GET    | `rfq/compare-quotes/{id}`         | admin.rfq.comparequotes  |

#### Budgets (`budgets/`)
Standard CRUD + transfer, summary, import-from-procore

#### Project Budget Management (`budgets/projects/`)
Cost code assignment, budget setup, budget summary, availability check (AJAX)

#### Budget Change Orders (`budget-change-orders/projects/`)
Index, create, store, show, submit, approve, reject, cancel

#### PO Change Orders (`po-change-orders/`)
Index, create, store, show, submit, approve, reject, cancel, check-budget

#### Receive Orders (`receive/`)
Standard CRUD + back order report, receiving summary

#### PO Templates (`templates/`)
Standard CRUD + duplicate, create-po-from-template

#### Approval Management (`approvals/`)
Dashboard, show, approve, reject, override, history, statistics

#### Approval Workflows (`approval-workflows/`)
Standard CRUD + toggle-status

#### Project Roles (`project-roles/`)
Standard CRUD + users-by-role (AJAX)

#### Procore Integration (`procore/`)
Dashboard, sync-all, sync-projects, sync-vendors, sync-cost-codes, sync-budgets, sync-commitments, push-po, sync-log, mappings, settings, test-connection

#### Accounting Integrations (`integrations/`)
CRUD + OAuth callback, test-connection, sync operations, logs, toggle-active

#### Settings Modules
| Prefix         | Name prefix           | CRUD routes |
|----------------|-----------------------|-------------|
| `costcodes/`   | admin.costcodes.*     | index, store, update, destroy, hierarchy |
| `uom/`         | admin.uom.*           | index, store, update, destroy |
| `taxgroups/`   | admin.taxgroups.*     | index, store, update, destroy |
| `equipment/`   | admin.equipment.*     | index, store, update, destroy |
| `permissions/` | admin.permissions.*   | index, store, update, destroy |
| `packages/`    | admin.packages.*      | index, store, update, destroy |
| `company/`     | admin.company.*       | index, update, smtp |

#### Company Management (Super Admin)
| Method | URI                         | Name                     |
|--------|-----------------------------|--------------------------|
| GET    | `companies/`                | admin.companies.index    |
| GET    | `companies/create`          | admin.companies.create   |
| POST   | `companies/`                | admin.companies.store    |
| GET    | `companies/{company}`       | admin.companies.show     |
| GET    | `companies/{company}/edit`  | admin.companies.edit     |
| PUT    | `companies/{company}`       | admin.companies.update   |
| DELETE | `companies/{company}`       | admin.companies.destroy  |
| POST   | `companies/{company}/switch`| admin.companies.switch   |

#### Reports (`reports/`)
| Method | URI                                | Name                              |
|--------|------------------------------------|-----------------------------------|
| GET    | `reports/budget-vs-actual`         | admin.reports.budget-vs-actual    |
| GET    | `reports/budget-vs-actual/export`  | admin.reports.budget-vs-actual.export |
| GET    | `reports/budget-drilldown/{p}/{c}` | admin.reports.budget-drilldown    |
| GET    | `reports/variance-analysis`        | admin.reports.variance-analysis   |
| GET    | `reports/change-orders`            | admin.reports.change-orders       |
| GET    | `reports/change-orders/export`     | admin.reports.change-orders.export|
| GET    | `reports/committed-vs-actual`      | admin.reports.committed-vs-actual |

### Supplier Portal Routes (prefix: `/supplier`)

| Method | URI                                    | Middleware            | Name                         |
|--------|----------------------------------------|-----------------------|------------------------------|
| GET    | `supplier/login`                       | guest:supplier        | supplier.login               |
| POST   | `supplier/login`                       | guest:supplier        | supplier.login.submit        |
| GET    | `supplier/register`                    | guest:supplier        | supplier.register            |
| POST   | `supplier/register`                    | guest:supplier        | supplier.register.submit     |
| GET    | `supplier/password/forgot`             | guest:supplier        | supplier.password.request    |
| POST   | `supplier/password/email`              | guest:supplier        | supplier.password.email      |
| GET    | `supplier/dashboard`                   | auth.supplier + verified | supplier.dashboard         |
| GET    | `supplier/profile`                     | auth.supplier + verified | supplier.profile           |
| GET    | `supplier/pricing/`                    | auth.supplier + verified | supplier.pricing.index     |
| GET    | `supplier/pricing/create`              | auth.supplier + verified | supplier.pricing.create    |
| POST   | `supplier/pricing/`                    | auth.supplier + verified | supplier.pricing.store     |
| GET    | `supplier/rfq/`                        | auth.supplier + verified | supplier.rfq.index         |
| GET    | `supplier/rfq/{rfq}`                   | auth.supplier + verified | supplier.rfq.show          |
| POST   | `supplier/rfq/{rfq}/quote`             | auth.supplier + verified | supplier.rfq.quote         |

### Webhook (No Auth)

| Method | URI               | Name             |
|--------|-------------------|------------------|
| POST   | `procore/webhook`  | procore.webhook  |

---

## 10. Key Workflows

### Purchase Order Lifecycle

```
    ┌─────────┐    Create    ┌─────────┐    Submit    ┌───────────┐
    │  Draft  │ ──────────→ │ Pending │ ──────────→ │ Submitted │
    └─────────┘              └─────────┘              └───────────┘
                                                            │
                            ┌──────────┐                    │
                            │ Rejected │ ←── Reject ────────┤
                            └──────────┘                    │
                                                      Approve (via workflow)
                                                            │
                                                            ▼
    ┌──────────┐   Procore   ┌──────────┐            ┌──────────┐
    │  Synced  │ ←───────── │   RTE    │ ←───────── │ Approved │
    └──────────┘    Sync     └──────────┘  Mark RTE  └──────────┘
```

**Status values in `porder_general_status`:** pending → submitted → approved/rejected
**Integration status in `integration_status`:** pending → rte → synced
**Delivery status in `porder_delivery_status`:** 0 (Not received) → 2 (Partial) → 1 (Fully received)

### Budget Lifecycle

```
    ┌─────────────┐    Budget CO     ┌─────────────────┐
    │   Setup     │ ──────────────→ │  Budget Change   │
    │ (Original)  │                  │  Order (Draft)   │
    └─────────────┘                  └─────────────────┘
         │                                   │
    PO Created                          Submit for
         │                              Approval
         ▼                                   │
    ┌─────────────┐                          ▼
    │  Committed  │                  ┌─────────────────┐
    │  (via PO)   │                  │    Approved      │ → Updates budget_revised_amount
    └─────────────┘                  └─────────────────┘
         │
    Goods Received
         │
         ▼
    ┌─────────────┐
    │   Actual    │
    │  (Spent)    │
    └─────────────┘
```

**Budget formula:**
`Available = Revised Amount - Committed Amount - Spent Amount`

**Budget thresholds:**
- < 75% utilization → OK (green)
- 75–89% utilization → WARNING (notification sent)
- >= 90% utilization → CRITICAL (notification sent, may require override)

### Approval Workflow

```
    Entity Submitted
         │
         ▼
    ┌────────────────────────┐
    │ Find Matching Workflow │ ← Checks: entity type, amount range, project
    └────────────────────────┘
         │
         ├── No workflow found → AUTO-APPROVE → Execute entity logic
         │
         ├── Workflow found:
         │   ├── Create ApprovalRequest (Level 1 of N)
         │   ├── Resolve approvers from ProjectRole (role-based)
         │   │   └── Filter by: role name, approval_limit >= amount
         │   └── Send ApprovalPendingNotification
         │
         ▼
    ┌──────────────────┐
    │ Approver Action  │
    ├──────────────────┤
    │ Approve:         │
    │  ├── More levels? → Advance to Level N+1 → Notify next approvers
    │  └── Final level? → Mark APPROVED → Execute entity logic
    │                    │
    │ Reject:           │
    │  └── Mark REJECTED → Update entity status
    └──────────────────┘
```

**Role-based resolution:** Workflows specify `approver_roles` (e.g., `["project_manager", "director"]`). The system queries `project_roles` for users with those roles on the relevant project, optionally filtered by `approval_limit`.

### Receive Order Flow

```
    Purchase Order (Approved)
         │
         ▼
    ┌────────────────────┐
    │ Create Receive     │ ← Slip number, date, item quantities
    │ Order              │
    └────────────────────┘
         │
         ├── Update delivery status on PO:
         │   ├── All items fully received → status = 1 (Fully Received)
         │   ├── Some items received      → status = 2 (Partially Received)
         │   └── No items received        → status = 0 (Not Received)
         │
         ├── Update budget actuals:
         │   └── Budget.spend(received_amount) for each cost code
         │
         ├── Recalculate backorders:
         │   └── BackorderService::recalcForPo()
         │
         └── If partial → Notify admins of backorder items
```

### RFQ to Purchase Order Flow

```
    ┌───────────┐   Send to    ┌───────────────┐
    │ Create    │ ──────────→ │ Suppliers      │
    │ RFQ       │  suppliers  │ Receive RFQ    │
    └───────────┘              └───────────────┘
                                      │
                               Submit quotes
                                      │
                                      ▼
                              ┌───────────────┐
                              │ Compare       │
                              │ Quotes        │
                              └───────────────┘
                                      │
                               Select winner
                                      │
                                      ▼
                              ┌───────────────┐
                              │ Convert to PO │ → PO created with quoted prices
                              │               │ → Supplier catalog updated
                              │               │ → RFQ marked as CONVERTED
                              └───────────────┘
```

---

## Appendix: Model Reference

### Models NOT Using CompanyScope

These models are either global/system-level or are the tenant table itself:

| Model                    | Table                        | Reason                      |
|--------------------------|------------------------------|-----------------------------|
| Company                  | companies                    | IS the tenant table         |
| JMC_MASTER_JOB           | jmc_master_job               | External legacy data        |
| JMC_MASTER_COST_CODE     | jmc_master_cost_code         | External legacy data        |
| SupplierCatalog          | supplier_catalogs            | Scoped via supplier FK      |
| ItemPriceHistory         | item_price_history           | Scoped via item/supplier FK |
| PoTemplate               | po_templates                 | May need CompanyScope       |
| PoTemplateItem           | po_template_items            | Scoped via template FK      |
| Commitment               | commitments                  | Procore-specific            |
| ProcoreSyncLog           | procore_sync_logs            | Log data                    |
| UnitOfMeasure            | units_of_measure             | Global lookup               |
| TaxGroup                 | tax_groups                   | May need CompanyScope       |
| PermissionTemplate       | permission_templates         | May need CompanyScope       |
| Setting                  | settings                     | Global config               |
| ItemPackageDetail        | item_package_details         | Scoped via package FK       |
| ChecklistItem            | checklist_items              | Scoped via checklist FK     |
| ChecklistPerformance     | checklist_performances       | May need CompanyScope       |
| ChecklistPerformanceDetail | checklist_performance_details | Scoped via performance FK |
| IntegrationFieldMapping  | integration_field_mappings   | Scoped via integration FK   |
