# Company Management Workflow Guide

**POApp Multi-Tenancy System**  
**Version:** 1.0  
**Last Updated:** January 31, 2026

---

## Overview

This guide explains how to manage companies (tenants) in the POApp system. Company management allows super administrators to create, manage, and switch between different tenant organizations.

---

## User Roles

### Super Administrator (u_type = 1)
**Permissions:**
- Create, view, edit, delete companies
- Switch between companies to manage data
- See all companies in the system
- Access company management interface

**Access:**
- Company management UI: `/admincontrol/companies`
- Company switcher: Topbar dropdown
- Companies menu: Sidebar navigation

### Regular User (u_type = 2, 3, etc.)
**Permissions:**
- View only their own company's data
- Cannot access company management
- Cannot switch companies

**Restrictions:**
- No company management UI
- No company switcher visible
- Automatic data isolation to their company

---

## Company Management Interface

### Accessing Company Management

**For Super Admins:**

1. **Via Sidebar Menu:**
   - Look for "Companies" icon in left sidebar
   - Click to access company index

2. **Via Topbar Dropdown:**
   - Click company name dropdown (top right)
   - Select "Manage Companies"

**URL:** `/admincontrol/companies`

---

## Workflows

### 1. Creating a New Company

**When to Use:**
- Adding a new client/tenant organization
- Setting up a new business entity
- Onboarding a new construction company

**Steps:**

1. **Navigate to Company Management**
   - Click "Companies" in sidebar
   - Or use topbar dropdown → "Manage Companies"

2. **Click "Create Company" Button**
   - Located at top right of company index page

3. **Fill in Company Details**
   - **Company Name** (required)
     - Example: "Acme Construction LLC"
     - Used for display throughout system
   
   - **Subdomain** (optional)
     - Example: "acme"
     - Auto-generates from name if left blank
     - Must be unique across all companies
     - Used for future subdomain-based routing
   
   - **Status** (checkbox)
     - ✅ Active: Company can operate normally
     - ❌ Inactive: Company access disabled

4. **Click "Create Company"**
   - System validates inputs
   - Creates company with unique ID
   - Redirects to company index with success message

**Validation Rules:**
- Company name: Required, max 255 characters
- Subdomain: Optional, unique, lowercase, alphanumeric + hyphens
- Status: Defaults to active

**Auto-Generated:**
- ID: Sequential integer
- Created/Updated timestamps
- Subdomain (if not provided)

---

### 2. Editing an Existing Company

**When to Use:**
- Correcting company information
- Updating company status (activate/deactivate)
- Changing subdomain

**Steps:**

1. **Locate Company**
   - Go to `/admincontrol/companies`
   - Find company in table

2. **Click "Edit" Button**
   - Yellow pencil icon in Actions column

3. **Update Company Information**
   - Modify name, subdomain, or status
   - Cannot change ID (system-generated)

4. **Click "Update Company"**
   - System validates changes
   - Updates database
   - Redirects with success message

**Important Notes:**
- Subdomain must remain unique
- Changing status to inactive prevents company users from logging in
- Cannot delete required fields

---

### 3. Viewing Company Details

**When to Use:**
- Reviewing company statistics
- Checking data counts (users, projects, POs)
- Before making management decisions

**Steps:**

1. **Click Company Name or "View" Icon**
   - Blue eye icon in company table

2. **Review Company Dashboard**
   - **Company Information:**
     - ID, Name, Subdomain
     - Status, Created/Updated dates
   
   - **Statistics Cards:**
     - Total Users
     - Active/Total Projects
     - Pending/Total Purchase Orders
     - Total Suppliers
     - Total Items
   
   - **Recent Activity:**
     - Latest 5 purchase orders
     - PO numbers, project names, status

3. **Take Action if Needed**
   - **Switch to Company:** Manage company's data
   - **Edit Company:** Modify information
   - **Delete Company:** Only if empty (no data)

---

### 4. Switching to a Company

**When to Use:**
- Managing data for a specific tenant
- Troubleshooting issues in a company
- Creating test data for a company
- Viewing reports for a specific tenant

