<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Development-only controller to seed the database via HTTP.
 * Only available when APP_DEBUG=true.
 * This exists because artisan commands hang on this project.
 */
class DevSeedController extends Controller
{
    public function run()
    {
        if (!config('app.debug')) {
            abort(403, 'Only available in debug mode');
        }

        set_time_limit(120);
        $messages = [];

        try {
            $companyId = $this->seedCompany($messages);
            $this->seedUsers($companyId, $messages);
            $uomIds = $this->seedUnitsOfMeasure($messages);
            $categoryIds = $this->seedItemCategories($companyId, $messages);
            $costCodeIds = $this->seedCostCodes($companyId, $messages);
            $taxGroupIds = $this->seedTaxGroups($messages);
            $projectIds = $this->seedProjects($companyId, $messages);
            $supplierIds = $this->seedSuppliers($companyId, $messages);
            $itemCodes = $this->seedItems($companyId, $categoryIds, $costCodeIds, $uomIds, $messages);

            $messages[] = '';
            $messages[] = '=== Seeding Complete ===';
            $messages[] = '';
            $messages[] = 'Login Credentials (password: admin123):';
            $messages[] = '  Super Admin:     superadmin@demo.com';
            $messages[] = '  Company Admin:   admin@demo.com';
            $messages[] = '  Project Manager: manager@demo.com';
            $messages[] = '  Viewer:          viewer@demo.com';
            $messages[] = '  Regular User:    user@demo.com';

            return response('<pre style="font-family:monospace;padding:20px;background:#f8f9fa;">' .
                e(implode("\n", $messages)) . '</pre>')
                ->header('Content-Type', 'text/html');
        } catch (\Throwable $e) {
            $messages[] = "ERROR: " . $e->getMessage();
            $messages[] = $e->getFile() . ':' . $e->getLine();
            return response('<pre style="color:red;padding:20px;">' .
                e(implode("\n", $messages)) . '</pre>', 500)
                ->header('Content-Type', 'text/html');
        }
    }

    public function debugAuth()
    {
        if (!config('app.debug')) {
            abort(403);
        }

        $email = request('email', 'superadmin@demo.com');
        $password = 'admin123';
        $results = [];

        // 1. Check user exists
        $user = DB::table('users')->where('email', $email)->first();
        if (!$user) {
            return response('<pre>User not found: ' . e($email) . '</pre>', 404);
        }

        $results[] = "User found: id={$user->id}, email={$user->email}, u_type={$user->u_type}";
        $results[] = "Password hash: {$user->password}";
        $results[] = "Hash length: " . strlen($user->password);
        $results[] = "Hash starts with \$2y\$: " . (str_starts_with($user->password, '$2y$') ? 'YES' : 'NO');
        $results[] = "";

        // 2. Test Hash::check directly
        $hashCheck = Hash::check($password, $user->password);
        $results[] = "Hash::check('admin123', stored_hash): " . ($hashCheck ? 'TRUE (MATCH)' : 'FALSE (NO MATCH)');

        // 3. Test password_verify directly
        $phpCheck = password_verify($password, $user->password);
        $results[] = "password_verify('admin123', stored_hash): " . ($phpCheck ? 'TRUE' : 'FALSE');
        $results[] = "";

        // 4. Generate a new hash and verify it
        $newHash = Hash::make($password);
        $results[] = "Fresh Hash::make('admin123'): {$newHash}";
        $results[] = "Hash::check against fresh hash: " . (Hash::check($password, $newHash) ? 'TRUE' : 'FALSE');
        $results[] = "";

        // 5. Try Auth::attempt
        $results[] = "PHP version: " . phpversion();
        $results[] = "PASSWORD_BCRYPT constant: " . PASSWORD_BCRYPT;
        $results[] = "";

        try {
            $authResult = \Illuminate\Support\Facades\Auth::attempt(['email' => $email, 'password' => $password]);
            $results[] = "Auth::attempt result: " . ($authResult ? 'TRUE (SUCCESS)' : 'FALSE (FAILED)');
            if ($authResult) {
                $authUser = \Illuminate\Support\Facades\Auth::user();
                $results[] = "Auth::user(): id={$authUser->id}, email={$authUser->email}";
                \Illuminate\Support\Facades\Auth::logout();
            }
        } catch (\Throwable $e) {
            $results[] = "Auth::attempt EXCEPTION: " . $e->getMessage();
            $results[] = "  at " . $e->getFile() . ':' . $e->getLine();
        }

        // 6. Fix: update password if it doesn't verify
        if (!$hashCheck) {
            $results[] = "";
            $results[] = "--- AUTO-FIX: Updating password hash ---";
            DB::table('users')->where('id', $user->id)->update(['password' => $newHash]);
            $results[] = "Password updated to fresh hash.";

            // Re-test
            $updatedUser = DB::table('users')->where('id', $user->id)->first();
            $reCheck = Hash::check($password, $updatedUser->password);
            $results[] = "Re-check after update: " . ($reCheck ? 'TRUE (FIXED)' : 'FALSE (STILL BROKEN)');
        }

        return response('<pre style="padding:20px;font-family:monospace;">' .
            e(implode("\n", $results)) . '</pre>')
            ->header('Content-Type', 'text/html');
    }

