<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_master', function (Blueprint $table) {
            $table->bigIncrements('cl_id');
            $table->string('cl_name');
            $table->string('cl_frequency')->nullable();
            $table->json('cl_eq_ids')->nullable(); // equipment ids
            $table->json('cl_user_ids')->nullable(); // assigned user ids
            $table->date('cl_start_date')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamp('created_date')->nullable();
            $table->timestamp('modified_date')->nullable();
        });

        Schema::create('checklist_details', function (Blueprint $table) {
            $table->bigIncrements('cli_id');
            $table->unsignedBigInteger('cl_id');
            $table->string('cli_item');
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamp('created_date')->nullable();
            $table->timestamp('modified_date')->nullable();

            $table->foreign('cl_id')->references('cl_id')->on('checklist_master')->onDelete('cascade');
        });

        Schema::create('cl_perform_master', function (Blueprint $table) {
            $table->bigIncrements('cl_p_id');
            $table->unsignedBigInteger('cl_id');
            $table->unsignedBigInteger('cl_eq_id')->nullable();
            $table->date('cl_p_date')->nullable();
            $table->json('cl_p_item_values')->nullable(); // optional JSON payload when details not used
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamp('created_date')->nullable();
            $table->timestamp('modified_date')->nullable();

            $table->foreign('cl_id')->references('cl_id')->on('checklist_master')->onDelete('cascade');
        });

        Schema::create('cl_perform_details', function (Blueprint $table) {
            $table->bigIncrements('cl_pd_id');
            $table->unsignedBigInteger('cl_p_id');
            $table->unsignedBigInteger('cl_pd_cli_id')->nullable(); // checklist item id
            $table->string('cl_pd_cli_value')->nullable();
            $table->text('cl_pd_cli_notes')->nullable();
            $table->string('cl_pd_cli_attachment')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamp('created_date')->nullable();
            $table->timestamp('modified_date')->nullable();

            $table->foreign('cl_p_id')->references('cl_p_id')->on('cl_perform_master')->onDelete('cascade');
            $table->foreign('cl_pd_cli_id')->references('cli_id')->on('checklist_details')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cl_perform_details');
        Schema::dropIfExists('cl_perform_master');
        Schema::dropIfExists('checklist_details');
        Schema::dropIfExists('checklist_master');
    }
};
