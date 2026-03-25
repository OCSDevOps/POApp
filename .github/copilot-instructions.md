# POApp AI Coding Instructions

## Architecture Overview

This is a **Laravel 9** enterprise purchase order management system migrated from CodeIgniter. Manages purchase orders, receive orders, suppliers, projects, budgets, change orders, approvals, and integrates with Procore for project management.

**Key Directory**: All application code is in `html/` (the Laravel root). The root-level folders are archives/documentation.

### Multi-Tenancy: ✅ PRODUCTION READY (Phase 3 Complete - Jan 2026)
**Architecture**: Single-database, shared-schema with discriminator column pattern.
- **Isolation Method**: `company_id` column on 28+ models + automatic global scopes
- **Tenant Context**: Session-based via `SetTenantContext` middleware (sets on auth)
- **Super Admin Access**: Can switch companies via `session(['company_id' => $id])`
- **Security Status**: 70+ raw queries secured, 21 automated tests passing

**THE GOLDEN RULE**: Every query MUST filter by company_id. Use `CompanyScope` trait on all tenant models.
```php
// ✅ CORRECT - Eloquent auto-scopes
$pos = PurchaseOrder::all(); // Only current company

// ❌ WRONG - Raw query without filter
DB::table('purchase_order_master')->get(); // DATA LEAK!

// ✅ CORRECT - Raw query with filter
DB::table('purchase_order_master')
  ->where('company_id', session('company_id'))
  ->get();
```

### Core Domain Models
**Tenant-Scoped (28 models with CompanyScope trait):**
- **Purchase Orders** (`PurchaseOrder`, `PurchaseOrderItem`) - Core PO lifecycle management
- **Receive Orders** (`ReceiveOrder`, `ReceiveOrderItem`) - Goods receipt tracking
- **Projects** & **Suppliers** (`Project`, `ProjectRole`, `Supplier`, `SupplierUser`)
- **Items** (`Item`, `ItemCategory`, `ItemPackage`, `ItemPricing`) - Catalog with rental types
- **Budgets** (`Budget`, `BudgetChangeOrder`, `CostCode`, `ProjectCostCode`) - Financial tracking
- **Change Orders** (`PoChangeOrder`) - PO modification workflow
- **Approvals** (`ApprovalWorkflow`, `ApprovalRequest`) - Multi-level approval system
- **Checklists** (`Checklist`, `ChecklistPerformance`) - Quality control workflows
- **RFQ System** (`Rfq`, `RfqItem`, `RfqSupplier`, `RfqQuote`) - Quote management
- **Integrations** (`AccountingIntegration`, `IntegrationSyncLog`) - ERP sync

**Non-Scoped (System-wide):**
- `Company` - The tenant entity itself
- `User` - Has `company_id`, scoped but can be super admin (u_type=1)

### Data Layer Conventions
- **Legacy table names**: Models use explicit `$table` and `$primaryKey` (e.g., `purchase_order_master`, `porder_id`)
- **Timestamps**: Most models disable timestamps (`public $timestamps = false`) and use custom `_created_at`/`_modified_at` fields
- **Status fields**: Use integer codes (1=active, 0=inactive) instead of boolean
- **Naming**: Snake_case with prefixes (e.g., `porder_`, `sup_`, `proj_`, `item_`, `budget_`)
- **Multi-tenancy**: All tenant models use `CompanyScope` trait - automatically adds `company_id` filter + auto-injection on create

## Critical Developer Workflows

### Setup & Build
```bash
cd html
composer install
npm install && npm run build  # Vite for assets
php artisan key:generate
php artisan migrate
php artisan db:seed --class=CompanySeeder  # Creates test companies + assigns existing data
```

### Testing & Debugging
- **Automated Tests**: 21+ PHPUnit tests passing - BUILD TESTS AS YOU BUILD FEATURES
  - Run: `cd html && php artisan test` or `vendor/bin/phpunit`
  - Company management: `php artisan test --filter CompanyManagementTest`
  - Multi-tenancy isolation: `php artisan test --filter MultiTenancyIsolationTest`
  - CLI testing tool: `php artisan test:multi-tenancy --verbose`