    private function seedCompany(array &$msg): int
    {
        $existing = DB::table('companies')->where('name', 'Demo Construction Co')->first();
        if ($existing) {
            $msg[] = '[SKIP] Company "Demo Construction Co" already exists (id=' . $existing->id . ')';
            return $existing->id;
        }
        $id = DB::table('companies')->insertGetId([
            'name' => 'Demo Construction Co', 'subdomain' => 'demo', 'status' => 1,
            'settings' => json_encode(['currency' => 'USD', 'timezone' => 'America/New_York']),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $msg[] = "[OK] Created company (id=$id)";
        return $id;
    }

    private function seedUsers(int $companyId, array &$msg): void
    {
        $users = [
            ['name' => 'Super Admin',    'email' => 'superadmin@demo.com', 'username' => 'superadmin',   'u_type' => 1],
            ['name' => 'Company Admin',  'email' => 'admin@demo.com',      'username' => 'companyadmin', 'u_type' => 2],
            ['name' => 'Project Manager','email' => 'manager@demo.com',    'username' => 'manager',      'u_type' => 3],
            ['name' => 'Viewer User',    'email' => 'viewer@demo.com',     'username' => 'viewer',       'u_type' => 4],
            ['name' => 'Regular User',   'email' => 'user@demo.com',       'username' => 'user',         'u_type' => 0],
        ];
        foreach ($users as $u) {
            if (DB::table('users')->where('email', $u['email'])->exists()) {
                // Always reset password to ensure it's valid
                DB::table('users')->where('email', $u['email'])->update([
                    'password' => Hash::make('admin123'),
                    'company_id' => $companyId,
                    'u_type' => $u['u_type'],
                    'u_status' => 1,
                ]);
                $msg[] = "[OK] User {$u['email']} - password reset";
                continue;
            }
            DB::table('users')->insert([
                'name' => $u['name'], 'email' => $u['email'], 'username' => $u['username'],
                'password' => Hash::make('admin123'), 'company_id' => $companyId,
                'u_type' => $u['u_type'], 'u_status' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $msg[] = "[OK] User {$u['email']} (u_type={$u['u_type']})";
        }
    }

    private function seedUnitsOfMeasure(array &$msg): array
    {
        $uoms = ['Each', 'Box', 'Cubic Yard', 'Linear Foot', 'Square Foot', 'Ton', 'Gallon', 'Bag'];
        $ids = [];
        foreach ($uoms as $name) {
            $existing = DB::table('unit_of_measure_tab')->where('uom_name', $name)->first();
            if ($existing) { $ids[] = $existing->uom_id; continue; }
            $ids[] = DB::table('unit_of_measure_tab')->insertGetId([
                'uom_name' => $name, 'uom_detail' => $name,
                'uom_createdate' => now(), 'uom_createby' => 1, 'uom_status' => 1,
            ], 'uom_id');
        }
        $msg[] = "[OK] UOM: " . count($ids);
        return $ids;
    }

    private function seedItemCategories(int $companyId, array &$msg): array
    {
        $categories = ['Concrete & Masonry', 'Structural Steel', 'Lumber & Wood', 'Electrical',
                        'Plumbing', 'HVAC', 'Safety Equipment', 'Rental Equipment'];
        $ids = [];
        foreach ($categories as $name) {
            $existing = DB::table('item_category_tab')->where('icat_name', $name)->where('company_id', $companyId)->first();
            if ($existing) { $ids[] = $existing->icat_id; continue; }
            $ids[] = DB::table('item_category_tab')->insertGetId([
                'icat_name' => $name, 'icat_details' => "$name materials",
                'icat_status' => 1, 'icat_createby' => 1, 'icat_createdate' => now(),
                'company_id' => $companyId,
            ], 'icat_id');
        }
        $msg[] = "[OK] Categories: " . count($ids);
        return $ids;
    }

    private function seedCostCodes(int $companyId, array &$msg): array
    {
        $codes = [
            ['cc_no' => '01', 'cc_description' => 'General Requirements', 'cc_full_code' => '01', 'cc_level' => 1, 'cc_parent_code' => '01'],
            ['cc_no' => '03', 'cc_description' => 'Concrete',             'cc_full_code' => '03', 'cc_level' => 1, 'cc_parent_code' => '03'],
            ['cc_no' => '05', 'cc_description' => 'Metals',               'cc_full_code' => '05', 'cc_level' => 1, 'cc_parent_code' => '05'],
            ['cc_no' => '06', 'cc_description' => 'Wood/Plastics',        'cc_full_code' => '06', 'cc_level' => 1, 'cc_parent_code' => '06'],
            ['cc_no' => '26', 'cc_description' => 'Electrical',           'cc_full_code' => '26', 'cc_level' => 1, 'cc_parent_code' => '26'],
            ['cc_no' => '03-10', 'cc_description' => 'Cast-in-Place Concrete', 'cc_full_code' => '03-10', 'cc_level' => 2, 'cc_parent_code' => '03'],
            ['cc_no' => '03-20', 'cc_description' => 'Reinforcing Steel',      'cc_full_code' => '03-20', 'cc_level' => 2, 'cc_parent_code' => '03'],
            ['cc_no' => '05-10', 'cc_description' => 'Structural Steel',       'cc_full_code' => '05-10', 'cc_level' => 2, 'cc_parent_code' => '05'],
            ['cc_no' => '26-10', 'cc_description' => 'Medium-Voltage Elec',    'cc_full_code' => '26-10', 'cc_level' => 2, 'cc_parent_code' => '26'],
        ];
        $ids = [];
        foreach ($codes as $cc) {
            $existing = DB::table('cost_code_master')->where('cc_no', $cc['cc_no'])->where('company_id', $companyId)->first();
            if ($existing) { $ids[] = $existing->cc_id; continue; }
            $ids[] = DB::table('cost_code_master')->insertGetId(array_merge($cc, [
                'cc_status' => 1, 'cc_createby' => 1, 'cc_createdate' => now(), 'company_id' => $companyId,
            ]), 'cc_id');
        }
        $msg[] = "[OK] Cost codes: " . count($ids);
        return $ids;
    }

    private function seedTaxGroups(array &$msg): array
    {
        $groups = [
            ['name' => 'No Tax', 'percentage' => 0.00],
            ['name' => 'State Tax', 'percentage' => 6.25],
            ['name' => 'Full Tax', 'percentage' => 8.875],
        ];
        $ids = [];
        foreach ($groups as $g) {
            $existing = DB::table('taxgroup_master')->where('name', $g['name'])->first();
            if ($existing) { $ids[] = $existing->id; continue; }
            $ids[] = DB::table('taxgroup_master')->insertGetId(array_merge($g, ['created_at' => now()]));
        }
        $msg[] = "[OK] Tax groups: " . count($ids);
        return $ids;
    }

    private function seedProjects(int $companyId, array &$msg): array
    {
        $projects = [
            ['proj_name' => 'Downtown Office Tower',     'proj_number' => 'DOT-001'],
            ['proj_name' => 'Harbor Bridge Renovation',  'proj_number' => 'HBR-002'],
            ['proj_name' => 'Riverside School Expansion', 'proj_number' => 'RSE-003'],
        ];
        $ids = [];
        foreach ($projects as $p) {
            $existing = DB::table('project_master')->where('proj_number', $p['proj_number'])->where('company_id', $companyId)->first();
            if ($existing) { $ids[] = $existing->proj_id; continue; }
            $ids[] = DB::table('project_master')->insertGetId(array_merge($p, [
                'proj_status' => 1, 'proj_createby' => 1, 'proj_createdate' => now(),
                'proj_address' => 'Demo Address', 'proj_contact' => 0,
                'proj_start_date' => '2026-01-15', 'proj_end_date' => '2027-06-30',
                'company_id' => $companyId,
            ]), 'proj_id');
        }
        $msg[] = "[OK] Projects: " . count($ids);
        return $ids;
    }

    private function seedSuppliers(int $companyId, array &$msg): array
    {
        $suppliers = [
            ['sup_name' => 'Apex Steel Industries', 'sup_code' => 'APEX-001', 'sup_email' => 'orders@apexsteel.com'],
            ['sup_name' => 'ReadyMix Concrete Co',  'sup_code' => 'RMC-002',  'sup_email' => 'dispatch@readymix.com'],
            ['sup_name' => 'National Lumber Supply', 'sup_code' => 'NLS-003',  'sup_email' => 'sales@nationallumber.com'],
            ['sup_name' => 'ProElectric Wholesale',  'sup_code' => 'PEW-004',  'sup_email' => 'info@proelectric.com'],
        ];
        $ids = [];
        foreach ($suppliers as $s) {
            $existing = DB::table('supplier_master')->where('sup_code', $s['sup_code'])->where('company_id', $companyId)->first();
            if ($existing) { $ids[] = $existing->sup_id; continue; }
            $ids[] = DB::table('supplier_master')->insertGetId(array_merge($s, [
                'sup_status' => 1, 'sup_createby' => 1, 'sup_createdate' => now(),
                'sup_address' => 'Demo Address', 'sup_contact_person' => 'Demo Contact', 'sup_phone' => '555-000-0000',
                'company_id' => $companyId,
            ]), 'sup_id');
        }
        $msg[] = "[OK] Suppliers: " . count($ids);
        return $ids;
    }

    private function seedItems(int $companyId, array $catIds, array $ccIds, array $uomIds, array &$msg): array
    {
        $items = [
            ['item_code' => 'CONC-001', 'item_name' => 'Ready Mix Concrete 4000 PSI', 'item_cat_ms' => $catIds[0] ?? 1, 'item_ccode_ms' => $ccIds[5] ?? 1, 'item_unit_ms' => $uomIds[2] ?? 1],
            ['item_code' => 'STL-001',  'item_name' => 'W12x26 Structural Beam',      'item_cat_ms' => $catIds[1] ?? 1, 'item_ccode_ms' => $ccIds[7] ?? 1, 'item_unit_ms' => $uomIds[3] ?? 1],
            ['item_code' => 'LBR-001',  'item_name' => '2x4x8 SPF Stud',              'item_cat_ms' => $catIds[2] ?? 1, 'item_ccode_ms' => $ccIds[3] ?? 1, 'item_unit_ms' => $uomIds[0] ?? 1],
            ['item_code' => 'ELEC-001', 'item_name' => '12/2 Romex Wire 250ft',       'item_cat_ms' => $catIds[3] ?? 1, 'item_ccode_ms' => $ccIds[8] ?? 1, 'item_unit_ms' => $uomIds[1] ?? 1],
            ['item_code' => 'SAFE-001', 'item_name' => 'Hard Hat - White',             'item_cat_ms' => $catIds[6] ?? 1, 'item_ccode_ms' => $ccIds[0] ?? 1, 'item_unit_ms' => $uomIds[0] ?? 1],
        ];
        $codes = [];
        foreach ($items as $item) {
            $existing = DB::table('item_master')->where('item_code', $item['item_code'])->where('company_id', $companyId)->first();
            if ($existing) { $codes[] = $item['item_code']; continue; }
            DB::table('item_master')->insert(array_merge($item, [
                'item_description' => $item['item_name'],
                'item_status' => 1, 'item_createby' => 1, 'item_createdate' => now(),
                'company_id' => $companyId,
            ]));
            $codes[] = $item['item_code'];
        }
        $msg[] = "[OK] Items: " . count($codes);
        return $codes;
    }
}
