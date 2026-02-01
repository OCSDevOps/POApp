# POApp - Purchase Order Management System

**Multi-Tenant Construction Purchase Order Management Application**

POApp is a Laravel-based purchase order management system designed for construction companies. It provides comprehensive tools for managing purchase orders, receive orders, suppliers, projects, budgets, and integrates with Procore for project management.

**Version:** 1.0 (Multi-Tenancy MVP)  
**Framework:** Laravel 9  
**Status:** Production Ready

---

## Features

### Core Functionality
- 📋 **Purchase Order Management** - Create, approve, and track POs with multi-level approval workflows
- 📦 **Receive Order Tracking** - Goods receipt tracking with backorder handling
- 🏗️ **Project Management** - Multi-project support with budget tracking
- 🏢 **Supplier Management** - Supplier catalogs, pricing, and relationship management
- 💰 **Budget Control** - Cost code tracking, budget constraints, and variance analysis
- ✅ **Checklists** - Quality control workflows and performance tracking
- 🔧 **Equipment Tracking** - Rental and non-rental item management

### Multi-Tenancy (Phase 3 - NEW)
- 🏢 **Company Isolation** - Complete data segregation per tenant
- 🔐 **Secure Scoping** - Automatic filtering on 28+ models
- 👨‍💼 **Super Admin Management** - Company creation, editing, and switching
- 🔄 **Context Switching** - Seamless company switching for administrators
- 📊 **Company-Level Reporting** - All reports respect tenant boundaries

### Integrations
- **Procore API** - Two-way sync for projects, cost codes, and commitments
- **Vite** - Modern asset bundling for CSS/JS

---

## System Requirements

- **PHP:** 8.0+
- **MySQL:** 5.7+ or 8.0+
- **Composer:** 2.0+
- **Node.js:** 16+ (for asset compilation)
- **Web Server:** Apache/Nginx

---

## Installation

### 1. Clone Repository

```bash
git clone <repository-url>
cd POApp/html
```

### 2. Install Dependencies

```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment

Edit `.env` file:

```env
# Application
APP_NAME=POApp
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=poapp
DB_USERNAME=root
DB_PASSWORD=

# Procore Integration (Optional)
PROCORE_CLIENT_ID=
PROCORE_CLIENT_SECRET=
PROCORE_REDIRECT_URI=
```

### 5. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed companies (multi-tenancy)
php artisan db:seed --class=CompanySeeder
```

This creates:
- Default Company (ID: 1) - for existing data
- 3 test companies (Acme, Test Co, etc.)
- Assigns all existing data to Default Company

### 6. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server

```bash
php artisan serve
```

Access at: `http://localhost:8000`

---

## Multi-Tenancy Setup

### Architecture

POApp uses **single-database multi-tenancy** with company-level isolation:
- All tenants in one database
- `company_id` column on all tenant-scoped tables
- Automatic filtering via `CompanyScope` trait
- Session-based tenant context

### Initial Setup

**1. Seed Companies:**
```bash
php artisan db:seed --class=CompanySeeder
```

**2. Assign Users to Companies:**
```sql
UPDATE users SET company_id = 1 WHERE company_id IS NULL;
```

**3. Create Super Admin:**
```sql
UPDATE users SET u_type = 1 WHERE email = 'admin@example.com';
```

**4. Login as Super Admin:**
- Access: `/admincontrol/companies`
- Create new companies
- Switch between companies
- Manage tenant data

### Company Management

**Creating Companies (Super Admin Only):**
1. Navigate to "Companies" in sidebar
2. Click "Create Company"
3. Fill in company details:
   - Name (required)
   - Subdomain (auto-generates if empty)
   - Status (active/inactive)
4. Save

**Switching Companies (Super Admin Only):**
1. Click company dropdown in topbar
2. Select company from list
3. Context switches immediately
4. All data filtered to selected company

