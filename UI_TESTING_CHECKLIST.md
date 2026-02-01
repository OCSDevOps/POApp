# UI Testing Checklist - POApp Multi-Tenancy

**Testing Date:** January 31, 2026  
**Server:** http://localhost:8000  
**Status:** IN PROGRESS

---

## Test Environment Setup

✅ **Server Started:** `php artisan serve` running on http://localhost:8000  
⏳ **Database:** Verify connection active  
⏳ **Test Users:** Create super admin and regular user  
⏳ **Test Companies:** Seed companies or create manually

---

## Pre-Testing Setup

### 1. Database Verification
```bash
# Check database connection
php artisan db:show

# Run migrations if needed
php artisan migrate

# Seed test companies
php artisan db:seed --class=CompanySeeder
```

### 2. Create Test Users

**Super Admin User:**
```sql
-- Check existing users
SELECT id, name, email, u_type, company_id FROM users LIMIT 5;

-- Update existing user to super admin
UPDATE users SET u_type = 1 WHERE id = 1;

-- OR create new super admin
INSERT INTO users (name, email, password, u_type, company_id, created_at, updated_at)
VALUES ('Super Admin', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NOW(), NOW());
-- Password: "password"
```

**Regular User:**
```sql
-- Create regular user for Company 1
INSERT INTO users (name, email, password, u_type, company_id, created_at, updated_at)
VALUES ('Regular User', 'user@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1, NOW(), NOW());
```

---

## UI Testing Checklist

### A. Authentication & Access

#### Test 1: Login Screen
- [ ] **URL:** http://localhost:8000
- [ ] Page loads without errors
- [ ] Login form displays correctly
- [ ] Email and password fields present
- [ ] "Remember Me" checkbox visible
- [ ] "Login" button styled correctly
- [ ] No console errors (F12 DevTools)

**Actions:**
1. Open http://localhost:8000
2. Check page layout and styling
3. Verify all form elements
4. Open browser DevTools (F12) → Check Console for errors

**Expected:** Clean login page, no JavaScript errors

---

#### Test 2: Login as Super Admin
- [ ] **Credentials:** admin@test.com / password
- [ ] Login successful
- [ ] Redirects to dashboard
- [ ] Company switcher visible in topbar
- [ ] "Companies" menu visible in sidebar
- [ ] Dashboard shows company indicator alert

**Actions:**
1. Enter super admin credentials
2. Click "Login"
3. Verify redirect to dashboard
4. Check topbar for company dropdown
5. Check sidebar for "Companies" link
6. Check dashboard for blue alert banner

**Expected:** 
- Successful login
- Company switcher present
- Companies menu visible
- Dashboard alert showing current company

---

#### Test 3: Login as Regular User
- [ ] **Credentials:** user@test.com / password
- [ ] Login successful
- [ ] Redirects to dashboard
- [ ] Company switcher NOT visible
- [ ] "Companies" menu NOT visible
- [ ] Dashboard alert NOT visible

**Actions:**
1. Logout from super admin
2. Login as regular user
3. Verify dashboard loads
4. Confirm NO company switcher in topbar
5. Confirm NO "Companies" in sidebar
6. Confirm NO dashboard alert

**Expected:**
- Successful login
- No company management features visible
- Clean dashboard without multi-tenancy UI

---

### B. Company Management (Super Admin Only)

#### Test 4: Company Index Page
- [ ] **URL:** http://localhost:8000/admincontrol/companies
- [ ] Page loads (as super admin)
- [ ] Returns 403 (as regular user)
- [ ] DataTable displays companies
- [ ] Columns: ID, Name, Subdomain, Status, Users, Projects, POs, Created, Actions
- [ ] "Create Company" button visible (top right)
- [ ] Pagination works if >25 companies
- [ ] Search/filter functionality works
- [ ] "Current" badge shows on active company

