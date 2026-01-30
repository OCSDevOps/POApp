<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->integer('backordered_qty')->default(0)->after('po_detail_quantity');
            $table->date('expected_backorder_date')->nullable()->after('backordered_qty');
            $table->tinyInteger('backorder_status')->default(0)->after('expected_backorder_date')->comment('0=none,1=backordered,2=fulfilled');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropColumn(['backordered_qty', 'expected_backorder_date', 'backorder_status']);
        });
    }
};
