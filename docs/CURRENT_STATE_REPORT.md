# POApp Current State Report

Last verified: 2026-03-21

## Snapshot

- The app boots and the active automated suite is green after the recent schema, service, controller, and view fixes.
- Latest verification on 2026-03-21:
  - `php artisan migrate:fresh --seed --force` succeeded
  - `php artisan migrate:fresh --force` succeeded
  - `php artisan test` returned `138 passed`
- Legacy phase/status docs and low-value placeholder tests were moved into `docs/archive/reference_only/` so the project root reflects the current state instead of historical snapshots.
- The active documentation set is now centered on this report, `DATABASE_STATUS.md`, `COST_CODE_REFERENCE.md`, `LOGIN_CREDENTIALS.md`, and `tests/TEST_SUITE_README.md`.

## What Is Working

- Multi-tenancy is active across company management, company switching, tenant scoping, and supplier authentication coverage.
- Budget management, approval routing, purchase order workflows, PO change orders, and attachment authorization all have active automated coverage.
- Backorder tracking, supplier pricing, RFQ quote submission, and cost code hierarchy editing now have active feature coverage instead of placeholder tests or UI stubs.
- Legacy schema compatibility has been repaired for users, approvals, project roles, RFQ tables, backorder fields, and core purchasing/budget relationships.
- The cost code hierarchy editor now supports the standard three-segment structure from the March 2020 reference list and cascades section/category renames to descendants.
- The full March 2020 catalog now lives in `database/data/standard_cost_codes_march_2020.json` and is provisioned automatically for seeded tenants and newly created companies.
- Each tenant now receives six reusable cost code templates by default: one full catalog template plus five top-level section templates.
- Project cost code assignment now exposes the seeded template pack so teams can apply the full catalog or a section template directly from the UI.
- The demo seeder now uses the March 2020 standard cost codes instead of the older legacy shorthand codes.
- The test database can be rebuilt from migrations, which gives us a repeatable baseline for regression testing.
- The repository layout is now cleaner at the top level, with active docs under `docs/`, utility scripts under `tools/`, and historical material separated into archive folders.

## Pending Issues And TODOs

1. Do a manual browser pass on the highest-risk admin flow: create PO, submit and approve, receive items, attach files, and process a PO change order.
2. Add usage-safety checks before deleting equipment records, as noted in `app/Http/Controllers/Admin/EquipmentController.php`.
3. Finish documenting the remaining diagnostics and maintenance entry points under `tools/` so contributors know which scripts are still worth using.

## Known Risks

- Browser-only interaction coverage is still thinner than server-side feature and service coverage.
- Some retained reference docs, especially older architecture and migration notes, still contain historical SQL Server or legacy path assumptions. They are useful context, but not authoritative status documents.
- There is no CI gate yet enforcing migrations plus the full suite on every change.
- The seeded demo-data validation flow and the clean test-suite flow are intentionally separate. The automated suite should run against a clean migrated test DB, while seeded smoke validation should use `php artisan migrate:fresh --seed`.

## Future Enhancements

- Add CI to run migrations and the full Laravel test suite automatically.
- Add browser automation for critical purchasing and approval paths.
- Expand budget and variance reporting, including export flows.
- Add cleanup and retention policy support around attachments and uploaded files.
- Add richer template management around the seeded catalog, such as category-level packs or template cloning from the standard set.
- Continue consolidating developer diagnostics into a smaller documented set of supported tools.