**Actions:**
1. Login as super admin
2. Navigate to Companies (sidebar or /admincontrol/companies)
3. Check table layout and data
4. Try search functionality
5. Check pagination
6. Verify action buttons (View/Edit/Switch/Delete)

**Expected:**
- Table displays all companies
- DataTable features work (search, sort, pagination)
- Current company marked with badge
- All action buttons visible

**Screenshots to Take:**
- Full company index page
- Table with multiple companies
- Action buttons hover states

---

#### Test 5: Create Company Page
- [ ] **URL:** http://localhost:8000/admincontrol/companies/create
- [ ] Page loads correctly
- [ ] "Create Company" heading visible
- [ ] Form fields present:
  - [ ] Company Name (required)
  - [ ] Subdomain (optional)
  - [ ] Status checkbox (active)
- [ ] Help sidebar displays correctly
- [ ] "Create Company" button styled
- [ ] "Cancel" link present
- [ ] Validation messages appear for errors

**Actions:**
1. Click "Create Company" from index
2. Verify form layout
3. Try submitting empty form (should show validation errors)
4. Fill in valid data:
   - Name: "Test Company ABC"
   - Subdomain: "testabc"
   - Status: Checked
5. Submit form
6. Verify redirect to index with success message

**Expected:**
- Clean form layout
- Validation works (name required)
- Subdomain auto-generates if empty
- Success message after creation
- New company appears in table

**Test Cases:**
- [ ] Submit without name → Error: "Name is required"
- [ ] Submit with duplicate subdomain → Error: "Subdomain already exists"
- [ ] Submit with name only → Success (auto-generate subdomain)
- [ ] Submit with all fields → Success

**Screenshots:**
- Create form (empty)
- Validation errors
- Success message

---

#### Test 6: Edit Company Page
- [ ] **URL:** http://localhost:8000/admincontrol/companies/{id}/edit
- [ ] Page loads with company data pre-filled
- [ ] "Edit Company" heading shows company name
- [ ] Form fields editable:
  - [ ] Company Name
  - [ ] Subdomain
  - [ ] Status (dropdown)
- [ ] Stats sidebar shows:
  - [ ] Created date
  - [ ] Updated date
- [ ] "Update Company" button present
- [ ] "Cancel" link present
- [ ] Validation works on update

**Actions:**
1. From company index, click "Edit" on a company
2. Verify form pre-filled with data
3. Modify company name
4. Submit form
5. Verify redirect with success message
6. Check company name updated in table

**Test Cases:**
- [ ] Update name only → Success
- [ ] Update subdomain to duplicate → Error
- [ ] Change status to inactive → Success
- [ ] Update all fields → Success

**Screenshots:**
- Edit form with data
- Updated success message

---

#### Test 7: View Company Details (Show Page)
- [ ] **URL:** http://localhost:8000/admincontrol/companies/{id}
- [ ] Page loads correctly
- [ ] Company name in page heading
- [ ] Status badge displays (Active/Inactive)
- [ ] Action buttons visible:
  - [ ] "Switch to This Company" (if not current)
  - [ ] "Current Company" badge (if active)
  - [ ] "Edit" button
  - [ ] "Back" button
- [ ] Statistics cards display:
  - [ ] Users count
  - [ ] Projects (active/total)
  - [ ] Purchase Orders (pending/total)
  - [ ] Suppliers count
- [ ] Company details table shows:
  - [ ] ID, Name, Subdomain, Status
  - [ ] Created/Updated timestamps
  - [ ] Items count
- [ ] Recent purchase orders section:
  - [ ] Shows latest 5 POs
  - [ ] PO#, Project, Status, Date columns
  - [ ] Empty state if no POs
- [ ] Danger Zone visible only if company empty

**Actions:**
1. From company index, click company name or "View" icon
2. Verify all statistics are correct
3. Check recent POs section
4. Try action buttons:
   - Click "Switch to This Company"
   - Click "Edit"
   - Click "Back"

