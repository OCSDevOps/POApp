<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('project_master')) {
            Schema::create('project_master', function (Blueprint $table) {
                $table->bigIncrements('proj_id');
                $table->string('proj_number', 100)->nullable()->unique();
                $table->string('proj_name', 255);
                $table->text('proj_address')->nullable();
                $table->text('proj_description')->nullable();
                $table->unsignedBigInteger('proj_contact')->nullable();
                $table->date('proj_start_date')->nullable();
                $table->date('proj_end_date')->nullable();
                $table->tinyInteger('proj_status')->default(1);
                $table->unsignedBigInteger('proj_createby')->nullable();
                $table->dateTime('proj_createdate')->nullable();
                $table->unsignedBigInteger('proj_modifyby')->nullable();
                $table->dateTime('proj_modifydate')->nullable();
                $table->unsignedBigInteger('procore_project_id')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('proj_default_calendar_id')->nullable();
                $table->string('proj_scheduling_mode', 20)->default('AUTO');
                $table->dateTime('proj_progress_date')->nullable();
                $table->dateTime('proj_target_finish_date')->nullable();

                $table->index('proj_status');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('project_details')) {
            Schema::create('project_details', function (Blueprint $table) {
                $table->bigIncrements('pdetail_id');
                $table->unsignedBigInteger('pdetail_proj_ms');
                $table->unsignedBigInteger('pdetail_user')->nullable();
                $table->text('pdetail_info')->nullable();
                $table->dateTime('pdetail_createdate')->nullable();
                $table->tinyInteger('pdetail_status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('pdetail_proj_ms');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('supplier_master')) {
            Schema::create('supplier_master', function (Blueprint $table) {
                $table->bigIncrements('sup_id');
                $table->string('sup_code', 100)->nullable()->unique();
                $table->string('sup_name', 255);
                $table->string('sup_email', 255)->nullable();
                $table->string('sup_phone', 100)->nullable();
                $table->text('sup_address')->nullable();
                $table->string('sup_contact_person', 255)->nullable();
                $table->text('sup_details')->nullable();
                $table->unsignedTinyInteger('sup_type')->default(1);
                $table->tinyInteger('sup_status')->default(1);
                $table->unsignedBigInteger('sup_createby')->nullable();
                $table->dateTime('sup_createdate')->nullable();
                $table->unsignedBigInteger('sup_modifyby')->nullable();
                $table->dateTime('sup_modifydate')->nullable();
                $table->unsignedBigInteger('procore_supplier_id')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('sup_status');
                $table->index('sup_type');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('unit_of_measure_tab')) {
            Schema::create('unit_of_measure_tab', function (Blueprint $table) {
                $table->bigIncrements('uom_id');
                $table->string('uom_code', 50)->nullable();
                $table->string('uom_name', 191);
                $table->string('uom_detail', 255)->nullable();
                $table->dateTime('uom_createdate')->nullable();
                $table->unsignedBigInteger('uom_createby')->nullable();
                $table->dateTime('uom_modifydate')->nullable();
                $table->tinyInteger('uom_status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('uom_status');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('item_category_tab')) {
            Schema::create('item_category_tab', function (Blueprint $table) {
                $table->bigIncrements('icat_id');
                $table->string('icat_name', 255);
                $table->text('icat_details')->nullable();
                $table->tinyInteger('icat_status')->default(1);
                $table->unsignedBigInteger('icat_createby')->nullable();
                $table->dateTime('icat_createdate')->nullable();
                $table->unsignedBigInteger('icat_modifyby')->nullable();
                $table->dateTime('icat_modifydate')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('icat_status');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('cost_code_master')) {
            Schema::create('cost_code_master', function (Blueprint $table) {
                $table->bigIncrements('cc_id');
                $table->string('cc_no', 50);
                $table->string('cc_description', 255);
                $table->text('cc_details')->nullable();
                $table->string('cc_parent_code', 50)->nullable();
                $table->string('cc_category_code', 50)->nullable();
                $table->string('cc_subcategory_code', 50)->nullable();
                $table->unsignedTinyInteger('cc_level')->default(1);
                $table->string('cc_full_code', 100)->nullable();
                $table->tinyInteger('cc_status')->default(1);
                $table->unsignedBigInteger('cc_createby')->nullable();
                $table->dateTime('cc_createdate')->nullable();
                $table->unsignedBigInteger('cc_modifyby')->nullable();
                $table->dateTime('cc_modifydate')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('cc_no');
                $table->index('cc_parent_code');
                $table->index('cc_full_code');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('item_master')) {
            Schema::create('item_master', function (Blueprint $table) {
                $table->bigIncrements('item_id');
                $table->string('item_code', 100)->unique();
                $table->string('item_name', 255);
                $table->text('item_description')->nullable();
                $table->unsignedBigInteger('item_cat_ms')->nullable();
                $table->unsignedBigInteger('item_ccode_ms')->nullable();
                $table->unsignedBigInteger('item_unit_ms')->nullable();
                $table->tinyInteger('item_status')->default(1);
                $table->unsignedBigInteger('item_createby')->nullable();
                $table->dateTime('item_createdate')->nullable();
                $table->unsignedBigInteger('item_modifyby')->nullable();
                $table->dateTime('item_modifydate')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('item_cat_ms');
                $table->index('item_ccode_ms');
                $table->index('item_unit_ms');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('item_package_master')) {
            Schema::create('item_package_master', function (Blueprint $table) {
                $table->bigIncrements('ipack_id');
                $table->string('ipack_name', 255);
                $table->text('ipack_details')->nullable();
                $table->integer('ipack_totalitem')->default(0);
                $table->decimal('ipack_total_qty', 15, 4)->default(0);
                $table->dateTime('ipack_createdate')->nullable();
                $table->unsignedBigInteger('ipack_createby')->nullable();
                $table->dateTime('ipack_modifydate')->nullable();
                $table->unsignedBigInteger('ipack_modifyby')->nullable();
                $table->tinyInteger('ipack_status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('ipack_status');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('item_package_details')) {
            Schema::create('item_package_details', function (Blueprint $table) {
                $table->bigIncrements('ipdetail_id');
                $table->string('ipdetail_autogen', 100)->nullable();
                $table->unsignedBigInteger('ipdetail_ipack_ms');
                $table->unsignedBigInteger('ipdetail_item_ms')->nullable();
                $table->decimal('ipdetail_quantity', 15, 4)->default(0);
                $table->text('ipdetail_info')->nullable();
                $table->dateTime('ipdetail_createdate')->nullable();
                $table->tinyInteger('ipdetail_status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('ipdetail_ipack_ms');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('taxgroup_master')) {
            Schema::create('taxgroup_master', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 191);
                $table->decimal('percentage', 10, 2)->default(0);
                $table->dateTime('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('supplier_catalog_tab')) {
            Schema::create('supplier_catalog_tab', function (Blueprint $table) {
                $table->bigIncrements('supcat_id');
                $table->unsignedBigInteger('supcat_supplier');
                $table->string('supcat_item_code', 100);
                $table->string('supcat_sku_no', 100)->nullable();
                $table->unsignedBigInteger('supcat_uom')->nullable();
                $table->decimal('supcat_price', 15, 2)->default(0);
                $table->date('supcat_lastdate')->nullable();
                $table->text('supcat_details')->nullable();
                $table->dateTime('supcat_createdate')->nullable();
                $table->unsignedBigInteger('supcat_createby')->nullable();
                $table->dateTime('supcat_modifydate')->nullable();
                $table->unsignedBigInteger('supcat_modifyby')->nullable();
                $table->tinyInteger('supcat_status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('supcat_supplier');
                $table->index('supcat_item_code');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('budget_master')) {
            Schema::create('budget_master', function (Blueprint $table) {
                $table->bigIncrements('budget_id');
                $table->unsignedBigInteger('budget_project_id')->nullable();
                $table->unsignedBigInteger('budget_cost_code_id')->nullable();
                $table->decimal('budget_original_amount', 15, 2)->default(0);
                $table->decimal('budget_revised_amount', 15, 2)->default(0);
                $table->decimal('budget_committed_amount', 15, 2)->default(0);
                $table->decimal('budget_spent_amount', 15, 2)->default(0);
                $table->decimal('budget_remaining_amount', 15, 2)->default(0);
                $table->string('budget_fiscal_year', 10)->nullable();
                $table->text('budget_notes')->nullable();
                $table->tinyInteger('budget_status')->default(1);
                $table->unsignedBigInteger('budget_created_by')->nullable();
                $table->dateTime('budget_created_at')->nullable();
                $table->unsignedBigInteger('budget_modified_by')->nullable();
                $table->dateTime('budget_modified_at')->nullable();
                $table->unsignedBigInteger('procore_budget_id')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->decimal('budget_change_orders_total', 15, 2)->default(0);
                $table->decimal('budget_committed', 15, 2)->default(0);
                $table->decimal('budget_actual', 15, 2)->default(0);
                $table->decimal('budget_warning_threshold', 15, 2)->default(80.00);
                $table->decimal('budget_critical_threshold', 15, 2)->default(95.00);
                $table->decimal('committed', 15, 2)->default(0);
                $table->decimal('actual', 15, 2)->default(0);
                $table->boolean('warning_notification_sent')->default(false);
                $table->boolean('critical_notification_sent')->default(false);
                $table->decimal('original_amount', 15, 2)->default(0);
                $table->decimal('variance', 15, 2)->default(0);

                $table->index('budget_project_id');
                $table->index('budget_cost_code_id');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('purchase_order_master')) {
            Schema::create('purchase_order_master', function (Blueprint $table) {
                $table->bigIncrements('porder_id');
                $table->string('porder_no', 100)->unique();
                $table->unsignedBigInteger('porder_project_ms')->nullable();
                $table->unsignedBigInteger('porder_supplier_ms')->nullable();
                $table->unsignedBigInteger('porder_cost_code')->nullable();
                $table->text('porder_address')->nullable();
                $table->text('porder_delivery_note')->nullable();
                $table->text('porder_description')->nullable();
                $table->integer('porder_total_item')->default(0);
                $table->decimal('porder_total_amount', 15, 2)->default(0);
                $table->decimal('porder_total_tax', 15, 2)->default(0);
                $table->tinyInteger('porder_delivery_status')->default(0);
                $table->tinyInteger('porder_status')->default(1);
                $table->dateTime('porder_createdate')->nullable();
                $table->unsignedBigInteger('porder_createby')->nullable();
                $table->dateTime('porder_modifydate')->nullable();
                $table->unsignedBigInteger('porder_modifyby')->nullable();
                $table->decimal('porder_original_total', 15, 2)->default(0);
                $table->decimal('porder_change_orders_total', 15, 2)->default(0);
                $table->string('integration_status', 50)->default('pending');
                $table->unsignedBigInteger('procore_po_id')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('porder_project_ms');
                $table->index('porder_supplier_ms');
                $table->index('porder_cost_code');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('purchase_order_details')) {
            Schema::create('purchase_order_details', function (Blueprint $table) {
                $table->bigIncrements('po_detail_id');
                $table->string('po_detail_autogen', 100)->nullable();
                $table->unsignedBigInteger('po_detail_porder_ms');
                $table->string('po_detail_item', 100);
                $table->string('po_detail_sku', 100)->nullable();
                $table->string('po_detail_taxcode', 50)->nullable();
                $table->string('po_detail_tax_group', 100)->nullable();
                $table->unsignedBigInteger('po_detail_ccode')->nullable();
                $table->decimal('po_detail_quantity', 15, 4)->default(0);
                $table->decimal('po_detail_unitprice', 15, 2)->default(0);
                $table->decimal('po_detail_subtotal', 15, 2)->default(0);
                $table->decimal('po_detail_taxamount', 15, 2)->default(0);
                $table->decimal('po_detail_total', 15, 2)->default(0);
                $table->decimal('backordered_qty', 15, 4)->default(0);
                $table->tinyInteger('backorder_status')->default(0);
                $table->text('backorder_notes')->nullable();
                $table->date('expected_backorder_date')->nullable();
                $table->dateTime('po_detail_createdate')->nullable();
                $table->tinyInteger('po_detail_status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('po_detail_porder_ms');
                $table->index('po_detail_item');
                $table->index('po_detail_ccode');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('receive_order_master')) {
            Schema::create('receive_order_master', function (Blueprint $table) {
                $table->bigIncrements('rorder_id');
                $table->unsignedBigInteger('rorder_porder_ms')->nullable();
                $table->string('rorder_slip_no', 100)->nullable();
                $table->text('rorder_infoset')->nullable();
                $table->date('rorder_date')->nullable();
                $table->integer('rorder_totalitem')->default(0);
                $table->decimal('rorder_totalamount', 15, 2)->default(0);
                $table->dateTime('rorder_createdate')->nullable();
                $table->unsignedBigInteger('rorder_createby')->nullable();
                $table->dateTime('rorder_modifydate')->nullable();
                $table->unsignedBigInteger('rorder_modifyby')->nullable();
                $table->tinyInteger('rorder_status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('rorder_porder_ms');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('receive_order_details')) {
            Schema::create('receive_order_details', function (Blueprint $table) {
                $table->bigIncrements('ro_detail_id');
                $table->unsignedBigInteger('ro_detail_rorder_ms');
                $table->string('ro_detail_item', 100);
                $table->decimal('ro_detail_quantity', 15, 4)->default(0);
                $table->dateTime('ro_detail_createdate')->nullable();
                $table->tinyInteger('ro_detail_status')->default(1);
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('ro_detail_rorder_ms');
                $table->index('ro_detail_item');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('commitment_master')) {
            Schema::create('commitment_master', function (Blueprint $table) {
                $table->bigIncrements('commit_id');
                $table->unsignedBigInteger('commit_project_id')->nullable();
                $table->unsignedBigInteger('commit_supplier_id')->nullable();
                $table->unsignedBigInteger('commit_cost_code_id')->nullable();
                $table->string('commit_number', 100)->nullable()->unique();
                $table->string('commit_title', 255)->nullable();
                $table->text('commit_description')->nullable();
                $table->decimal('commit_original_value', 15, 2)->default(0);
                $table->decimal('commit_approved_cos', 15, 2)->default(0);
                $table->decimal('commit_pending_cos', 15, 2)->default(0);
                $table->decimal('commit_invoiced_amount', 15, 2)->default(0);
                $table->decimal('commit_paid_amount', 15, 2)->default(0);
                $table->date('commit_start_date')->nullable();
                $table->date('commit_end_date')->nullable();
                $table->tinyInteger('commit_status')->default(1);
                $table->unsignedBigInteger('commit_created_by')->nullable();
                $table->dateTime('commit_created_at')->nullable();
                $table->unsignedBigInteger('commit_modified_by')->nullable();
                $table->dateTime('commit_modified_at')->nullable();
                $table->unsignedBigInteger('procore_commitment_id')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('commit_project_id');
                $table->index('commit_supplier_id');
                $table->index('commit_cost_code_id');
                $table->index('company_id');
            });
        }

        if (!Schema::hasTable('item_price_history')) {
            Schema::create('item_price_history', function (Blueprint $table) {
                $table->bigIncrements('iph_id');
                $table->unsignedBigInteger('iph_item_id');
                $table->unsignedBigInteger('iph_supplier_id');
                $table->decimal('iph_old_price', 15, 2)->default(0);
                $table->decimal('iph_new_price', 15, 2)->default(0);
                $table->date('iph_effective_date');
                $table->text('iph_notes')->nullable();
                $table->unsignedBigInteger('iph_created_by')->nullable();
                $table->dateTime('iph_created_at')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();

                $table->index('iph_item_id');
                $table->index('iph_supplier_id');
                $table->index('company_id');
            });
        }

        $this->createOrReplaceView('purchase_order_detail', 'purchase_order_details');
        $this->createOrReplaceView('supplier_catalog', 'supplier_catalog_tab');
    }

    public function down(): void
    {
        $this->dropViewIfExists('supplier_catalog');
        $this->dropViewIfExists('purchase_order_detail');

        Schema::dropIfExists('item_price_history');
        Schema::dropIfExists('commitment_master');
        Schema::dropIfExists('receive_order_details');
        Schema::dropIfExists('receive_order_master');
        Schema::dropIfExists('purchase_order_details');
        Schema::dropIfExists('purchase_order_master');
        Schema::dropIfExists('budget_master');
        Schema::dropIfExists('supplier_catalog_tab');
        Schema::dropIfExists('taxgroup_master');
        Schema::dropIfExists('item_package_details');
        Schema::dropIfExists('item_package_master');
        Schema::dropIfExists('item_master');
        Schema::dropIfExists('cost_code_master');
        Schema::dropIfExists('item_category_tab');
        Schema::dropIfExists('unit_of_measure_tab');
        Schema::dropIfExists('supplier_master');
        Schema::dropIfExists('project_details');
        Schema::dropIfExists('project_master');
    }

    private function createOrReplaceView(string $viewName, string $sourceTable): void
    {
        if (!Schema::hasTable($sourceTable)) {
            return;
        }

        $this->dropViewIfExists($viewName);
        DB::statement("CREATE VIEW `{$viewName}` AS SELECT * FROM `{$sourceTable}`");
    }

    private function dropViewIfExists(string $viewName): void
    {
        DB::statement("DROP VIEW IF EXISTS `{$viewName}`");
    }
};
