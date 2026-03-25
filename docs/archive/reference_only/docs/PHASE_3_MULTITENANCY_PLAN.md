# Phase 3: Multi-Tenancy Implementation Plan

## 🎯 Objective
Transform POApp from single-tenant to **true multi-tenant SaaS** with company-based data isolation. Zero data leakage between tenants.

## 📋 Current Status
- **Phase 2.2**: COMPLETE (Budget management with reporting)
- **Multi-Tenancy**: NOT IMPLEMENTED (MVP uses projects as logical groupings)
- **Security Risk**: No company-level isolation - all users can potentially access all data

## 🏗️ Architecture Overview

### Tenancy Model: **Shared Database, Discriminator Column**
- **Single Database**: `porder_db` (existing SQL Server)
- **Discriminator**: `company_id` column on all tenant-scoped tables
- **Global Scopes**: Eloquent automatically filters by session company
- **Middleware**: `SetTenantContext` establishes company from session

### Why This Approach?
✅ Cost-effective (no separate databases per tenant)  
✅ Simple migrations (existing data preserved)  
✅ Easy cross-tenant reporting (super admin)  
✅ Proven pattern for B2B SaaS  

❌ Requires careful scope enforcement (risk of data leakage)  
❌ Cannot guarantee physical isolation  

---

## 📊 Implementation Phases

### Phase 3.1: Multi-Tenancy Foundation (2 hours)
**Goal**: Create core multi-tenancy infrastructure

**Tasks:**
1. Create `companies` table migration
   - `company_id` (PK, auto-increment)
   - `company_name` (varchar 255, unique)
   - `company_code` (varchar 50, unique)
   - `status` (tinyint: 1=active, 0=inactive)
   - `settings` (text/json: timezone, currency, etc.)
   - `subscription_tier` (varchar 50: free, pro, enterprise)
   - `subscription_expires` (datetime, nullable)
   - `created_at`, `updated_at`, `created_by`, `updated_by`

2. Create migration to add `company_id` to all tenant-scoped tables:
   - `users` (company_id, foreign key)
   - `project_master` (company_id)
   - `supplier_master` (company_id)
   - `purchase_order_master` (company_id)
   - `receive_order_master` (company_id)
   - `budget_master` (company_id)
   - `cost_code_master` (company_id)
   - `item_master` (company_id)
   - `purchase_order_details` (company_id)
   - `receive_order_details` (company_id)
   - `project_cost_codes` (company_id)
   - `budget_change_orders` (company_id)
   - `po_change_orders` (company_id)
   - `approval_workflows` (company_id)
   - `approval_requests` (company_id)
   - `project_roles` (company_id)
   - `accounting_integrations` (company_id)
   - `integration_sync_logs` (company_id)
   - And ~10 more tables

3. Create `Company` model with relationships:
   ```php
   class Company extends Model {
       protected $table = 'companies';
       protected $primaryKey = 'company_id';
       protected $fillable = ['company_name', 'company_code', 'status', 'settings', ...];
       
       // Relationships
       public function users() { return $this->hasMany(User::class, 'company_id'); }
       public function projects() { return $this->hasMany(Project::class, 'company_id'); }
       public function purchaseOrders() { return $this->hasMany(PurchaseOrder::class, 'company_id'); }
       // ... etc for all tenant-scoped models
   }
   ```

4. Create `CompanyScope` trait:
   ```php
   trait CompanyScope {
       protected static function bootCompanyScope() {
           // Auto-scope all queries by session company
           static::addGlobalScope('company', function (Builder $builder) {
               if (session()->has('company_id')) {
                   $builder->where($builder->getModel()->getTable() . '.company_id', session('company_id'));
               }
           });
           
           // Auto-inject company_id on create
           static::creating(function ($model) {
               if (! $model->company_id && session()->has('company_id')) {
                   $model->company_id = session('company_id');
               }
           });
       }
   }
   ```

**Deliverables:**
- ✅ `2026_01_30_100000_create_companies_table.php`
- ✅ `2026_01_30_100001_add_company_id_to_all_tables.php`
- ✅ `app/Models/Company.php`
- ✅ `app/Traits/CompanyScope.php`

---

### Phase 3.2: Middleware & Context Management (1 hour)
**Goal**: Establish company context for every request

