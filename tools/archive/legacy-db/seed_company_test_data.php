<?php
/**
 * Phase 3.2: Seed test data for multi-tenancy validation
 * Run directly: php seed_company_test_data.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "========================================\n";
echo "Phase 3.2: Multi-Tenancy Test Data Seeder\n";
echo "========================================\n\n";

// Get existing companies
$companies = DB::table('companies')->orderBy('id')->get();

echo "Existing companies:\n";
foreach ($companies as $company) {
    echo "  - ID {$company->id}: {$company->name} (subdomain: {$company->subdomain})\n";
}

// Get companies for data seeding
$defaultCompany = DB::table('companies')->where('subdomain', 'default')->first();
$testCompany = DB::table('companies')->where('subdomain', 'test')->first();
$acmeCompany = DB::table('companies')->where('subdomain', 'acme')->first();

if (!$testCompany || !$acmeCompany) {
    echo "\nError: Test companies not found. Run Phase 3.1 SQL script first.\n";
    exit(1);
}

echo "\n========================================\n";
echo "Seeding test users...\n";
echo "========================================\n";

$testUsers = [
    // Test Construction Co users
    [
        'name' => 'John Smith',
        'username' => 'john.smith',
        'email' => 'john.smith@testconstruction.com',
        'password' => Hash::make('password123'),
        'u_type' => 0, // Regular user
        'company_id' => $testCompany->id,
    ],
    [
        'name' => 'Sarah Johnson',
        'username' => 'sarah.johnson',
        'email' => 'sarah.johnson@testconstruction.com',
        'password' => Hash::make('password123'),
        'u_type' => 0,
        'company_id' => $testCompany->id,
    ],
    // Acme Builders users
    [
        'name' => 'Mike Davis',
        'username' => 'mike.davis',
        'email' => 'mike.davis@acmebuilders.com',
        'password' => Hash::make('password123'),
        'u_type' => 0,
        'company_id' => $acmeCompany->id,
    ],
    [
        'name' => 'Emily Chen',
        'username' => 'emily.chen',
        'email' => 'emily.chen@acmebuilders.com',
        'password' => Hash::make('password123'),
        'u_type' => 0,
        'company_id' => $acmeCompany->id,
    ],
];

foreach ($testUsers as $userData) {
    if (!DB::table('users')->where('email', $userData['email'])->exists()) {
        DB::table('users')->insert($userData);
        echo "  ✓ Created user: {$userData['name']} ({$userData['email']})\n";
    } else {
        echo "  - User already exists: {$userData['email']}\n";
    }
}

echo "\n========================================\n";
echo "Seeding test suppliers...\n";
echo "========================================\n";

$testSuppliers = [
    // Test Construction Co suppliers
    [
        'sup_name' => 'ABC Supply Co',
        'sup_address' => '123 Supply Street',
        'sup_email' => 'sales@abcsupply.com',
        'sup_phone' => '555-1000',
        'sup_status' => 1,
        'company_id' => $testCompany->id,
    ],
    [
        'sup_name' => 'BuildMart Wholesale',
        'sup_address' => '456 Warehouse Blvd',
        'sup_email' => 'orders@buildmart.com',
        'sup_phone' => '555-2000',
        'sup_status' => 1,
        'company_id' => $testCompany->id,
    ],
    // Acme Builders suppliers
    [
        'sup_name' => 'Premier Materials Inc',
        'sup_address' => '789 Industrial Pkwy',
        'sup_email' => 'info@premiermaterials.com',
        'sup_phone' => '555-3000',
        'sup_status' => 1,
        'company_id' => $acmeCompany->id,
    ],
    [
        'sup_name' => 'Quality Lumber & Hardware',
        'sup_address' => '321 Lumber Lane',
        'sup_email' => 'sales@qualitylumber.com',
        'sup_phone' => '555-4000',
        'sup_status' => 1,
        'company_id' => $acmeCompany->id,
    ],
];

foreach ($testSuppliers as $supplierData) {
    if (!DB::table('supplier_master')->where('sup_email', $supplierData['sup_email'])->exists()) {
        DB::table('supplier_master')->insert($supplierData);
        echo "  ✓ Created supplier: {$supplierData['sup_name']}\n";
    } else {
        echo "  - Supplier already exists: {$supplierData['sup_name']}\n";
    }
}

echo "\n========================================\n";
echo "Seeding test projects...\n";
echo "========================================\n";

$testProjects = [
    // Test Construction Co projects
    [
        'proj_name' => 'Downtown Office Building',
        'proj_address' => '100 Main Street',
        'proj_status' => 1,
        'company_id' => $testCompany->id,
    ],
    [
        'proj_name' => 'Riverside Shopping Center',
        'proj_address' => '200 River Road',
        'proj_status' => 1,
        'company_id' => $testCompany->id,
    ],
    // Acme Builders projects
    [
        'proj_name' => 'Parkview Residential Complex',
        'proj_address' => '300 Park Avenue',
        'proj_status' => 1,
        'company_id' => $acmeCompany->id,
    ],
    [
        'proj_name' => 'Tech Campus Phase 2',
        'proj_address' => '400 Innovation Drive',
        'proj_status' => 1,
        'company_id' => $acmeCompany->id,
    ],
];

foreach ($testProjects as $projectData) {
    if (!DB::table('project_master')->where('proj_name', $projectData['proj_name'])->where('company_id', $projectData['company_id'])->exists()) {
        DB::table('project_master')->insert($projectData);
        echo "  ✓ Created project: {$projectData['proj_name']}\n";
    } else {
        echo "  - Project already exists: {$projectData['proj_name']}\n";
    }
}

echo "\n========================================\n";
echo "Data Summary by Company\n";
echo "========================================\n\n";

$companies = DB::table('companies')->orderBy('id')->get();

foreach ($companies as $company) {
    echo "Company: {$company->name} (ID: {$company->id})\n";
    echo "  Subdomain: {$company->subdomain}\n";

    $userCount = DB::table('users')->where('company_id', $company->id)->count();
    $supplierCount = DB::table('supplier_master')->where('company_id', $company->id)->count();
    $projectCount = DB::table('project_master')->where('company_id', $company->id)->count();
    $poCount = DB::table('purchase_order_master')->where('company_id', $company->id)->count();

    echo "  Users: {$userCount}\n";
    echo "  Suppliers: {$supplierCount}\n";
    echo "  Projects: {$projectCount}\n";
    echo "  Purchase Orders: {$poCount}\n";
    echo "\n";
}

echo "========================================\n";
echo "Phase 3.2 Test Data Seeding Complete!\n";
echo "========================================\n\n";

echo "Next steps:\n";
echo "1. Test login with different company users\n";
echo "2. Verify company_id is set in session\n";
echo "3. Check that users only see their company's data\n";
echo "\nTest credentials (all passwords: password123):\n";
echo "  - john.smith@testconstruction.com (Test Construction Co)\n";
echo "  - mike.davis@acmebuilders.com (Acme Builders Inc)\n";
