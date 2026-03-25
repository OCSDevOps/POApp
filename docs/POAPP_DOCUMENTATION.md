# POApp - Comprehensive Project Documentation

**Document Version:** 1.0
**Date:** February 8, 2026
**Application:** Purchase Order Management System (POApp)
**Platform:** Laravel 9 / SQL Server / Bootstrap 5

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [System Architecture](#2-system-architecture)
3. [Database Schema Reference](#3-database-schema-reference)
4. [Feature Documentation](#4-feature-documentation)
5. [Information Flow Diagrams](#5-information-flow-diagrams)
6. [Use Case Catalog](#6-use-case-catalog)
7. [Project Progress Report](#7-project-progress-report)
8. [Version 2 Enhancement Recommendations](#8-version-2-enhancement-recommendations)

---

## 1. Executive Summary

### 1.1 Overview

POApp is a comprehensive **multi-tenant Purchase Order Management System** built for construction and project-based businesses. It provides end-to-end procurement lifecycle management from requisition through receiving, with integrated budget controls, approval workflows, and external system integrations.

### 1.2 Target Users

| User Role | Description |
|-----------|-------------|
| **Super Admin** | System administrator managing multiple tenant companies |
| **Admin User** | Company-level administrator with full feature access |
| **Project Manager** | Manages projects, budgets, and purchase orders |
| **Finance User** | Handles budget approvals, change orders, accounting integration |
| **Staff** | Creates purchase orders and receive orders |
| **Supplier User** | External vendor using the supplier self-service portal |

### 1.3 Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend Framework | Laravel | 9.x |
| Language | PHP | 8.x |
| Database | Microsoft SQL Server Express | DESKTOP-Q2001NS\SQLEXPRESS |
| Database Name | porder_db | - |
| Frontend CSS | Bootstrap (CDN) | 5.3.0 |
| Frontend JS | jQuery (CDN) | 3.7.0 |
| Data Tables | DataTables (CDN) | 1.13.4 |
| Charts | Chart.js (CDN) | Latest |
| Icons | Font Awesome (CDN) | 6.4.0 |
| Build Tool | Vite | 3.0 |
| API Auth | Laravel Sanctum | 3.0 |
| HTTP Client | Guzzle | 7.2 |

### 1.4 Current Status Snapshot

| Metric | Count |
|--------|-------|
| Eloquent Models | 49 |
| Controllers | 41 (35 Admin + 6 Supplier) |
| Services | 11 |
| Blade Views | 103+ |
| Route Definitions | 315 (285 web + 29 supplier + 1 API) |
| Database Tables | 40+ |
| SQL Server Views | 4 |
| Database Migrations | 15 |
| Automated Tests | 21+ |
| Admin Routes Verified | 51/51 passing |

---

## 2. System Architecture

### 2.1 Directory Structure

```
POApp/html/                          # Laravel Application Root
├── app/
│   ├── Console/Commands/            # Artisan CLI commands
│   ├── Exceptions/                  # Exception handlers
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # 35 admin feature controllers
│   │   │   ├── Supplier/           # 6 supplier portal controllers
│   │   │   ├── AuthController.php  # Admin authentication
│   │   │   └── DashboardController.php
│   │   └── Middleware/
│   │       ├── Authenticate.php           # Admin auth guard
│   │       ├── SupplierAuthenticate.php   # Supplier auth guard
│   │       ├── SetTenantContext.php       # Multi-tenant context
│   │       ├── EnsureSupplierEmailIsVerified.php
│   │       ├── RedirectIfAuthenticated.php
│   │       └── [standard Laravel middleware]
│   ├── Models/                      # 49 Eloquent models
│   │   └── Scopes/                 # Global query scopes
│   ├── Notifications/              # Email/database notifications
│   ├── Policies/                   # Authorization policies
│   ├── Providers/
│   │   ├── AppServiceProvider.php  # View composers, bindings
│   │   ├── AuthServiceProvider.php # Gates & policies
│   │   └── RouteServiceProvider.php # Route loading
│   ├── Services/                   # 8 business logic services
│   │   └── Integrations/          # 3 integration services
│   ├── Traits/
│   │   └── CompanyScope.php       # Multi-tenancy trait
│   └── View/Components/           # Blade components
│
├── config/
│   ├── app.php                    # Application config
│   ├── auth.php                   # Dual-guard auth config
│   ├── database.php               # SQL Server connection
│   └── [standard configs]
│
├── database/
│   ├── factories/                 # Model factories
│   ├── migrations/                # 15 migration files
│   └── seeders/                   # Database seeders
│
├── resources/views/
│   ├── admin/                     # Admin panel views (25 subdirectories)
│   │   ├── approval-workflows/    # Approval workflow CRUD
│   │   ├── approvals/             # Approval inbox/processing
│   │   ├── backorders/            # Backorder tracking
│   │   ├── budget/                # Budget CRUD
│   │   ├── budget-change-orders/  # Budget change orders
│   │   ├── budgets/               # Project budget setup
│   │   ├── checklists/            # Checklist management
│   │   ├── companies/             # Company management (super admin)
│   │   ├── company/               # Company settings
│   │   ├── costcodes/             # Cost code management
│   │   ├── equipment/             # Equipment tracking
│   │   ├── integrations/          # Accounting integrations
│   │   ├── item/                  # Item catalog management
│   │   ├── packages/              # Item packages
│   │   ├── permissions/           # Permission templates
│   │   ├── po-change-orders/      # PO change orders
│   │   ├── porder/                # Purchase orders
│   │   ├── pricing/               # Supplier pricing
│   │   ├── procore/               # Procore integration
│   │   ├── project/               # Project management
│   │   ├── project-roles/         # Project role assignment
│   │   ├── receive/               # Receiving orders
│   │   ├── reports/               # Reports & analytics
│   │   ├── rfq/                   # RFQ management
│   │   ├── supplier/              # Supplier management
│   │   ├── support/               # Support system
│   │   ├── taxgroups/             # Tax groups
│   │   ├── template/              # PO templates
│   │   ├── tenants/               # Tenant management
│   │   └── uom/                   # Units of measure
│   ├── auth/                      # Login/registration views
│   ├── layouts/
│   │   └── admin.blade.php        # Master admin layout
│   ├── pdf_view/                  # PDF templates
│   └── supplier/                  # Supplier portal views
│
├── routes/
│   ├── web.php                    # 285 admin routes (~900 lines)
│   ├── supplier.php               # 29 supplier portal routes
│   └── api.php                    # 1 API route (Sanctum)
│
├── tests/
│   ├── Feature/
│   │   ├── Admin/                 # Admin feature tests
│   │   ├── Supplier/              # Supplier portal tests
│   │   ├── CompanyManagementTest.php
│   │   ├── MultiTenancyIsolationTest.php
│   │   └── MultiTenancyTest.php
│   └── Unit/                      # Unit tests
│
└── legacy_archive/                # Original CodeIgniter application
    └── codeigniter/
        └── db/                    # Legacy SQL scripts
```

### 2.2 Multi-Tenancy Architecture

POApp implements a **single-database, shared-schema** multi-tenancy model where all tenant data resides in the same database, isolated by a `company_id` column.

#### 2.2.1 CompanyScope Trait (`app/Traits/CompanyScope.php`)

The `CompanyScope` trait is applied to 28 models and provides:

1. **Automatic Query Filtering**: Adds `WHERE company_id = session('company_id')` to all SELECT queries via a Laravel Global Scope
2. **Auto-Injection on Create**: Sets `company_id` from session when creating new records
3. **Helper Scopes**:
   - `forCompany($id)` - Query a specific company's data
   - `allCompanies()` - Bypass scoping (for super admin operations)
4. **Ownership Checks**:
   - `isOwnedByCurrentCompany()` - Verify record belongs to current tenant
   - `isOwnedByCompany($id)` - Verify record belongs to specific tenant

```
Models Using CompanyScope (28):
User, Project, Supplier, PurchaseOrder, PurchaseOrderItem,
ReceiveOrder, ReceiveOrderItem, Item, Budget, CostCode,
BudgetChangeOrder, PoChangeOrder, ApprovalWorkflow,
ApprovalRequest, ProjectRole, AccountingIntegration,
IntegrationSyncLog, IntegrationFieldMapping, SupplierUser,
Rfq, RfqItem, RfqSupplier, RfqQuote, PoTemplate,
PoTemplateItem, Equipment, ProjectCostCode, Commitment

Models WITHOUT CompanyScope:
Company (root entity), ItemCategory, UnitOfMeasure,
TaxGroup, SupplierCatalog, ProcoreSyncLog,
ItemPriceHistory, ItemPricing
```

#### 2.2.2 SetTenantContext Middleware (`app/Http/Middleware/SetTenantContext.php`)

Executes on every authenticated request:

```
Request → SetTenantContext Middleware
           │
           ├─ Check auth (web OR supplier guard)
           ├─ Extract user.company_id
           ├─ Set session: company_id, company_name
           ├─ Inject tenant_company_id into Request
           ├─ Share with all views: current_company_id, current_company_name
           └─ Log tenant context
```

**Special Cases:**
- Super Admin (u_type=1): Can proceed without company_id, can switch companies
- Supplier Users: Use supplier guard, mapped to company via supplier_id
- Regular users without company: Blocked with HTTP 403

#### 2.2.3 Company Switching (Super Admin)

Super admins can switch between tenant companies via a dropdown in the topbar:
- `POST /admincontrol/companies/switch` updates session company context
- All subsequent queries automatically filter to the selected company
- View composer in `AppServiceProvider` loads switchable companies list

### 2.3 Authentication System

#### 2.3.1 Dual-Guard Configuration (`config/auth.php`)

| Guard | Driver | Provider | Model | Purpose |
|-------|--------|----------|-------|---------|
| `web` (default) | session | users | `App\Models\User` | Admin portal |
| `supplier` | session | supplier_users | `App\Models\SupplierUser` | Supplier portal |

#### 2.3.2 Admin Login Flow

```
GET /                  → Login page (auth/login view)
POST /validate_login   → AuthController@validateLogin
  ├─ Validate email + password
  ├─ Auth::attempt(['email' => ..., 'password' => ...])
  ├─ On success:
  │   ├─ Set session: name, company_id, company_name
  │   └─ Redirect → /admincontrol/dashboard
  └─ On failure:
      └─ Redirect → / with error message
```

#### 2.3.3 Supplier Login Flow

```
GET /supplier/login          → Supplier login form
POST /supplier/login         → Supplier/AuthController@login
  ├─ Auth::guard('supplier')->attempt(...)
  ├─ On success → /supplier/dashboard
  └─ On failure → /supplier/login with error

Additional supplier features:
- Self-registration: GET/POST /supplier/register
- Email verification required (MustVerifyEmail interface)
- Password reset: /supplier/password/forgot → email → reset
```

#### 2.3.4 Middleware Chain

```
Admin Routes:
  auth → SetTenantContext → [Controller]

Supplier Routes:
  auth.supplier → SetTenantContext → verified.supplier → [Controller]

Guest Routes (login pages):
  guest / guest:supplier → [Controller]
```

### 2.4 Route Organization

All routes are organized in three files:

#### `routes/web.php` (285 routes)

```
/ (root)                     → Login page
/validate_login              → Login POST
/logout                      → Logout

/admincontrol/               → Admin prefix (middleware: auth)
  ├── dashboard              → Main dashboard
  ├── companies/*            → Company management (super admin)
  ├── tenants/*              → Tenant administration
  ├── projects/*             → Project CRUD
  ├── suppliers/*            → Supplier CRUD
  ├── items/*                → Item catalog CRUD + pricing + import/export
  ├── porder/*               → Purchase orders + AJAX endpoints
  ├── receive/*              → Receive orders + reports
  ├── budgets/*              → Budget CRUD + setup + summary
  ├── budget-change-orders/* → Budget change orders
  ├── po-change-orders/*     → PO change orders
  ├── approvals/*            → Approval inbox + processing
  ├── approval-workflows/*   → Workflow configuration
  ├── project-roles/*        → Project role assignment
  ├── rfq/*                  → RFQ management
  ├── templates/*            → PO templates
  ├── costcodes/*            → Cost code management
  ├── taxgroups/*            → Tax group management
  ├── uom/*                  → Unit of measure management
  ├── packages/*             → Item packages
  ├── pricing/*              → Supplier pricing
  ├── backorders/*           → Backorder tracking
  ├── equipment/*            → Equipment management
  ├── checklists/*           → Checklist management
  ├── perform-checklists/*   → Checklist execution
  ├── permissions/*          → Permission templates
  ├── procore/*              → Procore integration
  ├── integrations/*         → Accounting integrations
  ├── reports/*              → Reports & analytics
  ├── company/*              → Company settings
  └── support/*              → Support system
```

#### `routes/supplier.php` (29 routes)

```
/supplier/
  ├── login, register        → Authentication
  ├── email/verify/*         → Email verification
  ├── dashboard              → Supplier dashboard
  ├── profile                → Profile management
  ├── pricing/*              → Catalog pricing management
  └── rfq/*                  → RFQ viewing and quoting
```

#### `routes/api.php` (1 route)

```
/api/user                    → Get authenticated user (Sanctum)
```

### 2.5 Frontend Architecture

#### 2.5.1 Master Layout (`resources/views/layouts/admin.blade.php`)

```
┌──────────────────────────────────────────────────────────┐
│                    TOPBAR (60px)                          │
│  [Welcome, User]     [Company Switcher]  [Profile ▼]     │
├──────────┬───────────────────────────────────────────────┤
│          │                                               │
│ SIDEBAR  │           CONTENT AREA                        │
│ (250px)  │                                               │
│          │  ┌─ Alert Messages ─┐                         │
│ Dashboard│  │ Success/Error    │                         │
│ PO Mgmt  │  └─────────────────┘                         │
│ Projects │                                               │
│ Inventory│  @yield('content')                            │
│ Integr.  │                                               │
│ Reports  │                                               │
│ Settings │  @stack('styles')                             │
│          │  @stack('scripts')                            │
│ Logout   │                                               │
│          │                                               │
└──────────┴───────────────────────────────────────────────┘
```

#### 2.5.2 CDN Dependencies

| Library | CDN URL | Purpose |
|---------|---------|---------|
| Bootstrap 5.3.0 CSS | cdn.jsdelivr.net | Layout, components, utilities |
| Bootstrap 5.3.0 JS | cdn.jsdelivr.net | Interactive components |
| jQuery 3.7.0 | code.jquery.com | DOM manipulation, AJAX |
| DataTables 1.13.4 | cdn.datatables.net | Sortable/searchable tables |
| Font Awesome 6.4.0 | cdnjs.cloudflare.com | Icons |
| Chart.js | cdn.jsdelivr.net | Charts & graphs |

#### 2.5.3 View Pattern

All admin views follow this pattern:
```blade
@extends('layouts.admin')
@section('title', 'Page Title')

@section('content')
    {{-- Page content --}}
@endsection

@push('scripts')
<script>
    // Page-specific JavaScript
</script>
@endpush

@push('styles')
<style>
    /* Page-specific CSS */
</style>
@endpush
```

### 2.6 Request Lifecycle Diagram

```
HTTP Request
    │
    ▼
┌─────────────────────┐
│   Laravel Router     │ ← routes/web.php / supplier.php
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│   Middleware Stack   │
│  1. EncryptCookies   │
│  2. VerifyCsrfToken  │
│  3. Authenticate     │ ← Redirects to /login if unauthenticated
│  4. SetTenantContext  │ ← Sets session(company_id)
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│    Controller        │ ← app/Http/Controllers/Admin/*
│  ┌────────────────┐  │
│  │ Service Layer   │  │ ← app/Services/*
│  │ (Business Logic)│  │
│  └───────┬────────┘  │
│          │           │
│  ┌───────▼────────┐  │
│  │ Eloquent Models │  │ ← app/Models/*
│  │ + CompanyScope  │  │ ← Auto-filters by company_id
│  └───────┬────────┘  │
│          │           │
│  ┌───────▼────────┐  │
│  │  SQL Server     │  │ ← porder_db
│  └────────────────┘  │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│   Blade View         │ ← resources/views/admin/*
│  @extends('layouts   │
│   .admin')           │
│  + DataTables        │
│  + Chart.js          │
└─────────────────────┘
          │
          ▼
    HTTP Response
```

---

## 3. Database Schema Reference

### 3.1 Legacy Tables (from CodeIgniter migration)

These tables existed before the Laravel migration and follow legacy naming conventions:

#### Core Business Tables

| Table | Primary Key | Description | Key Columns |
|-------|------------|-------------|-------------|
| `project_master` | `proj_id` | Projects | proj_number, proj_name, proj_address, proj_description, proj_contact, proj_status (1/0), company_id |
| `project_details` | `proj_dtl_id` | Project detail records | proj_dtl_proj_id (FK), detail fields |
| `supplier_master` | `sup_id` | Suppliers/Vendors | sup_name, sup_email, sup_phone, sup_address, sup_contact_person, sup_details, sup_status (1/0), company_id |
| `purchase_order_master` | `porder_id` | Purchase Orders | porder_no, porder_project_ms (FK), porder_supplier_ms (FK), porder_total_amount, porder_total_tax, porder_status (1/0), porder_delivery_status (0/1/2), company_id |
| `purchase_order_details` | `po_detail_id` | PO Line Items | po_detail_porder_ms (FK), po_detail_item, po_detail_quantity, po_detail_rate, po_detail_amount, backordered_qty, backorder_status |
| `receive_order_master` | `rorder_id` | Goods Receipts | rorder_porder_ms (FK), rorder_slip_no, rorder_date, rorder_totalitem, rorder_totalamount, rorder_status (1/0), company_id |
| `receive_order_details` | `ro_detail_id` | Receipt Line Items | ro_detail_rorder_ms (FK), ro_detail_item, ro_detail_quantity |
| `item_master` | `item_id` | Item Catalog | item_code, item_name, item_description, item_cat_ms (FK), item_ccode_ms (FK), item_unit_ms (FK), item_status (1/0), company_id |
| `cost_code_master` | `cc_id` | Cost Codes | cc_no, cc_description, cc_details, cc_parent_code, cc_category_code, cc_subcategory_code, cc_level (1/2/3), cc_full_code, cc_status (1/0), company_id |
| `budget_master` | `budget_id` | Budgets | budget_project_id (FK), budget_cost_code_id (FK), budget_original_amount, budget_revised_amount, budget_committed_amount, budget_spent_amount, budget_remaining_amount, budget_fiscal_year, budget_status (1/0), company_id |

#### Reference Tables

| Table | Primary Key | Description | Key Columns |
|-------|------------|-------------|-------------|
| `item_category_tab` | `icat_id` | Item Categories | icat_name, icat_details, icat_status (1/0) |
| `unit_of_measure_tab` | `uom_id` | Units of Measure | uom_name, uom_code, uom_status (1/0) |
| `taxgroup_master` | `taxgroup_id` | Tax Groups | taxgroup_name, percentage (INT), taxgroup_status (1/0) |
| `supplier_catalog_tab` | `supcat_id` | Supplier-Item Pricing | supcat_supplier (FK), supcat_item_code, supcat_sku_no, supcat_price, supcat_lastdate, supcat_status (1/0) |

#### User Tables

| Table | Primary Key | Description | Key Columns |
|-------|------------|-------------|-------------|
| `users` | `id` | Admin Users (Laravel) | name, email, password, company_id, u_type, u_status |
| `user_info` | `u_id` | Legacy User Info | u_type, u_access, username, password, phone, email, firstname, lastname, status |
| `master_user_type` | `mu_id` | User Type Lookup | mu_name |

### 3.2 Laravel-Added Tables

These tables were created via Laravel migrations:

#### Multi-Tenancy (Phase 3)

| Table | Primary Key | Description |
|-------|------------|-------------|
| `companies` | `id` | Tenant companies - name, subdomain, status, address, settings (JSON) |
| `personal_access_tokens` | `id` | Laravel Sanctum API tokens |

#### Supplier Portal (Phase 1.2)

| Table | Primary Key | Description |
|-------|------------|-------------|
| `supplier_users` | `id` | Supplier portal users - name, email, password, supplier_id (FK), company_id (FK), email_verified_at |

#### Item Pricing (Phase 1.3)

| Table | Primary Key | Description |
|-------|------------|-------------|
| `item_pricing` | `pricing_id` | Time-based pricing - item_id, supplier_id, project_id, unit_price, effective_from/to, status |

#### RFQ System (Phase 1.4)

| Table | Primary Key | Description |
|-------|------------|-------------|
| `rfq_master` | `rfq_id` | RFQ headers - rfq_no, rfq_project_id, rfq_title, rfq_due_date, rfq_status |
| `rfq_items` | `rfqi_id` | RFQ line items - rfqi_rfq_id (FK), rfqi_item_id, rfqi_quantity, rfqi_target_price |
| `rfq_suppliers` | `rfqs_id` | RFQ supplier assignments - rfqs_rfq_id (FK), rfqs_supplier_id, rfqs_sent_date, rfqs_response_date |
| `rfq_quotes` | `rfqq_id` | Supplier quotes - rfqq_rfqs_id (FK), rfqq_rfqi_id (FK), rfqq_quoted_price, rfqq_lead_time_days |

#### Budget Management (Phase 2.2)

| Table | Primary Key | Description |
|-------|------------|-------------|
| `project_cost_codes` | `id` | Project-CostCode assignments - project_id, cost_code_id, is_active |
| `budget_change_orders` | `bco_id` | Budget COs - bco_number, budget_id, project_id, cost_code_id, bco_type (increase/decrease/transfer), bco_amount, bco_status |
| `po_change_orders` | `poco_id` | PO COs - poco_number, purchase_order_id, poco_type, poco_amount, poco_details (JSON), poco_status |
| `approval_workflows` | `workflow_id` | Approval config - workflow_name, workflow_type, approval_level, amount_threshold_min/max, approver_user_ids (JSON), approval_logic |
| `approval_requests` | `request_id` | Approval queue - workflow_id, request_type, entity_id, request_amount, current_level, required_levels, request_status, approval_history (JSON) |

#### Accounting Integration (Phase 2.1)

| Table | Primary Key | Description |
|-------|------------|-------------|
| `accounting_integrations` | `id` | Integration config - integration_type (sage/quickbooks), OAuth credentials, auto_sync settings, is_active |
| `integration_sync_logs` | `id` | Sync audit log - integration_id, sync_type, operation, status, records_attempted/succeeded/failed |
| `integration_field_mappings` | `id` | Field mapping - integration_id, entity_type, local_field, external_field, transformation |

#### Procore Integration

| Table | Primary Key | Description |
|-------|------------|-------------|
| `procore_auth` | `id` | Procore API credentials |
| `procore_project_mapping` | `ppm_id` | Project mapping - ppm_local_project_id, ppm_procore_project_id, ppm_last_sync_at |
| `procore_cost_code_mapping` | `pccm_id` | Cost code mapping - pccm_local_cost_code_id, pccm_procore_cost_code_id, pccm_procore_project_id |
| `procore_sync_log` | `sync_id` | Sync log - sync_type, sync_direction, sync_status, sync_message, sync_created_at |

#### Equipment & Checklists

| Table | Primary Key | Description |
|-------|------------|-------------|
| `eq_master` | `equip_id` | Equipment assets - name, tag, type, category, status, location, supplier, operator |
| `checklist_master` | `cl_id` | Checklists - cl_name, cl_frequency, cl_eq_ids (JSON), cl_user_ids (JSON) |
| `checklist_details` | `cli_id` | Checklist items - cl_id (FK), cli_item |
| `cl_perform_master` | `cl_p_id` | Checklist execution - cl_id (FK), cl_eq_id, cl_p_date, cl_p_item_values (JSON) |
| `cl_perform_details` | `cl_pd_id` | Execution details - cl_p_id (FK), cl_pd_cli_id (FK), cl_pd_cli_value, cl_pd_cli_notes |
| `permission_master` | `id` | Permission templates |

### 3.3 SQL Server Views

#### `vw_budget_summary`
Aggregates budget data with utilization calculations.

| Column | Source | Description |
|--------|--------|-------------|
| budget_id | budget_master | Budget record ID |
| proj_id, proj_number, proj_name | project_master | Project info |
| cc_id, cost_code, cost_code_name | cost_code_master | Cost code info |
| budget_original_amount | budget_master | Original budget |
| budget_revised_amount | budget_master | Revised budget |
| budget_committed_amount | budget_master | Committed to POs |
| budget_spent_amount | budget_master | Actual spend |
| budget_remaining_amount | budget_master | Remaining balance |
| budget_fiscal_year | budget_master | Fiscal year |
| budget_utilization_pct | Calculated | (committed + spent) / revised * 100 |

#### `vw_receiving_summary`
Summarizes receiving activity with PO and project context.

| Column | Source | Description |
|--------|--------|-------------|
| rorder_id, rorder_slip_no, rorder_date | receive_order_master | Receipt info |
| porder_id, porder_no | purchase_order_master | PO reference |
| proj_name | project_master | Project name |
| sup_name | supplier_master | Supplier name |
| rorder_totalitem, rorder_totalamount | receive_order_master | Receipt totals |
| status_text | Calculated | Active/Cancelled |

#### `vw_back_order_report`
Identifies PO line items with outstanding quantities.

| Column | Source | Description |
|--------|--------|-------------|
| porder_id, porder_no, porder_createdate | purchase_order_master | PO info |
| item_code | purchase_order_details | Item identifier |
| ordered_qty | purchase_order_details | Quantity ordered |
| received_qty | SUM(receive_order_details) | Quantity received |
| back_order_qty | Calculated | ordered - received |
| proj_name, sup_name | Joins | Project and supplier |

**Filter**: Only active POs where ordered_qty > received_qty

#### `vw_item_pricing_summary`
Joins item, category, cost code, UOM, supplier, and catalog data.

| Column | Source | Description |
|--------|--------|-------------|
| item_id, item_code, item_name | item_master | Item info |
| category_name | item_category_tab | Category |
| cost_code | cost_code_master | Cost code |
| unit_of_measure | unit_of_measure_tab | UOM |
| sup_id, supplier_name | supplier_master | Supplier |
| supplier_sku | supplier_catalog_tab | Supplier SKU |
| current_price | supplier_catalog_tab | Current price |

### 3.4 Naming Conventions

| Convention | Pattern | Examples |
|-----------|---------|----------|
| Table suffixes | `_master`, `_tab`, `_details` | project_master, item_category_tab, receive_order_details |
| Column prefixes | Match table abbreviation | proj_name, porder_no, sup_email, item_code, cc_no |
| Foreign keys | `{prefix}_{reference}_ms` | porder_project_ms, porder_supplier_ms, item_cat_ms |
| Status columns | `{prefix}_status` | proj_status (tinyint: 1=active, 0=inactive) |
| Date columns | `{prefix}_createdate`, `{prefix}_modifydate` | porder_createdate, item_modifydate |
| Audit columns | `{prefix}_createby`, `{prefix}_modifyby` | porder_createby (user ID) |

---

## 4. Feature Documentation

### 4.1 Purchase Order Management

**Description**: End-to-end purchase order lifecycle from creation through receiving, with budget validation, approval workflows, and PDF generation.

**Key Files**:
- Controller: `app/Http/Controllers/Admin/PurchaseOrderController.php`
- Model: `app/Models/PurchaseOrder.php`, `app/Models/PurchaseOrderItem.php`
- Service: `app/Services/PurchaseOrderService.php`
- Views: `resources/views/admin/porder/`

**Data Flow**:
```
1. Create PO
   User selects project → system loads budget info
   User selects supplier → system loads catalog prices
   User adds line items → real-time budget check (AJAX)
   System auto-generates PO number (PO-XXXXXX)

2. Submit PO
   System checks if approval workflow exists for amount
   If yes → routes to first approver (ApprovalService)
   If no → auto-approved

3. Budget Integration
   On PO creation → budget_committed_amount += PO total
   Budget threshold check: 75% warning, 90% critical
   Over-budget → requires override permission

4. Receive Against PO
   Staff creates receive order against PO
   Updates porder_delivery_status:
     0 = Not Received
     1 = Fully Received
     2 = Partially Received
   Updates budget_spent_amount with actual received amounts

5. PO Change Orders
   If PO amount changes after creation → auto-creates PCO
   PCO goes through approval if amount exceeds threshold
```

**AJAX Endpoints**:
| Endpoint | Purpose |
|----------|---------|
| `admin.porder.itemlist` | Load items filtered by project |
| `admin.porder.cataloglist` | Load supplier catalog with prices |
| `admin.porder.projectaddress` | Auto-fill delivery address |
| `admin.porder.check-budget` | Real-time budget availability check |

**PO Statuses**:
| Status | Value | Description |
|--------|-------|-------------|
| Active | 1 | PO is active and can be received against |
| Inactive | 0 | PO is deactivated/closed |

**Delivery Statuses**:
| Status | Value | Description |
|--------|-------|-------------|
| Not Received | 0 | No goods received yet |
| Fully Received | 1 | All items received in full |
| Partially Received | 2 | Some items/quantities received |

---

### 4.2 Budget Management & Job Costing

**Description**: Project-based budget tracking by cost code with real-time utilization monitoring, threshold alerts, and change order management.

**Key Files**:
- Controllers: `BudgetController.php`, `ProjectBudgetController.php`, `BudgetReportController.php`
- Model: `app/Models/Budget.php`, `app/Models/BudgetChangeOrder.php`
- Service: `app/Services/BudgetService.php`
- Views: `resources/views/admin/budget/`, `resources/views/admin/budgets/`

**Budget Setup Flow**:
```
1. Assign Cost Codes to Project
   Admin selects project → assigns relevant cost codes
   Creates project_cost_codes records

2. Set Budget Amounts
   For each assigned cost code:
   - Enter original budget amount
   - Set fiscal year
   - Optional: Set warning/critical thresholds

3. Budget Tracking (Automatic)
   Committed = Sum of PO line items for this project/cost code
   Actual = Sum of received goods for this project/cost code
   Remaining = Revised Budget - Committed - Actual
   Utilization = (Committed + Actual) / Revised * 100
```

**Budget Fields**:
| Field | Description |
|-------|-------------|
| Original Amount | Initial budget allocation |
| Revised Amount | Current budget (original + approved BCOs) |
| Committed | Total of active PO amounts |
| Actual/Spent | Total of received goods amounts |
| Remaining | Revised - Committed - Actual |
| Utilization % | (Committed + Actual) / Revised * 100 |

**Threshold Monitoring**:
| Level | Trigger | Action |
|-------|---------|--------|
| Warning | 75% utilization | Email notification to project stakeholders |
| Critical | 90% utilization | Urgent email notification |
| Over Budget | >100% utilization | Requires override permission for new POs |

---

### 4.3 RFQ (Request for Quote)

**Description**: Formal quotation process for soliciting competitive bids from multiple suppliers before creating purchase orders.

**Key Files**:
- Controller: `app/Http/Controllers/Admin/RfqController.php`
- Models: `app/Models/Rfq.php`, `RfqItem.php`, `RfqSupplier.php`, `RfqQuote.php`
- Service: `app/Services/PurchaseOrderService.php` (RFQ methods)
- Views: `resources/views/admin/rfq/`

**RFQ Workflow**:
```
1. Create RFQ
   Select project, add items with quantities and target prices
   Assign multiple suppliers to quote

2. Send RFQ
   System emails RFQ to selected suppliers
   Status changes: Draft → Sent

3. Receive Quotes
   Suppliers respond with quoted prices, lead times, validity dates
   Admin records quotes in system (or supplier submits via portal)

4. Compare Quotes
   Side-by-side comparison matrix
   Items as rows, suppliers as columns
   Highlights best price per item

5. Convert to PO
   Select winning supplier
   Auto-creates PO with quoted prices
   Updates supplier catalog with new prices
   RFQ status → Converted
```

**RFQ Statuses**:
| Status | Value | Description |
|--------|-------|-------------|
| Draft | 1 | Being created, editable |
| Sent | 2 | Sent to suppliers |
| Received | 3 | Quotes received |
| Converted | 4 | Converted to PO |
| Cancelled | 5 | Cancelled |

---

### 4.4 Change Order Management

**Description**: Formal process for modifying budgets and purchase orders after initial approval, with full audit trail and approval workflows.

**Key Files**:
- Controllers: `BudgetChangeOrderController.php`, `PoChangeOrderController.php`
- Models: `BudgetChangeOrder.php`, `PoChangeOrder.php`
- Service: `PoChangeOrderService.php`
- Views: `resources/views/admin/budget-change-orders/`, `resources/views/admin/po-change-orders/`

#### Budget Change Orders (BCO)

**Types**:
| Type | Description |
|------|-------------|
| Increase | Add funds to a cost code budget |
| Decrease | Reduce funds from a cost code budget |
| Transfer | Move funds between cost codes |

**BCO Lifecycle**:
```
Draft → Submit for Approval → Pending Approval → Approved/Rejected
                                                       │
                                                       ▼
                                              Budget Updated
                                              (revised_amount adjusted)
```

**Auto-numbering**: BCO-{YEAR}-{0001} (e.g., BCO-2026-0001)

#### PO Change Orders (PCO)

**Types**:
| Type | Description |
|------|-------------|
| Amount Change | Modify PO total amount |
| Item Change | Add/remove/modify line items |
| Date Change | Update delivery date |
| Other | Other modifications |

**PCO Lifecycle**:
```
Draft → Submit → Budget Validation → Approval → PO Updated
                      │                              │
                      ▼                              ▼
               Budget check for          porder_change_orders_total
               amount increases          updated on PO record
```

**Auto-numbering**: PCO-{YEAR}-{0001}

**CO Statuses** (both BCO and PCO):
| Status | Description |
|--------|-------------|
| draft | Being created, editable |
| pending_approval | Submitted, awaiting approval |
| approved | Approved and applied |
| rejected | Rejected (can be edited and resubmitted) |
| cancelled | Cancelled |

---

### 4.5 Approval Workflows

**Description**: Configurable multi-level approval routing based on entity type, amount thresholds, and user roles.

**Key Files**:
- Controllers: `ApprovalController.php`, `ApprovalWorkflowController.php`
- Models: `ApprovalWorkflow.php`, `ApprovalRequest.php`, `ProjectRole.php`
- Service: `app/Services/ApprovalService.php`
- Views: `resources/views/admin/approvals/`, `resources/views/admin/approval-workflows/`

**Supported Entity Types**:
| Type | Description |
|------|-------------|
| budget | Budget creation/modification |
| budget_co | Budget change orders |
| po | Purchase orders |
| po_co | PO change orders |
| receive_order | Goods receipts |

**Workflow Configuration**:
```
Workflow Name: "PO Approval - High Value"
  Type: po
  Level: 1
  Amount Range: $10,000 - $50,000
  Approvers: [user_id: 5, user_id: 8]
  Logic: any (any one approver can approve)

Workflow Name: "PO Approval - Executive"
  Type: po
  Level: 2
  Amount Range: $50,000 - NULL (unlimited)
  Approvers: Roles [Director, Executive]
  Logic: all (all must approve)
```

**Approval Flow**:
```
Entity Created → ApprovalService.submitForApproval()
    │
    ├─ Find matching workflow(s) by type + amount
    ├─ If no workflow → auto-approve
    ├─ If workflow found:
    │   ├─ Create ApprovalRequest
    │   ├─ Set current_level = 1
    │   ├─ Resolve approvers (by user ID or project role)
    │   └─ Notify first approver
    │
    ▼
Approver Action:
    ├─ Approve:
    │   ├─ If more levels needed → advance to next level
    │   └─ If final level → execute approval (update entity status)
    │
    └─ Reject:
        └─ Update entity status to rejected
            (can be edited and resubmitted)
```

**Project Roles** (used for role-based approval routing):
| Role | Default Permissions |
|------|-------------------|
| Staff | Create PO |
| PM (Project Manager) | Create PO, Approve PO |
| Manager | Approve PO, Create Budget CO |
| Director | All approvals, higher limits |
| Finance | Budget override, accounting |
| Executive | Unlimited approval authority |
| Admin | Full system access |

---

### 4.6 Receiving & Backorder Tracking

**Description**: Goods receipt processing against purchase orders with partial delivery support, backorder tracking, and notification system.

**Key Files**:
- Controller: `ReceiveOrderController.php`
- Models: `ReceiveOrder.php`, `ReceiveOrderItem.php`
- Services: `PurchaseOrderService.php`, `BackorderService.php`
- Views: `resources/views/admin/receive/`

**Receiving Flow**:
```
1. Select PO to Receive
   List of POs with status != Fully Received
   Shows ordered qty, received qty, remaining qty per item

2. Create Receive Order
   Enter slip number and date
   Enter received quantities (cannot exceed remaining)
   System validates quantities

3. Process Receipt
   ├─ Create receive_order_master + receive_order_details records
   ├─ Update PO delivery status:
   │   - All items fully received → status = 1 (Fully Received)
   │   - Some items remaining → status = 2 (Partially Received)
   ├─ Update budget actual amounts
   └─ Check for backorders → send notifications if applicable

4. Backorder Management
   System calculates: backordered_qty = ordered - received
   Updates backorder_status on PO items:
     0 = None
     1 = Backordered
     2 = Fulfilled
   Sends notifications to admins and supplier portal users
```

**Reports**:
- **Receiving Summary**: All receipt activity with PO/project context
- **Back Order Report**: Outstanding items grouped by supplier
- Uses SQL views: `vw_receiving_summary`, `vw_back_order_report`

---

### 4.7 Item & Supplier Catalog

**Description**: Master item catalog with supplier-specific pricing, price history tracking, and price comparison tools.

**Key Files**:
- Controllers: `ItemController.php`, `SupplierController.php`
- Models: `Item.php`, `Supplier.php`, `SupplierCatalog.php`, `ItemPriceHistory.php`
- Service: `ItemPricingService.php`
- Views: `resources/views/admin/item/`, `resources/views/admin/supplier/`

**Item Master Features**:
- CRUD with category, cost code, and UOM assignments
- CSV import/export for bulk operations
- Price comparison across suppliers
- Price history tracking with date ranges
- Pricing summary report (aggregated from `vw_item_pricing_summary`)

**Supplier Catalog Features**:
- Supplier-specific SKUs and pricing
- Time-based pricing with effective dates
- Project-specific pricing overrides
- Automatic price update when RFQ converted to PO

**Price Tracking**:
```
ItemPriceHistory records:
  item_id, supplier_id, old_price, new_price,
  effective_date, changed_by, notes
```

---

### 4.8 Project Management

**Description**: Project master data management with hierarchical cost codes and role-based access control.

**Key Files**:
- Controllers: `ProjectController.php`, `ProjectRoleController.php`, `CostCodeController.php`
- Models: `Project.php`, `CostCode.php`, `ProjectRole.php`, `ProjectCostCode.php`
- Views: `resources/views/admin/project/`, `resources/views/admin/costcodes/`

**Cost Code Hierarchy** (3 levels):
```
Level 1: Parent Code (e.g., 01 - General Requirements)
  └─ Level 2: Category (e.g., 01.100 - Temporary Facilities)
       └─ Level 3: Subcategory (e.g., 01.100.001 - Temporary Offices)
```

**Project Roles**:
- Assign users to projects with specific roles
- Each role has configurable permissions (can_create_po, can_approve_po, etc.)
- Approval limits per role (e.g., PM can approve up to $25,000)
- Used by ApprovalService for routing decisions

---

### 4.9 Procore Integration

**Description**: Two-way synchronization with Procore construction management platform for projects, vendors, cost codes, budgets, and commitments.

**Key Files**:
- Controller: `ProcoreController.php`
- Service: `app/Services/ProcoreService.php` (26KB)
- Views: `resources/views/admin/procore/`

**Sync Capabilities**:

| Direction | Entity | Source → Destination |
|-----------|--------|---------------------|
| Inbound | Projects | Procore → POApp |
| Inbound | Vendors/Suppliers | Procore → POApp |
| Inbound | Cost Codes | Procore → POApp |
| Inbound | Budgets | Procore → POApp |
| Inbound | Commitments | Procore → POApp |
| Outbound | Purchase Orders | POApp → Procore |

**Mapping System**:
- `procore_project_mapping`: Links local projects to Procore projects
- `procore_cost_code_mapping`: Links local cost codes to Procore cost codes
- Unmapped entities displayed for manual mapping

**Webhook Support**:
- Endpoint: `POST /procore/webhook` (no auth required)
- Events: project.update, budget.update, commitment.update
- Auto-triggers relevant sync on webhook receipt

**Sync Logging**:
- All sync operations logged to `procore_sync_log`
- Tracks: type, direction, status, message, request/response data

---

### 4.10 Accounting Integration

**Description**: Integration with accounting systems (Sage 300, QuickBooks Online) for automated export/import of purchase orders, vendors, and items.

**Key Files**:
- Controller: `IntegrationController.php`
- Services: `Integrations/BaseIntegrationService.php`, `SageIntegrationService.php`, `QuickBooksIntegrationService.php`
- Views: `resources/views/admin/integrations/`

**Supported Systems**:
| System | Auth Method | Status |
|--------|-----------|--------|
| Sage 300 | OAuth 2.0 | Backend complete |
| QuickBooks Online | OAuth 2.0 | Backend complete |

**Auto-Sync Options**:
- Purchase Orders: Export POs to accounting system
- Vendors: Import/export vendor records
- Items: Sync item master data
- Invoices: Export invoice data

**Field Mapping**:
- Configurable field mappings between POApp and external system
- Transformation rules: uppercase, date_format, currency conversion
- Stored in `integration_field_mappings` table

**Sync Logging**:
- Every sync operation logged with status, record counts, errors
- Duration tracking for performance monitoring

---

### 4.11 Supplier Portal

**Description**: Self-service portal for suppliers to manage their catalog, respond to RFQs, and view purchase order information.

**Key Files**:
- Controllers: `Supplier/AuthController.php`, `Supplier/DashboardController.php`, `Supplier/RfqController.php`, `Supplier/ItemPricingController.php`
- Views: `resources/views/supplier/`

**Features**:
| Feature | Description |
|---------|-------------|
| Self-Registration | Suppliers create their own accounts |
| Email Verification | Required before accessing portal |
| Dashboard | Overview of RFQs, catalog items, expiring prices |
| RFQ Response | View and submit quotes for RFQs |
| Catalog Management | Manage item pricing and SKUs |
| Price Updates | Update prices with effective dates |

**Authentication**: Uses separate `supplier` guard with `supplier_users` table.

---

### 4.12 Reporting & Analytics

**Description**: Comprehensive reporting suite for budget analysis, spending trends, change order tracking, and variance analysis.

**Key Files**:
- Controllers: `BudgetReportController.php`, `ChangeOrderReportController.php`, `CommittedActualReportController.php`
- Service: `ReportExportService.php`
- Views: `resources/views/admin/reports/`

**Available Reports**:

| Report | Description | Export Formats |
|--------|-------------|---------------|
| Budget vs Actual | Compare budgeted vs actual spending by cost code | Excel, PDF |
| Cost Code Drilldown | All POs and receipts for a specific cost code | CSV |
| Variance Analysis | Top variances, utilization distribution chart | - |
| Committed vs Actual Timeline | Monthly spending trend with charts | CSV |
| Change Orders | Combined BCO and PCO report with filters | CSV |
| Backorder Report | Outstanding deliveries by supplier | - |
| Receiving Summary | Goods receipt activity history | - |

**Budget vs Actual Report Features**:
- Summary cards: Total Budget, Committed, Actual, Remaining
- Utilization status counts: On Track / At Risk / Over Budget
- Overall utilization progress bar
- Detailed table with per-cost-code breakdown
- Drill-down to individual transactions

**Charts** (using Chart.js):
- Monthly Committed vs Actual (bar chart)
- Cumulative Spending Trend (line chart)
- Budget Utilization Distribution (bar chart)

---

### 4.13 Equipment & Checklists

**Description**: Equipment asset tracking and inspection checklist management for construction sites.

**Key Files**:
- Controllers: `EquipmentController.php`, `ChecklistController.php`, `PerformChecklistController.php`
- Views: `resources/views/admin/equipment/`, `resources/views/admin/checklists/`

**Equipment Tracking**:
- Asset details: name, tag, type, category, brand, model, year
- Status tracking: Available, In Use, Maintenance, Retired
- Location and operator assignment
- Usage readings and remaining life calculation
- Linked to supplier for maintenance/purchase tracking

**Checklist System**:
- Create checklists with multiple inspection items
- Assign to equipment and users
- Set frequency: daily, weekly, monthly
- Perform checklists with pass/fail values and notes
- Attachment support for photos/documents
- Completion tracking and history

---

## 5. Information Flow Diagrams

### 5.1 Purchase Order Lifecycle

```
┌──────────┐    ┌───────────┐    ┌───────────┐    ┌──────────┐
│  CREATE   │───▶│  BUDGET   │───▶│ APPROVAL  │───▶│  ACTIVE  │
│   PO      │    │  CHECK    │    │  ROUTING  │    │   PO     │
└──────────┘    └─────┬─────┘    └─────┬─────┘    └────┬─────┘
                      │                │                │
                 Budget OK?       Approved?         ┌───▼───┐
                 ├─ Yes ──▶       ├─ Yes ──▶        │RECEIVE │
                 └─ No:           └─ No:            │ GOODS  │
                   Over-budget      Rejected        └───┬───┘
                   warning          (edit &             │
                   (needs           resubmit)      ┌────▼────┐
                   override)                       │ UPDATE  │
                                                   │ BUDGET  │
                                                   │ ACTUALS │
                                                   └────┬────┘
                                                        │
                                               ┌───────▼───────┐
                                               │ FULLY/PARTIAL │
                                               │   RECEIVED?   │
                                               ├─ Partial: backorder
                                               └─ Full: PO closed
```

### 5.2 Budget Validation Flow

```
┌────────────┐
│ PO Created │
│ Amount: $X │
└──────┬─────┘
       │
       ▼
┌──────────────────┐     ┌──────────────────┐
│ Find Budget for  │────▶│ Budget Exists?   │
│ Project +        │     │                  │
│ Cost Code        │     ├─ No → Warning    │
└──────────────────┘     │   (proceed w/o   │
                         │    tracking)     │
                         ├─ Yes ──▼         │
                         └──────────────────┘
                                │
                    ┌───────────▼───────────┐
                    │ Calculate Utilization  │
                    │ New = (Committed + X)  │
                    │       / Revised * 100  │
                    └───────────┬───────────┘
                                │
              ┌─────────────────┼─────────────────┐
              │                 │                  │
         < 75%            75% - 89%          ≥ 90%
              │                 │                  │
              ▼                 ▼                  ▼
        ┌──────────┐    ┌──────────────┐   ┌──────────────┐
        │  APPROVE │    │   WARNING    │   │  CRITICAL    │
        │  (Green) │    │   (Yellow)   │   │  (Red)       │
        └──────────┘    │  Email sent  │   │  Email sent  │
                        │  to PM       │   │  May need    │
                        └──────────────┘   │  override    │
                                           └──────────────┘
```

### 5.3 Approval Workflow Routing

```
┌──────────────────────────────┐
│  Entity Submitted for        │
│  Approval                    │
│  (PO, BCO, PCO, Budget)      │
│  Amount: $X                  │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│  ApprovalService.submit()    │
│  ┌────────────────────────┐  │
│  │ Find workflows where:  │  │
│  │  type = entity_type    │  │
│  │  min ≤ $X ≤ max        │  │
│  │  is_active = true      │  │
│  │  ORDER BY sort_order   │  │
│  └──────────┬─────────────┘  │
└─────────────┼────────────────┘
              │
     ┌────────┴────────┐
     │                 │
  No Workflow      Workflow Found
     │                 │
     ▼                 ▼
┌─────────┐    ┌──────────────────┐
│  AUTO-  │    │  Create Request   │
│ APPROVE │    │  Level 1 of N     │
└─────────┘    └────────┬─────────┘
                        │
                        ▼
               ┌──────────────────┐
               │ Resolve Approvers │
               │ ┌────────────────┤
               │ │ By User IDs    │ → Direct user list
               │ │ By Roles       │ → Project roles with
               │ │                │   approval_limit ≥ $X
               │ └────────────────┤
               └────────┬─────────┘
                        │
                        ▼
               ┌──────────────────┐
               │ Notify Approver  │
               │ (Email + DB)     │
               └────────┬─────────┘
                        │
            ┌───────────┴───────────┐
            │                       │
         APPROVE                 REJECT
            │                       │
            ▼                       ▼
     ┌─────────────┐         ┌──────────┐
     │ More Levels? │         │  Entity  │
     ├─ Yes: Next   │         │ Rejected │
     │   Level      │         │ (editable│
     ├─ No: Execute │         │  & re-   │
     │   Approval   │         │ submit)  │
     └─────────────┘         └──────────┘
```

### 5.4 RFQ-to-PO Conversion

```
┌─────────┐     ┌─────────┐     ┌──────────┐     ┌──────────┐
│  CREATE  │────▶│  SEND   │────▶│ RECEIVE  │────▶│ COMPARE  │
│  RFQ     │     │  TO     │     │ QUOTES   │     │ QUOTES   │
│          │     │SUPPLIERS│     │          │     │          │
└─────────┘     └─────────┘     └──────────┘     └────┬─────┘
                                                      │
Items:                                          ┌─────▼─────┐
- Widget A x100                                 │  SELECT   │
- Bolt B x500                                   │  WINNER   │
                                                └─────┬─────┘
Suppliers:                                            │
- Acme Corp                                     ┌─────▼─────┐
- Beta Ltd                                      │ CONVERT   │
- Gamma Inc                                     │  TO PO    │
                                                └─────┬─────┘
                                                      │
                                                ┌─────▼──────────┐
                                                │ Auto-Actions:  │
                                                │ • Create PO    │
                                                │ • Update       │
                                                │   supplier     │
                                                │   catalog      │
                                                │ • RFQ status → │
                                                │   Converted    │
                                                └────────────────┘
```

### 5.5 Procore Sync Flow

```
┌──────────────────────────────────────────────┐
│                 PROCORE CLOUD                 │
│  Projects | Vendors | Cost Codes | Budgets   │
└─────────────────┬────────────────────────────┘
                  │
        ┌─────────┴─────────┐
        │                   │
   PULL (Inbound)      PUSH (Outbound)
        │                   │
        ▼                   ▲
┌───────────────┐   ┌───────────────┐
│ ProcoreService│   │ ProcoreService│
│ .syncAll()    │   │ .pushPO()     │
└───────┬───────┘   └───────┬───────┘
        │                   │
        ▼                   │
┌───────────────────────────┴──────┐
│           MAPPING LAYER          │
│  procore_project_mapping         │
│  procore_cost_code_mapping       │
│                                  │
│  Local ID ←→ Procore ID         │
└───────────────┬──────────────────┘
                │
                ▼
┌───────────────────────────────────┐
│          LOCAL DATABASE           │
│  project_master                   │
│  supplier_master                  │
│  cost_code_master                 │
│  budget_master                    │
│  purchase_order_master            │
└───────────────────────────────────┘
                │
                ▼
┌───────────────────────────────────┐
│         SYNC LOGGING              │
│  procore_sync_log                 │
│  - sync_type, sync_direction      │
│  - sync_status, sync_message      │
│  - sync_request_data              │
│  - sync_response_data             │
│  - sync_created_at                │
└───────────────────────────────────┘
```

---

## 6. Use Case Catalog

### Actors

| Actor | Description |
|-------|-------------|
| **Admin** | Company administrator with full access |
| **PM** | Project Manager managing specific projects |
| **Finance** | Financial controller handling budgets and approvals |
| **Staff** | General staff creating POs and receiving goods |
| **Supplier** | External vendor using supplier portal |
| **Super Admin** | System-wide administrator managing tenants |

### Use Cases

#### UC-01: Create Purchase Order
**Actor**: Staff / PM
**Precondition**: Project, supplier, and items exist in system
**Flow**:
1. Navigate to Purchase Orders → Create New
2. Select project (system loads budget info)
3. Select supplier (system loads catalog prices)
4. Add line items with quantities and prices
5. System performs real-time budget check
6. Submit PO for approval (if workflow configured)
7. System generates PO number and saves
**Postcondition**: PO created, budget committed amount updated

#### UC-02: Approve Purchase Order
**Actor**: PM / Finance / Director
**Precondition**: PO submitted for approval, user is assigned approver
**Flow**:
1. Receive notification of pending approval
2. Navigate to Approvals → Dashboard
3. Review PO details, amounts, budget impact
4. Approve or reject with comments
5. If multi-level: routes to next approver
6. If final approval: PO becomes active
**Postcondition**: PO approved/rejected, entity status updated

#### UC-03: Receive Goods Against PO
**Actor**: Staff
**Precondition**: Active PO exists
**Flow**:
1. Navigate to Receive Orders → Create
2. Select PO from list
3. View ordered vs already received quantities
4. Enter received quantities for each item
5. Enter slip number and date
6. Submit receive order
7. System updates PO delivery status and budget actuals
**Postcondition**: Receipt recorded, budget actual updated, backorders calculated

#### UC-04: Create and Process Budget Change Order
**Actor**: PM / Finance
**Precondition**: Budget exists for project/cost code
**Flow**:
1. Navigate to project budget → Change Orders
2. Select type: Increase, Decrease, or Transfer
3. Enter amount and reason
4. Submit for approval
5. Approver reviews and approves
6. System updates budget revised amount
**Postcondition**: Budget revised, change order in audit trail

#### UC-05: RFQ Workflow
**Actor**: PM / Staff
**Precondition**: Items and suppliers exist
**Flow**:
1. Create RFQ with project, items, and target prices
2. Assign suppliers to quote
3. Send RFQ (emails suppliers)
4. Record received quotes
5. Compare quotes side-by-side
6. Select winning supplier
7. Convert to purchase order
**Postcondition**: PO created from best quote, supplier catalog updated

#### UC-06: Supplier Self-Service
**Actor**: Supplier
**Precondition**: Supplier user registered and verified
**Flow**:
1. Login to supplier portal
2. View dashboard (pending RFQs, catalog stats)
3. Respond to RFQ with quoted prices
4. Manage catalog items and pricing
5. View purchase orders issued to them
**Postcondition**: Quotes submitted, catalog updated

#### UC-07: Budget Monitoring and Alerts
**Actor**: System (automated) / PM / Finance
**Precondition**: Budgets configured with thresholds
**Flow**:
1. PO created or goods received
2. System recalculates utilization percentage
3. If > 75%: warning notification sent
4. If > 90%: critical notification sent
5. If > 100%: override required for new POs
6. PM/Finance reviews alerts on dashboard
**Postcondition**: Stakeholders notified, budget status visible

#### UC-08: Procore Sync
**Actor**: Admin
**Precondition**: Procore credentials configured
**Flow**:
1. Navigate to Procore → Dashboard
2. Click Sync All (or individual sync buttons)
3. System pulls projects, vendors, cost codes from Procore
4. System maps entities to local records
5. Push POs back to Procore as commitments
6. View sync log for results/errors
**Postcondition**: Data synchronized between systems

#### UC-09: Generate Budget Report
**Actor**: PM / Finance
**Precondition**: Budgets exist for project
**Flow**:
1. Navigate to Reports → Budget vs Actual
2. Select project
3. View summary cards (total, committed, actual, remaining)
4. Review detailed table by cost code
5. Click cost code for drill-down to individual POs
6. Export to Excel or PDF
**Postcondition**: Report generated for analysis

#### UC-10: Multi-Tenant Company Management
**Actor**: Super Admin
**Precondition**: Logged in as super admin (u_type=1)
**Flow**:
1. Navigate to Companies
2. Create new company (name, subdomain, settings)
3. Assign users to company
4. Switch between companies via dropdown
5. View any company's data in isolation
**Postcondition**: New tenant created, data isolated

#### UC-11: Configure Approval Workflow
**Actor**: Admin
**Precondition**: Users and project roles exist
**Flow**:
1. Navigate to Approval Workflows → Create
2. Set workflow name and entity type (PO, BCO, etc.)
3. Define amount threshold range
4. Choose approver type: specific users or project roles
5. Set approval logic: any (one) or all (unanimous)
6. Activate workflow
**Postcondition**: Workflow active, future submissions auto-routed

#### UC-12: Set Up Project Budget
**Actor**: PM / Finance
**Precondition**: Project and cost codes exist
**Flow**:
1. Navigate to project → Assign Cost Codes
2. Select relevant cost codes for this project
3. Navigate to Budget Setup
4. Enter budget amounts per cost code
5. Set fiscal year and optional thresholds
6. Save budget
**Postcondition**: Project budgets established, tracking begins

#### UC-13: Manage Equipment
**Actor**: Admin / Staff
**Precondition**: Equipment categories defined
**Flow**:
1. Navigate to Equipment
2. Add equipment with asset details
3. Assign location and operator
4. Track status changes (Available → In Use → Maintenance)
5. Monitor usage readings and remaining life
**Postcondition**: Equipment tracked in system

#### UC-14: Perform Inspection Checklist
**Actor**: Staff
**Precondition**: Checklist created and assigned
**Flow**:
1. Navigate to Perform Checklists → Create
2. Select checklist and equipment
3. Go through each checklist item
4. Record pass/fail and notes
5. Attach photos if needed
6. Submit completed checklist
**Postcondition**: Inspection recorded with audit trail

#### UC-15: Import Items from CSV
**Actor**: Admin
**Precondition**: CSV file prepared with item data
**Flow**:
1. Navigate to Items → Import
2. Upload CSV file
3. System validates and imports records
4. View import results (success count, errors)
**Postcondition**: Items added to catalog

#### UC-16: Connect Accounting System
**Actor**: Admin
**Precondition**: Accounting system credentials available
**Flow**:
1. Navigate to Integrations → Create
2. Select integration type (Sage/QuickBooks)
3. Complete OAuth authorization flow
4. Configure auto-sync preferences
5. Set field mappings
6. Test connection
**Postcondition**: Integration active, auto-sync enabled

#### UC-17: PO Template Usage
**Actor**: Staff / PM
**Precondition**: PO template created with items
**Flow**:
1. Navigate to PO Templates
2. Select template
3. Choose project and supplier
4. Adjust quantities as needed
5. Create PO from template
**Postcondition**: PO created with pre-configured items

#### UC-18: Price Comparison
**Actor**: PM / Staff
**Precondition**: Item listed by multiple suppliers
**Flow**:
1. Navigate to item detail page
2. Click "Price Comparison"
3. View all supplier prices sorted by cost
4. Review price history over time
5. Use data to inform purchasing decisions
**Postcondition**: Pricing intelligence available

#### UC-19: Handle PO Change Order
**Actor**: PM / Finance
**Precondition**: Active PO exists
**Flow**:
1. Edit PO amounts → system auto-creates PCO
2. Or: manually create PCO
3. System validates budget for increases
4. Submit PCO for approval
5. Approved → PO totals updated
**Postcondition**: PO modified with audit trail

#### UC-20: Switch Company Context
**Actor**: Super Admin
**Precondition**: Multiple companies exist
**Flow**:
1. Click company name in topbar dropdown
2. Select target company
3. All views and queries now filter to selected company
4. Navigate and manage selected company's data
**Postcondition**: Working in context of selected company

---

## 7. Project Progress Report

### 7.1 Phase History

The POApp was originally built with CodeIgniter and has been migrated to Laravel 9. Development has proceeded in three major phases:

#### Phase 1: Core Features

| Sub-Phase | Feature | Status |
|-----------|---------|--------|
| 1.1 | Laravel Migration (from CodeIgniter) | Complete |
| 1.2 | Supplier Portal (registration, auth, dashboard) | Complete |
| 1.3 | Item Pricing System (time-based, project-specific) | Complete |
| 1.4 | RFQ System (create, send, quote, compare, convert) | Complete |
| 1.5 | Backorder Tracking (auto-calc, notifications) | Complete |
| 1.6 | Checklists & Equipment | Complete |

#### Phase 2: Budget & Integrations

| Sub-Phase | Feature | Status |
|-----------|---------|--------|
| 2.1 | Accounting Integration (Sage, QuickBooks) | 80% - Backend complete, UI functional |
| 2.2 | Budget Management System | 90% - All features working, minor refinements |

#### Phase 3: Multi-Tenancy

| Sub-Phase | Feature | Status |
|-----------|---------|--------|
| 3.1 | Multi-Tenancy Foundation (companies table, migrations) | Complete |
| 3.2 | Middleware & Context Management | Complete |
| 3.3 | Apply Global Scopes (28 models) | Complete |
| 3.4 | Update Controllers with Company Context (70+ queries) | Complete |
| 3.5 | Company Management UI | Complete |
| 3.6 | Data Migration & Testing (21 tests) | Complete |
| 3.7 | Security Audit & Documentation | Complete |

### 7.2 Feature Completion Matrix

| Feature Area | Backend | Frontend | Tests | Overall |
|-------------|---------|----------|-------|---------|
| Authentication (Admin) | 100% | 100% | - | 100% |
| Authentication (Supplier) | 100% | 100% | 100% | 100% |
| Dashboard | 100% | 100% | - | 100% |
| Purchase Orders | 100% | 100% | - | 95% |
| Receive Orders | 100% | 100% | - | 95% |
| Budget Management | 100% | 100% | - | 90% |
| Budget Change Orders | 100% | 100% | - | 95% |
| PO Change Orders | 100% | 100% | - | 95% |
| Approval Workflows | 100% | 100% | - | 95% |
| RFQ Management | 100% | 100% | - | 90% |
| Item Catalog | 100% | 100% | - | 95% |
| Supplier Management | 100% | 100% | - | 95% |
| Project Management | 100% | 100% | - | 95% |
| Cost Codes | 100% | 100% | - | 95% |
| Reporting | 100% | 100% | - | 90% |
| Procore Integration | 100% | 100% | - | 85% |
| Accounting Integration | 100% | 100% | - | 80% |
| Equipment | 100% | 100% | - | 90% |
| Checklists | 100% | 100% | - | 90% |
| Multi-Tenancy | 100% | 100% | 100% | 100% |
| Supplier Portal | 100% | 100% | 100% | 95% |

### 7.3 Code Metrics

| Category | Count | Details |
|----------|-------|---------|
| Models | 49 | 28 with CompanyScope trait |
| Controllers | 41 | 35 Admin + 6 Supplier |
| Services | 11 | 8 core + 3 integration |
| Blade Views | 103+ | Across 25+ directories |
| Route Definitions | 315 | 285 web + 29 supplier + 1 API |
| Middleware | 7 | Custom middleware classes |
| Migrations | 15 | Database migrations |
| Test Files | 7 | Feature tests |
| Test Assertions | 21+ | Multi-tenancy isolation tests |
| Database Tables | 40+ | Legacy + Laravel |
| SQL Views | 4 | Reporting views |
| PHP Packages | 7 | Core dependencies |

### 7.4 Testing Status

| Test Area | Status | Files | Coverage |
|-----------|--------|-------|----------|
| Multi-Tenancy Isolation | Passing | 2 test files | 21 tests |
| Company Management | Passing | 1 test file | Complete |
| Supplier Authentication | Passing | 2 test files | Login, register, password |
| Backorder Tracking | Passing | 1 test file | Core flow |
| Item Pricing | Passing | 1 test file | CRUD + history |
| HTTP Route Verification | Passing | 51/51 routes | All admin GET routes |
| Purchase Orders | Not Written | - | 0% |
| Budget Management | Not Written | - | 0% |
| Approval Workflows | Not Written | - | 0% |
| RFQ Workflow | Not Written | - | 0% |
| Change Orders | Not Written | - | 0% |
| Integration Services | Not Written | - | 0% |
| Reports | Not Written | - | 0% |

### 7.5 Known Issues

| Priority | Issue | Impact | Status |
|----------|-------|--------|--------|
| HIGH | PurchaseOrderItem model may reference wrong table name (`purchase_order_items` vs `purchase_order_details`) | PO line item operations could fail | **RESOLVED** — Model was correct; fixed 6 documentation/SQL script files |
| MEDIUM | 4 misplaced files in `app/Http/Middleware/` directory | Code organization | **RESOLVED** — 4 draft files deleted |
| MEDIUM | 7 misplaced files in `routes/` directory | Code organization | **RESOLVED** — 6 draft files deleted, 1 moved to correct location |
| LOW | 14 blade files use slash notation `layouts/admin` vs dot `layouts.admin` | Inconsistency (both work) | **RESOLVED** — All 14 files converted to dot notation |
| LOW | Some views still reference BS4 component patterns | Visual inconsistencies | **RESOLVED** — All BS4 remnants fixed or CSS compat added |

> **All known issues have been resolved as of February 8, 2026.**

### 7.6 Recent Fixes (February 2026)

The following 21+ issues were identified through automated HTTP route testing and fixed:

**Schema Mismatches Fixed**:
- `proj_company_id` → `company_id` in 4 controllers
- `proj_no` → `proj_number` in 3 controllers + 4 views
- `u_name` → `name` in ProjectRoleController
- `user_master` → `users` table reference
- `aw_type` → `workflow_type` in approval workflows
- `sync_started_at` → `sync_created_at` in Procore sync log
- `local_project_id` → `ppm_local_project_id` in Procore mappings
- `local_cost_code_id` → `pccm_local_cost_code_id` in Procore mappings
- Removed `company_id` filters from DB view queries (views don't have company_id)
- Removed `user_info` references from dashboard (table lacks company_id)
- Removed `supplier_catalog_tab.company_id` filters (column doesn't exist)
- Fixed SQL Server GROUP BY requirements in item pricing summary
- `icat_name` → `category_name` in pricing summary view

**Missing Tables Created**:
- `checklist_master`, `checklist_details`
- `cl_perform_master`, `cl_perform_details`
- `eq_master` (equipment)
- `permission_master`
- `procore_auth`

**View/Layout Fixes**:
- Fixed 17 broken `@extends` references (wrong layout names)
- Removed duplicate `@endsection` from 4 report views
- Created 46 missing blade view files
- Fixed login redirect from `/dashboard` to `/admincontrol/dashboard`
- Fixed RouteServiceProvider HOME constant

### 7.7 Known Issues Resolution (February 8, 2026)

The following additional fixes resolved all remaining known issues:

**Documentation & SQL Script Fixes (HIGH)**:
- Fixed `purchase_order_items` → `purchase_order_details` in 6 files: `apply_missing_schema.sql`, `verify_database.sql`, `run_sqlserver_migrations.php`, `check_phase_tables.php`, `MIGRATION_README.md`, `PHASE_3_4_AUDIT.md`
- Fixed 11 wrong table name mappings in `ARCHITECTURE.md` (projects→project_master, suppliers→supplier_master, items→item_master, etc.)

**Misplaced File Cleanup (MEDIUM)**:
- Deleted 10 misplaced draft files from `app/Http/Middleware/` and `routes/` directories
- Moved `routes/auth.blade.php` to `resources/views/supplier/layouts/auth.blade.php` (supplier auth layout)

**Blade Template Standards (LOW)**:
- Converted 14 files from slash notation (`layouts/auth`, `layouts/dashboard`) to dot notation (`layouts.auth`, `layouts.dashboard`)

**Bootstrap 4 → 5 Compatibility (LOW)**:
- `float-right` → `float-end` in change-orders report (4 instances)
- `mr-2` → `me-2` in budget summary (4 instances)
- `badge-*-lighten` → `bg-*` in dashboard/profile views (3 files)
- `data-toggle` → `data-bs-toggle` in ccsummary view
- Added CSS compatibility classes in admin layout for `text-right`, `text-left`, `no-gutters`, `float-right`, `float-left` (used in 70+ report views)

---

## 8. Version 2 Enhancement Recommendations

### 8.1 Performance & Scalability

#### 8.1.1 Caching Strategy
- **Redis/Memcached Integration**: Cache frequently queried data (project lists, cost codes, UOMs, tax groups)
- **Query Result Caching**: Cache budget summary calculations (invalidate on PO/RO creation)
- **View Fragment Caching**: Cache sidebar navigation and dashboard summary cards
- **Session Store**: Move session storage from file to Redis for faster access

#### 8.1.2 Queue Jobs
- **Email Notifications**: Move all email sending to queue jobs (approval notifications, budget warnings, RFQ emails)
- **Report Generation**: Generate large reports (Excel, PDF) asynchronously
- **Procore Sync**: Run sync operations as background jobs with progress tracking
- **CSV Import**: Process large item imports asynchronously

#### 8.1.3 Database Optimization
- **Query Optimization**: Profile and optimize N+1 queries in list views
- **Eager Loading**: Ensure all relationship loads use `with()` for batch loading
- **Index Review**: Add composite indexes for frequently filtered columns (company_id + status, project_id + cost_code_id)
- **Pagination**: Replace `get()` with `paginate()` where returning large datasets
- **Read Replicas**: Consider SQL Server read replicas for report queries

### 8.2 User Experience

#### 8.2.1 Modern Frontend
- **Laravel Livewire or Inertia.js**: Replace full page reloads with reactive components
- **Alpine.js**: Add lightweight interactivity without jQuery dependency
- **Real-time Updates**: WebSocket notifications for approval requests and budget alerts
- **Toastr Notifications**: Replace Bootstrap alerts with toast notifications
- **Dark Mode**: Add dark mode toggle with CSS variables

#### 8.2.2 Mobile Responsiveness
- **Mobile-First Redesign**: Optimize all views for tablet and mobile use
- **Progressive Web App (PWA)**: Enable offline access for field workers
- **Touch-Friendly Controls**: Larger buttons and swipe gestures for mobile
- **Responsive Data Tables**: Collapsible columns on small screens

#### 8.2.3 Improved Interactions
- **Drag-and-Drop**: Reorder PO line items, cost code hierarchy, checklist items
- **Autocomplete Search**: Global search across POs, projects, suppliers, items
- **Inline Editing**: Edit cost codes, tax groups, UOMs without separate form pages
- **Bulk Operations**: Select multiple POs for bulk approval, status change, or export
- **Keyboard Shortcuts**: Quick navigation (Ctrl+N for new PO, etc.)

#### 8.2.4 Dashboard Improvements
- **Customizable Widgets**: Let users arrange dashboard cards
- **Quick Actions**: One-click shortcuts for common tasks
- **Recent Activity Feed**: Show recent POs, approvals, receipts
- **KPI Tracking**: Key performance indicators with trend arrows

### 8.3 Security Enhancements

#### 8.3.1 Authentication
- **Two-Factor Authentication (2FA)**: TOTP-based 2FA for admin users
- **SSO Integration**: SAML/OpenID Connect for enterprise customers
- **Password Policies**: Enforce complexity, expiration, and history
- **Login Audit**: Track all login attempts with IP, user agent, geolocation

#### 8.3.2 Authorization
- **Fine-Grained Permissions**: Replace u_type integer with RBAC system (Spatie/laravel-permission)
- **Resource-Level Policies**: Laravel Policies for every model (not just Company)
- **API Scoping**: Granular Sanctum token abilities
- **Data Export Controls**: Restrict export access by role

#### 8.3.3 Audit & Compliance
- **Comprehensive Audit Log**: Log all data changes (create, update, delete) with before/after values
- **Immutable Audit Trail**: Separate audit database that can't be modified
- **IP Whitelisting**: Restrict admin access to approved IP ranges
- **Session Management**: View and revoke active sessions
- **GDPR Compliance**: Data export and deletion capabilities

### 8.4 Feature Enhancements

#### 8.4.1 Advanced Reporting
- **Custom Report Builder**: Let users define their own report filters and columns
- **Scheduled Reports**: Automatic email delivery of reports on schedule
- **Dashboard Analytics**: Interactive charts with drill-down (replace static Chart.js)
- **Trend Analysis**: Spending trends over time, supplier performance scores
- **Forecasting**: Budget burn rate projection based on historical spending

#### 8.4.2 Document Management
- **File Attachments**: Attach documents to POs, RFQs, change orders
- **Document Templates**: Customizable PO and report PDF templates
- **Digital Signatures**: Electronic signature capture for approvals
- **OCR Integration**: Auto-extract data from scanned invoices/receipts

#### 8.4.3 Communication
- **In-App Messaging**: Comments/notes on POs, approvals, and change orders
- **Email Integration**: Send POs directly to suppliers via email
- **Notification Center**: Centralized notification management with read/unread status
- **SMS Alerts**: Critical budget alerts via SMS

#### 8.4.4 Inventory Management
- **Warehouse Tracking**: Track item quantities by location
- **Minimum Stock Alerts**: Auto-generate POs when stock falls below threshold
- **Barcode/QR Scanning**: Mobile scanning for receiving and inventory counts
- **Material Requisitions**: Internal request workflow before PO creation

#### 8.4.5 Contract Management
- **Subcontractor Contracts**: Manage subcontractor agreements with milestones
- **Insurance Tracking**: Track supplier insurance certificates and expiration
- **Compliance Documents**: Manage required compliance documentation
- **Retention Tracking**: Handle retention amounts on construction contracts

### 8.5 Integration Enhancements

#### 8.5.1 Expanded Accounting
- **Xero Integration**: Add Xero as third accounting system option
- **Multi-Currency**: Support international projects with currency conversion
- **Tax Automation**: Auto-calculate taxes based on jurisdiction
- **Invoice Matching**: Three-way match (PO, Receipt, Invoice)

#### 8.5.2 Procore Enhancements
- **Complete Webhook Coverage**: Handle all Procore webhook events
- **Bidirectional Budget Sync**: Push budget changes back to Procore
- **Change Event Sync**: Sync change events and change orders
- **Drawing Integration**: Link PO items to Procore drawings

#### 8.5.3 New Integrations
- **REST API**: Full REST API for third-party integrations
- **Zapier/Make Integration**: Connect to 1000+ apps via webhooks
- **ERP Systems**: SAP, Oracle, Microsoft Dynamics connectors
- **Banking**: Direct bank feed for payment reconciliation
- **Project Management**: Integration with Microsoft Project, Primavera P6

#### 8.5.4 Mobile API
- **Native Mobile App**: iOS/Android apps for field workers
- **Offline Capability**: Queue operations when offline, sync when connected
- **Push Notifications**: Real-time alerts on mobile devices
- **Camera Integration**: Photo capture for receiving and inspections

### 8.6 DevOps & Infrastructure

#### 8.6.1 CI/CD Pipeline
- **Automated Testing**: Run test suite on every commit (GitHub Actions / Azure DevOps)
- **Code Quality**: PHPStan/Psalm static analysis, Laravel Pint formatting
- **Automated Deployment**: Zero-downtime deployments with rollback capability
- **Environment Parity**: Docker containers for consistent dev/staging/production environments

#### 8.6.2 Testing Infrastructure
- **Unit Tests**: 80%+ code coverage target for services and models
- **Integration Tests**: End-to-end tests for critical workflows (PO creation, budget validation)
- **Browser Tests**: Laravel Dusk tests for JavaScript-heavy pages (PO create form)
- **Load Testing**: Simulate concurrent users to identify bottlenecks
- **API Tests**: Automated API endpoint testing with Postman/Newman

#### 8.6.3 Monitoring & Observability
- **Application Monitoring**: Laravel Telescope in staging, Sentry/Bugsnag in production
- **Performance Monitoring**: New Relic or Datadog APM
- **Database Monitoring**: SQL Server query performance tracking
- **Uptime Monitoring**: External health checks and status page
- **Log Aggregation**: Centralized logging with ELK stack or Papertrail

#### 8.6.4 Infrastructure
- **Docker Containerization**: Dockerize application for consistent deployments
- **Cloud Migration**: Consider Azure (for SQL Server) or AWS
- **CDN for Assets**: Move static assets to CDN for faster loading
- **Database Backups**: Automated backup and point-in-time recovery
- **Horizontal Scaling**: Load balancer with multiple application servers

### 8.7 Architecture Improvements

#### 8.7.1 Framework Upgrade
- **Laravel 11**: Upgrade from Laravel 9 to 11 for latest features and security patches
- **PHP 8.3+**: Leverage typed properties, enums, fibers, and performance improvements
- **SQL Server Driver**: Upgrade to latest sqlsrv driver for better performance

#### 8.7.2 Design Patterns
- **Event-Driven Architecture**: Use Laravel Events for cross-cutting concerns (audit logging, notifications, integrations)
- **Repository Pattern**: Abstract database queries behind repository interfaces
- **DTOs (Data Transfer Objects)**: Replace array passing between services with typed DTOs
- **Action Classes**: Single-responsibility action classes instead of fat controllers
- **Value Objects**: Typed money/currency, PO numbers, and status enums

#### 8.7.3 API-First Approach
- **API Layer**: Build comprehensive REST API alongside web interface
- **API Documentation**: OpenAPI/Swagger specification auto-generated
- **API Versioning**: Versioned API endpoints (v1, v2) for backward compatibility
- **GraphQL**: Consider GraphQL for complex reporting queries

#### 8.7.4 Module Architecture
- **Laravel Modules**: Organize code into domain modules (PO, Budget, RFQ, etc.)
- **Shared Kernel**: Common traits, services, and utilities as shared library
- **Plugin System**: Allow third-party modules to extend functionality
- **Feature Flags**: Toggle features per tenant without code deployment

### 8.8 Priority Recommendations

| Priority | Recommendation | Impact | Effort |
|----------|---------------|--------|--------|
| **P0** | Laravel 11 upgrade + PHP 8.3 | Security, performance | Medium |
| **P0** | Comprehensive test suite | Reliability | High |
| **P1** | Redis caching + queue jobs | Performance | Medium |
| **P1** | 2FA + audit logging | Security | Medium |
| **P1** | File attachments on POs | User productivity | Low |
| **P1** | CI/CD pipeline | Developer productivity | Medium |
| **P2** | REST API | Integration capabilities | High |
| **P2** | Livewire/Inertia frontend | User experience | High |
| **P2** | Mobile responsive redesign | Field accessibility | High |
| **P2** | Custom report builder | Business intelligence | Medium |
| **P3** | Native mobile app | Field operations | Very High |
| **P3** | Inventory management | Feature expansion | High |
| **P3** | ERP connectors | Enterprise integration | Very High |
| **P3** | Microservices architecture | Scalability | Very High |

---

## Appendix A: File Reference

### Controllers (`app/Http/Controllers/Admin/`)

| Controller | Feature |
|-----------|---------|
| AdminDashboardController | Main dashboard |
| ApprovalController | Approval inbox & processing |
| ApprovalWorkflowController | Workflow configuration |
| BackorderController | Backorder tracking |
| BudgetController | Budget CRUD |
| BudgetReportController | Budget reports |
| ChangeOrderReportController | Change order reports |
| ChecklistController | Checklist management |
| CommittedActualReportController | Spending reports |
| CompanyController | Company CRUD (super admin) |
| CostCodeController | Cost code management |
| EquipmentController | Equipment tracking |
| IntegrationController | Accounting integrations |
| ItemController | Item catalog |
| ItemPricingController | Item pricing |
| PoChangeOrderController | PO change orders |
| PoTemplateController | PO templates |
| ProcoreController | Procore integration |
| ProjectController | Project management |
| ProjectBudgetController | Project budget setup |
| ProjectRoleController | Project roles |
| PurchaseOrderController | Purchase orders |
| ReceiveOrderController | Receiving |
| RfqController | RFQ management |
| SupplierController | Supplier management |
| TenantManagementController | Tenant admin |

### Services (`app/Services/`)

| Service | Responsibility |
|---------|---------------|
| ApprovalService | Multi-level approval routing |
| BackorderService | Backorder calculation & notification |
| BudgetService | Budget validation, thresholds, change orders |
| ItemPricingService | Time-based pricing management |
| PoChangeOrderService | PO change order processing |
| ProcoreService | Procore API integration (26KB) |
| PurchaseOrderService | PO creation, RFQ, receiving (24KB) |
| ReportExportService | CSV/Excel/PDF export |
| Integrations/BaseIntegrationService | Abstract base for accounting |
| Integrations/SageIntegrationService | Sage 300 integration (15KB) |
| Integrations/QuickBooksIntegrationService | QuickBooks integration |

### Models (`app/Models/`)

| Model | Table | PK |
|-------|-------|-----|
| User | users | id |
| Company | companies | id |
| Project | project_master | proj_id |
| Supplier | supplier_master | sup_id |
| PurchaseOrder | purchase_order_master | porder_id |
| PurchaseOrderItem | purchase_order_details | po_detail_id |
| ReceiveOrder | receive_order_master | rorder_id |
| ReceiveOrderItem | receive_order_details | ro_detail_id |
| Item | item_master | item_id |
| ItemCategory | item_category_tab | icat_id |
| UnitOfMeasure | unit_of_measure_tab | uom_id |
| TaxGroup | taxgroup_master | taxgroup_id |
| CostCode | cost_code_master | cc_id |
| Budget | budget_master | budget_id |
| BudgetChangeOrder | budget_change_orders | bco_id |
| PoChangeOrder | po_change_orders | poco_id |
| ApprovalWorkflow | approval_workflows | workflow_id |
| ApprovalRequest | approval_requests | request_id |
| ProjectRole | project_roles | role_id |
| Rfq | rfq_master | rfq_id |
| SupplierUser | supplier_users | id |
| SupplierCatalog | supplier_catalog_tab | supcat_id |
| AccountingIntegration | accounting_integrations | id |
| ProcoreSyncLog | procore_sync_log | sync_id |
| Equipment | eq_master | equip_id |

---

*End of Document*
