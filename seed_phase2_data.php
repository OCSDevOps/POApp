<?php
/**
 * Direct PHP script to seed Phase 2 test data
 * Bypasses Laravel's autoloader to avoid memory issues
 */

// Database connection
$server = "DESKTOP-Q2001NS\SQLEXPRESS";
$database = "porder_db";

$conn = sqlsrv_connect($server, [
    "Database" => $database,
    "TrustServerCertificate" => true,
]);

if (!$conn) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

echo "Connected to SQL Server\n\n";
echo "Starting Phase 2 Test Data Seeding...\n\n";

// Get existing data
$companies = sqlsrv_query($conn, "SELECT TOP 1 * FROM companies");
if ($companies === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}
$company = sqlsrv_fetch_array($companies, SQLSRV_FETCH_ASSOC);

$projects = sqlsrv_query($conn, "SELECT TOP 1 * FROM project_master");
if ($projects === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}
$project = sqlsrv_fetch_array($projects, SQLSRV_FETCH_ASSOC);

$users = sqlsrv_query($conn, "SELECT TOP 1 * FROM users");
if ($users === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}
$user = sqlsrv_fetch_array($users, SQLSRV_FETCH_ASSOC);

if (!$company) {
    die("❌ No companies found. Run CompanySeeder first.\n");
}

if (!$project) {
    die("❌ No projects found. Create projects first.\n");
}

echo "Using Company: {$company['name']} (ID: {$company['id']})\n";
echo "Using Project: {$project['proj_name']} (ID: {$project['proj_id']})\n\n";

// ==================================================
// 1. SEED COST CODE HIERARCHY
// ==================================================
echo "Seeding Cost Code Hierarchy...\n";

$costCodes = [
    // Level 1: Categories
    ['01-00-00', 'General Conditions', 1, null, 1],
    ['02-00-00', 'Site Work', 1, null, 2],
    ['03-00-00', 'Concrete', 1, null, 3],
    ['04-00-00', 'Masonry', 1, null, 4],
    ['05-00-00', 'Metals', 1, null, 5],
    ['06-00-00', 'Wood & Plastics', 1, null, 6],
    ['09-00-00', 'Finishes', 1, null, 7],
    ['15-00-00', 'Mechanical', 1, null, 8],
    ['16-00-00', 'Electrical', 1, null, 9],
    
    // Level 2: Site Work Subcategories
    ['02-10-00', 'Site Preparation', 2, '02-00-00', 1],
    ['02-20-00', 'Earthwork', 2, '02-00-00', 2],
    ['02-30-00', 'Utilities', 2, '02-00-00', 3],
    ['02-40-00', 'Paving', 2, '02-00-00', 4],
    
    // Level 3: Site Preparation Details
    ['02-10-01', 'Demolition', 3, '02-10-00', 1],
    ['02-10-02', 'Tree Removal', 3, '02-10-00', 2],
    ['02-10-03', 'Site Clearing', 3, '02-10-00', 3],
    
    // Level 3: Earthwork Details
    ['02-20-01', 'Excavation', 3, '02-20-00', 1],
    ['02-20-02', 'Grading', 3, '02-20-00', 2],
    ['02-20-03', 'Fill & Compaction', 3, '02-20-00', 3],
    
    // Level 2: Concrete Subcategories
    ['03-10-00', 'Concrete Formwork', 2, '03-00-00', 1],
    ['03-20-00', 'Concrete Reinforcing', 2, '03-00-00', 2],
    ['03-30-00', 'Cast-in-Place Concrete', 2, '03-00-00', 3],
    
    // Level 3: Concrete Details
    ['03-30-01', 'Foundation Concrete', 3, '03-30-00', 1],
    ['03-30-02', 'Slab-on-Grade', 3, '03-30-00', 2],
    ['03-30-03', 'Structural Concrete', 3, '03-30-00', 3],
    
    // Level 2: Mechanical Subcategories
    ['15-10-00', 'Plumbing', 2, '15-00-00', 1],
    ['15-20-00', 'HVAC', 2, '15-00-00', 2],
    ['15-30-00', 'Fire Protection', 2, '15-00-00', 3],
    
    // Level 3: HVAC Details
    ['15-20-01', 'Ductwork', 3, '15-20-00', 1],
    ['15-20-02', 'HVAC Equipment', 3, '15-20-00', 2],
    ['15-20-03', 'Controls', 3, '15-20-00', 3],
];

