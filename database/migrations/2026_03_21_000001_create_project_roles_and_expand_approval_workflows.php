<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('project_roles')) {
            Schema::create('project_roles', function (Blueprint $table) {
                $table->id('role_id');
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('project_id');
                $table->unsignedBigInteger('user_id');
                $table->string('role_name', 100);
                $table->boolean('can_create_po')->default(false);
                $table->boolean('can_approve_po')->default(false);
                $table->boolean('can_create_budget_co')->default(false);
                $table->boolean('can_approve_budget_co')->default(false);
                $table->boolean('can_override_budget')->default(false);
                $table->decimal('approval_limit', 15, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'project_id']);
                $table->index(['project_id', 'role_name', 'is_active']);
                $table->index('user_id');
            });
        }

        if (Schema::hasTable('approval_workflows')) {
            Schema::table('approval_workflows', function (Blueprint $table) {
                if (!Schema::hasColumn('approval_workflows', 'project_id')) {
                    $table->unsignedBigInteger('project_id')->nullable()->after('workflow_type');
                    $table->index(['project_id', 'workflow_type', 'is_active'], 'approval_workflows_project_type_active_index');
                }

                if (!Schema::hasColumn('approval_workflows', 'approver_roles')) {
                    $table->json('approver_roles')->nullable()->after('approver_user_ids');
                }
            });

            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE approval_workflows MODIFY approver_user_ids JSON NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_roles');
    }
};
