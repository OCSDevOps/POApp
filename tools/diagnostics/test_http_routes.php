<?php
/**
 * HTTP-based route tester - logs in and tests all admin GET routes via curl
 * Routes use prefix: /admincontrol/
 */

$baseUrl = 'http://localhost:8001';

// Step 1: Get CSRF token from login page (login is at root /)
$ch = curl_init();
$cookieFile = tempnam(sys_get_temp_dir(), 'laravel_cookies_');

curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HEADER => false,
]);
$loginPage = curl_exec($ch);

// Extract CSRF token - Laravel @csrf generates: <input type="hidden" name="_token" value="...">
preg_match('/name="_token"\s+value="([^"]+)"/', $loginPage, $m);
if (empty($m[1])) {
    preg_match('/value="([^"]+)"\s*name="_token"/', $loginPage, $m);
}
if (empty($m[1])) {
    preg_match('/_token.*?value="([a-zA-Z0-9]+)"/', $loginPage, $m);
}

$token = $m[1] ?? '';
if (!$token) {
    echo "ERROR: Could not get CSRF token from login page\n";
    echo "Login page response length: " . strlen($loginPage) . "\n";
    $pos = strpos($loginPage, '<form');
    if ($pos !== false) {
        echo "Form section:\n" . substr($loginPage, $pos, 500) . "\n";
    } else {
        echo substr($loginPage, 0, 500) . "\n";
    }
    exit(1);
}
echo "Got CSRF token: " . substr($token, 0, 10) . "...\n";

// Step 2: Login via POST to validate_login
curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/validate_login",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => $token,
        'email' => 'admin@demo.com',
        'password' => 'admin123',
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: text/html',
    ],
    CURLOPT_FOLLOWLOCATION => true,
]);
$dashResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
echo "Login response: HTTP $httpCode, redirected to: $finalUrl\n";

if (strpos($finalUrl, 'admincontrol/dashboard') !== false || strpos($dashResponse, 'Dashboard') !== false) {
    echo "Login successful!\n\n";
} else {
    echo "WARNING: Login may have failed. Final URL: $finalUrl\n";
    if ($httpCode >= 500) {
        if (preg_match('/exception_message[^>]*>([^<]+)/', $dashResponse, $em)) {
            echo "Server Error: " . trim($em[1]) . "\n";
        }
    }
    echo "Continuing anyway...\n\n";
}

