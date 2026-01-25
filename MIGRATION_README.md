# POAPP Migration Guide: CodeIgniter to Laravel

## Overview

This document outlines the migration from CodeIgniter to Laravel for the POAPP (Purchase Order Application) project.

## Issues Fixed

### 1. PHP Syntax Errors in `curlget.php`
- **Line 24**: Added missing semicolon after `$accessToken=$responseData['access_token']`
- **Line 74**: Changed `json_decode($response3,yes)` to `json_decode($response3, true)`

### 2. Framework Conflict Resolution
The project had both CodeIgniter and Laravel frameworks coexisting. The migration consolidates everything into Laravel.

## New Laravel Structure

### Models Created
- [`app/Models/PurchaseOrder.php`](app/Models/PurchaseOrder.php) - Purchase order management
- [`app/Models/Project.php`](app/Models/Project.php) - Project management
- [`app/Models/Supplier.php`](app/Models/Supplier.php) - Supplier management
- [`app/Models/Item.php`](app/Models/Item.php) - Item/product management
- [`app/Models/ItemCategory.php`](app/Models/ItemCategory.php) - Item categories
- [`app/Models/CostCode.php`](app/Models/CostCode.php) - Cost codes
- [`app/Models/ReceiveOrder.php`](app/Models/ReceiveOrder.php) - Receive order management

### Controllers Created
- [`app/Http/Controllers/Admin/AdminDashboardController.php`](app/Http/Controllers/Admin/AdminDashboardController.php) - Dashboard with statistics
- [`app/Http/Controllers/Admin/PurchaseOrderController.php`](app/Http/Controllers/Admin/PurchaseOrderController.php) - Full CRUD for purchase orders
- [`app/Http/Controllers/Admin/ProjectController.php`](app/Http/Controllers/Admin/ProjectController.php) - Project management
- [`app/Http/Controllers/Admin/SupplierController.php`](app/Http/Controllers/Admin/SupplierController.php) - Supplier management
- [`app/Http/Controllers/Admin/CostCodeController.php`](app/Http/Controllers/Admin/CostCodeController.php) - Cost code master data (CRUD with lock/unlock via status)
- [`app/Http/Controllers/Admin/UnitOfMeasureController.php`](app/Http/Controllers/Admin/UnitOfMeasureController.php) - Unit of measure master data
- [`app/Http/Controllers/Admin/TaxGroupController.php`](app/Http/Controllers/Admin/TaxGroupController.php) - Tax group master data
- [`app/Http/Controllers/Admin/EquipmentController.php`](app/Http/Controllers/Admin/EquipmentController.php) - Equipment master data

### Services Created
- [`app/Services/ProcoreService.php`](app/Services/ProcoreService.php) - Procore API integration

### Views Created
- [`resources/views/layouts/admin.blade.php`](resources/views/layouts/admin.blade.php) - Admin layout template
- [`resources/views/admin/main.blade.php`](resources/views/admin/main.blade.php) - Dashboard view
- [`resources/views/admin/porder/porder_list_view.blade.php`](resources/views/admin/porder/porder_list_view.blade.php) - Purchase orders list
- [`resources/views/admin/costcodes/index.blade.php`](resources/views/admin/costcodes/index.blade.php) - Cost code management UI
- [`resources/views/admin/uom/index.blade.php`](resources/views/admin/uom/index.blade.php) - Unit of measure management UI
- [`resources/views/admin/taxgroups/index.blade.php`](resources/views/admin/taxgroups/index.blade.php) - Tax group management UI
- [`resources/views/admin/equipment/index.blade.php`](resources/views/admin/equipment/index.blade.php) - Equipment management UI

### Routes Updated
- [`routes/web.php`](routes/web.php) - All admin routes with proper naming conventions

## CodeIgniter Files to Remove

After verifying the Laravel migration is working correctly, the following CodeIgniter files/directories can be safely removed:

### Directories to Remove
```
application/          # CodeIgniter application folder
system/              # CodeIgniter system folder
db/                  # Old database folder (if not needed)
style/               # Old styles (if migrated to public/assets)
__MACOSX/            # macOS artifacts
PHPMailer/           # Use Laravel Mail instead
```

### Files to Remove
```
index.php            # CodeIgniter entry point (root level)
contributing.md      # CodeIgniter contribution guide
readme.rst           # CodeIgniter readme
license.txt          # CodeIgniter license
curlget.php          # Test file (functionality moved to ProcoreService)
error_log            # Old error log from 2014
master.zip           # Archive file
pmanager.zip         # Archive file
```

## Migration Mapping

### CodeIgniter → Laravel Controller Mapping

| CodeIgniter Controller | Laravel Controller |
|----------------------|-------------------|
| `Admin_access` | `AuthController` |
| `admincontrol/Dashboard` | `Admin/AdminDashboardController` |
| `admincontrol/Porder` | `Admin/PurchaseOrderController` |
| `admincontrol/Projects` | `Admin/ProjectController` |
| `admincontrol/Suppliers` | `Admin/SupplierController` |

### CodeIgniter → Laravel Model Mapping

| CodeIgniter Model | Laravel Model |
|------------------|---------------|
| `Admin_m` | `User` + various service methods |
| `Main_m` | Split into specific models |
| `Procore_Model` | `ProcoreService` |

### Route Mapping

| CodeIgniter Route | Laravel Route |
|------------------|---------------|
| `/admin_access` | `/` (login) |
| `/admincontrol/dashboard` | `/admincontrol/dashboard` |
| `/admincontrol/porder/all_purchase_order_list` | `/admincontrol/porder` |
| `/admincontrol/porder/add_new_purchase_order` | `/admincontrol/porder/add_new_purchase_order` |

## Next Steps

1. **Test the Laravel routes** - Ensure all routes work correctly
2. **Migrate remaining views** - Convert remaining CodeIgniter views to Blade templates
3. **Database migrations** - Create Laravel migrations for the existing database schema
4. **Remove CodeIgniter files** - After thorough testing, remove legacy files
5. **Update .htaccess** - Ensure proper routing to Laravel's public folder

## Running the Application

```bash
# Install dependencies
composer install
npm install

# Generate application key (if not set)
php artisan key:generate

# Run migrations (if created)
php artisan migrate

# Start development server
php artisan serve
```

## Security Notes

1. The `.env` file contains sensitive credentials - ensure it's in `.gitignore`
2. Update database credentials for production
3. Remove debug mode in production (`APP_DEBUG=false`)
4. Regenerate `APP_KEY` for production

## Database Tables Used

Based on the CodeIgniter models, the following tables are used:
- `user_info` - User accounts
- `user_details` - User profile details
- `master_user_type` - User types/roles
- `permission_master` - User permissions
- `purchase_order_master` - Purchase orders
- `purchase_order_items` - PO line items
- `receive_order_master` - Receive orders
- `project_master` - Projects
- `project_details` - Project details
- `supplier_master` - Suppliers
- `supplier_catalog_tab` - Supplier catalog
- `item_master` - Items/products
- `item_category_tab` - Item categories
- `cost_code_master` - Cost codes
- `unit_of_measure_tab` - Units of measure
- `taxgroup_master` - Tax groups
- `template_master` - Email templates
- `procore_auth` - Procore API credentials
- `budget_summary_master` - Budget summaries
- `budget_line_items` - Budget line items
- `request_purchase_order` - RFQ orders
