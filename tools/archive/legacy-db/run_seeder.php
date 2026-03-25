<?php
/**
 * Standalone seeder runner - bypasses Artisan kernel to avoid OOM.
 * Uses ACTUAL database column names (not model fillable names).
 * Usage: php run_seeder.php
 */
ini_set('memory_limit', '512M');

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->instance('request', Illuminate\Http\Request::capture());
$app->bootstrapWith([
    Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    Illuminate\Foundation\Bootstrap\BootProviders::class,
]);

echo "=== POApp Sample Data Seeder ===\n\n";

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    $password = Hash::make('admin123');

    // ==========================================
    // 1. Company
    // ==========================================
    echo "Creating company...\n";
    $existing = DB::table('companies')->where('name', 'Demo Construction Co')->first();
    if ($existing) {
        $companyId = $existing->id;
        echo "  ~ Already exists (id={$companyId})\n";
    } else {
        $companyId = DB::table('companies')->insertGetId([
            'name' => 'Demo Construction Co',
            'subdomain' => 'demo',
            'status' => 1,
            'settings' => json_encode(['currency' => 'USD', 'timezone' => 'America/New_York']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "  + Created (id={$companyId})\n";
    }

    // ==========================================
    // 2. Users (all levels)
    // ==========================================
    echo "\nCreating users...\n";
    $users = [
        ['name' => 'Super Admin',     'email' => 'superadmin@demo.com', 'username' => 'superadmin',    'u_type' => 1],
        ['name' => 'Company Admin',   'email' => 'admin@demo.com',      'username' => 'companyadmin',  'u_type' => 2],
        ['name' => 'Project Manager', 'email' => 'manager@demo.com',    'username' => 'manager',       'u_type' => 3],
        ['name' => 'Viewer User',     'email' => 'viewer@demo.com',     'username' => 'viewer',        'u_type' => 4],
        ['name' => 'Regular User',    'email' => 'user@demo.com',       'username' => 'regularuser',   'u_type' => 0],
    ];
    foreach ($users as $u) {
        if (!DB::table('users')->where('email', $u['email'])->exists()
            && !DB::table('users')->where('username', $u['username'])->exists()) {
            DB::table('users')->insert([
                'name' => $u['name'], 'email' => $u['email'], 'username' => $u['username'],
                'avatar' => '', 'password' => $password, 'company_id' => $companyId,
                'u_type' => $u['u_type'], 'u_status' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            echo "  + {$u['name']} ({$u['email']}) u_type={$u['u_type']}\n";
        } else {
            echo "  ~ {$u['email']} already exists\n";
        }
    }

    // ==========================================
    // 3. Units of Measure (NO company_id)
    // ==========================================
    echo "\nCreating units of measure...\n";
    $uomData = ['Each', 'Box', 'Cubic Yard', 'Linear Foot', 'Square Foot', 'Ton', 'Gallon', 'Bag'];
    $uomIds = [];
    foreach ($uomData as $uomName) {
        $ex = DB::table('unit_of_measure_tab')->where('uom_name', $uomName)->first();
        if ($ex) {
            $uomIds[] = $ex->uom_id;
        } else {
            $uomIds[] = DB::table('unit_of_measure_tab')->insertGetId([
                'uom_name' => $uomName, 'uom_detail' => $uomName,
                'uom_createdate' => now(), 'uom_createby' => 1, 'uom_status' => 1,
            ], 'uom_id');
            echo "  + {$uomName}\n";
        }
    }

    // ==========================================
    // 4. Item Categories (NO company_id, uses icat_details not icat_description)
    // ==========================================
    echo "\nCreating item categories...\n";
    $catNames = ['Concrete & Masonry', 'Structural Steel', 'Lumber & Wood', 'Electrical', 'Plumbing', 'HVAC', 'Safety Equipment', 'Rental Equipment'];
    $categoryIds = [];
    foreach ($catNames as $catName) {
        $ex = DB::table('item_category_tab')->where('icat_name', $catName)->first();
        if ($ex) {
            $categoryIds[] = $ex->icat_id;
        } else {
            $categoryIds[] = DB::table('item_category_tab')->insertGetId([
                'icat_name' => $catName, 'icat_details' => $catName . ' materials',
                'icat_createdate' => now(), 'icat_createby' => 1, 'icat_status' => 1,
            ], 'icat_id');
            echo "  + {$catName}\n";
        }
    }

    // ==========================================
    // 5. Cost Codes (HAS company_id, uses cc_description)
    // ==========================================
    echo "\nCreating cost codes...\n";
    $ccData = [
        ['cc_no' => '01', 'cc_description' => 'General Requirements', 'cc_parent_code' => '01', 'cc_level' => 1, 'cc_full_code' => '01'],
        ['cc_no' => '03', 'cc_description' => 'Concrete', 'cc_parent_code' => '03', 'cc_level' => 1, 'cc_full_code' => '03'],
        ['cc_no' => '05', 'cc_description' => 'Metals', 'cc_parent_code' => '05', 'cc_level' => 1, 'cc_full_code' => '05'],
        ['cc_no' => '06', 'cc_description' => 'Wood & Plastics', 'cc_parent_code' => '06', 'cc_level' => 1, 'cc_full_code' => '06'],
        ['cc_no' => '26', 'cc_description' => 'Electrical', 'cc_parent_code' => '26', 'cc_level' => 1, 'cc_full_code' => '26'],
        ['cc_no' => '03-10', 'cc_description' => 'Cast-in-Place Concrete', 'cc_parent_code' => '03', 'cc_category_code' => '10', 'cc_level' => 2, 'cc_full_code' => '03-10'],
        ['cc_no' => '03-20', 'cc_description' => 'Reinforcing Steel', 'cc_parent_code' => '03', 'cc_category_code' => '20', 'cc_level' => 2, 'cc_full_code' => '03-20'],
        ['cc_no' => '05-10', 'cc_description' => 'Structural Steel Framing', 'cc_parent_code' => '05', 'cc_category_code' => '10', 'cc_level' => 2, 'cc_full_code' => '05-10'],
        ['cc_no' => '26-10', 'cc_description' => 'Medium-Voltage Electrical', 'cc_parent_code' => '26', 'cc_category_code' => '10', 'cc_level' => 2, 'cc_full_code' => '26-10'],
    ];
    $costCodeIds = [];
    foreach ($ccData as $cc) {
        $ex = DB::table('cost_code_master')->where('cc_full_code', $cc['cc_full_code'])->where('company_id', $companyId)->first();
        if ($ex) {
            $costCodeIds[] = $ex->cc_id;
        } else {
            $cc['cc_category_code'] = $cc['cc_category_code'] ?? null;
            $costCodeIds[] = DB::table('cost_code_master')->insertGetId(array_merge($cc, [
                'cc_status' => 1, 'cc_createby' => 1, 'cc_createdate' => now(), 'company_id' => $companyId,
            ]), 'cc_id');
            echo "  + {$cc['cc_full_code']} {$cc['cc_description']}\n";
        }
    }

    // ==========================================
    // 6. Tax Groups (NO company_id, no description)
    // ==========================================
    echo "\nCreating tax groups...\n";
    $taxData = [
        ['name' => 'No Tax', 'percentage' => 0],
        ['name' => 'State Tax', 'percentage' => 6],
        ['name' => 'Full Tax', 'percentage' => 9],
    ];
    $taxGroupIds = [];
    foreach ($taxData as $tg) {
        $ex = DB::table('taxgroup_master')->where('name', $tg['name'])->first();
        if ($ex) {
            $taxGroupIds[] = $ex->id;
        } else {
            $taxGroupIds[] = DB::table('taxgroup_master')->insertGetId(array_merge($tg, ['created_at' => now()]));
            echo "  + {$tg['name']} ({$tg['percentage']}%)\n";
        }
    }

    // ==========================================
    // 7. Projects (actual cols: proj_number, proj_name, proj_address, proj_description, proj_contact)
    // ==========================================
    echo "\nCreating projects...\n";
    $projData = [
        ['proj_number' => 'DOT-001', 'proj_name' => 'Downtown Office Tower',      'proj_address' => '123 Main Street, New York, NY 10001', 'proj_description' => 'New 20-story office building'],
        ['proj_number' => 'HBR-002', 'proj_name' => 'Harbor Bridge Renovation',   'proj_address' => '500 Harbor Drive, Boston, MA 02101',   'proj_description' => 'Bridge structural repair and widening'],
        ['proj_number' => 'RSE-003', 'proj_name' => 'Riverside School Expansion', 'proj_address' => '789 Education Blvd, Chicago, IL 60601', 'proj_description' => 'New wing addition to existing school'],
    ];
    $projectIds = [];
    foreach ($projData as $p) {
        $ex = DB::table('project_master')->where('proj_number', $p['proj_number'])->where('company_id', $companyId)->first();
        if ($ex) {
            $projectIds[] = $ex->proj_id;
        } else {
            $projectIds[] = DB::table('project_master')->insertGetId(array_merge($p, [
                'proj_status' => 1, 'proj_createby' => 1, 'proj_createdate' => now(), 'company_id' => $companyId,
            ]), 'proj_id');
            echo "  + {$p['proj_name']} ({$p['proj_number']})\n";
        }
    }

    // ==========================================
    // 8. Suppliers (actual cols: sup_name, sup_address, sup_contact_person, sup_phone, sup_email, sup_details)
    // ==========================================
    echo "\nCreating suppliers...\n";
    $supData = [
        ['sup_name' => 'Apex Steel Industries',  'sup_email' => 'orders@apexsteel.com',       'sup_phone' => '555-100-2001', 'sup_contact_person' => 'Robert Chen',  'sup_address' => '1000 Industrial Pkwy, Pittsburgh, PA', 'sup_details' => 'Steel fabrication and supply'],
        ['sup_name' => 'ReadyMix Concrete Co',   'sup_email' => 'dispatch@readymix.com',      'sup_phone' => '555-200-3002', 'sup_contact_person' => 'Maria Lopez',  'sup_address' => '2500 Quarry Road, Newark, NJ',         'sup_details' => 'Ready-mix concrete and aggregate'],
        ['sup_name' => 'National Lumber Supply',  'sup_email' => 'sales@nationallumber.com',   'sup_phone' => '555-300-4003', 'sup_contact_person' => 'James Wilson', 'sup_address' => '3800 Timber Lane, Portland, OR',       'sup_details' => 'Lumber and engineered wood products'],
        ['sup_name' => 'ProElectric Wholesale',   'sup_email' => 'info@proelectric.com',       'sup_phone' => '555-400-5004', 'sup_contact_person' => 'Sarah Miller', 'sup_address' => '600 Volt Avenue, Dallas, TX',          'sup_details' => 'Electrical supplies and equipment'],
    ];
    $supplierIds = [];
    foreach ($supData as $s) {
        $ex = DB::table('supplier_master')->where('sup_name', $s['sup_name'])->where('company_id', $companyId)->first();
        if ($ex) {
            $supplierIds[] = $ex->sup_id;
        } else {
            $supplierIds[] = DB::table('supplier_master')->insertGetId(array_merge($s, [
                'sup_status' => 1, 'sup_createby' => 1, 'sup_createdate' => now(), 'company_id' => $companyId,
            ]), 'sup_id');
            echo "  + {$s['sup_name']}\n";
        }
    }

    // ==========================================
    // 9. Items (actual cols: item_code, item_name, item_description, item_ccode_ms, item_cat_ms, item_unit_ms)
    // ==========================================
    echo "\nCreating items...\n";
    $itemData = [
        ['item_code' => 'CONC-001', 'item_name' => 'Ready Mix Concrete 4000 PSI', 'item_description' => '4000 PSI ready-mix',          'item_cat_ms' => $categoryIds[0], 'item_ccode_ms' => $costCodeIds[5], 'item_unit_ms' => $uomIds[2]],
        ['item_code' => 'CONC-002', 'item_name' => 'Rebar #5 Grade 60',           'item_description' => '#5 reinforcing bar 20ft',      'item_cat_ms' => $categoryIds[0], 'item_ccode_ms' => $costCodeIds[6], 'item_unit_ms' => $uomIds[3]],
        ['item_code' => 'CONC-003', 'item_name' => 'Portland Cement Type I',      'item_description' => '94lb bag Portland cement',     'item_cat_ms' => $categoryIds[0], 'item_ccode_ms' => $costCodeIds[5], 'item_unit_ms' => $uomIds[7]],
        ['item_code' => 'STL-001',  'item_name' => 'W12x26 Structural Beam',      'item_description' => 'Wide-flange beam A992 steel',  'item_cat_ms' => $categoryIds[1], 'item_ccode_ms' => $costCodeIds[7], 'item_unit_ms' => $uomIds[3]],
        ['item_code' => 'STL-002',  'item_name' => 'Steel Plate 1/2" x 4x8',     'item_description' => 'A36 steel plate 1/2 inch',     'item_cat_ms' => $categoryIds[1], 'item_ccode_ms' => $costCodeIds[7], 'item_unit_ms' => $uomIds[0]],
        ['item_code' => 'LBR-001',  'item_name' => '2x4x8 SPF Stud',             'item_description' => 'Framing stud SPF 8ft',         'item_cat_ms' => $categoryIds[2], 'item_ccode_ms' => $costCodeIds[3], 'item_unit_ms' => $uomIds[0]],
        ['item_code' => 'LBR-002',  'item_name' => '3/4" Plywood CDX',            'item_description' => '4x8 sheet CDX plywood',        'item_cat_ms' => $categoryIds[2], 'item_ccode_ms' => $costCodeIds[3], 'item_unit_ms' => $uomIds[0]],
        ['item_code' => 'ELEC-001', 'item_name' => '12/2 Romex Wire 250ft',       'item_description' => '12 AWG NM-B 250ft roll',       'item_cat_ms' => $categoryIds[3], 'item_ccode_ms' => $costCodeIds[8], 'item_unit_ms' => $uomIds[1]],
        ['item_code' => 'ELEC-002', 'item_name' => '200A Main Breaker Panel',     'item_description' => '200A 40-space panel',          'item_cat_ms' => $categoryIds[3], 'item_ccode_ms' => $costCodeIds[8], 'item_unit_ms' => $uomIds[0]],
        ['item_code' => 'SAFE-001', 'item_name' => 'Hard Hat - White',            'item_description' => 'OSHA approved hard hat',       'item_cat_ms' => $categoryIds[6], 'item_ccode_ms' => $costCodeIds[0], 'item_unit_ms' => $uomIds[0]],
    ];
    $itemCodes = [];
    foreach ($itemData as $item) {
        $ex = DB::table('item_master')->where('item_code', $item['item_code'])->where('company_id', $companyId)->first();
        if ($ex) {
            $itemCodes[] = $item['item_code'];
        } else {
            DB::table('item_master')->insert(array_merge($item, [
                'item_status' => 1, 'item_createby' => 1, 'item_createdate' => now(), 'company_id' => $companyId,
            ]));
            $itemCodes[] = $item['item_code'];
            echo "  + {$item['item_code']} - {$item['item_name']}\n";
        }
    }

    // ==========================================
    // 10. Supplier Catalog (NO company_id)
    // ==========================================
    echo "\nCreating supplier catalog...\n";
    $catalogEntries = [
        ['supcat_supplier' => $supplierIds[0], 'supcat_item_code' => 'STL-001',  'supcat_sku_no' => 'APX-W1226',  'supcat_uom' => $uomIds[3], 'supcat_price' => 40.50],
        ['supcat_supplier' => $supplierIds[0], 'supcat_item_code' => 'STL-002',  'supcat_sku_no' => 'APX-SP12',   'supcat_uom' => $uomIds[0], 'supcat_price' => 475.00],
        ['supcat_supplier' => $supplierIds[1], 'supcat_item_code' => 'CONC-001', 'supcat_sku_no' => 'RMC-4K',     'supcat_uom' => $uomIds[2], 'supcat_price' => 140.00],
        ['supcat_supplier' => $supplierIds[1], 'supcat_item_code' => 'CONC-002', 'supcat_sku_no' => 'RMC-R5G60',  'supcat_uom' => $uomIds[3], 'supcat_price' => 11.80],
        ['supcat_supplier' => $supplierIds[1], 'supcat_item_code' => 'CONC-003', 'supcat_sku_no' => 'RMC-PC1',    'supcat_uom' => $uomIds[7], 'supcat_price' => 13.50],
        ['supcat_supplier' => $supplierIds[2], 'supcat_item_code' => 'LBR-001',  'supcat_sku_no' => 'NLS-248SPF', 'supcat_uom' => $uomIds[0], 'supcat_price' => 4.95],
        ['supcat_supplier' => $supplierIds[2], 'supcat_item_code' => 'LBR-002',  'supcat_sku_no' => 'NLS-PLY34',  'supcat_uom' => $uomIds[0], 'supcat_price' => 46.00],
        ['supcat_supplier' => $supplierIds[3], 'supcat_item_code' => 'ELEC-001', 'supcat_sku_no' => 'PEW-122NM',  'supcat_uom' => $uomIds[1], 'supcat_price' => 119.50],
        ['supcat_supplier' => $supplierIds[3], 'supcat_item_code' => 'ELEC-002', 'supcat_sku_no' => 'PEW-200MB',  'supcat_uom' => $uomIds[0], 'supcat_price' => 275.00],
    ];
    foreach ($catalogEntries as $entry) {
        $ex = DB::table('supplier_catalog_tab')->where('supcat_supplier', $entry['supcat_supplier'])->where('supcat_item_code', $entry['supcat_item_code'])->first();
        if (!$ex) {
            DB::table('supplier_catalog_tab')->insert(array_merge($entry, [
                'supcat_lastdate' => now()->subDays(rand(1, 30)), 'supcat_details' => 'Catalog price',
                'supcat_createdate' => now(), 'supcat_createby' => 1, 'supcat_modifydate' => now(), 'supcat_modifyby' => 1, 'supcat_status' => 1,
            ]));
            echo "  + {$entry['supcat_sku_no']}\n";
        }
    }

    // ==========================================
    // 11. Purchase Orders + Line Items
    // Actual PO cols: porder_no, porder_project_ms, porder_supplier_ms, porder_address,
    //   porder_delivery_note, porder_description, porder_total_item, porder_total_amount,
    //   porder_delivery_status, porder_status (int), porder_total_tax
    // ==========================================
    echo "\nCreating purchase orders...\n";
    $poData = [
        [
            'header' => ['porder_no' => 'PO-2026-0001', 'porder_project_ms' => $projectIds[0], 'porder_supplier_ms' => $supplierIds[1],
                'porder_address' => '123 Main Street, New York, NY 10001', 'porder_delivery_note' => 'Deliver to loading dock B',
                'porder_description' => 'Foundation pour - Phase 1', 'porder_delivery_status' => 0, 'porder_status' => 0],
            'items' => [
                ['code' => 'CONC-001', 'sku' => 'Ready Mix 4000 PSI',  'qty' => 50,  'price' => 140.00, 'tax' => 621.25],
                ['code' => 'CONC-002', 'sku' => 'Rebar #5 Grade 60',   'qty' => 200, 'price' => 11.80,  'tax' => 209.38],
                ['code' => 'CONC-003', 'sku' => 'Portland Cement',     'qty' => 30,  'price' => 13.50,  'tax' => 35.95],
            ],
        ],
        [
            'header' => ['porder_no' => 'PO-2026-0002', 'porder_project_ms' => $projectIds[1], 'porder_supplier_ms' => $supplierIds[0],
                'porder_address' => '500 Harbor Drive, Boston, MA 02101', 'porder_delivery_note' => 'Crane required for offloading',
                'porder_description' => 'Bridge structural members', 'porder_delivery_status' => 2, 'porder_status' => 1],
            'items' => [
                ['code' => 'STL-001', 'sku' => 'W12x26 Beam',       'qty' => 120, 'price' => 40.50,  'tax' => 431.33],
                ['code' => 'STL-002', 'sku' => 'Steel Plate 1/2"',  'qty' => 8,   'price' => 475.00, 'tax' => 337.25],
            ],
        ],
        [
            'header' => ['porder_no' => 'PO-2026-0003', 'porder_project_ms' => $projectIds[2], 'porder_supplier_ms' => $supplierIds[2],
                'porder_address' => '789 Education Blvd, Chicago, IL 60601', 'porder_delivery_note' => 'Stage near Wing B entrance',
                'porder_description' => 'Framing materials - Wing B', 'porder_delivery_status' => 0, 'porder_status' => 0],
            'items' => [
                ['code' => 'LBR-001', 'sku' => '2x4x8 SPF Stud',   'qty' => 500, 'price' => 4.95,  'tax' => 219.47],
                ['code' => 'LBR-002', 'sku' => '3/4 Plywood CDX',   'qty' => 80,  'price' => 46.00, 'tax' => 326.50],
            ],
        ],
        [
            'header' => ['porder_no' => 'PO-2026-0004', 'porder_project_ms' => $projectIds[0], 'porder_supplier_ms' => $supplierIds[3],
                'porder_address' => '123 Main Street, New York, NY 10001', 'porder_delivery_note' => 'Electrical room floor 2',
                'porder_description' => 'Main electrical rough-in', 'porder_delivery_status' => 1, 'porder_status' => 1],
            'items' => [
                ['code' => 'ELEC-001', 'sku' => '12/2 Romex 250ft', 'qty' => 20,  'price' => 119.50, 'tax' => 212.11],
                ['code' => 'ELEC-002', 'sku' => '200A Panel',       'qty' => 3,   'price' => 275.00, 'tax' => 73.22],
                ['code' => 'SAFE-001', 'sku' => 'Hard Hat White',   'qty' => 25,  'price' => 18.50,  'tax' => 41.05],
            ],
        ],
        [
            'header' => ['porder_no' => 'PO-2026-0005', 'porder_project_ms' => $projectIds[1], 'porder_supplier_ms' => $supplierIds[3],
                'porder_address' => '500 Harbor Drive, Boston, MA 02101', 'porder_delivery_note' => '',
                'porder_description' => 'Safety gear - budget exceeded', 'porder_delivery_status' => 0, 'porder_status' => 3],
            'items' => [
                ['code' => 'SAFE-001', 'sku' => 'Hard Hat White',   'qty' => 100, 'price' => 18.50, 'tax' => 164.19],
            ],
        ],
    ];

    $poIds = [];
    foreach ($poData as $po) {
        $ex = DB::table('purchase_order_master')->where('porder_no', $po['header']['porder_no'])->where('company_id', $companyId)->first();
        if ($ex) {
            $poIds[] = $ex->porder_id;
            echo "  ~ {$po['header']['porder_no']} already exists\n";
            continue;
        }

        $subtotal = 0;
        $totalTax = 0;
        $itemCount = count($po['items']);
        foreach ($po['items'] as $item) {
            $subtotal += $item['qty'] * $item['price'];
            $totalTax += $item['tax'];
        }

        $porderId = DB::table('purchase_order_master')->insertGetId(array_merge($po['header'], [
            'porder_total_item'   => $itemCount,
            'porder_total_amount' => $subtotal,
            'porder_total_tax'    => $totalTax,
            'porder_createby'     => 1,
            'porder_createdate'   => now(),
            'company_id'          => $companyId,
        ]), 'porder_id');
        $poIds[] = $porderId;

        foreach ($po['items'] as $idx => $item) {
            $lineSub = $item['qty'] * $item['price'];
            DB::table('purchase_order_details')->insert([
                'po_detail_autogen'    => $po['header']['porder_no'] . '-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                'po_detail_porder_ms'  => $porderId,
                'po_detail_item'       => $item['code'],
                'po_detail_sku'        => $item['sku'],
                'po_detail_taxcode'    => '',
                'po_detail_quantity'   => $item['qty'],
                'po_detail_unitprice'  => $item['price'],
                'po_detail_subtotal'   => $lineSub,
                'po_detail_taxamount'  => $item['tax'],
                'po_detail_total'      => $lineSub + $item['tax'],
                'po_detail_createdate' => now(),
                'po_detail_status'     => 1,
                'company_id'           => $companyId,
            ]);
        }
        echo "  + {$po['header']['porder_no']} (status={$po['header']['porder_status']}) \$" . number_format($subtotal + $totalTax, 2) . "\n";
    }

    // ==========================================
    // 12. Receive Orders (actual cols: rorder_slip_no, rorder_infoset, rorder_totalitem, rorder_totalamount)
    // ==========================================
    echo "\nCreating receive orders...\n";

    if (isset($poIds[1]) && !DB::table('receive_order_master')->where('rorder_slip_no', 'RO-2026-0001')->where('company_id', $companyId)->exists()) {
        $roId = DB::table('receive_order_master')->insertGetId([
            'rorder_porder_ms' => $poIds[1], 'rorder_slip_no' => 'RO-2026-0001', 'rorder_date' => '2026-02-20',
            'rorder_infoset' => 'Partial delivery - beams only, plates on backorder',
            'rorder_totalitem' => 1, 'rorder_totalamount' => 3240.00,
            'rorder_status' => 1, 'rorder_createby' => 1, 'rorder_createdate' => now(), 'company_id' => $companyId,
        ], 'rorder_id');
        DB::table('receive_order_details')->insert([
            'ro_detail_rorder_ms' => $roId, 'ro_detail_item' => 'STL-001', 'ro_detail_quantity' => 80,
            'ro_detail_createdate' => now(), 'ro_detail_status' => 1, 'company_id' => $companyId,
        ]);
        echo "  + RO-2026-0001 (partial for PO-2026-0002)\n";
    }

    if (isset($poIds[3]) && !DB::table('receive_order_master')->where('rorder_slip_no', 'RO-2026-0002')->where('company_id', $companyId)->exists()) {
        $roId = DB::table('receive_order_master')->insertGetId([
            'rorder_porder_ms' => $poIds[3], 'rorder_slip_no' => 'RO-2026-0002', 'rorder_date' => '2026-01-25',
            'rorder_infoset' => 'Full delivery received and inspected',
            'rorder_totalitem' => 3, 'rorder_totalamount' => 3541.88,
            'rorder_status' => 1, 'rorder_createby' => 1, 'rorder_createdate' => now(), 'company_id' => $companyId,
        ], 'rorder_id');
        foreach ([['ELEC-001', 20], ['ELEC-002', 3], ['SAFE-001', 25]] as $ri) {
            DB::table('receive_order_details')->insert([
                'ro_detail_rorder_ms' => $roId, 'ro_detail_item' => $ri[0], 'ro_detail_quantity' => $ri[1],
                'ro_detail_createdate' => now(), 'ro_detail_status' => 1, 'company_id' => $companyId,
            ]);
        }
        echo "  + RO-2026-0002 (full for PO-2026-0004)\n";
    }

    // ==========================================
    // 13. Budgets
    // ==========================================
    echo "\nCreating budgets...\n";
    $budgets = [
        ['budget_project_id' => $projectIds[0], 'budget_cost_code_id' => $costCodeIds[5], 'budget_original_amount' => 150000, 'budget_revised_amount' => 155000, 'budget_committed_amount' => 9765.00, 'budget_spent_amount' => 0],
        ['budget_project_id' => $projectIds[0], 'budget_cost_code_id' => $costCodeIds[6], 'budget_original_amount' => 45000, 'budget_revised_amount' => 45000, 'budget_committed_amount' => 2569.38, 'budget_spent_amount' => 0],
        ['budget_project_id' => $projectIds[0], 'budget_cost_code_id' => $costCodeIds[8], 'budget_original_amount' => 85000, 'budget_revised_amount' => 90000, 'budget_committed_amount' => 3216.38, 'budget_spent_amount' => 3216.38],
        ['budget_project_id' => $projectIds[1], 'budget_cost_code_id' => $costCodeIds[7], 'budget_original_amount' => 250000, 'budget_revised_amount' => 275000, 'budget_committed_amount' => 8628.58, 'budget_spent_amount' => 3240.00],
        ['budget_project_id' => $projectIds[2], 'budget_cost_code_id' => $costCodeIds[3], 'budget_original_amount' => 75000, 'budget_revised_amount' => 75000, 'budget_committed_amount' => 6220.97, 'budget_spent_amount' => 0],
    ];
    foreach ($budgets as $b) {
        $ex = DB::table('budget_master')->where('budget_project_id', $b['budget_project_id'])->where('budget_cost_code_id', $b['budget_cost_code_id'])->where('company_id', $companyId)->first();
        if (!$ex) {
            DB::table('budget_master')->insert(array_merge($b, [
                'budget_fiscal_year' => 2026, 'budget_status' => 1,
                'budget_created_by' => 1, 'budget_created_at' => now(), 'company_id' => $companyId,
            ]));
            echo "  + Budget entry\n";
        }
    }

    // ==========================================
    // 14. Supplier Portal User
    // ==========================================
    echo "\nCreating supplier portal user...\n";
    if (!DB::table('supplier_users')->where('email', 'supplier@apexsteel.com')->exists()) {
        DB::table('supplier_users')->insert([
            'name' => 'Robert Chen (Apex Steel)', 'email' => 'supplier@apexsteel.com', 'phone' => '555-100-2001',
            'password' => $password, 'supplier_id' => $supplierIds[0], 'company_id' => $companyId,
            'status' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
        echo "  + supplier@apexsteel.com\n";
    }

    // ==========================================
    echo "\n============================================\n";
    echo "  SEEDING COMPLETE!\n";
    echo "============================================\n\n";
    echo "  LOGIN CREDENTIALS (password: admin123)\n";
    echo "  ----------------------------------------\n";
    echo "  ADMIN PORTAL (http://localhost:8000)\n";
    echo "  Super Admin    : superadmin@demo.com  (u_type=1, full access)\n";
    echo "  Company Admin  : admin@demo.com       (u_type=2, company admin)\n";
    echo "  Project Manager: manager@demo.com     (u_type=3, project mgmt)\n";
    echo "  Viewer         : viewer@demo.com      (u_type=4, read-only)\n";
    echo "  Regular User   : user@demo.com        (u_type=0, basic access)\n\n";
    echo "  SUPPLIER PORTAL (http://localhost:8000/supplier)\n";
    echo "  Supplier User  : supplier@apexsteel.com\n\n";
    echo "  All passwords  : admin123\n";
    echo "============================================\n\n";
    echo "  SAMPLE DATA SUMMARY:\n";
    echo "  - 1 Company, 5 Admin Users + 1 Supplier User\n";
    echo "  - 3 Projects, 4 Suppliers, 10 Items\n";
    echo "  - 8 Categories, 9 Cost Codes, 3 Tax Groups, 8 UOMs\n";
    echo "  - 9 Supplier Catalog entries\n";
    echo "  - 5 Purchase Orders (13 line items)\n";
    echo "  - 2 Receive Orders (4 detail rows)\n";
    echo "  - 5 Budget entries\n";
    echo "============================================\n";

} catch (\Exception $e) {
    echo "\n\nERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
