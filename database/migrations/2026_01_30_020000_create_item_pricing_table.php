<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_pricing', function (Blueprint $table) {
            $table->bigIncrements('pricing_id');
            $table->unsignedBigInteger('item_id')->index();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->decimal('unit_price', 15, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=active,0=expired');
            $table->timestamps();

            // Foreign keys intentionally omitted because legacy tables are unmanaged by Laravel migrations.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_pricing');
    }
};