**Regular Users:**
- Automatically scoped to their company
- Cannot see other companies' data
- Cannot access company management

### Security Features

✅ **Authorization:**
- CompanyPolicy restricts management to super admins
- Regular users: 403 Forbidden on company endpoints

✅ **Data Isolation:**
- CompanyScope trait on 28 models
- Automatic `company_id` filtering
- Direct ID access blocked across tenants

✅ **Session Protection:**
- SetTenantContext middleware enforces context
- Session manipulation ineffective

✅ **Query Security:**
- 70+ raw DB queries explicitly filtered
- All Eloquent queries automatically scoped

---

## User Roles

### Super Administrator (u_type = 1)
- All permissions
- Company management access
- Can switch between companies
- See all data when switched

### Administrator (u_type = 2)
- Company-scoped permissions
- Manage users, projects, POs within company
- Cannot access other companies
- Cannot manage companies

### Regular User (u_type = 3+)
- Permission template-based access
- Company-scoped data only
- CRUD permissions via `permission_master` table

---

## Development

### Directory Structure

```
html/
├── app/
│   ├── Http/
│   │   ├── Controllers/Admin/  # Admin controllers
│   │   └── Middleware/         # SetTenantContext
│   ├── Models/                 # Eloquent models
│   ├── Policies/               # CompanyPolicy
│   ├── Services/               # Business logic
│   └── Traits/                 # CompanyScope
├── database/
│   ├── migrations/             # Schema definitions
│   └── seeders/                # CompanySeeder
├── resources/
│   ├── views/admin/            # Blade templates
│   ├── css/                    # Stylesheets
│   └── js/                     # JavaScript
├── routes/
│   └── web.php                 # Application routes
└── tests/
    ├── Feature/                # Feature tests
    └── Unit/                   # Unit tests
```

### Key Files

**Multi-Tenancy:**
- `app/Models/Company.php` - Company model
- `app/Traits/CompanyScope.php` - Global scope trait
- `app/Http/Middleware/SetTenantContext.php` - Tenant context
- `app/Http/Controllers/Admin/CompaniesController.php` - Company CRUD
- `app/Policies/CompanyPolicy.php` - Authorization

**Core Models:**
- `app/Models/PurchaseOrder.php` - Purchase orders
- `app/Models/Project.php` - Projects
- `app/Models/Supplier.php` - Suppliers
- `app/Models/User.php` - Users

### Running Tests

```bash
# All tests
php artisan test

# Company management tests
php artisan test --filter CompanyManagementTest

# Multi-tenancy isolation tests
php artisan test --filter MultiTenancyIsolationTest

# CLI testing tool
php artisan test:multi-tenancy
```

### Code Style

- Follow PSR-12 coding standard
- Use Eloquent over raw queries
- Always use CompanyScope trait on tenant-scoped models
- Raw DB queries must filter by `company_id`

---

## Testing Multi-Tenancy

### Automated Tests (21 tests)

**CompanyManagementTest:**
- Super admin CRUD operations
- Authorization enforcement
- Validation rules
- Company switching

**MultiTenancyIsolationTest:**
- Cross-tenant data leakage prevention
- Model scope isolation
- Direct ID access blocking
- Session security

### Manual Testing

Follow the comprehensive checklist in `PHASE_3_6_TESTING_GUIDE.md`:
- Authorization tests
- Data isolation verification
- Security testing (URL manipulation)
- Report scoping validation

### CLI Testing Tool

```bash
php artisan test:multi-tenancy --verbose
```

Tests:
1. Company setup verification
2. Model scope isolation
3. Raw query security
4. Data leakage prevention
5. Company switching simulation

---

## Documentation

### Multi-Tenancy Documentation

- **[Security Audit](SECURITY_AUDIT_MULTITENANCY.md)** - Comprehensive security review
- **[Testing Guide](PHASE_3_6_TESTING_GUIDE.md)** - Test procedures and checklists
- **[Workflow Guide](COMPANY_MANAGEMENT_WORKFLOW.md)** - Company management workflows
- **[Multi-Tenancy Plan](PHASE_3_MULTITENANCY_PLAN.md)** - Implementation roadmap

