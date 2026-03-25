# Multi-Tenancy Architecture

## Overview

POApp implements a **Shared Database, Discriminator Column** multi-tenancy model. All tenant data resides in a single database, with data isolation enforced through a `company_id` discriminator column on all tenant-scoped tables.

## Architecture Pattern

```
┌─────────────────────────────────────────────────────────────┐
│                      SQL Server Database                     │
│                         (porder_db)                          │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐         │
│  │  Company A  │  │  Company B  │  │  Company C  │         │
│  │  company_id=1│  │  company_id=2│  │  company_id=3│        │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘         │
│         │                │                │                │
│  ┌──────▼────────────────▼────────────────▼──────┐         │
│  │           Tenant-Scoped Tables                │         │
│  │  • users          • purchase_orders           │         │
│  │  • projects       • receive_orders            │         │
│  │  • suppliers      • budgets                   │         │
│  │  • items          • cost_codes                │         │
│  │  • ...and 20+ more tables                    │         │
│  └──────────────────────────────────────────────┘         │
└─────────────────────────────────────────────────────────────┘
```

## Key Components

### 1. Company Model

The `Company` model represents a tenant in the system:

```php
// app/Models/Company.php
class Company extends Model {
    protected $table = 'companies';
    protected $fillable = ['name', 'subdomain', 'status', 'settings'];
    
    // Relationships to all tenant data
    public function users() { return $this->hasMany(User::class); }
    public function projects() { return $this->hasMany(Project::class); }
    // ... etc
}
```

### 2. CompanyScope (Global Scope)

All tenant-scoped models use the `CompanyScope` global scope to automatically filter queries by the current company:

```php
// app/Models/Scopes/CompanyScope.php
class CompanyScope implements Scope {
    public function apply(Builder $builder, Model $model) {
        if (session()->has('company_id')) {
            $builder->where($model->getTable() . '.company_id', session('company_id'));
        }
    }
}
```

### 3. SetTenantContext Middleware

Establishes company context for each request:

```php
// app/Http/Middleware/SetTenantContext.php
class SetTenantContext {
    public function handle($request, $next) {
        if (auth()->check() && !session()->has('company_id')) {
            session(['company_id' => auth()->user()->company_id]);
        }
        return $next($request);
    }
}
```

Registered in `app/Http/Kernel.php`:
```php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \App\Http\Middleware\SetTenantContext::class,
    ],
];
```

## Tenant Isolation Enforcement

### Automatic (via Global Scope)

All Eloquent queries automatically filter by company:

```php
// Returns ONLY current company's purchase orders
$purchaseOrders = PurchaseOrder::all();

// Scoped by company automatically
$projects = Project::where('status', 1)->get();
```

### Manual (for Raw Queries)

When using `DB::table()`, manually apply company filter:

```php
$companyId = Session::get('company_id');

$results = DB::table('purchase_order_master')
    ->where('company_id', $companyId)
    ->get();
```

### Bypassing Scope (Super Admin Only)

For cross-tenant operations (e.g., super admin reports):

```php
// Get data from all companies
$allOrders = PurchaseOrder::withoutGlobalScope('company')->get();

// Get data from specific company
$companyOrders = PurchaseOrder::forCompany($companyId)->get();
```

## Tenant-Scoped Tables

The following tables have `company_id` column and are tenant-scoped:

| Table | Description |
|-------|-------------|
| `users` | User accounts |
| `project_master` | Construction projects |
| `supplier_master` | Vendors/suppliers |
| `purchase_order_master` | Purchase orders |
| `receive_order_master` | Receipts/receiving |
| `item_master` | Items/products |
| `budget_master` | Project budgets |
| `cost_code_master` | Cost codes |
| `purchase_order_details` | PO line items |
| `receive_order_items` | RO line items |
| `budget_change_orders` | Budget modifications |
| `po_change_orders` | PO modifications |
| `approval_workflows` | Approval rules |
| `approval_requests` | Approval instances |
| `project_roles` | Project assignments |
| `project_cost_codes` | Project-cost code links |
| `accounting_integrations` | ERP integrations |
| `integration_sync_logs` | Sync history |
| `rfqs` | RFQ requests |
| `checklists` | Checklist templates |
| `equipment` | Equipment inventory |