$ccInserted = 0;
foreach ($costCodes as $cc) {
    // Check if exists
    $check = sqlsrv_query($conn, "SELECT COUNT(*) as cnt FROM cost_code_master WHERE cc_no = ?", [$cc[0]]);
    $row = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC);
    
    if ($row['cnt'] == 0) {
        $sql = "INSERT INTO cost_code_master (cc_no, cc_name, parent_code, full_code, level, sortorder, is_active, cc_created_by) 
                VALUES (?, ?, ?, ?, ?, ?, 1, ?)";
        $params = [$cc[0], $cc[1], $cc[3], $cc[0], $cc[2], $cc[4], $user['id']];
        
        if (sqlsrv_query($conn, $sql, $params)) {
            echo "  ✓ Created cost code: {$cc[0]} - {$cc[1]}\n";
            $ccInserted++;
        }
    }
}

echo "✓ Cost code hierarchy seeded ($ccInserted new codes)\n\n";

// ==================================================
// 2. SEED PROJECT ROLES
// ==================================================
echo "Seeding Project Roles...\n";

$roles = [
    ['Project Manager', 10000.00],
    ['Finance Manager', 50000.00],
    ['Director', 100000.00],
    ['Executive', null],
];

$rolesInserted = 0;
foreach ($roles as $role) {
    $check = sqlsrv_query($conn, "SELECT COUNT(*) as cnt FROM project_roles WHERE project_id = ? AND role = ?", 
        [$project['proj_id'], $role[0]]);
    if ($check === false) {
        echo "  ✗ Query failed for role {$role[0]}: " . print_r(sqlsrv_errors(), true) . "\n";
        continue;
    }
    $row = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC);
    
    if ($row['cnt'] == 0) {
        $sql = "INSERT INTO project_roles (company_id, project_id, user_id, role, approval_limit, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, 1, GETDATE())";
        $params = [$company['id'], $project['proj_id'], $user['id'], $role[0], $role[1]];
        
        if (sqlsrv_query($conn, $sql, $params)) {
            $limit = $role[1] ? '$' . number_format($role[1], 2) : 'Unlimited';
            echo "  ✓ Created role: {$role[0]} (limit: $limit)\n";
            $rolesInserted++;
        }
    }
}

echo "✓ Project roles seeded ($rolesInserted new roles)\n\n";

// ==================================================
// 3. SEED APPROVAL WORKFLOWS
// ==================================================
echo "Seeding Approval Workflows...\n";

$workflows = [
    ['Standard PO Approval', 'purchase_order', 5000.00, 'Project Manager', 1],
    ['Large PO Approval', 'purchase_order', 25000.00, 'Finance Manager', 2],
    ['Executive PO Approval', 'purchase_order', 75000.00, 'Executive', 3],
    ['Budget Change Order - Standard', 'budget_change_order', 10000.00, 'Finance Manager', 1],
    ['Budget Change Order - Large', 'budget_change_order', 50000.00, 'Director', 2],
    ['PO Change Order - Standard', 'po_change_order', 5000.00, 'Project Manager', 1],
];