- **Manual UI Testing**: Required for all features before production
  - Test CRUD via web interface with multiple companies
  - Verify permission templates restrict access correctly
  - Check Procore sync logs for integration errors
  - Follow `html/PHASE_3_6_TESTING_GUIDE.md` checklist
- **Logging**: Check `html/storage/logs/laravel.log`
- **Cache clear**: `php artisan optimize:clear` (fixes namespace/config issues)
- **Debug mode**: Set `APP_DEBUG=true` in `.env` (development only)

### Database Operations
- **Migrations**: Located in `html/database/migrations/`
- **Legacy DB**: Direct `DB::table()` queries common - **ALWAYS** add `->where('company_id', session('company_id'))` filter
- **Permission checks**: Controllers check `permission_master` table with `session('pt_id')`
- **Check schema**: `php artisan db:show` or `php html/check_tables.php`

### Multi-Tenancy Verification
```bash
# Seed test companies (if not done)
php artisan db:seed --class=CompanySeeder

# Run isolation tests
php artisan test --filter MultiTenancy

# CLI testing (simulates company switching)
php artisan test:multi-tenancy
```

### Version Control & Workflow
- **Git Strategy**: Commit after every significant task completion
  - Use descriptive commit messages: `feat: Add budget validation to PO service`
  - Branch naming: `feature/`, `bugfix/`, `refactor/`
  - Example workflow:
    ```bash
    git add .
    git commit -m "feat: Implement multi-tenant scope for PurchaseOrder model"
    git push origin feature/multi-tenancy
    ```
- **Development-First**: Focus on development environment before production
  - Test thoroughly in dev before deploying
  - Production deployment happens AFTER complete testing cycle

## Project-Specific Patterns

### Service Layer
Business logic lives in `app/Services/`:
- `PurchaseOrderService` (705 lines) - PO creation, budget validation, backorder handling, RFQ management
- `BudgetService` (462 lines) - Budget setup, change orders, threshold validation, job cost tracking
- `ApprovalService` (300+ lines) - Role-based approval routing, multi-level workflows
- `ProcoreService` - External API integration (OAuth, sync jobs, cost code imports)
- `BaseIntegrationService` - Abstract base for accounting system integrations (Sage, QuickBooks)

Example service usage:
```php
use App\Services\PurchaseOrderService;

$poService = app(PurchaseOrderService::class);
$po = $poService->createPurchaseOrder($data, $items);
```

### Controller Structure
Admin controllers in `app/Http/Controllers/Admin/` follow a pattern:
1. Permission checks via `permission_master` table lookup
2. Query with eager loading: `PurchaseOrder::with(['project', 'supplier'])`
3. Use Eloquent scopes: `->byProject($id)->byStatus($status)`
4. **CRITICAL**: Raw queries MUST filter by company_id for tenant isolation
5. Return Blade views from `resources/views/admin/`

### Multi-Tenancy Patterns
**Adding CompanyScope to New Models:**
```php
use App\Traits\CompanyScope;

class NewModel extends Model {
    use HasFactory, CompanyScope;  // Adds global scope automatically
    
    protected $fillable = [..., 'company_id'];
    // No need for booted() method - trait handles it
}
```

**Bypassing Scope (Super Admin Only):**
```php
// Get data from specific company
$data = Model::forCompany($companyId)->get();

// Get data from all companies (reports/analytics)
$allData = Model::allCompanies()->get();
```

**Session Context:**
- Middleware sets `session('company_id')` on login
- Controllers can access via `session('company_id')` or `$request->tenant_company_id`
- Views have `$current_company_id` and `$current_company_name` shared automatically

### Authentication & Permissions
- Custom auth (not Laravel Breeze/Jetstream) via `AuthController`
- Permission templates (`permission_master` table) control CRUD access
- Session-based permissions: Check `session('pt_id')` and `u_type` (1=admin)