// Step 3: Test all parameterless admin routes (prefix: /admincontrol)
$p = '/admincontrol';
$routes = [
    // Core
    "$p/dashboard" => 'Dashboard',
    "$p/companies" => 'Companies',

    // User Profile
    "$p/profile" => 'User Profile',

    // Projects
    "$p/projects" => 'Projects List',
    "$p/projects/create" => 'Projects Create',

    // Suppliers
    "$p/suppliers" => 'Suppliers List',
    "$p/suppliers/create" => 'Suppliers Create',

    // Items
    "$p/items" => 'Items List',
    "$p/items/create" => 'Items Create',
    "$p/items/import" => 'Items Import',
    "$p/items/pricing-summary" => 'Items Pricing Summary',

    // Purchase Orders
    "$p/porder" => 'PO List',
    "$p/porder/add_new_purchase_order" => 'PO Create',

    // Receive Orders
    "$p/receive" => 'RO List',
    "$p/receive/create" => 'RO Create (select PO)',
    "$p/receive/summary" => 'RO Summary',
    "$p/receive/back-order-report" => 'RO Back Order Report',

    // Budgets (budget. name prefix)
    "$p/budgets" => 'Budgets List',
    "$p/budgets/create" => 'Budgets Create',
    "$p/budgets/summary" => 'Budgets Summary',

    // PO Templates
    "$p/templates" => 'PO Templates List',
    "$p/templates/create" => 'PO Templates Create',

    // Cost Codes
    "$p/costcodes" => 'Cost Codes',

    // Tax Groups
    "$p/taxgroups" => 'Tax Groups',

    // UOM
    "$p/uom" => 'UOM',

    // Item Pricing
    "$p/pricing" => 'Supplier Pricing',

    // Backorders
    "$p/backorders" => 'Backorders',

    // RFQ
    "$p/rfq" => 'RFQ List',
    "$p/rfq/create" => 'RFQ Create',

    // Reports
    "$p/reports/budget-vs-actual" => 'Report: Budget vs Actual',

    // Approvals
    "$p/approvals" => 'Approvals Dashboard',
    "$p/approval-workflows" => 'Approval Workflows',
    "$p/approval-workflows/create" => 'Approval Workflows Create',

    // PO Change Orders
    "$p/po-change-orders" => 'PO Change Orders',

    // Procore
    "$p/procore" => 'Procore Dashboard',
    "$p/procore/settings" => 'Procore Settings',
    "$p/procore/project-mappings" => 'Procore Project Mappings',
    "$p/procore/cost-code-mappings" => 'Procore Cost Code Mappings',

    // Integrations
    "$p/integrations" => 'Integrations List',
    "$p/integrations/create" => 'Integrations Create',

    // Equipment
    "$p/equipment" => 'Equipment',

    // Permission Templates
    "$p/permissions" => 'Permission Templates',

    // Checklists
    "$p/checklists" => 'Checklists',
    "$p/checklists/create" => 'Checklists Create',

    // Perform Checklists
    "$p/perform-checklists" => 'Perform Checklists',
    "$p/perform-checklists/create" => 'Perform Checklists Create',

    // Support
    "$p/support" => 'Support',

    // Company Settings
    "$p/company" => 'Company Settings',

    // Tenants
    "$p/tenants" => 'Tenants',
    "$p/tenants/create" => 'Tenants Create',

    // Packages
    "$p/packages" => 'Item Packages',

    // Project Roles
    "$p/project-roles" => 'Project Roles',

    // Project Takeoffs & Estimates
    "$p/takeoffs" => 'Takeoffs List',
    "$p/takeoffs/create" => 'Takeoffs Create',

    // AI Settings
    "$p/ai-settings" => 'AI Settings',
];

$passed = 0;
$failed = 0;
$errors = [];

foreach ($routes as $uri => $label) {
    curl_setopt_array($ch, [
        CURLOPT_URL => "$baseUrl$uri",
        CURLOPT_HTTPGET => true,
        CURLOPT_POST => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

    $isError = false;
    $errorMsg = '';

    if ($httpCode >= 500) {
        $isError = true;
        // Extract Laravel/Ignition error message
        if (preg_match('/exception_message[^>]*>([^<]+)/', $response, $em)) {
            $errorMsg = "HTTP $httpCode - " . trim($em[1]);
        } elseif (preg_match('/<title[^>]*>(.*?)<\/title>/s', $response, $tm)) {
            $errorMsg = "HTTP $httpCode - " . trim(strip_tags($tm[1]));
        } else {
            $errorMsg = "HTTP $httpCode";
        }
    } elseif ($httpCode == 404) {
        $isError = true;
        $errorMsg = "HTTP 404 - Route not found";
    } elseif (strpos($finalUrl, 'login') !== false || $finalUrl === "$baseUrl/") {
        if (strpos($uri, 'login') === false) {
            $isError = true;
            $errorMsg = "Redirected to login (session/auth issue)";
        }
    }

    if ($isError) {
        $failed++;
        $errors[] = ['uri' => $uri, 'label' => $label, 'error' => $errorMsg];
        echo "FAIL: $label ($uri) - $errorMsg\n";
    } else {
        $passed++;
        echo "PASS: $label ($uri) - HTTP $httpCode (" . strlen($response) . " bytes)\n";
    }
}

curl_close($ch);
unlink($cookieFile);

echo "\n" . str_repeat('=', 60) . "\n";
echo "RESULTS: $passed passed, $failed failed out of " . count($routes) . " routes\n";
echo str_repeat('=', 60) . "\n";

if (!empty($errors)) {
    echo "\nFAILED ROUTES:\n";
    foreach ($errors as $e) {
        echo "  - {$e['label']} ({$e['uri']}): {$e['error']}\n";
    }
}
