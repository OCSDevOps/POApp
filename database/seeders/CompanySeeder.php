<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Services\CostCodeTemplateProvisioningService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $companies = collect([
            [
                'name' => 'Default Company',
                'subdomain' => 'default',
                'company_code' => 'DEFAULT',
                'email' => 'admin@defaultcompany.com',
                'phone' => '555-0100',
                'address' => '123 Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'USA',
                'status' => 1,
                'settings' => [
                    'budget_constraints_enabled' => true,
                    'approval_required_for_overbudget' => true,
                    'currency' => 'USD',
                ],
            ],
            [
                'name' => 'Test Construction Co',
                'subdomain' => 'test',
                'company_code' => 'TESTCO',
                'email' => 'admin@testco.com',
                'phone' => '555-0200',
                'address' => '456 Oak Avenue',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90001',
                'country' => 'USA',
                'status' => 1,
                'settings' => [
                    'budget_constraints_enabled' => false,
                    'approval_required_for_overbudget' => false,
                    'currency' => 'USD',
                ],
            ],
            [
                'name' => 'Acme Builders',
                'subdomain' => 'acme',
                'company_code' => 'ACME',
                'email' => 'admin@acmebuilders.com',
                'phone' => '555-0300',
                'address' => '789 Pine Street',
                'city' => 'Chicago',
                'state' => 'IL',
                'zip' => '60601',
                'country' => 'USA',
                'status' => 1,
                'settings' => [
                    'budget_constraints_enabled' => true,
                    'approval_required_for_overbudget' => true,
                    'currency' => 'USD',
                ],
            ],
        ])->map(fn (array $company) => Company::updateOrCreate(
            ['subdomain' => $company['subdomain']],
            $company
        ));

        $defaultCompany = $companies->firstWhere('subdomain', 'default');

        $this->command->info('Prepared companies:');
        foreach ($companies as $company) {
            $this->command->info("  - {$company->name} (ID: {$company->id})");
        }

        if ($defaultCompany) {
            $this->assignExistingDataToDefaultCompany((int) $defaultCompany->id);
        }

        $provisioningService = app(CostCodeTemplateProvisioningService::class);

        foreach ($companies as $company) {
            $summary = $provisioningService->provisionForCompany((int) $company->id);
            $this->command->info(sprintf(
                '  Provisioned %d March 2020 cost codes and %d reusable templates for %s',
                $summary['cost_codes'],
                $summary['templates'],
                $company->name
            ));
        }
    }

    protected function assignExistingDataToDefaultCompany(int $companyId): void
    {
        $tables = [
            'users',
            'project_master',
            'project_details',
            'supplier_master',
            'unit_of_measure_tab',
            'item_category_tab',
            'cost_code_master',
            'item_master',
            'supplier_catalog_tab',
            'budget_master',
            'purchase_order_master',
            'purchase_order_details',
            'receive_order_master',
            'receive_order_details',
            'supplier_users',
            'item_pricing',
            'rfq_master',
            'rfq_items',
            'rfq_suppliers',
            'rfq_quotes',
            'project_cost_codes',
            'budget_change_orders',
            'po_change_orders',
            'approval_workflows',
            'approval_requests',
            'project_roles',
            'attachments',
        ];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'company_id')) {
                continue;
            }

            $updated = DB::table($tableName)
                ->where(function ($query) {
                    $query->whereNull('company_id')
                        ->orWhere('company_id', 0);
                })
                ->update(['company_id' => $companyId]);

            if ($updated > 0) {
                $this->command->info("  Updated {$updated} existing records in {$tableName}");
            }
        }
    }
}
