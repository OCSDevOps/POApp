# POApp Database Status

Last verified: 2026-03-21

## Current State

- Automated verification is currently based on the MySQL test database `porder_db_test`.
- The schema can be rebuilt successfully with `php artisan migrate:fresh --seed --force`.
- The clean automated suite also passes after `php artisan migrate:fresh --force` followed by `php artisan test`.
- The app no longer depends on the older SQL Server-only status assumptions that were documented in the archived reports.
- The supported demo-data path is now `php artisan migrate:fresh --seed`, which creates one fully linked demo tenant plus base companies for multi-tenant switching.
- Cost code hierarchy management now aligns with the standard March 2020 three-segment format documented in `COST_CODE_REFERENCE.md`.
- The March 2020 standard catalog is now source-controlled and tenant-provisioned through `cost_code_templates` and `cost_code_template_items`.

## Verified Schema Areas

### Core And Tenant Foundations
- `users` supports legacy fields plus current authentication, company assignment, and two-factor/login compatibility fields.
- `companies` exists and is used for tenant scoping.
- Core legacy tables for projects, suppliers, items, purchase orders, receive orders, and budgets are present through the compatibility migrations.

### Purchasing And Receiving
- Purchase order and receive-order flows are supported by the legacy-core compatibility migrations plus the service-layer fixes.
- Backorder support fields exist on the purchase-order detail records for partial receive scenarios.

### Budgeting And Approvals
- Budget management tables exist for project cost codes, budget change orders, PO change orders, approval workflows, approval requests, and project roles.
- Approval relationships were expanded so project-role-based routing and cumulative approvals can work against the current schema.
- Cost code template tables now exist for reusable tenant template packs built from the March 2020 standard catalog.

### Supplier / RFQ / Pricing
- Supplier portal authentication tables are present.
- RFQ tables exist for RFQs, RFQ items, RFQ suppliers, and RFQ quotes.
- Item pricing and price-history tables exist for supplier pricing views and price-change tracking.
- Supplier pricing import, RFQ quote submission, and backorder flows all have active feature coverage against the current schema.

### Attachments
- The attachment system remains polymorphic and company-scoped.
- Attachment authorization was tightened so cross-company file access is explicitly blocked.

## Relationship Health

The following relationships are part of the currently working baseline:

- company -> users / projects / suppliers / purchasing data
- company -> cost codes / cost code templates
- project -> cost codes -> budgets
- project -> project roles -> approval workflows and approval requests
- purchase order -> line items -> receive orders / actuals
- RFQ -> RFQ items / RFQ suppliers / RFQ quotes
- supplier -> supplier catalog / item pricing / supplier users
- attachable entities -> attachments

## Pending Database Work

1. Do a focused FK and index audit on the oldest legacy-compatible tables to tighten constraints where safe.
2. Document the intended runtime database matrix more clearly so root docs do not drift between legacy SQL Server notes and the active MySQL test setup.
3. Consider whether we want a second tier of seeded templates beyond the current six-pack, such as category-level or trade-specific template groupings.
