# POApp

Laravel 9 purchase order and budget management application for construction workflows.

## Current Layout

- `app/` application code
- `database/migrations/` current schema baseline
- `database/seeders/` active seed path
- `docs/` active project and database documentation
- `docs/archive/reference_only/` historical status docs and retired placeholder tests
- `tests/` active automated suite
- `tools/` diagnostics, maintenance, and legacy reference scripts
- `archived/` large historical source archives kept for reference only

## Quick Start

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan test
```

Default local URLs:

- Admin app: `http://localhost:8000`
- Supplier portal: `http://localhost:8000/supplier`

## Active Seed Path

`php artisan migrate:fresh --seed` runs:

1. `CompanySeeder`
2. `FullApplicationDemoSeeder`

That produces:

- base companies for multi-tenant switching
- a fully linked demo tenant with users, projects, suppliers, items, budgets, POs, receive orders, pricing, RFQs, change orders, approvals, and supplier logins
- the full March 2020 standard cost code catalog for each seeded tenant
- six reusable cost code templates per tenant: one full catalog template plus five section templates

Use [docs/LOGIN_CREDENTIALS.md](docs/LOGIN_CREDENTIALS.md) for the current seeded accounts.

## Verification

Core verification commands:

```bash
php artisan migrate:fresh --seed
php artisan migrate:fresh --force
php artisan test
```

The PHPUnit configuration targets the MySQL test database `porder_db_test`.

## Documentation

Start with:

- [docs/README.md](docs/README.md)
- [docs/CURRENT_STATE_REPORT.md](docs/CURRENT_STATE_REPORT.md)
- [docs/DATABASE_STATUS.md](docs/DATABASE_STATUS.md)
- [docs/COST_CODE_REFERENCE.md](docs/COST_CODE_REFERENCE.md)
- [tests/TEST_SUITE_README.md](tests/TEST_SUITE_README.md)

## Notes

- Files under `docs/archive/reference_only/` and `tools/archive/` are not part of the current supported workflow.
- Files under `archived/` are kept only for historical reference during migration and comparison work.