### Legacy Documentation

- **[Migration Guide](MIGRATION_README.md)** - CodeIgniter to Laravel migration notes
- **[Feature Status](FEATURE_STATUS.md)** - Feature implementation tracking

---

## Configuration

### Environment Variables

```env
# Multi-Tenancy
# (Automatic via company_id session)

# Procore Integration
PROCORE_CLIENT_ID=your_client_id
PROCORE_CLIENT_SECRET=your_client_secret
PROCORE_REDIRECT_URI=http://localhost:8000/admincontrol/procore/callback

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=poapp

# Cache & Queue
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### Procore Setup

1. Create Procore developer app
2. Set OAuth redirect URI
3. Add credentials to `.env`
4. Navigate to Procore settings in admin panel
5. Authorize connection
6. Configure sync settings

---

## Deployment

### Production Checklist

✅ **Environment:**
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate production key
- [ ] Configure database credentials
- [ ] Set up queue workers

✅ **Multi-Tenancy:**
- [ ] Run CompanySeeder
- [ ] Assign users to companies
- [ ] Create super admin user
- [ ] Test company switching
- [ ] Verify data isolation

✅ **Optimization:**
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Build production assets: `npm run build`

✅ **Security:**
- [ ] Update all dependencies
- [ ] Configure SSL/HTTPS
- [ ] Set secure session cookies
- [ ] Review `SECURITY_AUDIT_MULTITENANCY.md`

### Web Server Configuration

**Apache (.htaccess included)**

**Nginx:**
```nginx
server {
    listen 80;
    server_name poapp.example.com;
    root /var/www/poapp/html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Troubleshooting

### Common Issues

**Memory Exhaustion (Seeder/Tests):**
```bash
php -d memory_limit=4G artisan db:seed --class=CompanySeeder
```

**Company Switcher Not Visible:**
- Verify `u_type = 1` in database
- Clear browser cache
- Check session storage

**Data Not Scoped:**
- Verify model uses `CompanyScope` trait
- Check session has `company_id`
- Clear cache: `php artisan optimize:clear`

**Cannot Delete Company:**
- Company must have no users, projects, or POs
- View company details to check counts
- Or mark company inactive instead

---

## Support & Contributing

### Reporting Issues

1. Check existing documentation
2. Review security audit for known limitations
3. Test in clean environment
4. Provide steps to reproduce

### Contributing

1. Fork repository
2. Create feature branch
3. Write tests for new features
4. Follow coding standards
5. Submit pull request

---

## Security

### Reporting Vulnerabilities

If you discover a security vulnerability, please email: [security@example.com]

### Security Features

- ✅ Multi-tenancy data isolation
- ✅ Authorization via policies
- ✅ CSRF protection
- ✅ SQL injection prevention (parameterized queries)
- ✅ Session security
- ✅ XSS protection (Blade escaping)

See `SECURITY_AUDIT_MULTITENANCY.md` for full security audit.

---

## License

Proprietary - All rights reserved

---

## Credits

**Built with:**
- Laravel 9
- Bootstrap 5
- DataTables
- Chart.js
- Font Awesome

**Migrated from CodeIgniter** - See `MIGRATION_README.md` for migration notes.

---

## Changelog

### Version 1.0 (January 2026)
- ✅ Complete multi-tenancy implementation (Phase 3)
- ✅ Company management interface
- ✅ Data isolation via CompanyScope
- ✅ 70+ raw queries secured
- ✅ 21 automated tests
- ✅ Security audit complete
- ✅ Production ready

### Version 0.9 (Pre-Multi-Tenancy)
- Base purchase order management
- Procore integration
- Budget tracking
- User management

---

**For detailed setup and usage, see documentation files in the root directory.**