**Test Scenarios:**
- [ ] View company with data (users/projects/POs)
- [ ] View empty company (no data)
- [ ] View current company (should show "Current" badge)
- [ ] View different company (should show "Switch" button)

**Expected:**
- Correct statistics
- Recent POs displayed
- Action buttons work
- Danger Zone only on empty companies

**Screenshots:**
- Company with data
- Empty company with Danger Zone
- Current company view

---

#### Test 8: Company Switching
- [ ] Topbar dropdown displays current company
- [ ] Dropdown lists all active companies
- [ ] Current company marked with checkmark
- [ ] "Manage Companies" link at bottom
- [ ] Clicking company switches context
- [ ] Dashboard updates after switch
- [ ] Success message appears
- [ ] Dropdown shows new current company

**Actions:**
1. Login as super admin
2. Note current company in topbar
3. Click company dropdown
4. Select different company
5. Verify page refreshes
6. Check dashboard alert shows new company
7. Check topbar shows new company
8. Navigate to POs/Projects → Verify data filtered

**Test Flow:**
1. Switch to Company A
2. Check dashboard stats (note PO count)
3. Switch to Company B
4. Check dashboard stats (should be different)
5. Switch back to Company A
6. Verify original stats return

**Expected:**
- Seamless switching
- Context persists
- All data filtered correctly
- No errors or delays

**Screenshots:**
- Company dropdown open
- Dashboard after switch
- Different stats per company

---

