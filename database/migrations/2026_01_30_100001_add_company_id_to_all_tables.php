<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add company_id to all tenant-scoped tables for multi-tenancy isolation.
     *
     * @return void
     */
    public function up()
    {
        // List of all tenant-scoped tables
        $tables = [
            'users',
            'project_master',
            'supplier_master',
            'purchase_order_master',
            'receive_order_master',
            'budget_master',
            'cost_code_master',
            'item_master',
            'item_category_master',
            'item_package_master',
            'purchase_order_details',
            'receive_order_details',
            'project_cost_codes',
            'budget_change_orders',
            'po_change_orders',
            'approval_workflows',
            'approval_requests',
            'project_roles',
            'accounting_integrations',
            'integration_sync_logs',
            'integration_field_mappings',
            'checklist_master',
            'checklist_performance',
            'procore_auth',
            'procore_sync_logs',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('company_id')->nullable()->after('id');
                    $table->index('company_id');
                });
                
                // Note: Foreign key constraints intentionally omitted for SQL Server compatibility
                // and to avoid circular dependency issues. Enforce referential integrity at application level.
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'users',
            'project_master',
            'supplier_master',
            'purchase_order_master',
            'receive_order_master',
            'budget_master',
            'cost_code_master',
            'item_master',
            'item_category_master',
            'item_package_master',
            'purchase_order_details',
            'receive_order_details',
            'project_cost_codes',
            'budget_change_orders',
            'po_change_orders',
            'approval_workflows',
            'approval_requests',
            'project_roles',
            'accounting_integrations',
            'integration_sync_logs',
            'integration_field_mappings',
            'checklist_master',
            'checklist_performance',
            'procore_auth',
            'procore_sync_logs',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropIndex(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};
