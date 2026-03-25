<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('po_change_orders') || DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE po_change_orders
            MODIFY poco_type ENUM('amount_change', 'item_change', 'date_change', 'term_change', 'other')
            NOT NULL DEFAULT 'amount_change'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('po_change_orders') || DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE po_change_orders
            MODIFY poco_type ENUM('amount_change', 'item_change', 'date_change', 'other')
            NOT NULL DEFAULT 'amount_change'
        ");
    }
};