**Steps:**

**Method 1: Via Company Index**
1. Go to `/admincontrol/companies`
2. Click green "Switch" button for desired company
3. System sets company context
4. Redirects to previous page

**Method 2: Via Topbar Dropdown**
1. Click company name dropdown (top right)
2. Select company from list
3. Current company marked with checkmark
4. Click to switch

**Method 3: Via Company Detail Page**
1. View company details
2. Click "Switch to This Company" button
3. Context changes immediately

**What Happens When You Switch:**
- Session `company_id` updated to selected company
- All data queries filtered to that company
- Dashboard shows company's data
- Reports reflect company's information
- Alert banner displays current company context

**Important Notes:**
- Only super admins can switch companies
- Regular users always see their own company's data
- Switch persists until you switch again or logout
- Each browser session independent

---

### 5. Deleting a Company

**When to Use:**
- Removing test companies
- Cleaning up inactive/unused tenants
- Decommissioning a client

**Prerequisites:**
- Company must have NO users
- Company must have NO projects
- Company must have NO purchase orders
- Essentially: Company must be completely empty

**Steps:**

1. **Verify Company is Empty**
   - View company details
   - Check statistics: All should be 0
   - Only empty companies show "Danger Zone" section

2. **Navigate to Danger Zone**
   - On company detail page (show view)
   - Red bordered section at bottom
   - Warning message displayed

3. **Click "Delete This Company" Button**
   - Red delete button in danger zone

4. **Confirm Deletion**
   - JavaScript confirmation popup
   - "Are you absolutely sure...?"
   - Click OK to proceed

5. **Company Permanently Deleted**
   - Cannot be undone
   - Redirects with success message
   - Company removed from database

**Protection Against Accidental Deletion:**
- Backend validation prevents deletion if company has data
- "Danger Zone" only appears if company is empty
- Confirmation dialog requires explicit confirmation
- Error message if company has users/projects/POs

**What Gets Deleted:**
- Company record only
- Associated data (if any exists) NOT deleted
- If company has data, deletion blocked with error

---

## Dashboard & Navigation

### Company Context Indicator

**Purpose:** Shows which company's data you're currently viewing

**Location:** Top of dashboard (main page)

**Appearance:**
- Blue alert banner
- Building icon + company name
- "(All data shown below is scoped to this company)"
- Dismissible (can close it)

**Visibility:**
- Only shown to super admins
- Regular users don't see it (they only have one company)

### Company Switcher Dropdown

**Purpose:** Quick company context switching

**Location:** Topbar navigation (top right)

**Features:**
- Shows current company name
- Building icon indicator
- Dropdown with all active companies
- Current company marked with checkmark
- "Manage Companies" link at bottom

**Usage:**
1. Click company name button
2. Dropdown opens with company list
3. Click company to switch
4. Form submits, context changes
5. Page refreshes with new company data

**Visibility:**
- Only super admins (u_type = 1)
- Hidden from regular users

### Companies Sidebar Menu

**Purpose:** Access company management interface

**Location:** Left sidebar navigation

**Features:**
- Building icon + "Companies" label
- Active state highlighting when on companies pages
- Direct link to `/admincontrol/companies`

**Visibility:**
- Only super admins
- Hidden from regular users

---

## Data Isolation & Security

### How Data Isolation Works

**Every Tenant-Scoped Table Has:**
- `company_id` column (integer, indexed)
- Foreign key relationship to `companies` table

**Automatic Filtering:**
- All model queries automatically filtered by company_id
- CompanyScope trait applies global scope
- Direct ID access blocked if from different company

**Example:**
```
Company A (ID: 1) - 50 purchase orders
Company B (ID: 2) - 30 purchase orders

Super admin switches to Company A:
- Dashboard shows: 50 purchase orders
- Can only view/edit Company A's POs

Super admin switches to Company B:
- Dashboard shows: 30 purchase orders
- Can only view/edit Company B's POs

Regular user in Company A:
- Always sees: 50 purchase orders
- Cannot switch to Company B
- Cannot see Company B data
```

