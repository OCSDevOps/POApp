<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateToDefaultCompany extends Seeder
{
    /**
     * Tables that need company_id assigned
     */
    protected $tenantTables = [
        'users' => ['column' => 'id', 'fk' => 'company_id'],
        'project_master' => ['column' => 'proj_id', 'fk' => 'company_id'],
        'supplier_master' => ['column' => 'sup_id', 'fk' => 'company_id'],
        'purchase_order_master' => ['column' => 'porder_id', 'fk' => 'company_id'],
        'receive_order_master' => ['column' => 'rorder_id', 'fk' => 'company_id'],
        'item_master' => ['column' => 'item_id', 'fk' => 'company_id'],
        'budget_master' => ['column' => 'budget_id', 'fk' => 'company_id'],
        'cost_code_master' => ['column' => 'cc_id', 'fk' => 'company_id'],
        'item_categories' => ['column' => 'icat_id', 'fk' => 'company_id'],
        'checklists' => ['column' => 'id', 'fk' => 'company_id'],
        'equipment' => ['column' => 'equip_id', 'fk' => 'company_id'],
        'purchase_order_details' => ['column' => 'pod_id', 'fk' => 'company_id'],
        'receive_order_items' => ['column' => 'roi_id', 'fk' => 'company_id'],
        'project_cost_codes' => ['column' => 'id', 'fk' => 'company_id'],
        'budget_change_orders' => ['column' => 'bco_id', 'fk' => 'company_id'],
        'po_change_orders' => ['column' => 'poco_id', 'fk' => 'company_id'],
        'approval_workflows' => ['column' => 'workflow_id', 'fk' => 'company_id'],
        'approval_requests' => ['column' => 'request_id', 'fk' => 'company_id'],
        'project_roles' => ['column' => 'id', 'fk' => 'company_id'],
        'accounting_integrations' => ['column' => 'id', 'fk' => 'company_id'],
        'integration_sync_logs' => ['column' => 'id', 'fk' => 'company_id'],
        'rfqs' => ['column' => 'rfq_id', 'fk' => 'company_id'],
        'rfq_items' => ['column' => 'id', 'fk' => 'company_id'],
        'rfq_quotes' => ['column' => 'id', 'fk' => 'company_id'],
        'checklist_performances' => ['column' => 'id', 'fk' => 'company_id'],
    ];

    /**
     * Run the data migration.
     */
    public function run()
    {
        echo "Starting migration to default company...\n\n";
        
        // Check if companies table has any records
        $existingCompany = Company::first();
        
        if ($existingCompany) {
            echo "Found existing company: {$existingCompany->name} (ID: {$existingCompany->id})\n";
            $defaultCompanyId = $existingCompany->id;
        } else {
            // Create default company
            echo "Creating default company...\n";
            $defaultCompany = Company::create([
                'name' => 'Default Company',
                'subdomain' => 'default',
                'status' => 1,
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'America/New_York',
                    'date_format' => 'm/d/Y',
                ],
            ]);
            $defaultCompanyId = $defaultCompany->id;
            echo "Created default company with ID: {$defaultCompanyId}\n";
        }
        
        echo "\nMigrating data to company_id: {$defaultCompanyId}\n";
        echo str_repeat('=', 60) . "\n\n";
        
        $totalUpdated = 0;
        
        foreach ($this->tenantTables as $table => $config) {
            $column = $config['column'];
            $fkColumn = $config['fk'];
            
            try {
                // Check if table exists
                if (!DB::getSchemaBuilder()->hasTable($table)) {
                    echo "  ⚠️  Table '{$table}' does not exist, skipping...\n";
                    continue;
                }
                
                // Check if company_id column exists
                if (!DB::getSchemaBuilder()->hasColumn($table, $fkColumn)) {
                    echo "  ⚠️  Column '{$fkColumn}' not found in '{$table}', skipping...\n";
                    continue;
                }
                
                // Count records with null company_id
                $nullCount = DB::table($table)->whereNull($fkColumn)->count();
                $totalCount = DB::table($table)->count();
                
                if ($totalCount === 0) {
                    echo "  ℹ️  Table '{$table}' is empty, skipping...\n";
                    continue;
                }
                
                if ($nullCount === 0) {
                    echo "  ✓  Table '{$table}' - all {$totalCount} records already have company_id\n";
                    continue;
                }
                
                // Update records
                $updated = DB::table($table)
                    ->whereNull($fkColumn)
                    ->update([$fkColumn => $defaultCompanyId]);
                
                echo "  ✓  Table '{$table}' - migrated {$updated} records\n";
                $totalUpdated += $updated;
                
            } catch (\Exception $e) {
                echo "  ✗  Error migrating '{$table}': " . $e->getMessage() . "\n";
                Log::error("Migration error for {$table}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        echo "\n" . str_repeat('=', 60) . "\n";
        echo "Migration complete!\n";
        echo "Total records migrated: {$totalUpdated}\n";
        echo "Default company ID: {$defaultCompanyId}\n\n";
        
        // Update the user's session if running in web context
        if (function_exists('session')) {
            session(['company_id' => $defaultCompanyId]);
            echo "Session company_id set to: {$defaultCompanyId}\n";
        }
    }
}
