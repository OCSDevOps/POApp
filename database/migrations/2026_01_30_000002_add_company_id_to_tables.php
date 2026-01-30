<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add company_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });

        // Add company_id to projects
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('proj_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });

        // Add company_id to suppliers
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('sup_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });

        // Add company_id to purchase_order_master
        Schema::table('purchase_order_master', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('porder_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });

        // Add company_id to items
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('item_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });

        // Add company_id to budgets
        Schema::table('budgets', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('budget_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });

        // Add company_id to receive_order_master
        Schema::table('receive_order_master', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('rorder_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });

        // Add company_id to item_categories
        Schema::table('item_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('icat_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });

        // Add company_id to cost_codes
        Schema::table('cost_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('cc_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });

        // Add company_id to checklists
        Schema::table('checklists', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });

        // Add company_id to equipment
        Schema::table('equipment', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('equip_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove foreign keys and columns in reverse order
        $tables = [
            'equipment',
            'checklists',
            'cost_codes',
            'item_categories',
            'receive_order_master',
            'budgets',
            'items',
            'purchase_order_master',
            'suppliers',
            'projects',
            'users'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign([$table . '_company_id_foreign']);
                $table->dropIndex([$table . '_company_id_index']);
                $table->dropColumn('company_id');
            });
        }
    }
};