$wfInserted = 0;
foreach ($workflows as $wf) {
    $check = sqlsrv_query($conn, "SELECT COUNT(*) as cnt FROM approval_workflows WHERE company_id = ? AND name = ?", 
        [$company['id'], $wf[0]]);
    $row = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC);
    
    if ($row['cnt'] == 0) {
        $sql = "INSERT INTO approval_workflows (company_id, project_id, name, entity_type, threshold_amount, 
                is_role_based, approval_role, approval_level, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, 1, ?, ?, 1, GETDATE())";
        $params = [$company['id'], $project['proj_id'], $wf[0], $wf[1], $wf[2], $wf[3], $wf[4]];
        
        if (sqlsrv_query($conn, $sql, $params)) {
            echo "  ✓ Created workflow: {$wf[0]} (threshold: \${$wf[2]})\n";
            $wfInserted++;
        }
    }
}

echo "✓ Approval workflows seeded ($wfInserted new workflows)\n\n";

// ==================================================
// 4. SEED SAMPLE BUDGETS
// ==================================================
echo "Seeding Sample Budgets...\n";

// Get cost code IDs
$costCodeMap = [];
$ccQuery = sqlsrv_query($conn, "SELECT cc_id, cc_no FROM cost_code_master WHERE cc_no IN ('02-10-01', '02-20-01', '03-30-01', '15-20-02', '16-00-00')");
while ($ccRow = sqlsrv_fetch_array($ccQuery, SQLSRV_FETCH_ASSOC)) {
    $costCodeMap[$ccRow['cc_no']] = $ccRow['cc_id'];
}

$budgets = [
    ['02-10-01', 25000.00, 25000.00, 18000.00, 15000.00],
    ['02-20-01', 50000.00, 55000.00, 42000.00, 38000.00],
    ['03-30-01', 150000.00, 150000.00, 125000.00, 110000.00],
    ['15-20-02', 75000.00, 80000.00, 72000.00, 65000.00],
    ['16-00-00', 100000.00, 100000.00, 85000.00, 75000.00],
];

$budgetsInserted = 0;
foreach ($budgets as $budget) {
    if (!isset($costCodeMap[$budget[0]])) {
        continue;
    }
    
    $ccId = $costCodeMap[$budget[0]];
    
    $check = sqlsrv_query($conn, "SELECT COUNT(*) as cnt FROM budget_master WHERE budget_project_id = ? AND budget_cost_code_id = ?", 
        [$project['proj_id'], $ccId]);
    $row = sqlsrv_fetch_array($check, SQLSRV_FETCH_ASSOC);
    
    if ($row['cnt'] == 0) {
        $variance = $budget[2] - ($budget[3] + $budget[4]);
        $utilization = ($budget[3] + $budget[4]) / $budget[2] * 100;
        
        $sql = "INSERT INTO budget_master (budget_project_id, budget_cost_code_id, budget_original_amount, 
                budget_revised_amount, original_amount, committed, actual, variance, 
                warning_notification_sent, critical_notification_sent, budget_created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE())";
        $params = [
            $project['proj_id'], $ccId, $budget[1], $budget[2], $budget[1], 
            $budget[3], $budget[4], $variance,
            $utilization >= 75 ? 1 : 0,
            $utilization >= 90 ? 1 : 0
        ];
        
        if (sqlsrv_query($conn, $sql, $params)) {
            $status = $utilization >= 90 ? '🔴' : ($utilization >= 75 ? '🟡' : '🟢');
            echo sprintf("  ✓ Created budget for %s: $%.2f (%s %.1f%% utilized)\n", 
                $budget[0], $budget[2], $status, $utilization);
            $budgetsInserted++;
        }
    }
}

echo "✓ Sample budgets seeded ($budgetsInserted new budgets)\n\n";

echo "================================================\n";
echo "✓ Phase 2 Test Data Seeding Complete!\n";
echo "================================================\n\n";

echo "Summary:\n";
echo "  - $ccInserted new cost codes in 3-level hierarchy\n";
echo "  - $rolesInserted new project roles with approval limits\n";
echo "  - $wfInserted new approval workflows\n";
echo "  - $budgetsInserted new sample budgets\n\n";

sqlsrv_close($conn);
