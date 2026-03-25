<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('purchase_order_details')) {
            return;
        }

        Schema::table('purchase_order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_details', 'backordered_qty')) {
                $table->decimal('backordered_qty', 15, 4)->default(0)->after('po_detail_quantity');
            }

            if (!Schema::hasColumn('purchase_order_details', 'expected_backorder_date')) {
                $table->date('expected_backorder_date')->nullable()->after('backordered_qty');
            }

            if (!Schema::hasColumn('purchase_order_details', 'backorder_status')) {
                $table->tinyInteger('backorder_status')->default(0)->after('expected_backorder_date')->comment('0=none,1=backordered,2=fulfilled');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('purchase_order_details')) {
            return;
        }

        Schema::table('purchase_order_details', function (Blueprint $table) {
            $columnsToDrop = array_filter([
                Schema::hasColumn('purchase_order_details', 'backordered_qty') ? 'backordered_qty' : null,
                Schema::hasColumn('purchase_order_details', 'expected_backorder_date') ? 'expected_backorder_date' : null,
                Schema::hasColumn('purchase_order_details', 'backorder_status') ? 'backorder_status' : null,
            ]);

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
