# Seeded Login Credentials

Last verified: 2026-03-21

Default local app URLs:

- Admin app: `http://localhost:8000`
- Supplier portal: `http://localhost:8000/supplier`

## Admin Users

All seeded admin-side demo users use password `admin123`.

| Role | Email | Company |
| --- | --- | --- |
| Super Admin | `superadmin@demo.com` | Demo Construction Co |
| Company Admin | `admin@demo.com` | Demo Construction Co |
| Project Manager | `manager@demo.com` | Demo Construction Co |
| Viewer | `viewer@demo.com` | Demo Construction Co |
| Regular User | `user@demo.com` | Demo Construction Co |

## Supplier Users

All seeded supplier users use password `admin123`.

| Supplier | Email | Company |
| --- | --- | --- |
| Apex Steel Industries | `supplier@apexsteel.com` | Demo Construction Co |
| ReadyMix Concrete Co | `supplier@readymix.com` | Demo Construction Co |

## Seed Data Notes

The canonical seed path is:

```bash
php artisan migrate:fresh --seed
```

That creates:

- base companies for tenant switching
- a fully linked demo company with projects, suppliers, items, budgets, pricing, RFQs, purchase orders, receive orders, approvals, and change orders

If you need the latest functional state of the seeded dataset, use `migrate:fresh --seed` rather than older archive docs or one-off seed scripts.