### Security Measures

**Authorization:**
- CompanyPolicy enforces super admin restriction
- 403 Forbidden for regular users attempting company management
- Route-level authorization checks

**Session Protection:**
- SetTenantContext middleware enforces company context
- User's company_id set on every request
- Session manipulation ineffective (middleware resets)

**Data Access Protection:**
- CompanyScope blocks cross-tenant queries
- Raw SQL queries explicitly filtered
- Direct ID access returns null if wrong company

**UI Protection:**
- Blade guards hide company features from non-super admins
- Navigation items conditionally rendered
- Forms/buttons only shown to authorized users

---

## Common Scenarios

### Scenario 1: Onboarding a New Client

**Goal:** Set up a new construction company in the system

**Steps:**
1. Create company via "Create Company" form
2. Switch to new company context
3. Create admin user for the company
4. Create projects for the company
5. Set up suppliers (if company-specific)
6. Create initial items/budgets
7. Switch back to default or another company

**Best Practice:**
- Use descriptive company names
- Choose logical subdomains (company abbreviation)
- Set status to active
- Assign company admin immediately

---

### Scenario 2: Troubleshooting Company Data

**Goal:** Help a user with an issue in their company

**Steps:**
1. Ask user which company they belong to
2. Switch to that company via topbar dropdown
3. Navigate to relevant section (POs, Projects, etc.)
4. View data as the company sees it
5. Make corrections if needed
6. Switch back when done

**Best Practice:**
- Always verify company context before making changes
- Check dashboard alert banner to confirm
- Document any changes made
- Switch back to prevent accidental changes in wrong company

---

### Scenario 3: Creating Test Data

**Goal:** Populate test company for demos or testing

**Steps:**
1. Create test company (e.g., "Demo Construction")
2. Switch to test company
3. Create demo projects, POs, suppliers
4. Generate sample reports
5. Mark company as inactive when not demoing

**Best Practice:**
- Use "Demo" or "Test" in company name
- Use test subdomain (e.g., "demo", "test1")
- Can deactivate instead of delete
- Keep separate from production data

---

### Scenario 4: Company Migration/Consolidation

**Goal:** Move one company's data to another (future feature)

**Current Limitation:**
- No built-in data migration between companies
- Would require manual SQL updates

**Workaround:**
1. Export data from source company (manual/SQL)
2. Update company_id in exported data
3. Import into target company (manual/SQL)

**Future Enhancement:**
- Company merge tool
- Data export/import functionality
- Automated migration wizard

---

### Scenario 5: Deactivating a Company

**Goal:** Temporarily disable a company without deleting

**Steps:**
1. Navigate to company list
2. Click "Edit" on target company
3. Change status to "Inactive" (uncheck checkbox)
4. Save changes

**Effects:**
- Company users cannot login
- Company not listed in active company dropdown
- Data preserved (not deleted)
- Can reactivate by changing status back

**Use Cases:**
- Client contract expired
- Temporary suspension
- Testing scenarios
- Account on hold

---

## Reporting & Analytics

### Company-Level Reports

**All Reports Respect Company Context:**
- Budget vs Actual
- Variance Analysis
- Committed Actual
- Change Order Reports
- Custom reports

**How It Works:**
- Report queries filtered by session('company_id')
- Only shows data for current company
- Super admin sees data for active company context

**Generating Cross-Company Reports:**

**Current:** Not available in UI

**Workaround for Super Admin:**
1. Switch to Company A
2. Generate/export report for Company A
3. Switch to Company B
4. Generate/export report for Company B
5. Manually consolidate

**Future Enhancement:**
- Multi-company comparison reports
- Consolidated dashboard
- Company-level analytics

---

## Best Practices

### For Super Administrators

1. **Always Verify Company Context**
   - Check dashboard alert banner
   - Confirm topbar dropdown shows correct company
   - Double-check before making changes

