<?php

namespace Database\Seeders;

use App\Services\CostCodeTemplateProvisioningService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FullApplicationDemoSeeder extends Seeder
{
    private int $companyId;
    private int $superAdminId;
    private int $companyAdminId;
    private int $managerId;
    private int $viewerId;
    private int $userId;

    /** @var array<string,int> */
    private array $uomIds = [];
    /** @var array<string,int> */
    private array $categoryIds = [];
    /** @var array<string,int> */
    private array $costCodeIds = [];
    /** @var array<string,int> */
    private array $projectIds = [];
    /** @var array<string,int> */
    private array $supplierIds = [];
    /** @var array<string,int> */
    private array $itemIds = [];
    /** @var array<string,int> */
    private array $budgetIds = [];
    /** @var array<string,int> */
    private array $poIds = [];
    /** @var array<string,int> */
    private array $workflowIds = [];

    public function run(): void
    {
        $this->command->info('Seeding full application demo data...');

        $this->companyId = $this->seedCompany();
        $this->seedUsers();
        $this->seedUnitsOfMeasure();
        $this->seedTaxGroups();
        $this->seedCategories();
        $this->seedCostCodes();
        $this->seedProjects();
        $this->seedSuppliers();
        $this->seedItems();
        $this->seedSupplierCatalog();
        $this->seedItemPricing();
        $this->seedItemPriceHistory();
        $this->seedProjectBudgets();
        $this->seedProjectRoles();
        $this->seedApprovalWorkflows();
        $this->seedPurchaseOrders();
        $this->seedReceiveOrders();
        $this->seedRfqs();
        $this->seedSupplierUsers();
        $this->seedChangeOrdersAndApprovalRequests();

        $this->command->info('Full demo dataset ready.');
    }

    private function seedCompany(): int
    {
        return $this->upsert(
            'companies',
            ['subdomain' => 'demo'],
            [
                'name' => 'Demo Construction Co',
                'company_code' => 'DEMO',
                'email' => 'admin@demo.com',
                'phone' => '555-1000',
                'address' => '100 Demo Plaza',
                'city' => 'Seattle',
                'state' => 'WA',
                'zip' => '98101',
                'country' => 'USA',
                'status' => 1,
                'settings' => json_encode([
                    'currency' => 'USD',
                    'timezone' => 'America/Los_Angeles',
                    'date_format' => 'm/d/Y',
                    'budget_constraints_enabled' => true,
                ]),
                'subscription_tier' => 'pro',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            'id'
        );
    }

    private function seedUsers(): void
    {
        $users = [
            'superadmin@demo.com' => ['name' => 'Super Admin', 'username' => 'superadmin', 'u_type' => 1],
            'admin@demo.com' => ['name' => 'Company Admin', 'username' => 'companyadmin', 'u_type' => 2],
            'manager@demo.com' => ['name' => 'Project Manager', 'username' => 'manager', 'u_type' => 3],
            'viewer@demo.com' => ['name' => 'Viewer User', 'username' => 'viewer', 'u_type' => 5],
            'user@demo.com' => ['name' => 'Regular User', 'username' => 'user', 'u_type' => 0],
        ];

        foreach ($users as $email => $user) {
            $id = $this->upsert(
                'users',
                ['email' => $email],
                [
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'password' => Hash::make('admin123'),
                    'company_id' => $this->companyId,
                    'u_type' => $user['u_type'],
                    'u_status' => 1,
                    'email_verified_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
                'id'
            );

            match ($email) {
                'superadmin@demo.com' => $this->superAdminId = $id,
                'admin@demo.com' => $this->companyAdminId = $id,
                'manager@demo.com' => $this->managerId = $id,
                'viewer@demo.com' => $this->viewerId = $id,
                'user@demo.com' => $this->userId = $id,
                default => null,
            };
        }
    }

    private function seedUnitsOfMeasure(): void
    {
        $units = [
            'EA' => ['name' => 'Each', 'detail' => 'Individual unit'],
            'BOX' => ['name' => 'Box', 'detail' => 'Box or case'],
            'CY' => ['name' => 'Cubic Yard', 'detail' => 'Concrete by cubic yard'],
            'LF' => ['name' => 'Linear Foot', 'detail' => 'Length in linear feet'],
            'SF' => ['name' => 'Square Foot', 'detail' => 'Area in square feet'],
            'TON' => ['name' => 'Ton', 'detail' => 'Short ton'],
            'GAL' => ['name' => 'Gallon', 'detail' => 'US gallon'],
            'BAG' => ['name' => 'Bag', 'detail' => 'Bag or sack'],
        ];

        foreach ($units as $code => $unit) {
            $this->uomIds[$code] = $this->upsert(
                'unit_of_measure_tab',
                ['uom_name' => $unit['name']],
                [
                    'uom_code' => $code,
                    'uom_detail' => $unit['detail'],
                    'uom_status' => 1,
                    'uom_createby' => $this->superAdminId,
                    'uom_createdate' => now(),
                ],
                'uom_id'
            );
        }
    }

    private function seedTaxGroups(): void
    {
        $groups = [
            ['name' => 'No Tax', 'percentage' => 0.00],
            ['name' => 'State Tax', 'percentage' => 6.25],
            ['name' => 'Combined Tax', 'percentage' => 8.75],
        ];

        foreach ($groups as $group) {
            $this->upsert(
                'taxgroup_master',
                ['name' => $group['name']],
                [
                    'percentage' => $group['percentage'],
                    'created_at' => now(),
                ],
                'id'
            );
        }
    }

    private function seedCategories(): void
    {
        $categories = [
            'CONCRETE' => 'Concrete & Masonry',
            'STEEL' => 'Structural Steel',
            'LUMBER' => 'Lumber & Wood',
            'ELECTRICAL' => 'Electrical',
            'SAFETY' => 'Safety Equipment',
        ];

        foreach ($categories as $key => $name) {
            $this->categoryIds[$key] = $this->upsert(
                'item_category_tab',
                ['icat_name' => $name, 'company_id' => $this->companyId],
                [
                    'icat_details' => $name . ' materials and supplies',
                    'icat_status' => 1,
                    'icat_createby' => $this->superAdminId,
                    'icat_createdate' => now(),
                ],
                'icat_id'
            );
        }
    }

    private function seedCostCodes(): void
    {
        app(CostCodeTemplateProvisioningService::class)->provisionForCompany(
            $this->companyId,
            $this->superAdminId
        );

        $this->costCodeIds = DB::table('cost_code_master')
            ->where('company_id', $this->companyId)
            ->whereIn('cc_full_code', [
                '2-01-06',
                '2-03-20',
                '2-03-30',
                '2-05-10',
                '2-06-10',
                '2-16-10',
            ])
            ->pluck('cc_id', 'cc_full_code')
            ->map(static fn ($id) => (int) $id)
            ->all();
    }

    private function seedProjects(): void
    {
        $projects = [
            'DOT-001' => [
                'name' => 'Downtown Office Tower',
                'address' => '123 Main Street, Seattle, WA 98101',
                'description' => 'High-rise office tower core and shell package',
                'start' => '2026-01-15',
                'end' => '2027-06-30',
            ],
            'HBR-002' => [
                'name' => 'Harbor Bridge Renovation',
                'address' => '500 Harbor Drive, Tacoma, WA 98402',
                'description' => 'Bridge steel and deck rehabilitation project',
                'start' => '2026-03-01',
                'end' => '2026-12-31',
            ],
            'RSE-003' => [
                'name' => 'Riverside School Expansion',
                'address' => '789 Education Blvd, Portland, OR 97201',
                'description' => 'Classroom and support wing expansion',
                'start' => '2026-02-01',
                'end' => '2027-01-31',
            ],
        ];

        foreach ($projects as $number => $project) {
            $this->projectIds[$number] = $this->upsert(
                'project_master',
                ['proj_number' => $number],
                [
                    'proj_name' => $project['name'],
                    'proj_address' => $project['address'],
                    'proj_description' => $project['description'],
                    'proj_start_date' => $project['start'],
                    'proj_end_date' => $project['end'],
                    'proj_status' => 1,
                    'proj_createby' => $this->companyAdminId,
                    'proj_createdate' => now(),
                    'company_id' => $this->companyId,
                ],
                'proj_id'
            );
        }
    }

    private function seedSuppliers(): void
    {
        $suppliers = [
            'APEX-001' => [
                'name' => 'Apex Steel Industries',
                'email' => 'orders@apexsteel.com',
                'phone' => '555-100-2001',
                'contact' => 'Robert Chen',
                'address' => '1000 Industrial Pkwy, Pittsburgh, PA 15201',
                'details' => 'Structural steel and fabricated metal supplier',
            ],
            'RMC-002' => [
                'name' => 'ReadyMix Concrete Co',
                'email' => 'dispatch@readymix.com',
                'phone' => '555-200-3002',
                'contact' => 'Maria Lopez',
                'address' => '2500 Quarry Road, Newark, NJ 07102',
                'details' => 'Concrete, rebar, and cement distributor',
            ],
            'NLS-003' => [
                'name' => 'National Lumber Supply',
                'email' => 'sales@nationallumber.com',
                'phone' => '555-300-4003',
                'contact' => 'James Wilson',
                'address' => '3800 Timber Lane, Portland, OR 97201',
                'details' => 'Dimensional lumber and sheet goods',
            ],
            'PEW-004' => [
                'name' => 'ProElectric Wholesale',
                'email' => 'info@proelectric.com',
                'phone' => '555-400-5004',
                'contact' => 'Sarah Miller',
                'address' => '600 Volt Avenue, Dallas, TX 75201',
                'details' => 'Electrical equipment and safety supplies',
            ],
        ];

        foreach ($suppliers as $code => $supplier) {
            $this->supplierIds[$code] = $this->upsert(
                'supplier_master',
                ['sup_code' => $code],
                [
                    'sup_name' => $supplier['name'],
                    'sup_email' => $supplier['email'],
                    'sup_phone' => $supplier['phone'],
                    'sup_address' => $supplier['address'],
                    'sup_contact_person' => $supplier['contact'],
                    'sup_details' => $supplier['details'],
                    'sup_type' => 1,
                    'sup_status' => 1,
                    'sup_createby' => $this->companyAdminId,
                    'sup_createdate' => now(),
                    'company_id' => $this->companyId,
                ],
                'sup_id'
            );
        }
    }

    private function seedItems(): void
    {
        $items = [
            'CONC-001' => ['name' => 'Ready Mix Concrete 4000 PSI', 'description' => 'Ready-mix concrete for structural pours', 'category' => 'CONCRETE', 'cost_code' => '2-03-30', 'uom' => 'CY'],
            'CONC-002' => ['name' => 'Rebar #5 Grade 60', 'description' => 'Grade 60 reinforcing bar in 20 foot lengths', 'category' => 'STEEL', 'cost_code' => '2-03-20', 'uom' => 'LF'],
            'CONC-003' => ['name' => 'Portland Cement Type I', 'description' => '94 pound bag Portland cement', 'category' => 'CONCRETE', 'cost_code' => '2-03-30', 'uom' => 'BAG'],
            'STL-001' => ['name' => 'W12x26 Structural Beam', 'description' => 'A992 structural steel beam', 'category' => 'STEEL', 'cost_code' => '2-05-10', 'uom' => 'LF'],
            'STL-002' => ['name' => 'Steel Plate 1/2 x 4 x 8', 'description' => 'A36 steel plate, half-inch thick', 'category' => 'STEEL', 'cost_code' => '2-05-10', 'uom' => 'EA'],
            'LBR-001' => ['name' => '2x4x8 SPF Stud', 'description' => 'Spruce-pine-fir framing stud', 'category' => 'LUMBER', 'cost_code' => '2-06-10', 'uom' => 'EA'],
            'LBR-002' => ['name' => '3/4 Plywood CDX', 'description' => '4x8 structural plywood sheet', 'category' => 'LUMBER', 'cost_code' => '2-06-10', 'uom' => 'EA'],
            'ELEC-001' => ['name' => '12/2 Romex Wire 250ft', 'description' => '12 AWG two conductor NM-B cable', 'category' => 'ELECTRICAL', 'cost_code' => '2-16-10', 'uom' => 'BOX'],
            'ELEC-002' => ['name' => '200A Main Breaker Panel', 'description' => '200 amp main breaker panel, 40 space', 'category' => 'ELECTRICAL', 'cost_code' => '2-16-10', 'uom' => 'EA'],
            'SAFE-001' => ['name' => 'Hard Hat - White', 'description' => 'OSHA-approved hard hat with ratchet suspension', 'category' => 'SAFETY', 'cost_code' => '2-01-06', 'uom' => 'EA'],
        ];

        foreach ($items as $code => $item) {
            $this->itemIds[$code] = $this->upsert(
                'item_master',
                ['item_code' => $code],
                [
                    'item_name' => $item['name'],
                    'item_description' => $item['description'],
                    'item_cat_ms' => $this->categoryIds[$item['category']],
                    'item_ccode_ms' => $this->costCodeIds[$item['cost_code']],
                    'item_unit_ms' => $this->uomIds[$item['uom']],
                    'item_status' => 1,
                    'item_createby' => $this->companyAdminId,
                    'item_createdate' => now(),
                    'company_id' => $this->companyId,
                ],
                'item_id'
            );
        }
    }

    private function seedSupplierCatalog(): void
    {
        $entries = [
            ['supplier' => 'APEX-001', 'item' => 'CONC-002', 'sku' => 'APX-RBR5', 'uom' => 'LF', 'price' => 12.05],
            ['supplier' => 'APEX-001', 'item' => 'CONC-003', 'sku' => 'APX-CEM1', 'uom' => 'BAG', 'price' => 13.95],
            ['supplier' => 'APEX-001', 'item' => 'STL-001', 'sku' => 'APX-W1226', 'uom' => 'LF', 'price' => 40.50],
            ['supplier' => 'APEX-001', 'item' => 'STL-002', 'sku' => 'APX-PLATE12', 'uom' => 'EA', 'price' => 475.00],
            ['supplier' => 'RMC-002', 'item' => 'CONC-001', 'sku' => 'RMC-4K', 'uom' => 'CY', 'price' => 140.00],
            ['supplier' => 'RMC-002', 'item' => 'CONC-002', 'sku' => 'RMC-RBR5', 'uom' => 'LF', 'price' => 11.80],
            ['supplier' => 'RMC-002', 'item' => 'CONC-003', 'sku' => 'RMC-CEM1', 'uom' => 'BAG', 'price' => 13.40],
            ['supplier' => 'NLS-003', 'item' => 'LBR-001', 'sku' => 'NLS-248SPF', 'uom' => 'EA', 'price' => 4.95],
            ['supplier' => 'NLS-003', 'item' => 'LBR-002', 'sku' => 'NLS-PLY34', 'uom' => 'EA', 'price' => 46.00],
            ['supplier' => 'PEW-004', 'item' => 'ELEC-001', 'sku' => 'PEW-122NM', 'uom' => 'BOX', 'price' => 119.50],
            ['supplier' => 'PEW-004', 'item' => 'ELEC-002', 'sku' => 'PEW-200MB', 'uom' => 'EA', 'price' => 275.00],
            ['supplier' => 'PEW-004', 'item' => 'SAFE-001', 'sku' => 'PEW-HH-W', 'uom' => 'EA', 'price' => 17.95],
        ];

        foreach ($entries as $entry) {
            $this->upsert(
                'supplier_catalog_tab',
                [
                    'supcat_supplier' => $this->supplierIds[$entry['supplier']],
                    'supcat_item_code' => $entry['item'],
                ],
                [
                    'supcat_sku_no' => $entry['sku'],
                    'supcat_uom' => $this->uomIds[$entry['uom']],
                    'supcat_price' => $entry['price'],
                    'supcat_lastdate' => now()->toDateString(),
                    'supcat_details' => 'Demo supplier catalog price',
                    'supcat_createdate' => now(),
                    'supcat_createby' => $this->companyAdminId,
                    'supcat_status' => 1,
                    'company_id' => $this->companyId,
                ],
                'supcat_id'
            );
        }
    }

    private function seedItemPricing(): void
    {
        $pricingRows = [
            ['item' => 'CONC-001', 'supplier' => 'RMC-002', 'project' => 'DOT-001', 'price' => 140.00, 'from' => '2026-01-10'],
            ['item' => 'CONC-002', 'supplier' => 'RMC-002', 'project' => 'DOT-001', 'price' => 11.80, 'from' => '2026-01-10'],
            ['item' => 'STL-001', 'supplier' => 'APEX-001', 'project' => 'HBR-002', 'price' => 40.50, 'from' => '2026-02-01'],
            ['item' => 'STL-002', 'supplier' => 'APEX-001', 'project' => 'HBR-002', 'price' => 475.00, 'from' => '2026-02-01'],
            ['item' => 'LBR-001', 'supplier' => 'NLS-003', 'project' => 'RSE-003', 'price' => 4.95, 'from' => '2026-02-05'],
            ['item' => 'LBR-002', 'supplier' => 'NLS-003', 'project' => 'RSE-003', 'price' => 46.00, 'from' => '2026-02-05'],
            ['item' => 'ELEC-001', 'supplier' => 'PEW-004', 'project' => 'DOT-001', 'price' => 119.50, 'from' => '2026-01-12'],
            ['item' => 'ELEC-002', 'supplier' => 'PEW-004', 'project' => 'DOT-001', 'price' => 275.00, 'from' => '2026-01-12'],
            ['item' => 'SAFE-001', 'supplier' => 'PEW-004', 'project' => 'HBR-002', 'price' => 17.95, 'from' => '2026-02-02'],
        ];

        foreach ($pricingRows as $row) {
            $this->upsert(
                'item_pricing',
                [
                    'item_id' => $this->itemIds[$row['item']],
                    'supplier_id' => $this->supplierIds[$row['supplier']],
                    'project_id' => $this->projectIds[$row['project']],
                ],
                [
                    'company_id' => $this->companyId,
                    'unit_price' => $row['price'],
                    'effective_from' => $row['from'],
                    'effective_to' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'pricing_id'
            );
        }
    }

    private function seedItemPriceHistory(): void
    {
        $historyRows = [
            ['item' => 'CONC-001', 'supplier' => 'RMC-002', 'old' => 137.50, 'new' => 140.00, 'date' => '2026-01-10'],
            ['item' => 'STL-001', 'supplier' => 'APEX-001', 'old' => 39.00, 'new' => 40.50, 'date' => '2026-02-01'],
            ['item' => 'LBR-002', 'supplier' => 'NLS-003', 'old' => 44.50, 'new' => 46.00, 'date' => '2026-02-05'],
            ['item' => 'ELEC-001', 'supplier' => 'PEW-004', 'old' => 118.00, 'new' => 119.50, 'date' => '2026-01-12'],
        ];

        foreach ($historyRows as $row) {
            $this->upsert(
                'item_price_history',
                [
                    'iph_item_id' => $this->itemIds[$row['item']],
                    'iph_supplier_id' => $this->supplierIds[$row['supplier']],
                    'iph_effective_date' => $row['date'],
                ],
                [
                    'iph_old_price' => $row['old'],
                    'iph_new_price' => $row['new'],
                    'iph_notes' => 'Demo price history entry',
                    'iph_created_by' => $this->companyAdminId,
                    'iph_created_at' => now(),
                    'company_id' => $this->companyId,
                ],
                'iph_id'
            );
        }
    }

    private function seedProjectBudgets(): void
    {
        $budgets = [
            ['project' => 'DOT-001', 'cost_code' => '2-03-30', 'original' => 150000.00, 'revised' => 155000.00, 'committed' => 7405.00, 'actual' => 0.00, 'notes' => 'Foundation concrete package'],
            ['project' => 'DOT-001', 'cost_code' => '2-03-20', 'original' => 45000.00, 'revised' => 45000.00, 'committed' => 2360.00, 'actual' => 0.00, 'notes' => 'Foundation reinforcing steel'],
            ['project' => 'DOT-001', 'cost_code' => '2-16-10', 'original' => 85000.00, 'revised' => 90000.00, 'committed' => 0.00, 'actual' => 3215.00, 'notes' => 'Main electrical rough-in'],
            ['project' => 'DOT-001', 'cost_code' => '2-01-06', 'original' => 12000.00, 'revised' => 12500.00, 'committed' => 0.00, 'actual' => 462.50, 'notes' => 'Safety supplies'],
            ['project' => 'HBR-002', 'cost_code' => '2-05-10', 'original' => 250000.00, 'revised' => 275000.00, 'committed' => 5420.00, 'actual' => 3240.00, 'notes' => 'Bridge structural steel'],
            ['project' => 'RSE-003', 'cost_code' => '2-06-10', 'original' => 75000.00, 'revised' => 75000.00, 'committed' => 6155.00, 'actual' => 0.00, 'notes' => 'Framing package'],
        ];

        foreach ($budgets as $budget) {
            $projectId = $this->projectIds[$budget['project']];
            $costCodeId = $this->costCodeIds[$budget['cost_code']];

            $this->upsert(
                'project_cost_codes',
                [
                    'project_id' => $projectId,
                    'cost_code_id' => $costCodeId,
                ],
                [
                    'company_id' => $this->companyId,
                    'is_active' => 1,
                    'notes' => 'Linked by demo seeder',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'id'
            );

            $remaining = $budget['revised'] - $budget['committed'] - $budget['actual'];
            $key = $budget['project'] . ':' . $budget['cost_code'];
            $this->budgetIds[$key] = $this->upsert(
                'budget_master',
                [
                    'budget_project_id' => $projectId,
                    'budget_cost_code_id' => $costCodeId,
                    'company_id' => $this->companyId,
                ],
                [
                    'budget_original_amount' => $budget['original'],
                    'budget_revised_amount' => $budget['revised'],
                    'budget_committed_amount' => $budget['committed'],
                    'budget_spent_amount' => $budget['actual'],
                    'budget_remaining_amount' => $remaining,
                    'budget_fiscal_year' => '2026',
                    'budget_notes' => $budget['notes'],
                    'budget_status' => 1,
                    'budget_created_by' => $this->companyAdminId,
                    'budget_created_at' => now(),
                    'budget_change_orders_total' => 0,
                    'budget_committed' => $budget['committed'],
                    'budget_actual' => $budget['actual'],
                    'budget_warning_threshold' => 80,
                    'budget_critical_threshold' => 95,
                    'committed' => $budget['committed'],
                    'actual' => $budget['actual'],
                    'warning_notification_sent' => 0,
                    'critical_notification_sent' => 0,
                    'original_amount' => $budget['original'],
                    'variance' => $remaining,
                ],
                'budget_id'
            );
        }
    }

    private function seedProjectRoles(): void
    {
        $roleTemplates = [
            ['role' => 'project_manager', 'user_id' => fn () => $this->managerId, 'create_po' => 1, 'approve_po' => 0, 'create_bco' => 1, 'approve_bco' => 0, 'override' => 0, 'limit' => null],
            ['role' => 'manager', 'user_id' => fn () => $this->companyAdminId, 'create_po' => 1, 'approve_po' => 1, 'create_bco' => 1, 'approve_bco' => 1, 'override' => 0, 'limit' => 25000.00],
            ['role' => 'finance', 'user_id' => fn () => $this->superAdminId, 'create_po' => 0, 'approve_po' => 1, 'create_bco' => 0, 'approve_bco' => 1, 'override' => 1, 'limit' => null],
            ['role' => 'executive', 'user_id' => fn () => $this->superAdminId, 'create_po' => 1, 'approve_po' => 1, 'create_bco' => 1, 'approve_bco' => 1, 'override' => 1, 'limit' => null],
            ['role' => 'staff', 'user_id' => fn () => $this->userId, 'create_po' => 0, 'approve_po' => 0, 'create_bco' => 0, 'approve_bco' => 0, 'override' => 0, 'limit' => null],
        ];

        foreach ($this->projectIds as $projectId) {
            foreach ($roleTemplates as $role) {
                $userId = $role['user_id']();
                $this->upsert(
                    'project_roles',
                    [
                        'company_id' => $this->companyId,
                        'project_id' => $projectId,
                        'user_id' => $userId,
                        'role_name' => $role['role'],
                    ],
                    [
                        'can_create_po' => $role['create_po'],
                        'can_approve_po' => $role['approve_po'],
                        'can_create_budget_co' => $role['create_bco'],
                        'can_approve_budget_co' => $role['approve_bco'],
                        'can_override_budget' => $role['override'],
                        'approval_limit' => $role['limit'],
                        'is_active' => 1,
                        'notes' => 'Demo project role',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    'role_id'
                );
            }
        }
    }

    private function seedApprovalWorkflows(): void
    {
        foreach ($this->projectIds as $projectCode => $projectId) {
            $definitions = [
                ['name' => "{$projectCode} PO Manager Approval", 'type' => 'po', 'level' => 1, 'min' => 5000, 'max' => 24999.99, 'roles' => ['manager']],
                ['name' => "{$projectCode} PO Finance Approval", 'type' => 'po', 'level' => 2, 'min' => 25000, 'max' => null, 'roles' => ['finance']],
                ['name' => "{$projectCode} Budget CO Manager Approval", 'type' => 'budget_co', 'level' => 1, 'min' => 5000, 'max' => 29999.99, 'roles' => ['manager']],
                ['name' => "{$projectCode} Budget CO Executive Approval", 'type' => 'budget_co', 'level' => 2, 'min' => 30000, 'max' => null, 'roles' => ['executive']],
                ['name' => "{$projectCode} PO CO Manager Approval", 'type' => 'po_co', 'level' => 1, 'min' => 2500, 'max' => 14999.99, 'roles' => ['manager']],
                ['name' => "{$projectCode} PO CO Finance Approval", 'type' => 'po_co', 'level' => 2, 'min' => 15000, 'max' => null, 'roles' => ['finance']],
            ];

            foreach ($definitions as $workflow) {
                $id = $this->upsert(
                    'approval_workflows',
                    [
                        'company_id' => $this->companyId,
                        'project_id' => $projectId,
                        'workflow_name' => $workflow['name'],
                    ],
                    [
                        'workflow_type' => $workflow['type'],
                        'approval_level' => $workflow['level'],
                        'amount_threshold_min' => $workflow['min'],
                        'amount_threshold_max' => $workflow['max'],
                        'approver_user_ids' => null,
                        'approver_roles' => json_encode($workflow['roles']),
                        'approval_logic' => 'any',
                        'is_active' => 1,
                        'sort_order' => $workflow['level'],
                        'workflow_notes' => 'Seeded workflow',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    'workflow_id'
                );

                $this->workflowIds[$projectCode . ':' . $workflow['type'] . ':' . $workflow['level']] = $id;
            }
        }
    }

    private function seedPurchaseOrders(): void
    {
        $orders = [
            [
                'number' => 'PO-2026-0001',
                'project' => 'DOT-001',
                'supplier' => 'RMC-002',
                'description' => 'Foundation pour - Phase 1',
                'created_by' => fn () => $this->managerId,
                'created_at' => '2026-01-20 09:00:00',
                'delivery_note' => 'Foundation staging area',
                'delivery_status' => 0,
                'status' => 4,
                'integration' => 'pending',
                'items' => [
                    ['item' => 'CONC-001', 'sku' => 'RMC-4K', 'qty' => 50, 'price' => 140.00, 'tax' => 0.00, 'backordered' => 0],
                    ['item' => 'CONC-002', 'sku' => 'RMC-RBR5', 'qty' => 200, 'price' => 11.80, 'tax' => 0.00, 'backordered' => 0],
                    ['item' => 'CONC-003', 'sku' => 'RMC-CEM1', 'qty' => 30, 'price' => 13.50, 'tax' => 0.00, 'backordered' => 0],
                ],
            ],
            [
                'number' => 'PO-2026-0002',
                'project' => 'HBR-002',
                'supplier' => 'APEX-001',
                'description' => 'Bridge structural members - main span',
                'created_by' => fn () => $this->companyAdminId,
                'created_at' => '2026-02-01 10:30:00',
                'delivery_note' => 'Deliver to staging yard B',
                'delivery_status' => 2,
                'status' => 5,
                'integration' => 'rte',
                'items' => [
                    ['item' => 'STL-001', 'sku' => 'APX-W1226', 'qty' => 120, 'price' => 40.50, 'tax' => 0.00, 'backordered' => 40, 'expected' => '2026-03-25'],
                    ['item' => 'STL-002', 'sku' => 'APX-PLATE12', 'qty' => 8, 'price' => 475.00, 'tax' => 0.00, 'backordered' => 8, 'expected' => '2026-03-25'],
                ],
            ],
            [
                'number' => 'PO-2026-0003',
                'project' => 'RSE-003',
                'supplier' => 'NLS-003',
                'description' => 'Framing materials - Wing B',
                'created_by' => fn () => $this->managerId,
                'created_at' => '2026-02-05 11:00:00',
                'delivery_note' => 'Unload at east laydown area',
                'delivery_status' => 0,
                'status' => 1,
                'integration' => 'pending',
                'items' => [
                    ['item' => 'LBR-001', 'sku' => 'NLS-248SPF', 'qty' => 500, 'price' => 4.95, 'tax' => 0.00, 'backordered' => 0],
                    ['item' => 'LBR-002', 'sku' => 'NLS-PLY34', 'qty' => 80, 'price' => 46.00, 'tax' => 0.00, 'backordered' => 0],
                ],
            ],
            [
                'number' => 'PO-2026-0004',
                'project' => 'DOT-001',
                'supplier' => 'PEW-004',
                'description' => 'Main electrical rough-in',
                'created_by' => fn () => $this->companyAdminId,
                'created_at' => '2026-01-10 08:15:00',
                'delivery_note' => 'Electrical room and storage container',
                'delivery_status' => 1,
                'status' => 5,
                'integration' => 'synced',
                'items' => [
                    ['item' => 'ELEC-001', 'sku' => 'PEW-122NM', 'qty' => 20, 'price' => 119.50, 'tax' => 0.00, 'backordered' => 0],
                    ['item' => 'ELEC-002', 'sku' => 'PEW-200MB', 'qty' => 3, 'price' => 275.00, 'tax' => 0.00, 'backordered' => 0],
                    ['item' => 'SAFE-001', 'sku' => 'PEW-HH-W', 'qty' => 25, 'price' => 18.50, 'tax' => 0.00, 'backordered' => 0],
                ],
            ],
            [
                'number' => 'PO-2026-0005',
                'project' => 'HBR-002',
                'supplier' => 'PEW-004',
                'description' => 'Budget exceeded - resubmit with reduced quantities',
                'created_by' => fn () => $this->managerId,
                'created_at' => '2026-02-03 15:00:00',
                'delivery_note' => 'Safety storage trailer',
                'delivery_status' => 0,
                'status' => 6,
                'integration' => 'pending',
                'items' => [
                    ['item' => 'SAFE-001', 'sku' => 'PEW-HH-W', 'qty' => 100, 'price' => 18.50, 'tax' => 0.00, 'backordered' => 0],
                ],
            ],
        ];

        foreach ($orders as $order) {
            $existing = DB::table('purchase_order_master')
                ->where('porder_no', $order['number'])
                ->first();

            if ($existing) {
                $this->poIds[$order['number']] = (int) $existing->porder_id;
                continue;
            }

            $subtotal = 0;
            $taxTotal = 0;
            foreach ($order['items'] as $item) {
                $lineSubtotal = $item['qty'] * $item['price'];
                $subtotal += $lineSubtotal;
                $taxTotal += $this->calculateTax($lineSubtotal, $item['tax']);
            }

            $projectId = $this->projectIds[$order['project']];
            $purchaseOrderId = DB::table('purchase_order_master')->insertGetId([
                'porder_no' => $order['number'],
                'porder_project_ms' => $projectId,
                'porder_supplier_ms' => $this->supplierIds[$order['supplier']],
                'porder_cost_code' => $this->costCodeIds[$this->costCodeForItem($order['items'][0]['item'])],
                'porder_address' => DB::table('project_master')->where('proj_id', $projectId)->value('proj_address'),
                'porder_delivery_note' => $order['delivery_note'],
                'porder_description' => $order['description'],
                'porder_total_item' => count($order['items']),
                'porder_total_amount' => $subtotal,
                'porder_total_tax' => $taxTotal,
                'porder_delivery_status' => $order['delivery_status'],
                'porder_status' => $order['status'],
                'porder_createdate' => $order['created_at'],
                'porder_createby' => $order['created_by'](),
                'porder_original_total' => $subtotal + $taxTotal,
                'porder_change_orders_total' => 0,
                'integration_status' => $order['integration'],
                'company_id' => $this->companyId,
            ], 'porder_id');

            foreach ($order['items'] as $index => $item) {
                $lineSubtotal = $item['qty'] * $item['price'];
                $lineTax = $this->calculateTax($lineSubtotal, $item['tax']);

                DB::table('purchase_order_details')->insert([
                    'po_detail_autogen' => $order['number'] . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'po_detail_porder_ms' => $purchaseOrderId,
                    'po_detail_item' => $item['item'],
                    'po_detail_sku' => $item['sku'],
                    'po_detail_taxcode' => (string) $item['tax'],
                    'po_detail_tax_group' => 'No Tax',
                    'po_detail_ccode' => $this->costCodeIds[$this->costCodeForItem($item['item'])],
                    'po_detail_quantity' => $item['qty'],
                    'po_detail_unitprice' => $item['price'],
                    'po_detail_subtotal' => $lineSubtotal,
                    'po_detail_taxamount' => $lineTax,
                    'po_detail_total' => $lineSubtotal + $lineTax,
                    'backordered_qty' => $item['backordered'],
                    'backorder_status' => $item['backordered'] > 0 ? 1 : 0,
                    'backorder_notes' => $item['backordered'] > 0 ? 'Awaiting supplier follow-up shipment' : null,
                    'expected_backorder_date' => $item['expected'] ?? null,
                    'po_detail_createdate' => $order['created_at'],
                    'po_detail_status' => 1,
                    'company_id' => $this->companyId,
                ]);
            }

            $this->poIds[$order['number']] = (int) $purchaseOrderId;
        }
    }

    private function seedReceiveOrders(): void
    {
        $orders = [
            [
                'slip' => 'RO-2026-0001',
                'po' => 'PO-2026-0002',
                'date' => '2026-02-20',
                'notes' => 'Partial delivery - beams only, plates remain on backorder',
                'items' => [
                    ['item' => 'STL-001', 'qty' => 80],
                ],
            ],
            [
                'slip' => 'RO-2026-0002',
                'po' => 'PO-2026-0004',
                'date' => '2026-01-25',
                'notes' => 'Full delivery received and inspected',
                'items' => [
                    ['item' => 'ELEC-001', 'qty' => 20],
                    ['item' => 'ELEC-002', 'qty' => 3],
                    ['item' => 'SAFE-001', 'qty' => 25],
                ],
            ],
        ];

        foreach ($orders as $order) {
            $existing = DB::table('receive_order_master')
                ->where('rorder_slip_no', $order['slip'])
                ->first();

            if ($existing) {
                continue;
            }

            $poId = $this->poIds[$order['po']];
            $totalAmount = 0;
            foreach ($order['items'] as $item) {
                $unitPrice = (float) DB::table('purchase_order_details')
                    ->where('po_detail_porder_ms', $poId)
                    ->where('po_detail_item', $item['item'])
                    ->value('po_detail_unitprice');
                $totalAmount += $unitPrice * $item['qty'];
            }

            $receiveOrderId = DB::table('receive_order_master')->insertGetId([
                'rorder_porder_ms' => $poId,
                'rorder_slip_no' => $order['slip'],
                'rorder_infoset' => $order['notes'],
                'rorder_date' => $order['date'],
                'rorder_totalitem' => count($order['items']),
                'rorder_totalamount' => $totalAmount,
                'rorder_createdate' => now(),
                'rorder_createby' => $this->companyAdminId,
                'rorder_status' => 1,
                'company_id' => $this->companyId,
            ], 'rorder_id');

            foreach ($order['items'] as $item) {
                DB::table('receive_order_details')->insert([
                    'ro_detail_rorder_ms' => $receiveOrderId,
                    'ro_detail_item' => $item['item'],
                    'ro_detail_quantity' => $item['qty'],
                    'ro_detail_createdate' => now(),
                    'ro_detail_status' => 1,
                    'company_id' => $this->companyId,
                ]);
            }
        }
    }

    private function seedRfqs(): void
    {
        $rfqId = $this->upsert(
            'rfq_master',
            ['rfq_no' => 'RFQ000001'],
            [
                'rfq_project_id' => $this->projectIds['HBR-002'],
                'company_id' => $this->companyId,
                'rfq_title' => 'Harbor Bridge material comparison',
                'rfq_description' => 'Competitive quote request for rebar and cement supply',
                'rfq_due_date' => '2026-03-10',
                'rfq_status' => 3,
                'rfq_created_by' => $this->companyAdminId,
                'rfq_created_at' => now(),
                'rfq_modified_by' => $this->companyAdminId,
                'rfq_modified_at' => now(),
            ],
            'rfq_id'
        );

        $rfqItems = [
            ['item' => 'CONC-002', 'uom' => 'LF', 'qty' => 300, 'target' => 12.00, 'notes' => 'Rebar for deck reinforcement'],
            ['item' => 'CONC-003', 'uom' => 'BAG', 'qty' => 100, 'target' => 14.25, 'notes' => 'Cement for repair mortar batches'],
        ];

        $rfqItemIds = [];
        foreach ($rfqItems as $row) {
            $rfqItemIds[$row['item']] = $this->upsert(
                'rfq_items',
                [
                    'rfqi_rfq_id' => $rfqId,
                    'rfqi_item_id' => $this->itemIds[$row['item']],
                ],
                [
                    'rfqi_uom_id' => $this->uomIds[$row['uom']],
                    'project_id' => $this->projectIds['HBR-002'],
                    'company_id' => $this->companyId,
                    'rfqi_quantity' => $row['qty'],
                    'rfqi_target_price' => $row['target'],
                    'rfqi_notes' => $row['notes'],
                    'rfqi_created_at' => now(),
                ],
                'rfqi_id'
            );
        }

        $rfqSuppliers = [
            'RMC-002' => ['status' => 4, 'sent' => now()->subDays(12), 'responded' => now()->subDays(8), 'notes' => 'Selected supplier'],
            'APEX-001' => ['status' => 3, 'sent' => now()->subDays(12), 'responded' => now()->subDays(9), 'notes' => 'Alternate quote received'],
        ];

        $rfqSupplierIds = [];
        foreach ($rfqSuppliers as $supplierCode => $row) {
            $rfqSupplierIds[$supplierCode] = $this->upsert(
                'rfq_suppliers',
                [
                    'rfqs_rfq_id' => $rfqId,
                    'rfqs_supplier_id' => $this->supplierIds[$supplierCode],
                ],
                [
                    'company_id' => $this->companyId,
                    'rfqs_sent_date' => $row['sent'],
                    'rfqs_response_date' => $row['responded'],
                    'rfqs_status' => $row['status'],
                    'rfqs_notes' => $row['notes'],
                    'rfqs_created_at' => now()->subDays(12),
                ],
                'rfqs_id'
            );
        }

        $quotes = [
            ['supplier' => 'RMC-002', 'item' => 'CONC-002', 'price' => 11.70, 'lead' => 7, 'valid' => '2026-03-31'],
            ['supplier' => 'RMC-002', 'item' => 'CONC-003', 'price' => 13.40, 'lead' => 3, 'valid' => '2026-03-31'],
            ['supplier' => 'APEX-001', 'item' => 'CONC-002', 'price' => 12.05, 'lead' => 10, 'valid' => '2026-03-25'],
            ['supplier' => 'APEX-001', 'item' => 'CONC-003', 'price' => 13.95, 'lead' => 6, 'valid' => '2026-03-25'],
        ];

        foreach ($quotes as $quote) {
            $this->upsert(
                'rfq_quotes',
                [
                    'rfqq_rfqs_id' => $rfqSupplierIds[$quote['supplier']],
                    'rfqq_rfqi_id' => $rfqItemIds[$quote['item']],
                ],
                [
                    'company_id' => $this->companyId,
                    'rfqq_quoted_price' => $quote['price'],
                    'rfqq_lead_time_days' => $quote['lead'],
                    'rfqq_valid_until' => $quote['valid'],
                    'rfqq_notes' => 'Demo supplier quote',
                    'rfqq_created_at' => now()->subDays(8),
                ],
                'rfqq_id'
            );
        }
    }

    private function seedSupplierUsers(): void
    {
        $users = [
            [
                'email' => 'supplier@apexsteel.com',
                'name' => 'Robert Chen (Apex Steel)',
                'phone' => '555-100-2001',
                'supplier' => 'APEX-001',
            ],
            [
                'email' => 'supplier@readymix.com',
                'name' => 'Maria Lopez (ReadyMix)',
                'phone' => '555-200-3002',
                'supplier' => 'RMC-002',
            ],
        ];

        foreach ($users as $user) {
            $this->upsert(
                'supplier_users',
                ['email' => $user['email']],
                [
                    'supplier_id' => $this->supplierIds[$user['supplier']],
                    'company_id' => $this->companyId,
                    'name' => $user['name'],
                    'phone' => $user['phone'],
                    'password' => Hash::make('admin123'),
                    'status' => 1,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'id'
            );
        }
    }

    private function seedChangeOrdersAndApprovalRequests(): void
    {
        $bcoId = $this->upsert(
            'budget_change_orders',
            ['bco_number' => 'BCO-2026-0001'],
            [
                'company_id' => $this->companyId,
                'budget_id' => $this->budgetIds['HBR-002:2-05-10'],
                'project_id' => $this->projectIds['HBR-002'],
                'cost_code_id' => $this->costCodeIds['2-05-10'],
                'bco_type' => 'increase',
                'bco_amount' => 18000.00,
                'previous_budget' => 275000.00,
                'new_budget' => 293000.00,
                'bco_reason' => 'Additional fabricated steel required after field verification',
                'bco_notes' => 'Pending owner review and approval',
                'bco_reference' => 'CO-STEEL-17',
                'bco_status' => 'pending_approval',
                'created_by' => $this->managerId,
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            'bco_id'
        );

        $poGrandTotal = (float) DB::table('purchase_order_master')
            ->where('porder_id', $this->poIds['PO-2026-0002'])
            ->value('porder_original_total');

        $pocoId = $this->upsert(
            'po_change_orders',
            ['poco_number' => 'PCO-2026-0001'],
            [
                'company_id' => $this->companyId,
                'purchase_order_id' => $this->poIds['PO-2026-0002'],
                'poco_type' => 'amount_change',
                'poco_amount' => 12500.00,
                'previous_total' => $poGrandTotal,
                'new_total' => $poGrandTotal + 12500.00,
                'poco_description' => 'Add supplemental bracing and connection plates',
                'poco_notes' => 'Pending approval before issue to supplier',
                'poco_reference' => 'PCO-BRIDGE-04',
                'poco_details' => json_encode([
                    ['item' => 'STL-002', 'change' => 'add', 'quantity' => 12, 'unit_price' => 475.00],
                ]),
                'poco_status' => 'pending_approval',
                'created_by' => $this->managerId,
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            'poco_id'
        );

        $requests = [
            [
                'entity_number' => 'PO-2026-0001',
                'workflow_id' => $this->workflowIds['DOT-001:po:1'],
                'request_type' => 'po',
                'entity_id' => $this->poIds['PO-2026-0001'],
                'amount' => 9765.00,
                'requested_by' => $this->managerId,
                'approver_id' => $this->companyAdminId,
                'notes' => 'Awaiting manager approval',
            ],
            [
                'entity_number' => 'BCO-2026-0001',
                'workflow_id' => $this->workflowIds['HBR-002:budget_co:1'],
                'request_type' => 'budget_co',
                'entity_id' => $bcoId,
                'amount' => 18000.00,
                'requested_by' => $this->managerId,
                'approver_id' => $this->companyAdminId,
                'notes' => 'Budget change order pending',
            ],
            [
                'entity_number' => 'PCO-2026-0001',
                'workflow_id' => $this->workflowIds['HBR-002:po_co:1'],
                'request_type' => 'po_co',
                'entity_id' => $pocoId,
                'amount' => 12500.00,
                'requested_by' => $this->managerId,
                'approver_id' => $this->companyAdminId,
                'notes' => 'PO change order pending',
            ],
        ];

        foreach ($requests as $request) {
            $this->upsert(
                'approval_requests',
                [
                    'company_id' => $this->companyId,
                    'request_type' => $request['request_type'],
                    'entity_id' => $request['entity_id'],
                ],
                [
                    'workflow_id' => $request['workflow_id'],
                    'entity_number' => $request['entity_number'],
                    'request_amount' => $request['amount'],
                    'current_level' => 1,
                    'required_levels' => 1,
                    'request_status' => 'pending',
                    'requested_by' => $request['requested_by'],
                    'approval_history' => json_encode([
                        [
                            'action' => 'submitted',
                            'user_id' => $request['requested_by'],
                            'user_name' => 'Project Manager',
                            'level' => 0,
                            'comments' => 'Submitted for approval',
                            'timestamp' => now()->toIso8601String(),
                        ],
                    ]),
                    'current_approver_id' => $request['approver_id'],
                    'request_notes' => $request['notes'],
                    'submitted_at' => now(),
                    'completed_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                'request_id'
            );
        }
    }

    private function costCodeForItem(string $itemCode): string
    {
        return match ($itemCode) {
            'CONC-001', 'CONC-003' => '2-03-30',
            'CONC-002' => '2-03-20',
            'STL-001', 'STL-002' => '2-05-10',
            'LBR-001', 'LBR-002' => '2-06-10',
            'ELEC-001', 'ELEC-002' => '2-16-10',
            'SAFE-001' => '2-01-06',
            default => '2-01-06',
        };
    }

    private function calculateTax(float $subtotal, float $taxRate): float
    {
        return round($subtotal * ($taxRate / 100), 2);
    }

    private function upsert(string $table, array $identity, array $values, string $primaryKey): int
    {
        $existing = DB::table($table)->where($identity)->first();

        if ($existing) {
            DB::table($table)->where($identity)->update($values);
            return (int) $existing->{$primaryKey};
        }

        return (int) DB::table($table)->insertGetId(array_merge($identity, $values), $primaryKey);
    }
}
