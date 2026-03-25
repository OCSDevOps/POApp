# POApp Active Test Suite

Last verified: 2026-03-21

## Scope

The active suite focuses on business-critical Laravel coverage:

- Feature coverage for company management, multi-tenancy, supplier authentication, cost code hierarchy, supplier pricing, RFQ quoting, backorders, workflow-level purchasing and budget behavior
- Unit coverage for approval, budget, purchase order, PO change order, and attachment services

## Current Status

- Run the full suite with `php artisan test`
- Latest verified result on 2026-03-21: `138 passed`
- The suite is expected to run against the `porder_db_test` MySQL database defined in `phpunit.xml`
- The green suite assumes a clean migrated test DB. Use `php artisan migrate:fresh --force` before `php artisan test` if the test database has been reseeded for smoke checks.
- Placeholder and example tests were moved to `docs/archive/reference_only/tests/` so the active suite only contains meaningful coverage

## Coverage Gaps

- Browser-only UI behaviors are not yet automated

## Useful Commands

```bash
php artisan test
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
php artisan test tests/Feature/Workflows
php artisan test tests/Unit/Services
php artisan migrate:fresh --seed --force
```

## Archive Note

If you need historical context, see `../docs/archive/reference_only/tests/`. Those files are reference-only and should not be re-enabled as-is; replace them with real tests when those areas are revisited.
