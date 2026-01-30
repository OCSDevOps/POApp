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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subdomain')->unique()->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive');
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('country', 50)->default('USA');
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->json('settings')->nullable()->comment('Company-specific configuration');
            $table->timestamps();
            
            $table->index('status');
            $table->index('subdomain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
