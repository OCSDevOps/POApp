<?php

namespace Database\Seeders\Legacy;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Seed the application with comprehensive sample data for frontend testing.
     * Creates users at all access levels, reference data, and transactional data.
     */
    public function run()
    {
        $this->command->info('=== POApp Sample Data Seeder ===');
        $this->command->info('');

        // 1. Company
        $companyId = $this->seedCompany();

        // 2. Users (all levels)
        $this->seedUsers($companyId);

        // 3. Reference Data
        $uomIds = $this->seedUnitsOfMeasure();
        $categoryIds = $this->seedItemCategories($companyId);
        $costCodeIds = $this->seedCostCodes($companyId);
        $taxGroupIds = $this->seedTaxGroups();

        // 4. Projects
        $projectIds = $this->seedProjects($companyId);

        // 5. Suppliers
        $supplierIds = $this->seedSuppliers($companyId);

        // 6. Items
        $itemCodes = $this->seedItems($companyId, $categoryIds, $costCodeIds, $uomIds);

        // 7. Supplier Catalog
        $this->seedSupplierCatalog($supplierIds, $itemCodes, $uomIds);

        // 8. Purchase Orders with Items
        $poIds = $this->seedPurchaseOrders($companyId, $projectIds, $supplierIds, $itemCodes, $taxGroupIds);

        // 9. Receive Orders
        $this->seedReceiveOrders($companyId, $poIds, $itemCodes);

        // 10. Budgets
        $this->seedBudgets($companyId, $projectIds, $costCodeIds);

        // 11. Supplier Portal User
        $this->seedSupplierUser($companyId, $supplierIds);

        $this->command->info('');
        $this->command->info('=== Seeding Complete ===');
        $this->printCredentials();
    }

    protected function seedCompany(): int
    {
        $this->command->info('Creating company...');

        // Check if company already exists
        $existing = DB::table('companies')->where('name', 'Demo Construction Co')->first();
        if ($existing) {
            $this->command->warn('  Company "Demo Construction Co" already exists (id=' . $existing->id . '). Skipping.');
            return $existing->id;
        }

        return DB::table('companies')->insertGetId([
            'name' => 'Demo Construction Co',
            'subdomain' => 'demo',
            'status' => 1,
            'settings' => json_encode([
                'currency' => 'USD',
                'timezone' => 'America/New_York',
                'date_format' => 'm/d/Y',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function seedUsers(int $companyId): void
    {
        $this->command->info('Creating users at all access levels...');

        $users = [
            [
                'name'       => 'Super Admin',
                'email'      => 'superadmin@demo.com',
                'username'   => 'superadmin',
                'password'   => Hash::make('admin123'),
                'company_id' => $companyId,
                'u_type'     => 1,
                'u_status'   => 1,
            ],
            [
                'name'       => 'Company Admin',
                'email'      => 'admin@demo.com',
                'username'   => 'companyadmin',
                'password'   => Hash::make('admin123'),
                'company_id' => $companyId,
                'u_type'     => 2,
                'u_status'   => 1,
            ],
            [
                'name'       => 'Project Manager',
                'email'      => 'manager@demo.com',
                'username'   => 'manager',
                'password'   => Hash::make('admin123'),
                'company_id' => $companyId,
                'u_type'     => 3,
                'u_status'   => 1,
            ],
            [
                'name'       => 'Viewer User',
                'email'      => 'viewer@demo.com',
                'username'   => 'viewer',
                'password'   => Hash::make('admin123'),
                'company_id' => $companyId,
                'u_type'     => 4,
                'u_status'   => 1,
            ],
            [
                'name'       => 'Regular User',
                'email'      => 'user@demo.com',
                'username'   => 'user',
                'password'   => Hash::make('admin123'),
                'company_id' => $companyId,
                'u_type'     => 0,
                'u_status'   => 1,
            ],
        ];

        foreach ($users as $user) {
            $exists = DB::table('users')->where('email', $user['email'])->exists();
            if (!$exists) {
                DB::table('users')->insert(array_merge($user, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $this->command->info("  + {$user['name']} ({$user['email']}) - u_type={$user['u_type']}");
            } else {
                $this->command->warn("  ~ {$user['email']} already exists, skipping.");
            }
        }
    }

    protected function seedUnitsOfMeasure(): array
    {
        $this->command->info('Creating units of measure...');

        $uoms = [
            ['uom_name' => 'Each',          'uom_detail' => 'Individual unit'],
            ['uom_name' => 'Box',            'uom_detail' => 'Box/case'],
            ['uom_name' => 'Cubic Yard',     'uom_detail' => 'CY'],
            ['uom_name' => 'Linear Foot',    'uom_detail' => 'LF'],
            ['uom_name' => 'Square Foot',    'uom_detail' => 'SF'],
            ['uom_name' => 'Ton',            'uom_detail' => 'Short ton (2000 lbs)'],
            ['uom_name' => 'Gallon',         'uom_detail' => 'US Gallon'],
            ['uom_name' => 'Bag',            'uom_detail' => 'Bag/sack'],
        ];

        $ids = [];
        foreach ($uoms as $uom) {
            $existing = DB::table('unit_of_measure_tab')->where('uom_name', $uom['uom_name'])->first();
            if ($existing) {
                $ids[] = $existing->uom_id;
            } else {
                $ids[] = DB::table('unit_of_measure_tab')->insertGetId(array_merge($uom, [
                    'uom_createdate' => now(),
                    'uom_createby'   => 1,
                    'uom_status'     => 1,
                ]), 'uom_id');
            }
        }

        return $ids;
    }

    protected function seedItemCategories(int $companyId): array
    {
        $this->command->info('Creating item categories...');

        $categories = [
            'Concrete & Masonry',
            'Structural Steel',
            'Lumber & Wood',
            'Electrical',
            'Plumbing',
            'HVAC',
            'Safety Equipment',
            'Rental Equipment',
        ];

        $ids = [];
        foreach ($categories as $catName) {
            $existing = DB::table('item_category_tab')
                ->where('icat_name', $catName)
                ->where('company_id', $companyId)
                ->first();
            if ($existing) {
                $ids[] = $existing->icat_id;
            } else {
                $ids[] = DB::table('item_category_tab')->insertGetId([
                    'icat_name'        => $catName,
                    'icat_description' => $catName . ' materials and supplies',
                    'icat_status'      => 1,
                    'icat_created_by'  => 1,
                    'icat_created_at'  => now(),
                    'company_id'       => $companyId,
                ], 'icat_id');
            }
        }

        return $ids;
    }

    protected function seedCostCodes(int $companyId): array
    {
        $this->command->info('Creating cost codes...');

        $costCodes = [
            // Parent codes (level 1)
            ['cc_no' => '01', 'cc_description' => 'General Requirements',  'cc_parent_code' => '01', 'cc_category_code' => null, 'cc_subcategory_code' => null, 'cc_level' => 1, 'cc_full_code' => '01'],
            ['cc_no' => '03', 'cc_description' => 'Concrete',              'cc_parent_code' => '03', 'cc_category_code' => null, 'cc_subcategory_code' => null, 'cc_level' => 1, 'cc_full_code' => '03'],
            ['cc_no' => '05', 'cc_description' => 'Metals',                'cc_parent_code' => '05', 'cc_category_code' => null, 'cc_subcategory_code' => null, 'cc_level' => 1, 'cc_full_code' => '05'],
            ['cc_no' => '06', 'cc_description' => 'Wood, Plastics, Composites', 'cc_parent_code' => '06', 'cc_category_code' => null, 'cc_subcategory_code' => null, 'cc_level' => 1, 'cc_full_code' => '06'],
            ['cc_no' => '26', 'cc_description' => 'Electrical',            'cc_parent_code' => '26', 'cc_category_code' => null, 'cc_subcategory_code' => null, 'cc_level' => 1, 'cc_full_code' => '26'],
            // Category codes (level 2)
            ['cc_no' => '03-10', 'cc_description' => 'Cast-in-Place Concrete', 'cc_parent_code' => '03', 'cc_category_code' => '10', 'cc_subcategory_code' => null, 'cc_level' => 2, 'cc_full_code' => '03-10'],
            ['cc_no' => '03-20', 'cc_description' => 'Reinforcing Steel',     'cc_parent_code' => '03', 'cc_category_code' => '20', 'cc_subcategory_code' => null, 'cc_level' => 2, 'cc_full_code' => '03-20'],
            ['cc_no' => '05-10', 'cc_description' => 'Structural Steel Framing', 'cc_parent_code' => '05', 'cc_category_code' => '10', 'cc_subcategory_code' => null, 'cc_level' => 2, 'cc_full_code' => '05-10'],
            ['cc_no' => '26-10', 'cc_description' => 'Medium-Voltage Electrical', 'cc_parent_code' => '26', 'cc_category_code' => '10', 'cc_subcategory_code' => null, 'cc_level' => 2, 'cc_full_code' => '26-10'],
        ];

        $ids = [];
        foreach ($costCodes as $cc) {
            $existing = DB::table('cost_code_master')
                ->where('cc_full_code', $cc['cc_full_code'])
                ->where('company_id', $companyId)
                ->first();
            if ($existing) {
                $ids[] = $existing->cc_id;
            } else {
                $ids[] = DB::table('cost_code_master')->insertGetId(array_merge($cc, [
                    'cc_status'     => 1,
                    'cc_created_by' => 1,
                    'cc_created_at' => now(),
                    'company_id'    => $companyId,
                ]), 'cc_id');
            }
        }

        return $ids;
    }

    protected function seedTaxGroups(): array
    {
        $this->command->info('Creating tax groups...');

        $groups = [
            ['name' => 'No Tax',     'percentage' => 0.00,  'description' => 'Tax exempt'],
            ['name' => 'State Tax',  'percentage' => 6.25,  'description' => 'State sales tax'],
            ['name' => 'Full Tax',   'percentage' => 8.875, 'description' => 'State + local combined tax'],
        ];

        $ids = [];
        foreach ($groups as $tg) {
            $existing = DB::table('taxgroup_master')->where('name', $tg['name'])->first();
            if ($existing) {
                $ids[] = $existing->id;
            } else {
                $ids[] = DB::table('taxgroup_master')->insertGetId(array_merge($tg, [
                    'created_at' => now(),
                ]));
            }
        }

        return $ids;
    }

    protected function seedProjects(int $companyId): array
    {
        $this->command->info('Creating projects...');

        $projects = [
            [
                'proj_name'    => 'Downtown Office Tower',
                'proj_number'  => 'DOT-001',
                'proj_address' => '123 Main Street, New York, NY 10001',
                'proj_country' => 'USA',
                'proj_start_date' => '2026-01-15',
                'proj_end_date'   => '2027-06-30',
            ],
            [
                'proj_name'    => 'Harbor Bridge Renovation',
                'proj_number'  => 'HBR-002',
                'proj_address' => '500 Harbor Drive, Boston, MA 02101',
                'proj_country' => 'USA',
                'proj_start_date' => '2026-03-01',
                'proj_end_date'   => '2026-12-31',
            ],
            [
                'proj_name'    => 'Riverside School Expansion',
                'proj_number'  => 'RSE-003',
                'proj_address' => '789 Education Blvd, Chicago, IL 60601',
                'proj_country' => 'USA',
                'proj_start_date' => '2026-02-01',
                'proj_end_date'   => '2027-01-31',
            ],
        ];

        $ids = [];
        foreach ($projects as $proj) {
            $existing = DB::table('project_master')
                ->where('proj_number', $proj['proj_number'])
                ->where('company_id', $companyId)
                ->first();
            if ($existing) {
                $ids[] = $existing->proj_id;
            } else {
                $ids[] = DB::table('project_master')->insertGetId(array_merge($proj, [
                    'proj_status'     => 1,
                    'proj_created_by' => 1,
                    'proj_created_at' => now(),
                    'company_id'      => $companyId,
                ]), 'proj_id');
            }
        }

        return $ids;
    }

    protected function seedSuppliers(int $companyId): array
    {
        $this->command->info('Creating suppliers...');

        $suppliers = [
            [
                'sup_name'           => 'Apex Steel Industries',
                'sup_code'           => 'APEX-001',
                'sup_email'          => 'orders@apexsteel.com',
                'sup_phone'          => '555-100-2001',
                'sup_contact_person' => 'Robert Chen',
                'sup_address'        => '1000 Industrial Pkwy',
                'sup_city'           => 'Pittsburgh',
                'sup_state'          => 'PA',
                'sup_zip'            => '15201',
            ],
            [
                'sup_name'           => 'ReadyMix Concrete Co',
                'sup_code'           => 'RMC-002',
                'sup_email'          => 'dispatch@readymix.com',
                'sup_phone'          => '555-200-3002',
                'sup_contact_person' => 'Maria Lopez',
                'sup_address'        => '2500 Quarry Road',
                'sup_city'           => 'Newark',
                'sup_state'          => 'NJ',
                'sup_zip'            => '07102',
            ],
            [
                'sup_name'           => 'National Lumber Supply',
                'sup_code'           => 'NLS-003',
                'sup_email'          => 'sales@nationallumber.com',
                'sup_phone'          => '555-300-4003',
                'sup_contact_person' => 'James Wilson',
                'sup_address'        => '3800 Timber Lane',
                'sup_city'           => 'Portland',
                'sup_state'          => 'OR',
                'sup_zip'            => '97201',
            ],
            [
                'sup_name'           => 'ProElectric Wholesale',
                'sup_code'           => 'PEW-004',
                'sup_email'          => 'info@proelectric.com',
                'sup_phone'          => '555-400-5004',
                'sup_contact_person' => 'Sarah Miller',
                'sup_address'        => '600 Volt Avenue',
                'sup_city'           => 'Dallas',
                'sup_state'          => 'TX',
                'sup_zip'            => '75201',
            ],
        ];

        $ids = [];
        foreach ($suppliers as $sup) {
            $existing = DB::table('supplier_master')
                ->where('sup_code', $sup['sup_code'])
                ->where('company_id', $companyId)
                ->first();
            if ($existing) {
                $ids[] = $existing->sup_id;
            } else {
                $ids[] = DB::table('supplier_master')->insertGetId(array_merge($sup, [
                    'sup_status'     => 1,
                    'sup_created_by' => 1,
                    'sup_created_at' => now(),
                    'company_id'     => $companyId,
                ]), 'sup_id');
            }
        }

        return $ids;
    }

    protected function seedItems(int $companyId, array $categoryIds, array $costCodeIds, array $uomIds): array
    {
        $this->command->info('Creating items...');

        // categoryIds: [Concrete, Steel, Lumber, Electrical, Plumbing, HVAC, Safety, Rental]
        // costCodeIds: [01-Gen, 03-Conc, 05-Metal, 06-Wood, 26-Elec, 03-10, 03-20, 05-10, 26-10]
        // uomIds: [Each, Box, CY, LF, SF, Ton, Gallon, Bag]
        $items = [
            ['item_code' => 'CONC-001', 'item_name' => 'Ready Mix Concrete 4000 PSI', 'item_description' => '4000 PSI ready-mix concrete', 'item_cat_ms' => $categoryIds[0] ?? 1, 'item_ccode_ms' => $costCodeIds[5] ?? 1, 'item_uom_ms' => $uomIds[2] ?? 1, 'item_price' => 145.00],
            ['item_code' => 'CONC-002', 'item_name' => 'Rebar #5 Grade 60',          'item_description' => '#5 reinforcing bar, grade 60, 20ft lengths', 'item_cat_ms' => $categoryIds[0] ?? 1, 'item_ccode_ms' => $costCodeIds[6] ?? 1, 'item_uom_ms' => $uomIds[3] ?? 1, 'item_price' => 12.50],
            ['item_code' => 'CONC-003', 'item_name' => 'Portland Cement Type I',      'item_description' => '94lb bag Portland cement', 'item_cat_ms' => $categoryIds[0] ?? 1, 'item_ccode_ms' => $costCodeIds[5] ?? 1, 'item_uom_ms' => $uomIds[7] ?? 1, 'item_price' => 14.75],
            ['item_code' => 'STL-001',  'item_name' => 'W12x26 Structural Beam',      'item_description' => 'Wide-flange beam W12x26, A992 steel', 'item_cat_ms' => $categoryIds[1] ?? 1, 'item_ccode_ms' => $costCodeIds[7] ?? 1, 'item_uom_ms' => $uomIds[3] ?? 1, 'item_price' => 42.00],
            ['item_code' => 'STL-002',  'item_name' => 'Steel Plate 1/2" x 4\' x 8\'', 'item_description' => 'A36 steel plate, 1/2 inch thick', 'item_cat_ms' => $categoryIds[1] ?? 1, 'item_ccode_ms' => $costCodeIds[7] ?? 1, 'item_uom_ms' => $uomIds[0] ?? 1, 'item_price' => 485.00],
            ['item_code' => 'LBR-001',  'item_name' => '2x4x8 SPF Stud',              'item_description' => 'Spruce-Pine-Fir stud, 2x4, 8ft', 'item_cat_ms' => $categoryIds[2] ?? 1, 'item_ccode_ms' => $costCodeIds[3] ?? 1, 'item_uom_ms' => $uomIds[0] ?? 1, 'item_price' => 5.25],
            ['item_code' => 'LBR-002',  'item_name' => '3/4" Plywood CDX',            'item_description' => '4x8 sheet, 3/4 inch CDX plywood', 'item_cat_ms' => $categoryIds[2] ?? 1, 'item_ccode_ms' => $costCodeIds[3] ?? 1, 'item_uom_ms' => $uomIds[0] ?? 1, 'item_price' => 48.50],
            ['item_code' => 'ELEC-001', 'item_name' => '12/2 Romex Wire 250ft',       'item_description' => '12 AWG, 2-conductor NM-B cable', 'item_cat_ms' => $categoryIds[3] ?? 1, 'item_ccode_ms' => $costCodeIds[8] ?? 1, 'item_uom_ms' => $uomIds[1] ?? 1, 'item_price' => 125.00],
            ['item_code' => 'ELEC-002', 'item_name' => '200A Main Breaker Panel',     'item_description' => '200 amp main breaker panel, 40-space', 'item_cat_ms' => $categoryIds[3] ?? 1, 'item_ccode_ms' => $costCodeIds[8] ?? 1, 'item_uom_ms' => $uomIds[0] ?? 1, 'item_price' => 285.00],
            ['item_code' => 'SAFE-001', 'item_name' => 'Hard Hat - White',            'item_description' => 'OSHA-approved hard hat, ratchet suspension', 'item_cat_ms' => $categoryIds[6] ?? 1, 'item_ccode_ms' => $costCodeIds[0] ?? 1, 'item_uom_ms' => $uomIds[0] ?? 1, 'item_price' => 18.50],
        ];

        $codes = [];
        foreach ($items as $item) {
            $existing = DB::table('item_master')
                ->where('item_code', $item['item_code'])
                ->where('company_id', $companyId)
                ->first();
            if ($existing) {
                $codes[] = $item['item_code'];
            } else {
                DB::table('item_master')->insertGetId(array_merge($item, [
                    'item_is_rentable' => 0,
                    'item_status'      => 1,
                    'item_created_by'  => 1,
                    'item_created_at'  => now(),
                    'company_id'       => $companyId,
                ]), 'item_id');
                $codes[] = $item['item_code'];
            }
        }

        return $codes;
    }

    protected function seedSupplierCatalog(array $supplierIds, array $itemCodes, array $uomIds): void
    {
        $this->command->info('Creating supplier catalog entries...');

        // Apex Steel sells steel and concrete items
        $catalogEntries = [
            ['supcat_supplier' => $supplierIds[0], 'supcat_item_code' => $itemCodes[3] ?? 'STL-001', 'supcat_sku_no' => 'APX-W1226',  'supcat_uom' => $uomIds[3] ?? 1, 'supcat_price' => 40.50],
            ['supcat_supplier' => $supplierIds[0], 'supcat_item_code' => $itemCodes[4] ?? 'STL-002', 'supcat_sku_no' => 'APX-SP12',   'supcat_uom' => $uomIds[0] ?? 1, 'supcat_price' => 475.00],
            // ReadyMix sells concrete items
            ['supcat_supplier' => $supplierIds[1], 'supcat_item_code' => $itemCodes[0] ?? 'CONC-001', 'supcat_sku_no' => 'RMC-4K',   'supcat_uom' => $uomIds[2] ?? 1, 'supcat_price' => 140.00],
            ['supcat_supplier' => $supplierIds[1], 'supcat_item_code' => $itemCodes[1] ?? 'CONC-002', 'supcat_sku_no' => 'RMC-R5G60', 'supcat_uom' => $uomIds[3] ?? 1, 'supcat_price' => 11.80],
            ['supcat_supplier' => $supplierIds[1], 'supcat_item_code' => $itemCodes[2] ?? 'CONC-003', 'supcat_sku_no' => 'RMC-PC1',   'supcat_uom' => $uomIds[7] ?? 1, 'supcat_price' => 13.50],
            // National Lumber sells lumber items
            ['supcat_supplier' => $supplierIds[2], 'supcat_item_code' => $itemCodes[5] ?? 'LBR-001', 'supcat_sku_no' => 'NLS-248SPF', 'supcat_uom' => $uomIds[0] ?? 1, 'supcat_price' => 4.95],
            ['supcat_supplier' => $supplierIds[2], 'supcat_item_code' => $itemCodes[6] ?? 'LBR-002', 'supcat_sku_no' => 'NLS-PLY34',  'supcat_uom' => $uomIds[0] ?? 1, 'supcat_price' => 46.00],
            // ProElectric sells electrical items
            ['supcat_supplier' => $supplierIds[3], 'supcat_item_code' => $itemCodes[7] ?? 'ELEC-001', 'supcat_sku_no' => 'PEW-122NM', 'supcat_uom' => $uomIds[1] ?? 1, 'supcat_price' => 119.50],
            ['supcat_supplier' => $supplierIds[3], 'supcat_item_code' => $itemCodes[8] ?? 'ELEC-002', 'supcat_sku_no' => 'PEW-200MB', 'supcat_uom' => $uomIds[0] ?? 1, 'supcat_price' => 275.00],
        ];

        foreach ($catalogEntries as $entry) {
            $existing = DB::table('supplier_catalog_tab')
                ->where('supcat_supplier', $entry['supcat_supplier'])
                ->where('supcat_item_code', $entry['supcat_item_code'])
                ->first();
            if (!$existing) {
                DB::table('supplier_catalog_tab')->insert(array_merge($entry, [
                    'supcat_lastdate'   => now()->subDays(rand(1, 30)),
                    'supcat_details'    => 'Catalog price',
                    'supcat_createdate' => now(),
                    'supcat_createby'   => 1,
                    'supcat_status'     => 1,
                ]));
            }
        }
    }

    protected function seedPurchaseOrders(int $companyId, array $projectIds, array $supplierIds, array $itemCodes, array $taxGroupIds): array
    {
        $this->command->info('Creating purchase orders with line items...');

        $poData = [
            // PO 1: Concrete for Downtown Office Tower (pending)
            [
                'header' => [
                    'porder_no'              => 'PO-2026-0001',
                    'porder_project_ms'      => $projectIds[0],
                    'porder_supplier_ms'     => $supplierIds[1],
                    'porder_description'     => 'Standard - Foundation pour - Phase 1',
                    'porder_createdate'      => '2026-01-20',
                    'porder_delivery_date'   => '2026-02-15',
                    'porder_delivery_status' => '0',
                    'porder_status'  => 1,
                ],
                'items' => [
                    ['code' => $itemCodes[0], 'sku' => 'Ready Mix 4000 PSI',  'qty' => 50,  'price' => 140.00, 'tax' => 621.25],
                    ['code' => $itemCodes[1], 'sku' => 'Rebar #5 Grade 60',   'qty' => 200, 'price' => 11.80,  'tax' => 209.38],
                    ['code' => $itemCodes[2], 'sku' => 'Portland Cement',      'qty' => 30,  'price' => 13.50,  'tax' => 35.95],
                ],
            ],
            // PO 2: Steel for Harbor Bridge (approved)
            [
                'header' => [
                    'porder_no'              => 'PO-2026-0002',
                    'porder_project_ms'      => $projectIds[1],
                    'porder_supplier_ms'     => $supplierIds[0],
                    'porder_description'     => 'Standard - Bridge structural members - main span',
                    'porder_createdate'      => '2026-02-01',
                    'porder_delivery_date'   => '2026-03-15',
                    'porder_delivery_status' => '2',
                    'porder_status'  => 1,
                ],
                'items' => [
                    ['code' => $itemCodes[3], 'sku' => 'W12x26 Beam',      'qty' => 120, 'price' => 40.50,  'tax' => 431.33],
                    ['code' => $itemCodes[4], 'sku' => 'Steel Plate 1/2"', 'qty' => 8,   'price' => 475.00, 'tax' => 337.25],
                ],
            ],
            // PO 3: Lumber for School Expansion (submitted)
            [
                'header' => [
                    'porder_no'              => 'PO-2026-0003',
                    'porder_project_ms'      => $projectIds[2],
                    'porder_supplier_ms'     => $supplierIds[2],
                    'porder_description'     => 'Standard - Framing materials - Wing B',
                    'porder_createdate'      => '2026-02-05',
                    'porder_delivery_date'   => '2026-02-20',
                    'porder_delivery_status' => '0',
                    'porder_status'  => 1,
                ],
                'items' => [
                    ['code' => $itemCodes[5], 'sku' => '2x4x8 SPF Stud',  'qty' => 500,  'price' => 4.95,  'tax' => 219.47],
                    ['code' => $itemCodes[6], 'sku' => '3/4 Plywood CDX',  'qty' => 80,   'price' => 46.00, 'tax' => 326.50],
                ],
            ],
            // PO 4: Electrical for Downtown Office (approved, fully received)
            [
                'header' => [
                    'porder_no'              => 'PO-2026-0004',
                    'porder_project_ms'      => $projectIds[0],
                    'porder_supplier_ms'     => $supplierIds[3],
                    'porder_description'     => 'Standard - Main electrical rough-in',
                    'porder_createdate'      => '2026-01-10',
                    'porder_delivery_date'   => '2026-01-25',
                    'porder_delivery_status' => '1',
                    'porder_status'  => 1,
                ],
                'items' => [
                    ['code' => $itemCodes[7], 'sku' => '12/2 Romex 250ft', 'qty' => 20,  'price' => 119.50, 'tax' => 212.11],
                    ['code' => $itemCodes[8], 'sku' => '200A Panel',       'qty' => 3,   'price' => 275.00, 'tax' => 73.22],
                    ['code' => $itemCodes[9], 'sku' => 'Hard Hat White',   'qty' => 25,  'price' => 18.50,  'tax' => 41.05],
                ],
            ],
            // PO 5: Safety equipment (rejected)
            [
                'header' => [
                    'porder_no'              => 'PO-2026-0005',
                    'porder_project_ms'      => $projectIds[1],
                    'porder_supplier_ms'     => $supplierIds[3],
                    'porder_description'     => 'Standard - Budget exceeded - resubmit with reduced quantities',
                    'porder_createdate'      => '2026-02-03',
                    'porder_delivery_date'   => '2026-02-10',
                    'porder_delivery_status' => '0',
                    'porder_status'  => 0,
                ],
                'items' => [
                    ['code' => $itemCodes[9], 'sku' => 'Hard Hat White',   'qty' => 100, 'price' => 18.50, 'tax' => 164.19],
                ],
            ],
        ];

        $poIds = [];
        foreach ($poData as $po) {
            $existing = DB::table('purchase_order_master')
                ->where('porder_no', $po['header']['porder_no'])
                ->where('company_id', $companyId)
                ->first();

            if ($existing) {
                $poIds[] = $existing->porder_id;
                continue;
            }

            // Calculate totals
            $subtotal = 0;
            $totalTax = 0;
            foreach ($po['items'] as $item) {
                $lineSubtotal = $item['qty'] * $item['price'];
                $subtotal += $lineSubtotal;
                $totalTax += $item['tax'];
            }

            $porderId = DB::table('purchase_order_master')->insertGetId(array_merge($po['header'], [
                'porder_total_amount' => $subtotal,
                'porder_total_tax'    => $totalTax,
                'porder_createby'     => 1,
                'company_id'          => $companyId,
            ]), 'porder_id');

            $poIds[] = $porderId;

            // Insert line items
            foreach ($po['items'] as $idx => $item) {
                $lineSubtotal = $item['qty'] * $item['price'];
                DB::table('purchase_order_details')->insert([
                    'po_detail_autogen'    => $po['header']['porder_no'] . '-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                    'po_detail_porder_ms'  => $porderId,
                    'po_detail_item'       => $item['code'],
                    'po_detail_sku'        => $item['sku'],
                    'po_detail_taxcode'    => null,
                    'po_detail_quantity'   => $item['qty'],
                    'po_detail_unitprice'  => $item['price'],
                    'po_detail_subtotal'   => $lineSubtotal,
                    'po_detail_taxamount'  => $item['tax'],
                    'po_detail_total'      => $lineSubtotal + $item['tax'],
                    'po_detail_createdate' => now(),
                    'po_detail_status'     => 1,
                    'company_id'           => $companyId,
                ]);
            }

            $statusLabel = $po['header']['porder_status'] == 1 ? 'active' : 'inactive';
            $this->command->info("  + {$po['header']['porder_no']} ({$statusLabel}) - \${$subtotal}");
        }

        return $poIds;
    }

    protected function seedReceiveOrders(int $companyId, array $poIds, array $itemCodes): void
    {
        $this->command->info('Creating receive orders...');

        // Receive order for PO-2026-0002 (partially received steel)
        if (isset($poIds[1])) {
            $existing = DB::table('receive_order_master')
                ->where('rorder_slip_no', 'RO-2026-0001')
                ->where('company_id', $companyId)
                ->first();

            if (!$existing) {
                $roId = DB::table('receive_order_master')->insertGetId([
                    'rorder_slip_no'     => 'RO-2026-0001',
                    'rorder_porder_ms'   => $poIds[1],
                    'rorder_date'        => '2026-02-20',
                    'rorder_infoset'     => 'Partial delivery - beams only, plates on backorder',
                    'rorder_status'      => 1,
                    'rorder_createby'    => 1,
                    'rorder_createdate'  => now(),
                    'company_id'         => $companyId,
                ], 'rorder_id');

                DB::table('receive_order_details')->insert([
                    'ro_detail_rorder_ms'  => $roId,
                    'ro_detail_item'       => $itemCodes[3] ?? 'STL-001',
                    'ro_detail_quantity'   => 80,
                    'ro_detail_createdate' => now(),
                    'ro_detail_status'     => 1,
                    'company_id'           => $companyId,
                ]);

                $this->command->info('  + RO-2026-0001 (partial receive for PO-2026-0002)');
            }
        }

        // Receive order for PO-2026-0004 (fully received electrical)
        if (isset($poIds[3])) {
            $existing = DB::table('receive_order_master')
                ->where('rorder_slip_no', 'RO-2026-0002')
                ->where('company_id', $companyId)
                ->first();

            if (!$existing) {
                $roId = DB::table('receive_order_master')->insertGetId([
                    'rorder_slip_no'     => 'RO-2026-0002',
                    'rorder_porder_ms'   => $poIds[3],
                    'rorder_date'        => '2026-01-25',
                    'rorder_infoset'     => 'Full delivery received and inspected',
                    'rorder_status'      => 1,
                    'rorder_createby'    => 1,
                    'rorder_createdate'  => now(),
                    'company_id'         => $companyId,
                ], 'rorder_id');

                // All 3 items received in full
                $roItems = [
                    ['item' => $itemCodes[7] ?? 'ELEC-001', 'qty' => 20],
                    ['item' => $itemCodes[8] ?? 'ELEC-002', 'qty' => 3],
                    ['item' => $itemCodes[9] ?? 'SAFE-001', 'qty' => 25],
                ];
                foreach ($roItems as $roItem) {
                    DB::table('receive_order_details')->insert([
                        'ro_detail_rorder_ms'  => $roId,
                        'ro_detail_item'       => $roItem['item'],
                        'ro_detail_quantity'   => $roItem['qty'],
                        'ro_detail_createdate' => now(),
                        'ro_detail_status'     => 1,
                        'company_id'           => $companyId,
                    ]);
                }

                $this->command->info('  + RO-2026-0002 (full receive for PO-2026-0004)');
            }
        }
    }

    protected function seedBudgets(int $companyId, array $projectIds, array $costCodeIds): void
    {
        $this->command->info('Creating budgets...');

        $budgets = [
            // Downtown Office Tower budgets
            ['budget_project_id' => $projectIds[0], 'budget_cost_code_id' => $costCodeIds[5] ?? 1, 'budget_original_amount' => 150000, 'budget_revised_amount' => 155000, 'budget_committed_amount' => 9765.00, 'budget_spent_amount' => 0],
            ['budget_project_id' => $projectIds[0], 'budget_cost_code_id' => $costCodeIds[6] ?? 1, 'budget_original_amount' => 45000, 'budget_revised_amount' => 45000, 'budget_committed_amount' => 2569.38, 'budget_spent_amount' => 0],
            ['budget_project_id' => $projectIds[0], 'budget_cost_code_id' => $costCodeIds[8] ?? 1, 'budget_original_amount' => 85000, 'budget_revised_amount' => 90000, 'budget_committed_amount' => 3216.38, 'budget_spent_amount' => 3216.38],
            // Harbor Bridge budgets
            ['budget_project_id' => $projectIds[1], 'budget_cost_code_id' => $costCodeIds[7] ?? 1, 'budget_original_amount' => 250000, 'budget_revised_amount' => 275000, 'budget_committed_amount' => 8628.58, 'budget_spent_amount' => 3240.00],
            // School Expansion budgets
            ['budget_project_id' => $projectIds[2], 'budget_cost_code_id' => $costCodeIds[3] ?? 1, 'budget_original_amount' => 75000, 'budget_revised_amount' => 75000, 'budget_committed_amount' => 6220.97, 'budget_spent_amount' => 0],
        ];

        foreach ($budgets as $budget) {
            $existing = DB::table('budget_master')
                ->where('budget_project_id', $budget['budget_project_id'])
                ->where('budget_cost_code_id', $budget['budget_cost_code_id'])
                ->where('company_id', $companyId)
                ->first();

            if (!$existing) {
                DB::table('budget_master')->insert(array_merge($budget, [
                    'budget_fiscal_year'  => 2026,
                    'budget_notes'        => null,
                    'budget_status'       => 1,
                    'budget_created_by'   => 1,
                    'budget_created_at'   => now(),
                    'company_id'          => $companyId,
                ]));
            }
        }
    }

    protected function seedSupplierUser(int $companyId, array $supplierIds): void
    {
        $this->command->info('Creating supplier portal user...');

        $exists = DB::table('supplier_users')->where('email', 'supplier@apexsteel.com')->exists();
        if (!$exists) {
            DB::table('supplier_users')->insert([
                'name'        => 'Robert Chen (Apex Steel)',
                'email'       => 'supplier@apexsteel.com',
                'phone'       => '555-100-2001',
                'password'    => Hash::make('admin123'),
                'supplier_id' => $supplierIds[0],
                'company_id'  => $companyId,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            $this->command->info('  + Supplier portal user: supplier@apexsteel.com');
        }
    }

    protected function printCredentials(): void
    {
        $this->command->info('');
        $this->command->info('============================================');
        $this->command->info('  LOGIN CREDENTIALS (password: admin123)');
        $this->command->info('============================================');
        $this->command->info('');
        $this->command->info('  ADMIN PORTAL (http://localhost:8000)');
        $this->command->info('  ----------------------------------------');
        $this->command->info('  Super Admin    : superadmin@demo.com');
        $this->command->info('                   (u_type=1, full access + company switching)');
        $this->command->info('  Company Admin  : admin@demo.com');
        $this->command->info('                   (u_type=2, company-level admin)');
        $this->command->info('  Project Manager: manager@demo.com');
        $this->command->info('                   (u_type=3, project management access)');
        $this->command->info('  Viewer         : viewer@demo.com');
        $this->command->info('                   (u_type=4, read-only access)');
        $this->command->info('  Regular User   : user@demo.com');
        $this->command->info('                   (u_type=0, basic access)');
        $this->command->info('');
        $this->command->info('  SUPPLIER PORTAL (http://localhost:8000/supplier)');
        $this->command->info('  ----------------------------------------');
        $this->command->info('  Supplier User  : supplier@apexsteel.com');
        $this->command->info('');
        $this->command->info('  All passwords  : admin123');
        $this->command->info('============================================');
    }
}