**Tasks:**
1. Create `SetTenantContext` middleware:
   ```php
   class SetTenantContext {
       public function handle($request, $next) {
           // Get company from session (set during login)
           if (!session()->has('company_id')) {
               // Fallback: Get user's company
               if (auth()->check() && auth()->user()->company_id) {
                   session(['company_id' => auth()->user()->company_id]);
               } else {
                   abort(403, 'No company context');
               }
           }
           
           // Store in request for easy access
           $request->merge(['company_id' => session('company_id')]);
           
           return $next($request);
       }
   }
   ```

2. Register middleware in `app/Http/Kernel.php`:
   - Add to `$middlewareGroups['web']` array (after `Authenticate`)

3. Update `AuthController` login:
   - Set `session(['company_id' => $user->company_id])` after successful login
   - Add company name to session for display

4. Create `CompanySeeder`:
   - Seed 3 sample companies: "Acme Construction", "BuildRight LLC", "Premier Projects"
   - Assign existing users to company 1 (default migration company)

**Deliverables:**
- ✅ `app/Http/Middleware/SetTenantContext.php`
- ✅ Updated `app/Http/Kernel.php`
- ✅ Updated `app/Http/Controllers/AuthController.php`
- ✅ `database/seeders/CompanySeeder.php`

---

### Phase 3.3: Apply Global Scopes to All Models (3 hours)
**Goal**: Enforce tenant isolation at the ORM level

**Tasks:**
1. Add `use CompanyScope;` to **all tenant-scoped models**:
   - `User.php`
   - `Project.php`
   - `Supplier.php`
   - `PurchaseOrder.php`
   - `ReceiveOrder.php`
   - `Item.php`
   - `Budget.php`
   - `CostCode.php`
   - `BudgetChangeOrder.php`
   - `PoChangeOrder.php`
   - `ApprovalWorkflow.php`
   - `ApprovalRequest.php`
   - `ProjectRole.php`
   - `ProjectCostCode.php`
   - `AccountingIntegration.php`
   - `IntegrationSyncLog.php`
   - `PurchaseOrderItem.php` (purchase_order_details)
   - `ReceiveOrderItem.php` (receive_order_details)
   - `Checklist.php`
   - `ChecklistPerformance.php`
   - ~25 models total

2. Update model `$fillable` arrays to include `'company_id'`

3. Test queries:
   ```php
   // Before: Returns ALL purchase orders
   PurchaseOrder::all();
   
   // After: Returns ONLY current company's purchase orders
   PurchaseOrder::all();
   
   // To bypass scope (super admin only):
   PurchaseOrder::withoutGlobalScope('company')->get();
   ```

**Deliverables:**
- ✅ 25+ models updated with `CompanyScope` trait
- ✅ All `$fillable` arrays include `company_id`

---

### Phase 3.4: Update Controllers with Company Context (4 hours)
**Goal**: Ensure all controllers respect company boundaries

**Tasks:**
1. **Audit all controllers** for hardcoded queries:
   - Search for `DB::table()` without `where('company_id', ...)` 
   - Convert to Eloquent where possible (scopes auto-apply)
   - Add manual `where('company_id', session('company_id'))` to direct queries

2. **Update store() methods** to explicitly inject `company_id`:
   ```php
   // Before
   PurchaseOrder::create($request->validated());
   
   // After (explicit injection, though scope handles it)
   PurchaseOrder::create(array_merge($request->validated(), [
       'company_id' => session('company_id')
   ]));
   ```

3. **Authorization checks** in controllers:
   - Verify user belongs to same company as resource
   - Example: `abort_unless($po->company_id === session('company_id'), 403);`

4. **Update reports** to respect company scope:
   - `BudgetReportController` already uses Eloquent (scopes auto-apply)
   - Check raw SQL in any custom reports

**Affected Controllers:**
- PurchaseOrderController
- ReceiveOrderController
- SupplierController
- ProjectController
- BudgetController
- BudgetChangeOrderController
- PoChangeOrderController
- ItemController
- ApprovalController
- CostCodeController
- ProjectRoleController
- ApprovalWorkflowController
- IntegrationController
- BudgetReportController
- ~15 controllers total

**Deliverables:**
- ✅ All controllers audited and updated
- ✅ Authorization checks added
- ✅ Raw SQL queries scoped

---

### Phase 3.5: Company Management UI (2 hours)
**Goal**: Allow super admins to manage companies

**Tasks:**
1. Create `CompanyController`:
   ```php
   // CRUD operations
   public function index() // List all companies
   public function create() // New company form
   public function store() // Save company
   public function edit($id) // Edit company
   public function update($id) // Update company
   public function destroy($id) // Soft delete
   
   // Company switcher (super admin only)
   public function switch($id) {
       session(['company_id' => $id]);
       return redirect()->route('admin.dashboard');
   }
   ```

