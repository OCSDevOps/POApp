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
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id('audit_id');
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('event_type', 120);
                $table->string('auditable_type', 190)->nullable();
                $table->string('auditable_id', 64)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 512)->nullable();
                $table->string('request_method', 10)->nullable();
                $table->string('request_url', 1024)->nullable();
                $table->text('old_values')->nullable();
                $table->text('new_values')->nullable();
                $table->text('meta')->nullable();
                $table->dateTime('created_at')->useCurrent();

                $table->index('company_id');
                $table->index('user_id');
                $table->index('event_type');
                $table->index('created_at');
                $table->index(['auditable_type', 'auditable_id'], 'idx_audit_auditable');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};
