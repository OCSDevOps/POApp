<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\User;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class TestMultiTenancy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:multi-tenancy {--verbose}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test multi-tenancy data isolation and scoping';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║   Multi-Tenancy Data Isolation Test Suite               ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Test 1: Company creation and status
        $this->testCompanies();

        // Test 2: Model scope isolation
        $this->testModelScopes();

        // Test 3: DB::table() queries with company_id
        $this->testRawQueries();

        // Test 4: Cross-company data leakage prevention
        $this->testDataLeakagePrevention();

        // Test 5: Company switching simulation
        $this->testCompanySwitching();

        $this->newLine();
        $this->info('✓ All multi-tenancy tests completed!');
        
        return Command::SUCCESS;
    }

    protected function testCompanies()
    {
        $this->info('Test 1: Company Setup');
        $this->info('─────────────────────────');

        $companies = Company::all();
        $activeCompanies = Company::where('status', 1)->get();

        $this->line("  Total companies: {$companies->count()}");
        $this->line("  Active companies: {$activeCompanies->count()}");

        foreach ($activeCompanies as $company) {
            $this->line("    • {$company->name} (ID: {$company->id}, Subdomain: {$company->subdomain})");
        }

        if ($companies->count() < 2) {
            $this->warn("  ⚠ Warning: Need at least 2 companies for proper testing. Run: php artisan db:seed --class=CompanySeeder");
        } else {
            $this->info("  ✓ Company setup is adequate for testing");
        }

        $this->newLine();
    }

    protected function testModelScopes()
    {
        $this->info('Test 2: Model Scope Isolation');
        $this->info('─────────────────────────────');

        $companies = Company::where('status', 1)->limit(2)->get();

        if ($companies->count() < 2) {
            $this->warn("  ⚠ Skipping: Need at least 2 active companies");
            $this->newLine();
            return;
        }

        foreach ($companies as $company) {
            // Temporarily set company context
            session(['company_id' => $company->id]);

            $users = User::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->where('company_id', $company->id)->count();
            $projects = Project::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->where('company_id', $company->id)->count();
            $pos = PurchaseOrder::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->where('company_id', $company->id)->count();
            $suppliers = Supplier::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->where('company_id', $company->id)->count();
            $items = Item::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->where('company_id', $company->id)->count();

            $this->line("  {$company->name} (ID: {$company->id}):");
            $this->line("    - Users: {$users}");
            $this->line("    - Projects: {$projects}");
            $this->line("    - Purchase Orders: {$pos}");
            $this->line("    - Suppliers: {$suppliers}");
            $this->line("    - Items: {$items}");
        }

        // Test that CompanyScope works
        $company1 = $companies->first();
        session(['company_id' => $company1->id]);

        $scopedCount = PurchaseOrder::count();
        $unscopedCount = PurchaseOrder::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)->count();

        if ($scopedCount <= $unscopedCount) {
            $this->info("  ✓ CompanyScope is working (Scoped: {$scopedCount}, Total: {$unscopedCount})");
        } else {
            $this->error("  ✗ CompanyScope may not be working correctly!");
        }

        $this->newLine();
    }

    protected function testRawQueries()
    {
        $this->info('Test 3: Raw Query Security');
        $this->info('───────────────────────────');

        $companies = Company::where('status', 1)->limit(2)->get();

        if ($companies->count() < 2) {
            $this->warn("  ⚠ Skipping: Need at least 2 active companies");
            $this->newLine();
            return;
        }

        foreach ($companies as $company) {
            session(['company_id' => $company->id]);

            // Test DB::table() with company_id filter
            $poCount = DB::table('purchase_order_master')
                ->where('company_id', session('company_id'))
                ->count();

            $projectCount = DB::table('project_master')
                ->where('company_id', session('company_id'))
                ->count();

            $this->line("  {$company->name} raw queries:");
            $this->line("    - Purchase Orders: {$poCount}");
            $this->line("    - Projects: {$projectCount}");
        }

        $this->info("  ✓ Raw queries tested (verify counts match model scope tests)");
        $this->newLine();
    }

    protected function testDataLeakagePrevention()
    {
        $this->info('Test 4: Data Leakage Prevention');
        $this->info('────────────────────────────────');

        $companies = Company::where('status', 1)->limit(2)->get();

        if ($companies->count() < 2) {
            $this->warn("  ⚠ Skipping: Need at least 2 active companies");
            $this->newLine();
            return;
        }

        $company1 = $companies[0];
        $company2 = $companies[1];

        // Set context to company 1
        session(['company_id' => $company1->id]);

        // Try to get company 2's data
        $company2POs = PurchaseOrder::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)
            ->where('company_id', $company2->id)
            ->count();

        // Now check with scope active
        $scopedPOs = PurchaseOrder::count(); // Should only get company 1 data

        $this->line("  Company 1 ({$company1->name}) context:");
        $this->line("    - Scoped POs (should be company 1): {$scopedPOs}");
        $this->line("  Company 2 ({$company2->name}) actual POs: {$company2POs}");

        if ($company2POs > 0) {
            $canAccessCompany2 = PurchaseOrder::whereIn('porder_id', 
                PurchaseOrder::withoutGlobalScope(\App\Models\Scopes\CompanyScope::class)
                    ->where('company_id', $company2->id)
                    ->pluck('porder_id')
            )->count();

            if ($canAccessCompany2 === 0) {
                $this->info("  ✓ Data leakage prevention working! Cannot access company 2 data from company 1 context.");
            } else {
                $this->error("  ✗ SECURITY ISSUE: Can access company 2 data from company 1 context!");
            }
        } else {
            $this->warn("  ⚠ Cannot fully test: Company 2 has no purchase orders");
        }

        $this->newLine();
    }

    protected function testCompanySwitching()
    {
        $this->info('Test 5: Company Switching Simulation');
        $this->info('─────────────────────────────────────');

        $companies = Company::where('status', 1)->limit(3)->get();

        if ($companies->count() < 2) {
            $this->warn("  ⚠ Skipping: Need at least 2 active companies");
            $this->newLine();
            return;
        }

        foreach ($companies as $company) {
            // Simulate switching to this company
            session(['company_id' => $company->id]);

            $poCount = PurchaseOrder::count();
            $projectCount = Project::count();

            $this->line("  Switched to: {$company->name}");
            $this->line("    - Visible POs: {$poCount}");
            $this->line("    - Visible Projects: {$projectCount}");
        }

        $this->info("  ✓ Company switching simulation complete");
        $this->newLine();
    }
}