2. Create Blade views:
   - `resources/views/admin/companies/index.blade.php` - Company list
   - `resources/views/admin/companies/form.blade.php` - Create/edit form
   - `resources/views/admin/companies/show.blade.php` - Company details

3. Add company switcher to admin header:
   ```blade
   @if(auth()->user()->u_type == 1) {{-- Super admin --}}
       <div class="company-switcher">
           <select onchange="window.location='/admin/companies/switch/' + this.value">
               @foreach($companies as $company)
                   <option value="{{ $company->company_id }}" 
                       {{ session('company_id') == $company->company_id ? 'selected' : '' }}>
                       {{ $company->company_name }}
                   </option>
               @endforeach
           </select>
       </div>
   @endif
   ```

4. Update dashboard to show company name:
   ```blade
   <h1>Dashboard - {{ session('company_name', 'No Company') }}</h1>
   ```

5. Add routes:
   ```php
   Route::prefix('admin/companies')->name('admin.companies.')->middleware(['auth'])->group(function () {
       Route::get('/', [CompanyController::class, 'index'])->name('index');
       Route::get('/create', [CompanyController::class, 'create'])->name('create');
       Route::post('/', [CompanyController::class, 'store'])->name('store');
       Route::get('/{id}/edit', [CompanyController::class, 'edit'])->name('edit');
       Route::put('/{id}', [CompanyController::class, 'update'])->name('update');
       Route::delete('/{id}', [CompanyController::class, 'destroy'])->name('destroy');
       Route::get('/switch/{id}', [CompanyController::class, 'switch'])->name('switch'); // Super admin
   });
   ```

**Deliverables:**
- ✅ `app/Http/Controllers/Admin/CompanyController.php`
- ✅ 3 Blade views for company management
- ✅ Company switcher in header
- ✅ Updated dashboard
- ✅ 7 new routes

---

### Phase 3.6: Data Migration & Testing (3 hours)
**Goal**: Migrate existing data to default company, test isolation

**Tasks:**
1. Create `MigrateToDefaultCompany` seeder:
   ```php
   // Create default company
   $defaultCompany = Company::create([
       'company_name' => 'Default Company',
       'company_code' => 'DEFAULT',
       'status' => 1,
   ]);
   
   // Assign ALL existing data to default company
   DB::table('users')->update(['company_id' => $defaultCompany->company_id]);
   DB::table('project_master')->update(['company_id' => $defaultCompany->company_id]);
   // ... repeat for all tenant-scoped tables
   ```

2. Create 2-3 test companies with sample data:
   ```php
   $company1 = Company::create(['company_name' => 'Test Construction Co']);
   $company2 = Company::create(['company_name' => 'BuildRight LLC']);
   
   // Create test users for each company
   // Create test projects, POs, budgets for each company
   ```

3. **Test data isolation**:
   - Login as company1 user → Verify ONLY company1 data visible
   - Login as company2 user → Verify ONLY company2 data visible
   - Check reports → No cross-tenant data
   - Check exports → No cross-tenant data
   - Test super admin company switcher

4. **Test edge cases**:
   - User tries to access another company's PO directly (by ID) → Should get 404/403
   - User tries to create PO linked to another company's project → Should fail validation
   - Reports should handle empty datasets gracefully

**Deliverables:**
- ✅ `database/seeders/MigrateToDefaultCompany.php`
- ✅ Test data for 3 companies
- ✅ Manual test checklist completed
- ✅ Zero data leakage confirmed

---

### Phase 3.7: Security Audit & Documentation (2 hours)
**Goal**: Ensure production-ready security, document architecture

**Tasks:**
1. **Query Audit**:
   - Search codebase for `DB::table()` → Verify company_id filtering
   - Search for `->all()` → Verify scopes apply
   - Search for `->get()` → Check for bypass attempts
   - Review all raw SQL queries

2. **Authorization Audit**:
   - Verify all controllers check user's company matches resource's company
   - Add Policy classes if needed (e.g., `PurchaseOrderPolicy`)
   - Test user cannot manipulate URL params to access other company data

3. **OWASP Checks** (per SAAS_ARCHITECT_MASTER.md):
   - ✅ Input validation: All FormRequests validate company_id
   - ✅ Output escaping: Blade templates escape by default
   - ✅ SQL Injection: Use Eloquent/prepared statements (no raw user input)
   - ✅ Access control: Middleware + scopes + authorization
   - ✅ PII encryption: Sensitive fields use encrypted casting