### Procore Integration
- API credentials stored in `procore_auth` table (company-scoped)
- Sync logs tracked in `ProcoreSyncLog` model
- Two-way sync: Import projects/cost codes, export POs as commitments
- Service handles OAuth token refresh automatically

### Budget & Approval System
**Budget Tracking (Phase 2.2 Complete):**
- Hierarchical cost codes (XX-XX-XX format) with rollup
- Budget change orders (BCO) with auto-numbering (BCO-2026-0001)
- PO change orders (PCO) with budget validation
- Threshold notifications at 75% warning, 90% critical
- Job cost tracking: Original + Change Orders + Committed + Actual

**Approval Workflows:**
- Role-based (project-specific roles: PM, Manager, Director, Finance, Executive)
- User-based assignments with amount thresholds
- Multi-level approval chains with override tracking
- Email & database notifications for pending approvals

## Code Style & Conventions

### Models
- Always define `$table` and `$primaryKey` explicitly
- Use `$fillable` arrays (mass assignment protection)
- Add Eloquent scopes for common filters (e.g., `scopeActive`, `scopeByProject`)
- Define relationships with explicit foreign keys: `belongsTo(Project::class, 'porder_project_ms', 'proj_id')`

### Validation
- Use inline validation in controllers: `$request->validate([...])`
- Financial fields: Store as DECIMAL, validate as numeric
- Date formats: `Y-m-d` for storage, format in views

### Database Transactions
Wrap multi-model operations:
```php
DB::beginTransaction();
try {
    // Create PO, items, update budget
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    Log::error('PO creation failed', ['error' => $e->getMessage()]);
}
```

## Integration Points

### External Dependencies
- **Procore API**: REST v1.0 - Company, Project, Cost Code, Commitment sync
- **Vite**: Frontend bundler for `resources/css/app.css` and `resources/js/app.js`
- **Blade Templates**: Server-rendered views in `resources/views/`

### Key Configuration Files
- `html/config/database.php` - DB connection (check `DB_CONNECTION=mysql`)
- `html/.env` - Procore credentials, app settings
- `html/routes/web.php` - All admin routes under `Admin\` namespace

## Migration Notes

This app was migrated from CodeIgniter. Legacy patterns remain:
- `legacy_archive/codeigniter/` contains old codebase (reference only, do not modify)
- **Migration Complete**: All active code is Laravel - no CodeIgniter in main `html/` directory
- Some tables lack migrations (created manually pre-Laravel)
- Direct SQL queries with `DB::table()` instead of Eloquent where models are missing
- See `html/MIGRATION_README.md` for detailed migration mapping

## Environment Configuration

### Development (Current Focus)
- `APP_ENV=local` in `.env`
- `APP_DEBUG=true` for error visibility
- Queue connection: `sync` (immediate processing)
- Cache driver: `file` or `array` for testing
- Database: MySQL local instance

### Production (Post-Testing)
- Will be configured after complete development testing
- Queue workers via Redis/database
- Cache optimization with Redis
- Error logging without debug exposure

## When Working on Features

1. **Adding new PO features**: Extend `PurchaseOrderService`, not controllers
2. **New models**: Follow existing naming (explicit table/primary key, disable timestamps if using custom fields)
3. **API integrations**: Add methods to `ProcoreService` or create new service classes
4. **UI changes**: Update Blade templates in `resources/views/admin/`
5. **Permissions**: Always check `permission_master` for non-admin users

## Common Pitfalls

- **Timestamps**: Don't use `created_at`/`updated_at` - models use `_created_at`/`_modified_at`
- **Primary keys**: Never assume `id` - always check model's `$primaryKey`
- **Multi-tenancy**: CRITICAL - Always filter by company_id in raw queries (`DB::table()`)
- **Budget constraints**: Feature controlled by `BudgetService::validateBudgetConstraints()`
- **CompanyScope bypass**: Only use `withoutGlobalScope('company')` for super admin cross-tenant queries
- **Session context**: If `session('company_id')` is null, user cannot access tenant data (except super admin)