#### Test 9: Delete Company
- [ ] Can only delete empty companies
- [ ] Delete button only appears if no data
- [ ] Confirmation dialog appears
- [ ] Cancel works (doesn't delete)
- [ ] Confirm deletes company
- [ ] Success message shown
- [ ] Company removed from table
- [ ] Error if company has data

**Actions:**
1. Create a test company (no data)
2. View company details
3. Scroll to Danger Zone
4. Click "Delete This Company"
5. Cancel confirmation → Verify not deleted
6. Click delete again
7. Confirm → Verify deleted
8. Try to delete company with users/projects → Verify error

**Test Cases:**
- [ ] Delete empty company → Success
- [ ] Delete company with users → Error
- [ ] Delete company with projects → Error
- [ ] Delete company with POs → Error
- [ ] Cancel deletion → No action

**Expected:**
- Can only delete truly empty companies
- Confirmation required
- Success/error messages appropriate

**Screenshots:**
- Danger Zone section
- Confirmation dialog
- Error message (company has data)
- Success message (empty company deleted)

---

### C. Navigation & UI Components

#### Test 10: Sidebar Navigation
- [ ] POAPP logo/brand visible
- [ ] Dashboard link works
- [ ] Purchase Orders link works
- [ ] Projects link works
- [ ] Suppliers link works
- [ ] Companies link visible (super admin only)
- [ ] Companies link hidden (regular user)
- [ ] Active state highlights current page
- [ ] Logout link works
- [ ] Responsive on mobile (toggle menu)

**Actions:**
1. Login as super admin
2. Check all sidebar links
3. Click each link → Verify navigation
4. Check active state highlighting
5. Resize browser → Test responsive behavior
6. Logout and login as regular user
7. Verify "Companies" link absent

**Expected:**
- All links functional
- Active page highlighted
- Companies menu conditional
- Responsive design works

---

#### Test 11: Topbar Navigation
- [ ] Welcome message shows user name
- [ ] Company switcher visible (super admin)
- [ ] Company switcher hidden (regular user)
- [ ] User dropdown works
- [ ] Profile link present (if implemented)
- [ ] Settings link present
- [ ] Logout link works
- [ ] Mobile hamburger menu works

**Actions:**
1. Check topbar as super admin
2. Verify company dropdown functional
3. Click user dropdown → Check links
4. Test logout
5. Login as regular user
6. Verify NO company dropdown
7. Test responsive behavior

---

#### Test 12: Dashboard Company Indicator
- [ ] Blue alert banner visible (super admin)
- [ ] Shows current company name
- [ ] Dismissible (X button works)
- [ ] Hidden for regular users
- [ ] Clear explanation text
- [ ] Proper styling (Bootstrap alert)

**Actions:**
1. Login as super admin
2. Check dashboard for alert
3. Read message (should say "Active Company Context: [Name]")
4. Click X to dismiss
5. Refresh page → Alert returns
6. Login as regular user → No alert

---

### D. Data Isolation Testing (Critical)

#### Test 13: Purchase Orders Isolation
- [ ] **Setup:** Create POs in Company A and Company B
- [ ] Switch to Company A → See only Company A POs
- [ ] Switch to Company B → See only Company B POs
- [ ] PO counts different per company
- [ ] Cannot access other company's PO by URL
- [ ] Edit form only shows own company's projects/suppliers

**Actions:**
1. Login as super admin
2. Switch to Company A
3. Note PO count on dashboard
4. Go to PO list → Note which POs visible
5. Switch to Company B
6. Check PO count (should be different)
7. Go to PO list → Different POs
8. Try URL manipulation:
   - Copy Company A PO URL
   - Switch to Company B
   - Paste Company A PO URL
   - Should get 404 or redirect

**Expected:**
- Complete data isolation
- URL manipulation blocked
- Counts accurate per company

---

#### Test 14: Projects Isolation
- [ ] Projects filtered by company
- [ ] Project dropdown shows only company projects
- [ ] Cannot access other company's project
- [ ] Project stats accurate per company

**Actions:**
1. Switch between companies
2. Go to Projects list
3. Verify different projects per company
4. Check project counts match

---

#### Test 15: Suppliers Isolation
- [ ] Suppliers filtered by company
- [ ] Supplier dropdown shows only company suppliers
- [ ] Supplier catalog scoped
- [ ] Cannot view other company's suppliers

---

#### Test 16: Reports Isolation
- [ ] Budget reports show only company data
- [ ] Variance analysis scoped correctly
- [ ] All reports respect company context
- [ ] No cross-company data leakage

**Actions:**
1. Generate reports in Company A
2. Note totals/data
3. Switch to Company B
4. Generate same reports
5. Verify different data

---

### E. Responsive Design Testing

#### Test 17: Desktop (1920x1080)
- [ ] Layout looks professional
- [ ] No horizontal scroll
- [ ] All elements aligned
- [ ] Tables fit viewport
- [ ] Buttons properly sized

#### Test 18: Tablet (768px)
- [ ] Sidebar toggles/collapses
- [ ] Tables responsive (horizontal scroll if needed)
- [ ] Forms stack vertically
- [ ] Touch-friendly buttons

#### Test 19: Mobile (375px)
- [ ] Hamburger menu works
- [ ] Company dropdown usable
- [ ] Forms mobile-optimized
- [ ] Tables scrollable
- [ ] All features accessible

**Actions:**
1. Use browser DevTools (F12)
2. Click device toolbar icon
3. Test different viewport sizes
4. Check all pages at each size

---

### F. Form Validation Testing

#### Test 20: Company Form Validations
- [ ] Name required → Error shown
- [ ] Subdomain unique → Error if duplicate
- [ ] Subdomain format → Lowercase, alphanumeric + hyphens
- [ ] Status field works
- [ ] Error messages styled correctly
- [ ] Success messages appear
- [ ] Form retains input on validation error

**Test Cases:**

| Test | Input | Expected Result |
|------|-------|----------------|
| Empty name | name="", subdomain="test" | Error: Name required |
| Duplicate subdomain | subdomain="default" | Error: Subdomain exists |
| Valid minimum | name="Test Co" | Success (auto-subdomain) |
| Valid full | All fields filled | Success |
| Long name | 300 characters | Error: Max 255 |
| Invalid subdomain | "Test_123!" | Error: Invalid format |

---

### G. Error Handling

#### Test 21: Authorization Errors
- [ ] Regular user accessing /admincontrol/companies → 403 page
- [ ] Unauthorized company switch → 403
- [ ] Clear error message displayed
- [ ] "Go Back" or "Home" link present

**Actions:**
1. Login as regular user
2. Manually navigate to /admincontrol/companies
3. Should see 403 Forbidden page
4. Check error message clarity

---

#### Test 22: Not Found Errors
- [ ] Invalid company ID → 404 page
- [ ] Non-existent route → 404 page
- [ ] Deleted company → 404 on access
- [ ] Custom 404 page styled

---

#### Test 23: Validation Errors
- [ ] Form validation errors display clearly
- [ ] Field-level error messages
- [ ] Error summary if multiple errors
- [ ] Red styling on invalid fields
- [ ] Form data preserved

---

### H. Performance Testing

#### Test 24: Page Load Times
- [ ] Company index < 2 seconds
- [ ] Company details < 1 second
- [ ] Company switch < 1 second
- [ ] Dashboard < 2 seconds
- [ ] Forms < 1 second

**Actions:**
1. Open DevTools Network tab
2. Record page load times
3. Document any slow pages

---

#### Test 25: DataTable Performance
- [ ] Handles 100+ companies smoothly
- [ ] Search instant (<100ms)
- [ ] Sorting fast
- [ ] Pagination responsive

---

### I. Browser Compatibility

#### Test 26: Cross-Browser Testing
- [ ] **Chrome** (latest) - All features work
- [ ] **Firefox** (latest) - All features work
- [ ] **Edge** (latest) - All features work
- [ ] **Safari** (if Mac available) - All features work

**Critical Features to Test Per Browser:**
- Login/logout
- Company switcher dropdown
- DataTables functionality
- Form submission
- Confirmation dialogs

---

### J. Console Error Check

#### Test 27: JavaScript Errors
- [ ] No console errors on login
- [ ] No console errors on dashboard
- [ ] No console errors on company pages
- [ ] No 404s for assets (CSS/JS/images)
- [ ] No CORS errors
- [ ] No deprecation warnings

**Actions:**
1. Keep F12 DevTools open during all tests
2. Check Console tab frequently
3. Note any red errors
4. Note any yellow warnings

---

## Test Results Summary

### Critical Issues (Must Fix)
- [ ] None found ✅
- [ ] Issue 1: [Description]
- [ ] Issue 2: [Description]

### High Priority Issues
- [ ] None found ✅
- [ ] Issue 1: [Description]

### Medium Priority Issues
- [ ] None found ✅
- [ ] Issue 1: [Description]

### Low Priority Issues (Polish)
- [ ] None found ✅
- [ ] Issue 1: [Description]

### UI/UX Improvements
- [ ] None found ✅
- [ ] Suggestion 1: [Description]

---

## Screenshots Taken
- [ ] Login page
- [ ] Dashboard (super admin)
- [ ] Dashboard (regular user)
- [ ] Company index with data
- [ ] Company create form
- [ ] Company edit form
- [ ] Company show page (with data)
- [ ] Company show page (empty with Danger Zone)
- [ ] Company switcher dropdown
- [ ] Navigation sidebar
- [ ] Validation errors
- [ ] Success messages
- [ ] 403 Forbidden page
- [ ] Mobile view (375px)
- [ ] Tablet view (768px)

---

## Sign-Off

**Tested By:** _________________  
**Date:** January 31, 2026  
**Overall Status:** ⏳ IN PROGRESS / ✅ PASSED / ❌ FAILED

**Notes:**
_________________________________________
_________________________________________
_________________________________________

**Approved for Production:** ☐ Yes ☐ No ☐ With Conditions

---

## Quick Test Commands

```bash
# Start server
php artisan serve

# Check database
php artisan db:show

# Seed companies
php artisan db:seed --class=CompanySeeder

# Clear cache
php artisan optimize:clear

# Run automated tests
php artisan test --filter CompanyManagementTest
```

---

**END OF UI TESTING CHECKLIST**
