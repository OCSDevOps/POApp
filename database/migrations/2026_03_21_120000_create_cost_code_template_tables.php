<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cost_code_templates')) {
            Schema::create('cost_code_templates', function (Blueprint $table) {
                $table->id('cct_id');
                $table->unsignedBigInteger('company_id')->nullable();
                $table->string('cct_key', 150)->nullable();
                $table->string('cct_name', 150);
                $table->string('cct_description', 500)->nullable();
                $table->tinyInteger('cct_status')->default(1);
                $table->unsignedBigInteger('cct_createby')->nullable();
                $table->dateTime('cct_createdate')->nullable()->useCurrent();
                $table->unsignedBigInteger('cct_modifyby')->nullable();
                $table->dateTime('cct_modifydate')->nullable();

                $table->index('company_id', 'idx_cct_company');
                $table->index('cct_status', 'idx_cct_status');
                $table->unique(['company_id', 'cct_key'], 'cost_code_templates_company_key_unique');
                $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            });
        } elseif (!Schema::hasColumn('cost_code_templates', 'cct_key')) {
            Schema::table('cost_code_templates', function (Blueprint $table) {
                $table->string('cct_key', 150)->nullable()->after('company_id');
            });
        }

        if (!Schema::hasTable('cost_code_template_items')) {
            Schema::create('cost_code_template_items', function (Blueprint $table) {
                $table->id('ccti_id');
                $table->unsignedBigInteger('ccti_template_id');
                $table->unsignedBigInteger('ccti_cost_code_id');
                $table->integer('ccti_sort_order')->default(0);

                $table->index('ccti_template_id', 'idx_ccti_template');
                $table->index('ccti_cost_code_id', 'idx_ccti_costcode');
                $table->unique(['ccti_template_id', 'ccti_cost_code_id'], 'cost_code_template_items_unique_code');
                $table->foreign('ccti_template_id')->references('cct_id')->on('cost_code_templates')->cascadeOnDelete();
                $table->foreign('ccti_cost_code_id')->references('cc_id')->on('cost_code_master')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_code_template_items');
        Schema::dropIfExists('cost_code_templates');
    }
};