## Security Considerations

### Data Leakage Prevention

1. **Global Scopes**: All models automatically filter by company
2. **Authorization Checks**: Controllers verify company ownership
3. **Session Validation**: Company context validated on each request
4. **Foreign Key Constraints**: Database-level referential integrity

### Common Vulnerabilities Prevented

```php
// Attack: User tries to access another company's PO by ID
// URL: /admin/porder/123 (belongs to Company B, User is from Company A)

// Prevention 1: Global Scope
$purchaseOrder = PurchaseOrder::find(123); 
// Returns null (not found for Company A)

// Prevention 2: Authorization Check
public function show($id) {
    $purchaseOrder = PurchaseOrder::findOrFail($id);
    
    abort_unless(
        $purchaseOrder->company_id === session('company_id'),
        403,
        'Unauthorized access'
    );
    
    return view('admin.porder.show', compact('purchaseOrder'));
}
```

## Company Management

### Creating a New Tenant

Super admins can create new companies via the Tenant Management UI:

1. Navigate to **Admin → Tenants**
2. Click **Add New Company**
3. Fill company details
4. Create admin user for the company
5. System automatically creates company with isolated data

### Company Switcher

Super admins can switch between companies:

```php
// app/Http/Controllers/Admin/TenantManagementController.php
public function switch($id) {
    $company = Company::findOrFail($id);
    
    session(['company_id' => $company->id]);
    session(['company_name' => $company->name]);
    
    return redirect()->route('admin.dashboard');
}
```

## Migration Strategy

### For Existing Data

Run the migration seeder to assign all existing data to a default company:

```bash
php artisan db:seed --class=MigrateToDefaultCompany
```

This:
1. Creates a "Default Company" if none exists
2. Updates all records with null `company_id` to the default company
3. Sets session company_id for the current user

### For New Data

When new models are created, the `company_id` is automatically injected:

```php
// Model boot method (via CompanyScope trait)
static::creating(function ($model) {
    if (!$model->company_id && session()->has('company_id')) {
        $model->company_id = session('company_id');
    }
});
```

## Testing Multi-Tenancy

### Manual Test Checklist

1. **Data Isolation**
   - [ ] Login as Company A user → Verify only Company A data visible
   - [ ] Login as Company B user → Verify only Company B data visible
   - [ ] Reports show correct company data only

2. **Company Switcher**
   - [ ] Super admin can switch between companies
   - [ ] Data updates correctly after switch
   - [ ] Session persists company context

3. **Security**
   - [ ] User cannot access another company's data via URL manipulation
   - [ ] API requests respect company scope
   - [ ] Exports contain only company data

4. **Data Creation**
   - [ ] New POs are assigned to correct company
   - [ ] New projects are assigned to correct company
   - [ ] Relationships maintain company integrity

## Performance Considerations

### Database Indexes

Ensure `company_id` columns are indexed for performance:

```sql
CREATE INDEX idx_company_id ON purchase_order_master(company_id);
CREATE INDEX idx_company_project ON project_master(company_id, proj_id);
```

### Query Optimization

The global scope adds a `WHERE company_id = ?` clause to all queries. This is efficient with proper indexing.

## Troubleshooting

### Common Issues

1. **"No company context" Error**
   - Check `SetTenantContext` middleware is registered
   - Verify user has `company_id` set
   - Check session is persisted

2. **Empty Data After Migration**
   - Run `MigrateToDefaultCompany` seeder
   - Check `company_id` was properly assigned
   - Verify user belongs to correct company

3. **Cross-Tenant Data Visible**
   - Ensure model has `CompanyScope` applied
   - Check `booted()` method is not commented out
   - Verify no `withoutGlobalScope` bypasses

## Future Enhancements

- **Subdomain Routing**: Route tenants via subdomain (`acme.poapp.com`)
- **Plan Limits**: Enforce subscription limits per company
- **Data Export**: Self-service data export for tenant offboarding
- **Custom Branding**: Per-company logos and colors

---

**Last Updated**: 2026-01-30  
**Phase**: 3 (Complete)
