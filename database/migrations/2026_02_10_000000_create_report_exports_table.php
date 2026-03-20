<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('report_exports')) {
            Schema::create('report_exports', function (Blueprint $table) {
                $table->id('report_export_id');
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('report_type', 120);
                $table->string('export_format', 20)->default('csv');
                $table->string('status', 20)->default('pending');
                $table->text('parameters')->nullable();
                $table->string('file_name', 255)->nullable();
                $table->string('file_path', 500)->nullable();
                $table->text('error_message')->nullable();
                $table->dateTime('queued_at')->nullable();
                $table->dateTime('started_at')->nullable();
                $table->dateTime('completed_at')->nullable();
                $table->dateTime('created_at')->useCurrent();
                $table->dateTime('updated_at')->useCurrent();

                $table->index('company_id');
                $table->index('user_id');
                $table->index('status');
                $table->index('report_type');
                $table->index('queued_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('report_exports');
    }
};
