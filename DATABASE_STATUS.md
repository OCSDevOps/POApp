# POApp Database Status - SQL Server
## Database: porder_db on DESKTOP-Q2001NS\SQLEXPRESS

Last Updated: 2026-01-30
Status: **ALL SCHEMA UPDATES APPLIED** ✅

## Database Connection
- **Server**: DESKTOP-Q2001NS\SQLEXPRESS  
- **Database**: porder_db
- **Authentication**: Windows Authentication (no username/password needed)
- **Laravel Connection**: sqlsrv (configured in .env)

## Phase 1.1: Multi-Tenancy (✅ COMPLETE)
- [x] companies table - **0 records** (needs seeding)
- [x] company_id added to 7 core tables
- [x] supplier_users table for supplier portal
- [x] CompanyScope global scope on models

## Phase 1.3: Item Pricing (✅ DATABASE READY - CODE TESTING NEEDED)
- [x] item_pricing table exists - **0 records**
- [ ] Test pricing CRUD via supplier portal
- [ ] Test admin pricing management
- [ ] Verify company_id scoping works

## Phase 1.4: RFQ System (✅ DATABASE READY - CODE TESTING NEEDED)
- [x] rfqs table exists - **0 records**
- [x] rfq_items table exists - **0 records**
- [x] rfq_suppliers table exists - **0 records**
- [x] rfq_quotes table exists - **0 records**
- [ ] Test RFQ creation by admin
- [ ] Test supplier quote submission
- [ ] Test multi-supplier comparison
- [ ] Verify email notifications

## Phase 1.5: Backorder Tracking (✅ DATABASE READY - CODE TESTING NEEDED)
- [x] purchase_order_details.backorder_qty exists
- [x] purchase_order_details.backorder_status exists  
- [x] purchase_order_details.backorder_notes exists
- [ ] Test backorder creation on partial receipt
- [ ] Test backorder status workflow
- [ ] Test backorder reports

## Phase 2.1: Accounting Integrations (✅ DATABASE READY)
- [x] accounting_integrations table exists - **0 records**
- [x] integration_sync_logs table exists - **0 records**
- [x] integration_field_mappings table exists - **0 records**
- [ ] QuickBooks integration (future)
- [ ] Sage Intacct integration (future)

## Phase 2.2: Budget Management (✅ CORE COMPLETE - 90%)
### Database Tables (All Created ✅)
- [x] project_cost_codes table - **0 records**
- [x] budget_change_orders table - **0 records**
- [x] po_change_orders table - **0 records**
- [x] approval_workflows table - **0 records**
- [x] approval_requests table - **0 records**
- [x] project_roles table - **0 records**

### Enhanced Columns (All Applied ✅)
#### budget_master enhancements:
- [x] original_amount (populated from budget_revised_amount)
- [x] committed (tracks PO commitments)
- [x] actual (tracks received costs)
- [x] variance (budget - committed - actual)
- [x] warning_notification_sent (75% threshold flag)
- [x] critical_notification_sent (90% threshold flag)

#### cost_code_master enhancements:
- [x] parent_code (for hierarchy: XX-XX-XX format)
- [x] full_code (complete code with parent path)
- [x] level (1=category, 2=subcategory, 3=detail)
- [x] sortorder (display ordering)
- [x] is_active (enable/disable codes)

### Backend Implementation (100% ✅)
- [x] BudgetService with validation and threshold monitoring
- [x] ApprovalService with workflow routing
- [x] ProjectRoleController for role management
- [x] ApprovalWorkflowController for workflow setup
- [x] CostCodeController with hierarchy methods
- [x] BudgetWarningNotification (email + database)
- [x] ApprovalPendingNotification
- [x] ChangeOrderNotification

### UI Implementation (100% ✅)
- [x] Project Role Management UI (select + index views)
- [x] Approval Workflow Setup UI (index + create views)
- [x] Cost Code Hierarchy UI (hierarchy + tree node views)
- [x] 15 new routes added to web.php

### Testing Status (0% ❌ - TO DO)
- [ ] Write BudgetServiceTest
- [ ] Write ApprovalServiceTest
- [ ] Write BudgetChangeOrderTest
- [ ] Write PoChangeOrderTest
- [ ] Write NotificationTest
- [ ] Manual UI testing of all management interfaces

### Missing Features (10% remaining)
- [ ] Reports & Analytics UI
  - [ ] Budget vs Actual by project
  - [ ] Variance analysis dashboard
  - [ ] Change Order summary reports
  - [ ] Committed vs Actual tracking
  - [ ] Excel/PDF export functionality

## Schema Verification Scripts Created
All located in `html/` directory:

1. **verify_sqlserver_schema.sql** - Complete verification of all tables/columns
2. **apply_missing_schema_fixed.sql** - Add missing columns (with correct table names)
3. **finalize_schema.sql** - Final updates (populate original_amount, create rfqs)

### How to Verify Database
```bash
# From html/ directory
sqlcmd -S "DESKTOP-Q2001NS\SQLEXPRESS" -d porder_db -E -i verify_sqlserver_schema.sql
```

### How to Apply Future Updates
```bash
# Create SQL script with GO separators for each batch
sqlcmd -S "DESKTOP-Q2001NS\SQLEXPRESS" -d porder_db -E -i your_update.sql
```

## Important Notes

### Table Name Mappings
Laravel models use different names than actual SQL Server tables:
- `PurchaseOrderItem` model → `purchase_order_details` table
- `CostCode` model → `cost_code_master` table (with `cc_no` not `cc_code`)
- `Budget` model → `budget_master` table (with `budget_revised_amount` not `budgeted_amount`)

### Artisan Limitations
Due to Symfony Finder memory issues, **do not use**:
- `php artisan migrate`
- `php artisan migrate:status`

**Instead use**: Direct SQL scripts or custom PHP scripts that bypass Symfony Finder.

### Foreign Key Constraints
The `rfqs` table was created **without foreign key constraints** to avoid dependency issues. Foreign keys can be added later if needed:
```sql
ALTER TABLE rfqs ADD CONSTRAINT FK_rfqs_company 
  FOREIGN KEY (company_id) REFERENCES companies(id);
```

## Next Steps

### Immediate (Database Complete ✅)
1. ~~Add missing rfqs table~~ DONE
2. ~~Add backorder fields to purchase_order_details~~ DONE
3. ~~Enhance budget_master table~~ DONE
4. ~~Add cost code hierarchy columns~~ DONE

### Priority 1 (Testing)
1. Seed companies table with test data
2. Test Phase 1.3 item pricing functionality
3. Test Phase 1.4 RFQ system end-to-end
4. Test Phase 1.5 backorder creation and tracking
5. Manually test all Phase 2.2 management UIs

### Priority 2 (Complete Phase 2.2)
1. Build Reports & Analytics UI (budget vs actual, variance, change orders)
2. Write comprehensive test suite for all Phase 2.2 features
3. Performance testing with realistic data volumes

### Priority 3 (Polish)
1. Add foreign key constraints to rfqs table
2. Document API endpoints
3. Create user documentation for budget management features

## Git History
Recent commits:
- `de80a67` - feat: Apply all Phase 1.3-2.2 schema updates to SQL Server
- Previous commits show Phase 2.2 UI implementation

## Support
For database issues:
1. Check Laravel logs: `html/storage/logs/laravel.log`
2. Verify connection: `Get-Content html/.env | Select-String -Pattern "^DB_"`
3. Test SQL Server: `sqlcmd -S "DESKTOP-Q2001NS\SQLEXPRESS" -d porder_db -E -Q "SELECT @@VERSION"`