4. **Create documentation**:
   - `MULTITENANCY_ARCHITECTURE.md` - How it works, developer guide
   - Update `FEATURE_STATUS.md` - Add Phase 3 completion
   - Update `.github/copilot-instructions.md` - Remove "NOT IMPLEMENTED" notes

5. **Performance testing**:
   - Test queries with 1000+ POs across 10 companies
   - Verify indexes exist on `company_id` columns
   - Add composite indexes where needed: `(company_id, porder_id)`

**Deliverables:**
- ✅ Security audit completed
- ✅ `MULTITENANCY_ARCHITECTURE.md` created
- ✅ `FEATURE_STATUS.md` updated
- ✅ `.github/copilot-instructions.md` updated
- ✅ Database indexes optimized

---

## 📊 Estimated Timeline

| Phase | Description | Hours | Status |
|-------|-------------|-------|--------|
| 3.1 | Multi-Tenancy Foundation | 2 | Not Started |
| 3.2 | Middleware & Context | 1 | Not Started |
| 3.3 | Global Scopes (25+ models) | 3 | Not Started |
| 3.4 | Controllers (15+ controllers) | 4 | Not Started |
| 3.5 | Company Management UI | 2 | Not Started |
| 3.6 | Data Migration & Testing | 3 | Not Started |
| 3.7 | Security Audit & Docs | 2 | Not Started |
| **TOTAL** | **Complete Multi-Tenancy** | **17 hours** | **0%** |

---

## 🚀 Deployment Strategy

### Development (Current)
1. Run migrations to add `companies` table and `company_id` columns
2. Seed default company and migrate existing data
3. Test with 2-3 sample companies
4. Verify zero data leakage

### Staging (Pre-Production)
1. Backup database before migration
2. Run migration to add company_id columns
3. Run seeder to assign all data to default company
4. Test all features with multiple companies
5. Performance test with realistic data volumes

### Production
1. Schedule maintenance window (30 min)
2. Backup database
3. Run migrations
4. Run data migration seeder
5. Verify application starts correctly
6. Test login + basic operations
7. Monitor logs for scope-related errors

---

## ⚠️ Risks & Mitigations

### Risk 1: Data Leakage
**Description**: User accesses another company's data  
**Likelihood**: High (without careful implementation)  
**Impact**: Critical (GDPR violation, trust loss)  
**Mitigation**:
- Global scopes on ALL tenant-scoped models
- Authorization checks in controllers
- Comprehensive testing with multiple companies
- Security audit before production

### Risk 2: Performance Degradation
**Description**: Adding company_id to queries slows down app  
**Likelihood**: Medium  
**Impact**: Medium  
**Mitigation**:
- Add indexes on `company_id` columns
- Use composite indexes: `(company_id, primary_key)`
- Test with realistic data volumes
- Monitor query performance

### Risk 3: Existing Data Migration
**Description**: Assigning company_id breaks relationships  
**Likelihood**: Low (single company currently)  
**Impact**: High  
**Mitigation**:
- Assign ALL existing data to default company (preserves relationships)
- Test thoroughly in development
- Backup production before migration

### Risk 4: Developer Mistakes
**Description**: New features forget to add CompanyScope  
**Likelihood**: Medium  
**Impact**: High  
**Mitigation**:
- Update coding instructions (copilot-instructions.md)
- Code review checklist: "Does new model have CompanyScope?"
- Automated tests for scope enforcement

---

## ✅ Success Criteria

Phase 3 is complete when:
1. ✅ All tables have `company_id` column with foreign key
2. ✅ All 25+ models use `CompanyScope` trait
3. ✅ Middleware establishes company context on every request
4. ✅ Company management UI allows CRUD operations
5. ✅ Super admin can switch between companies
6. ✅ Test data exists for 3+ companies
7. ✅ Zero data leakage confirmed (user A cannot see user B's data)
8. ✅ All controllers respect company boundaries
9. ✅ Reports and exports are company-scoped
10. ✅ Security audit passed
11. ✅ Documentation complete
12. ✅ Existing data migrated to default company

---

## 🎯 Next Steps After Phase 3

Once multi-tenancy is complete:
1. **Phase 4**: Subscription management (Stripe integration, billing, plan limits)
2. **Phase 5**: Advanced features (dashboards, analytics, mobile app)
3. **Phase 6**: Automated test suite (unit, feature, integration tests)
4. **Phase 7**: Production deployment (AWS, CI/CD, monitoring)

---

**Ready to begin Phase 3.1?** Let's build the multi-tenancy foundation!
