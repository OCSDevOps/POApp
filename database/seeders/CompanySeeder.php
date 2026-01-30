<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create default company for existing data
        $defaultCompany = Company::create([
            'name' => 'Default Company',
            'subdomain' => 'default',
            'status' => 1,
            'address' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10001',
            'country' => 'USA',
            'phone' => '555-0100',
            'email' => 'admin@defaultcompany.com',
            'settings' => [
                'budget_constraints_enabled' => true,
                'approval_required_for_overbudget' => true,
                'currency' => 'USD',
            ],
        ]);

        // Create test company for development
        $testCompany = Company::create([
            'name' => 'Test Construction Co',
            'subdomain' => 'test',
            'status' => 1,
            'address' => '456 Oak Avenue',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'zip' => '90001',
            'country' => 'USA',
            'phone' => '555-0200',
            'email' => 'admin@testco.com',
            'settings' => [
                'budget_constraints_enabled' => false,
                'approval_required_for_overbudget' => false,
                'currency' => 'USD',
            ],
        ]);

        // Create another test company
        $acmeCompany = Company::create([
            'name' => 'Acme Builders',
            'subdomain' => 'acme',
            'status' => 1,
            'address' => '789 Pine Street',
            'city' => 'Chicago',
            'state' => 'IL',
            'zip' => '60601',
            'country' => 'USA',
            'phone' => '555-0300',
            'email' => 'admin@acmebuilders.com',
            'settings' => [
                'budget_constraints_enabled' => true,
                'approval_required_for_overbudget' => true,
                'currency' => 'USD',
            ],
        ]);

        $this->command->info('Created companies:');
        $this->command->info("  - {$defaultCompany->name} (ID: {$defaultCompany->id})");
        $this->command->info("  - {$testCompany->name} (ID: {$testCompany->id})");
        $this->command->info("  - {$acmeCompany->name} (ID: {$acmeCompany->id})");
        
        // Update existing records to belong to default company
        $this->assignExistingDataToDefaultCompany($defaultCompany->id);
    }

    /**
     * Assign all existing data to the default company.
     *
     * @param int $companyId
     * @return void
     */
    protected function assignExistingDataToDefaultCompany(int $companyId)
    {
        $tables = [
            'users',
            'project_master' => 'projects',
            'supplier_master' => 'suppliers',
            'purchase_order_master',
            'item_master' => 'items',
            'budget_master' => 'budgets',
            'receive_order_master',
            'item_categories',
            'cost_codes',
            'checklists',
            'equipment',
        ];

        foreach ($tables as $key => $table) {
            $tableName = is_numeric($key) ? $table : $key;
            
            try {
                \DB::table($tableName)
                    ->whereNull('company_id')
                    ->update(['company_id' => $companyId]);
                
                $count = \DB::table($tableName)->where('company_id', $companyId)->count();
                $this->command->info("  Updated {$count} records in {$tableName}");
            } catch (\Exception $e) {
                $this->command->warn("  Could not update {$tableName}: " . $e->getMessage());
            }
        }
    }
}
