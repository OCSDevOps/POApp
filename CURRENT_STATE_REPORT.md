# POApp Current State Report

Last verified: 2026-03-21

## Snapshot

- The app boots and the active automated suite is green after the recent schema, service, controller, and view fixes.
- Latest verification on 2026-03-21: `php artisan migrate:fresh --force` succeeded and `php artisan test` returned `127 passed`.
- Legacy phase/status docs and low-value placeholder tests were moved into `archived/reference_only/` so the project root reflects the current state instead of historical snapshots.
- The active documentation set is now centered on this report, `DATABASE_STATUS.md`, and `tests/TEST_SUITE_README.md`.

## What Is Working

- Multi-tenancy is in active use across the app, including company management, company switching, tenant scoping, and supplier authentication coverage.
- Budget management, approval routing, purchase order workflows, PO change orders, and attachment authorization all have active automated coverage.
- Legacy schema compatibility has been repaired for users, approvals, project roles, RFQ tables, backorder fields, and core purchasing/budget relationships.
- The test database can be rebuilt from migrations, which gives us a repeatable baseline for regression testing.

## Pending Issues And TODOs

1. Replace the retired placeholder coverage for backorders, supplier item pricing, and RFQ quote submission with real feature tests that build the needed fixtures.
2. Do a manual browser pass on the highest-risk admin flow: create PO, submit/approve, receive items, attach files, and process a PO change order.
3. Finish the cost code hierarchy edit action that is still marked TODO in `resources/views/admin/costcodes/partials/hierarchy-node.blade.php`.
4. Add usage-safety checks before deleting equipment records, as noted in `app/Http/Controllers/Admin/EquipmentController.php`.
5. Review the ad-hoc root diagnostic scripts and either archive, document, or remove the ones that are no longer part of the normal workflow.

## Known Risks

- Real end-to-end coverage is still thinner than service-level coverage for supplier pricing, RFQ quoting, and backorder handling.
- Some retained reference docs, especially older architecture and migration notes, still contain historical SQL Server or legacy path assumptions. They are useful context, but not authoritative status documents.
- There is no CI gate yet enforcing migrations plus the full suite on every change.

## Future Enhancements

- Add CI to run migrations and the full Laravel test suite automatically.
- Add browser automation for critical purchasing and approval paths.
- Expand budget and variance reporting, including export flows.
- Add cleanup/retention policy support around attachments and uploaded files.
- Consolidate developer diagnostics into a documented tools or scripts folder.


