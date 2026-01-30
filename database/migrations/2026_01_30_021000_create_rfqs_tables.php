<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // RFQ master
        Schema::create('rfq_master', function (Blueprint $table) {
            $table->bigIncrements('rfq_id');
            $table->string('rfq_no')->unique();
            $table->unsignedBigInteger('rfq_project_id')->nullable()->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('rfq_title', 250);
            $table->text('rfq_description')->nullable();
            $table->date('rfq_due_date')->nullable();
            $table->tinyInteger('rfq_status')->default(1);
            $table->unsignedBigInteger('rfq_created_by')->nullable();
            $table->timestamp('rfq_created_at')->nullable();
            $table->unsignedBigInteger('rfq_modified_by')->nullable();
            $table->timestamp('rfq_modified_at')->nullable();
        });

        // RFQ items
        Schema::create('rfq_items', function (Blueprint $table) {
            $table->bigIncrements('rfqi_id');
            $table->unsignedBigInteger('rfqi_rfq_id')->index();
            $table->unsignedBigInteger('rfqi_item_id')->index();
            $table->unsignedBigInteger('rfqi_uom_id')->nullable()->index();
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->integer('rfqi_quantity');
            $table->decimal('rfqi_target_price', 15, 2)->nullable();
            $table->text('rfqi_notes')->nullable();
            $table->timestamp('rfqi_created_at')->nullable();
        });

        // RFQ suppliers
        Schema::create('rfq_suppliers', function (Blueprint $table) {
            $table->bigIncrements('rfqs_id');
            $table->unsignedBigInteger('rfqs_rfq_id')->index();
            $table->unsignedBigInteger('rfqs_supplier_id')->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->timestamp('rfqs_sent_date')->nullable();
            $table->timestamp('rfqs_response_date')->nullable();
            $table->tinyInteger('rfqs_status')->default(1);
            $table->text('rfqs_notes')->nullable();
            $table->timestamp('rfqs_created_at')->nullable();
        });

        // RFQ quotes
        Schema::create('rfq_quotes', function (Blueprint $table) {
            $table->bigIncrements('rfqq_id');
            $table->unsignedBigInteger('rfqq_rfqs_id')->index();
            $table->unsignedBigInteger('rfqq_rfqi_id')->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->decimal('rfqq_quoted_price', 15, 2);
            $table->integer('rfqq_lead_time_days')->nullable();
            $table->date('rfqq_valid_until')->nullable();
            $table->text('rfqq_notes')->nullable();
            $table->timestamp('rfqq_created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_quotes');
        Schema::dropIfExists('rfq_suppliers');
        Schema::dropIfExists('rfq_items');
        Schema::dropIfExists('rfq_master');
    }
};