2. **Document Company Switches**
   - Note why you switched
   - Track changes made in other companies
   - Switch back when done

3. **Use Descriptive Names**
   - Clear company names
   - Logical subdomains
   - Avoid abbreviations only you understand

4. **Regular Audits**
   - Review company list periodically
   - Remove test/demo companies
   - Update inactive statuses

5. **Backup Before Deletes**
   - Even though delete is protected
   - Export data if needed
   - Confirm with stakeholders

### For System Setup

1. **Initial Setup**
   - Create default company first
   - Assign existing data to default
   - Test multi-tenancy before production

2. **User Assignment**
   - Assign users to correct company during creation
   - Verify company_id on user records
   - Test user login after assignment

3. **Data Migration**
   - Run CompanySeeder to assign existing data
   - Verify all records have company_id
   - Test queries return expected results

4. **Testing**
   - Create at least 2 test companies
   - Add data to each company
   - Verify isolation by switching between them
   - Test regular user restrictions

---

## Troubleshooting

### Issue: Cannot Create Company

**Possible Causes:**
- Missing required field (name)
- Duplicate subdomain
- Not logged in as super admin

**Solutions:**
- Verify all required fields filled
- Check subdomain uniqueness
- Confirm u_type = 1 in database

---

### Issue: Company Switcher Not Visible

**Possible Causes:**
- Not a super admin (u_type != 1)
- Session not set correctly
- Cache issue

**Solutions:**
- Verify u_type in users table
- Clear browser cache
- Check session('u_type') in code
- Logout and login again

---

### Issue: Cannot Delete Company

**Possible Causes:**
- Company has users
- Company has projects
- Company has purchase orders

**Solutions:**
- View company details to see counts
- Delete/reassign users first
- Delete/reassign projects first
- Delete purchase orders first
- Or mark company inactive instead

---

### Issue: Wrong Data Showing After Switch

**Possible Causes:**
- Browser cache
- Session not updating
- CompanyScope not applied

**Solutions:**
- Hard refresh (Ctrl+F5)
- Check dashboard alert banner
- Verify session('company_id') correct
- Clear Laravel cache: `php artisan cache:clear`

---

### Issue: Regular User Can See Company Menu

**Possible Causes:**
- u_type set incorrectly
- Blade guard not working
- Session corrupted

**Solutions:**
- Verify u_type in database
- Check Blade template has @if(session('u_type') == 1)
- Logout and login
- Clear sessions: `php artisan session:clear`

---

## Future Enhancements

### Planned Features

1. **Subdomain-Based Routing**
   - `company1.poapp.com`
   - `company2.poapp.com`
   - Automatic company detection from subdomain

2. **Company-Specific Branding**
   - Custom logos per company
   - Company-specific themes
   - Branded purchase orders

3. **Data Export/Import**
   - Export company data for backup
   - Import data from other systems
   - Migrate data between companies

4. **Company Settings**
   - Custom workflows per company
   - Company-specific feature flags
   - Tenant-level configurations

5. **Audit Logging**
   - Track all company switches
   - Log data modifications per company
   - Activity reports per tenant

6. **Usage Analytics**
   - Storage per company
   - User activity per company
   - Purchase order volume tracking

---

## Quick Reference

### URLs
- Company Index: `/admincontrol/companies`
- Create Company: `/admincontrol/companies/create`
- Edit Company: `/admincontrol/companies/{id}/edit`
- View Company: `/admincontrol/companies/{id}`
- Switch Company: POST `/admincontrol/companies/{id}/switch`
- Delete Company: DELETE `/admincontrol/companies/{id}`

### Required Permissions
- All company management: `u_type = 1` (super admin)
- Regular operations: Automatic company_id filtering

### Key Files
- Controller: `app/Http/Controllers/Admin/CompaniesController.php`
- Policy: `app/Policies/CompanyPolicy.php`
- Model: `app/Models/Company.php`
- Middleware: `app/Http/Middleware/SetTenantContext.php`
- Views: `resources/views/admin/companies/*.blade.php`

---

**End of Workflow Guide**
