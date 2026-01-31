<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Phase2TestDataSeeder extends Seeder
{
    /**
     * Seed test data for Phase 2.2 Budget Management features
     * 
     * Seeds:
     * - 3 Companies
     * - Cost Code Hierarchy (XX-XX-XX format)
     * - Project Roles with approval limits
     * - Approval Workflows
     * - Sample budgets with realistic values
     */
    public function run()
    {
        echo "Starting Phase 2 Test Data Seeding...\n\n";
        
        // Get existing data
        $companies = DB::table('companies')->get();
        $projects = DB::table('project_master')->get();
        $users = DB::table('user_master')->get();
        
        if ($companies->isEmpty()) {
            echo "❌ No companies found. Run CompanySeeder first.\n";
            return;
        }
        
        if ($projects->isEmpty()) {
            echo "❌ No projects found. Create projects first.\n";
            return;
        }
        
        $company = $companies->first();
        $project = $projects->first();
        
        echo "Using Company: {$company->name} (ID: {$company->id})\n";
        echo "Using Project: {$project->proj_name} (ID: {$project->proj_id})\n\n";
        
        // ==================================================
        // 1. SEED COST CODE HIERARCHY (XX-XX-XX format)
        // ==================================================
        echo "Seeding Cost Code Hierarchy...\n";
        
        $costCodes = [
            // Level 1: Categories (XX-00-00)
            ['code' => '01-00-00', 'name' => 'General Conditions', 'level' => 1, 'parent' => null, 'order' => 1],
            ['code' => '02-00-00', 'name' => 'Site Work', 'level' => 1, 'parent' => null, 'order' => 2],
            ['code' => '03-00-00', 'name' => 'Concrete', 'level' => 1, 'parent' => null, 'order' => 3],
            ['code' => '04-00-00', 'name' => 'Masonry', 'level' => 1, 'parent' => null, 'order' => 4],
            ['code' => '05-00-00', 'name' => 'Metals', 'level' => 1, 'parent' => null, 'order' => 5],
            ['code' => '06-00-00', 'name' => 'Wood & Plastics', 'level' => 1, 'parent' => null, 'order' => 6],
            ['code' => '09-00-00', 'name' => 'Finishes', 'level' => 1, 'parent' => null, 'order' => 7],
            ['code' => '15-00-00', 'name' => 'Mechanical', 'level' => 1, 'parent' => null, 'order' => 8],
            ['code' => '16-00-00', 'name' => 'Electrical', 'level' => 1, 'parent' => null, 'order' => 9],
            
            // Level 2: Subcategories (XX-XX-00) - Site Work
            ['code' => '02-10-00', 'name' => 'Site Preparation', 'level' => 2, 'parent' => '02-00-00', 'order' => 1],
            ['code' => '02-20-00', 'name' => 'Earthwork', 'level' => 2, 'parent' => '02-00-00', 'order' => 2],
            ['code' => '02-30-00', 'name' => 'Utilities', 'level' => 2, 'parent' => '02-00-00', 'order' => 3],
            ['code' => '02-40-00', 'name' => 'Paving', 'level' => 2, 'parent' => '02-00-00', 'order' => 4],
            
            // Level 3: Detail Codes (XX-XX-XX) - Site Preparation
            ['code' => '02-10-01', 'name' => 'Demolition', 'level' => 3, 'parent' => '02-10-00', 'order' => 1],
            ['code' => '02-10-02', 'name' => 'Tree Removal', 'level' => 3, 'parent' => '02-10-00', 'order' => 2],
            ['code' => '02-10-03', 'name' => 'Site Clearing', 'level' => 3, 'parent' => '02-10-00', 'order' => 3],
            
            // Level 3: Earthwork Detail
            ['code' => '02-20-01', 'name' => 'Excavation', 'level' => 3, 'parent' => '02-20-00', 'order' => 1],
            ['code' => '02-20-02', 'name' => 'Grading', 'level' => 3, 'parent' => '02-20-00', 'order' => 2],
            ['code' => '02-20-03', 'name' => 'Fill & Compaction', 'level' => 3, 'parent' => '02-20-00', 'order' => 3],
            
            // Level 2: Concrete Subcategories
            ['code' => '03-10-00', 'name' => 'Concrete Formwork', 'level' => 2, 'parent' => '03-00-00', 'order' => 1],
            ['code' => '03-20-00', 'name' => 'Concrete Reinforcing', 'level' => 2, 'parent' => '03-00-00', 'order' => 2],
            ['code' => '03-30-00', 'name' => 'Cast-in-Place Concrete', 'level' => 2, 'parent' => '03-00-00', 'order' => 3],
            
            // Level 3: Concrete Details
            ['code' => '03-30-01', 'name' => 'Foundation Concrete', 'level' => 3, 'parent' => '03-30-00', 'order' => 1],
            ['code' => '03-30-02', 'name' => 'Slab-on-Grade', 'level' => 3, 'parent' => '03-30-00', 'order' => 2],
            ['code' => '03-30-03', 'name' => 'Structural Concrete', 'level' => 3, 'parent' => '03-30-00', 'order' => 3],
            
            // Level 2: Mechanical Subcategories
            ['code' => '15-10-00', 'name' => 'Plumbing', 'level' => 2, 'parent' => '15-00-00', 'order' => 1],
            ['code' => '15-20-00', 'name' => 'HVAC', 'level' => 2, 'parent' => '15-00-00', 'order' => 2],
            ['code' => '15-30-00', 'name' => 'Fire Protection', 'level' => 2, 'parent' => '15-00-00', 'order' => 3],
            
            // Level 3: HVAC Details
            ['code' => '15-20-01', 'name' => 'Ductwork', 'level' => 3, 'parent' => '15-20-00', 'order' => 1],
            ['code' => '15-20-02', 'name' => 'HVAC Equipment', 'level' => 3, 'parent' => '15-20-00', 'order' => 2],
            ['code' => '15-20-03', 'name' => 'Controls', 'level' => 3, 'parent' => '15-20-00', 'order' => 3],
        ];
        
        foreach ($costCodes as $ccData) {
            // Check if cost code exists
            $exists = DB::table('cost_code_master')
                ->where('cc_no', $ccData['code'])
                ->exists();
            
            if (!$exists) {
                DB::table('cost_code_master')->insert([
                    'cc_no' => $ccData['code'],
                    'cc_name' => $ccData['name'],
                    'parent_code' => $ccData['parent'],
                    'full_code' => $ccData['code'],
                    'level' => $ccData['level'],
                    'sortorder' => $ccData['order'],
                    'is_active' => 1,
                    'cc_created_by' => $users->first()->u_id ?? 1,
                ]);
                echo "  ✓ Created cost code: {$ccData['code']} - {$ccData['name']}\n";
            }
        }
        
        echo "✓ Cost code hierarchy seeded\n\n";
        
        // ==================================================
        // 2. SEED PROJECT ROLES WITH APPROVAL LIMITS
        // ==================================================
        echo "Seeding Project Roles...\n";
        
        $roles = [
            ['role' => 'Project Manager', 'user_id' => $users->first()->u_id ?? 1, 'approval_limit' => 10000.00],
            ['role' => 'Finance Manager', 'user_id' => $users->skip(1)->first()->u_id ?? 2, 'approval_limit' => 50000.00],
            ['role' => 'Director', 'user_id' => $users->first()->u_id ?? 1, 'approval_limit' => 100000.00],
            ['role' => 'Executive', 'user_id' => $users->first()->u_id ?? 1, 'approval_limit' => null], // Unlimited
        ];
        
        foreach ($roles as $roleData) {
            $exists = DB::table('project_roles')
                ->where('project_id', $project->proj_id)
                ->where('role', $roleData['role'])
                ->exists();
            
            if (!$exists) {
                DB::table('project_roles')->insert([
                    'company_id' => $company->id,
                    'project_id' => $project->proj_id,
                    'user_id' => $roleData['user_id'],
                    'role' => $roleData['role'],
                    'approval_limit' => $roleData['approval_limit'],
                    'is_active' => 1,
                    'created_at' => now(),
                ]);
                echo "  ✓ Created role: {$roleData['role']} (limit: " . 
                     ($roleData['approval_limit'] ? '$' . number_format($roleData['approval_limit'], 2) : 'Unlimited') . ")\n";
            }
        }
        
        echo "✓ Project roles seeded\n\n";
        
        // ==================================================
        // 3. SEED APPROVAL WORKFLOWS
        // ==================================================
        echo "Seeding Approval Workflows...\n";
        
        $workflows = [
            [
                'name' => 'Standard PO Approval',
                'type' => 'purchase_order',
                'threshold' => 5000.00,
                'is_role_based' => 1,
                'approval_role' => 'Project Manager',
                'level' => 1,
            ],
            [
                'name' => 'Large PO Approval',
                'type' => 'purchase_order',
                'threshold' => 25000.00,
                'is_role_based' => 1,
                'approval_role' => 'Finance Manager',
                'level' => 2,
            ],
            [
                'name' => 'Executive PO Approval',
                'type' => 'purchase_order',
                'threshold' => 75000.00,
                'is_role_based' => 1,
                'approval_role' => 'Executive',
                'level' => 3,
            ],
            [
                'name' => 'Budget Change Order - Standard',
                'type' => 'budget_change_order',
                'threshold' => 10000.00,
                'is_role_based' => 1,
                'approval_role' => 'Finance Manager',
                'level' => 1,
            ],
            [
                'name' => 'Budget Change Order - Large',
                'type' => 'budget_change_order',
                'threshold' => 50000.00,
                'is_role_based' => 1,
                'approval_role' => 'Director',
                'level' => 2,
            ],
            [
                'name' => 'PO Change Order - Standard',
                'type' => 'po_change_order',
                'threshold' => 5000.00,
                'is_role_based' => 1,
                'approval_role' => 'Project Manager',
                'level' => 1,
            ],
        ];
        
        foreach ($workflows as $wfData) {
            $exists = DB::table('approval_workflows')
                ->where('name', $wfData['name'])
                ->where('company_id', $company->id)
                ->exists();
            
            if (!$exists) {
                DB::table('approval_workflows')->insert([
                    'company_id' => $company->id,
                    'project_id' => $project->proj_id,
                    'name' => $wfData['name'],
                    'entity_type' => $wfData['type'],
                    'threshold_amount' => $wfData['threshold'],
                    'is_role_based' => $wfData['is_role_based'],
                    'approval_role' => $wfData['approval_role'],
                    'approval_level' => $wfData['level'],
                    'is_active' => 1,
                    'created_at' => now(),
                ]);
                echo "  ✓ Created workflow: {$wfData['name']} (threshold: \${$wfData['threshold']})\n";
            }
        }
        
        echo "✓ Approval workflows seeded\n\n";
        
        // ==================================================
        // 4. SEED SAMPLE BUDGETS WITH REALISTIC VALUES
        // ==================================================
        echo "Seeding Sample Budgets...\n";
        
        // Get some cost codes for budgets
        $costCodeIds = DB::table('cost_code_master')
            ->whereIn('cc_no', ['02-10-01', '02-20-01', '03-30-01', '15-20-02', '16-00-00'])
            ->pluck('cc_id', 'cc_no');
        
        $budgets = [
            ['cost_code' => '02-10-01', 'original' => 25000.00, 'current' => 25000.00, 'committed' => 18000.00, 'actual' => 15000.00],
            ['cost_code' => '02-20-01', 'original' => 50000.00, 'current' => 55000.00, 'committed' => 42000.00, 'actual' => 38000.00],
            ['cost_code' => '03-30-01', 'original' => 150000.00, 'current' => 150000.00, 'committed' => 125000.00, 'actual' => 110000.00],
            ['cost_code' => '15-20-02', 'original' => 75000.00, 'current' => 80000.00, 'committed' => 72000.00, 'actual' => 65000.00],
            ['cost_code' => '16-00-00', 'original' => 100000.00, 'current' => 100000.00, 'committed' => 85000.00, 'actual' => 75000.00],
        ];
        
        foreach ($budgets as $budgetData) {
            if (!isset($costCodeIds[$budgetData['cost_code']])) {
                continue;
            }
            
            $ccId = $costCodeIds[$budgetData['cost_code']];
            
            $exists = DB::table('budget_master')
                ->where('budget_project_id', $project->proj_id)
                ->where('budget_cost_code_id', $ccId)
                ->exists();
            
            if (!$exists) {
                $utilization = ($budgetData['committed'] + $budgetData['actual']) / $budgetData['current'] * 100;
                
                DB::table('budget_master')->insert([
                    'budget_project_id' => $project->proj_id,
                    'budget_cost_code_id' => $ccId,
                    'budget_original_amount' => $budgetData['original'],
                    'budget_revised_amount' => $budgetData['current'],
                    'original_amount' => $budgetData['original'],
                    'committed' => $budgetData['committed'],
                    'actual' => $budgetData['actual'],
                    'variance' => $budgetData['current'] - ($budgetData['committed'] + $budgetData['actual']),
                    'warning_notification_sent' => $utilization >= 75 ? 1 : 0,
                    'critical_notification_sent' => $utilization >= 90 ? 1 : 0,
                    'budget_created_at' => now(),
                ]);
                
                $status = $utilization >= 90 ? '🔴' : ($utilization >= 75 ? '🟡' : '🟢');
                echo "  ✓ Created budget for {$budgetData['cost_code']}: \${$budgetData['current']} " .
                     "({$status} {$utilization}% utilized)\n";
            }
        }
        
        echo "✓ Sample budgets seeded\n\n";
        
        echo "================================================\n";
        echo "✓ Phase 2 Test Data Seeding Complete!\n";
        echo "================================================\n\n";
        
        echo "Summary:\n";
        echo "  - " . count($costCodes) . " cost codes in 3-level hierarchy\n";
        echo "  - " . count($roles) . " project roles with approval limits\n";
        echo "  - " . count($workflows) . " approval workflows\n";
        echo "  - " . count($budgets) . " sample budgets with realistic data\n\n";
        
        echo "Next Steps:\n";
        echo "  1. Test budget management features in UI\n";
        echo "  2. Create Purchase Orders to test approval workflows\n";
        echo "  3. Create Budget Change Orders to test BCO approvals\n";
        echo "  4. View budget reports and dashboards\n";
    }
}
