<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add company_id to a legacy table if it exists and does not already have it.
     */
    private function addCompanyColumn(string $tableName, string $afterColumn): void
    {
        if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'company_id')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($afterColumn) {
            $table->unsignedBigInteger('company_id')->nullable()->after($afterColumn);
            $table->index('company_id');
        });
    }

    /**
     * Drop company_id from a legacy table if present.
     */
    private function dropCompanyColumn(string $tableName): void
    {
        if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'company_id')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            try {
                $table->dropForeign([$tableName . '_company_id_foreign']);
            } catch (\Throwable $e) {
                // Ignore when the FK was never created in this environment.
            }

            try {
                $table->dropIndex([$tableName . '_company_id_index']);
            } catch (\Throwable $e) {
                // Ignore when the index name differs or was never created.
            }

            $table->dropColumn('company_id');
        });
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->addCompanyColumn('users', 'id');
        $this->addCompanyColumn('project_master', 'proj_id');
        $this->addCompanyColumn('supplier_master', 'sup_id');
        $this->addCompanyColumn('purchase_order_master', 'porder_id');
        $this->addCompanyColumn('item_master', 'item_id');
        $this->addCompanyColumn('budget_master', 'budget_id');
        $this->addCompanyColumn('receive_order_master', 'rorder_id');
        $this->addCompanyColumn('item_category_tab', 'icat_id');
        $this->addCompanyColumn('cost_code_master', 'cc_id');
        $this->addCompanyColumn('checklist_master', 'cl_id');
        $this->addCompanyColumn('eq_master', 'eq_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'eq_master',
            'checklist_master',
            'cost_code_master',
            'item_category_tab',
            'receive_order_master',
            'budget_master',
            'item_master',
            'purchase_order_master',
            'supplier_master',
            'project_master',
            'users',
        ];

        foreach ($tables as $tableName) {
            $this->dropCompanyColumn($tableName);
        }
    }
};
