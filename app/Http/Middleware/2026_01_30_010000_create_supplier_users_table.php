<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('supplier_master', 'sup_id')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('status')->default(true)->comment('1=active, 0=inactive');
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['company_id', 'email']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_users');
    }
};