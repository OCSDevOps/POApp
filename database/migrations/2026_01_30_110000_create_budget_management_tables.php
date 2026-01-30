<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for budget management system.
     * 
     * Creates:
     * 1. project_cost_codes - Many-to-many project-cost code assignment
     * 2. budget_change_orders - Track budget modifications
     * 3. po_change_orders - Track PO modifications
     * 4. approval_workflows - Define approval rules and routing
     * 5. approval_requests - Individual approval requests with status
     */
    public function up()
    {
        // 1. Project Cost Code Assignment (Many-to-Many)
        if (!Schema::hasTable('project_cost_codes')) {
            Schema::create('project_cost_codes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('project_id'); // References proj_id in project_master
                $table->unsignedBigInteger('cost_code_id'); // References cc_id in cost_code_master
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->unique(['project_id', 'cost_code_id'], 'unique_project_cost_code');
                $table->index(['project_id', 'is_active']);
                $table->index(['cost_code_id']);
            });
        }

        // 2. Budget Change Orders
        if (!Schema::hasTable('budget_change_orders')) {
            Schema::create('budget_change_orders', function (Blueprint $table) {
                $table->id('bco_id');
                $table->unsignedBigInteger('company_id');
                $table->string('bco_number', 50)->unique(); // BCO-2026-001
                $table->unsignedBigInteger('budget_id'); // References budget_id in budget_master
                $table->unsignedBigInteger('project_id'); // References proj_id
                $table->unsignedBigInteger('cost_code_id'); // References cc_id
                $table->enum('bco_type', ['increase', 'decrease', 'transfer'])->default('increase');
                $table->decimal('bco_amount', 15, 2); // Positive or negative
                $table->decimal('previous_budget', 15, 2); // Budget before this CO
                $table->decimal('new_budget', 15, 2); // Budget after this CO
                $table->text('bco_reason')->nullable();
                $table->text('bco_notes')->nullable();
                $table->string('bco_reference', 100)->nullable(); // External reference
                $table->enum('bco_status', ['draft', 'pending_approval', 'approved', 'rejected', 'cancelled'])->default('draft');
                $table->unsignedBigInteger('created_by'); // User ID
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('no action');
                $table->index(['project_id', 'bco_status']);
                $table->index(['budget_id']);
                $table->index(['bco_number']);
                $table->index(['created_at']);
            });
        }

        // 3. PO Change Orders
        if (!Schema::hasTable('po_change_orders')) {
            Schema::create('po_change_orders', function (Blueprint $table) {
                $table->id('poco_id');
                $table->unsignedBigInteger('company_id');
                $table->string('poco_number', 50)->unique(); // PCO-2026-001
                $table->unsignedBigInteger('purchase_order_id'); // References porder_id
                $table->enum('poco_type', ['amount_change', 'item_change', 'date_change', 'other'])->default('amount_change');
                $table->decimal('poco_amount', 15, 2)->default(0); // Change amount (positive/negative)
                $table->decimal('previous_total', 15, 2); // PO total before CO
                $table->decimal('new_total', 15, 2); // PO total after CO
                $table->text('poco_description');
                $table->text('poco_notes')->nullable();
                $table->string('poco_reference', 100)->nullable();
                $table->json('poco_details')->nullable(); // Line item changes
                $table->enum('poco_status', ['draft', 'pending_approval', 'approved', 'rejected', 'cancelled'])->default('draft');
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('no action');
                $table->index(['purchase_order_id', 'poco_status']);
                $table->index(['poco_number']);
                $table->index(['created_at']);
            });
        }

        // 4. Approval Workflows (Define approval rules)
        if (!Schema::hasTable('approval_workflows')) {
            Schema::create('approval_workflows', function (Blueprint $table) {
                $table->id('workflow_id');
                $table->unsignedBigInteger('company_id');
                $table->string('workflow_name', 100);
                $table->enum('workflow_type', ['budget', 'budget_co', 'po', 'po_co', 'receive_order'])->index();
                $table->integer('approval_level')->default(1); // Sequential approval levels
                $table->decimal('amount_threshold_min', 15, 2)->default(0);
                $table->decimal('amount_threshold_max', 15, 2)->nullable(); // NULL = no max
                $table->json('approver_user_ids'); // Array of user IDs who can approve at this level
                $table->enum('approval_logic', ['any', 'all'])->default('any'); // any = one approver enough, all = all must approve
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->text('workflow_notes')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index(['company_id', 'workflow_type', 'is_active']);
                $table->index(['approval_level', 'amount_threshold_min']);
            });
        }

        // 5. Approval Requests (Individual approval instances)
        if (!Schema::hasTable('approval_requests')) {
            Schema::create('approval_requests', function (Blueprint $table) {
                $table->id('request_id');
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('workflow_id')->nullable(); // Which workflow was used
                $table->enum('request_type', ['budget', 'budget_co', 'po', 'po_co', 'receive_order'])->index();
                $table->unsignedBigInteger('entity_id'); // ID of the entity being approved (budget_id, bco_id, porder_id, poco_id)
                $table->string('entity_number', 50)->nullable(); // Human-readable identifier
                $table->decimal('request_amount', 15, 2);
                $table->integer('current_level')->default(1);
                $table->integer('required_levels')->default(1); // Total levels needed
                $table->enum('request_status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending')->index();
                $table->unsignedBigInteger('requested_by'); // Who initiated
                $table->json('approval_history')->nullable(); // Array of approval actions
                $table->unsignedBigInteger('current_approver_id')->nullable();
                $table->text('request_notes')->nullable();
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
                
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('no action');
                $table->index(['request_type', 'entity_id']);
                $table->index(['request_status', 'current_approver_id']);
                $table->index(['requested_by']);
            });
        }

        // Add change order tracking columns to budget_master if not exist
        if (Schema::hasTable('budget_master')) {
            Schema::table('budget_master', function (Blueprint $table) {
                if (!Schema::hasColumn('budget_master', 'budget_original_amount')) {
                    $table->decimal('budget_original_amount', 15, 2)->default(0)->after('budget_amount');
                }
                if (!Schema::hasColumn('budget_master', 'budget_change_orders_total')) {
                    $table->decimal('budget_change_orders_total', 15, 2)->default(0)->after('budget_original_amount');
                }
                if (!Schema::hasColumn('budget_master', 'budget_committed')) {
                    $table->decimal('budget_committed', 15, 2)->default(0)->after('budget_change_orders_total');
                }
                if (!Schema::hasColumn('budget_master', 'budget_actual')) {
                    $table->decimal('budget_actual', 15, 2)->default(0)->after('budget_committed');
                }
            });
        }

        // Add change order tracking to purchase_order_master if not exist
        if (Schema::hasTable('purchase_order_master')) {
            Schema::table('purchase_order_master', function (Blueprint $table) {
                if (!Schema::hasColumn('purchase_order_master', 'porder_original_total')) {
                    $table->decimal('porder_original_total', 15, 2)->default(0)->after('porder_total');
                }
                if (!Schema::hasColumn('purchase_order_master', 'porder_change_orders_total')) {
                    $table->decimal('porder_change_orders_total', 15, 2)->default(0)->after('porder_original_total');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_workflows');
        Schema::dropIfExists('po_change_orders');
        Schema::dropIfExists('budget_change_orders');
        Schema::dropIfExists('project_cost_codes');

        // Remove added columns
        if (Schema::hasTable('budget_master')) {
            Schema::table('budget_master', function (Blueprint $table) {
                $table->dropColumn(['budget_original_amount', 'budget_change_orders_total', 'budget_committed', 'budget_actual']);
            });
        }

        if (Schema::hasTable('purchase_order_master')) {
            Schema::table('purchase_order_master', function (Blueprint $table) {
                $table->dropColumn(['porder_original_total', 'porder_change_orders_total']);
            });
        }
    }
};
