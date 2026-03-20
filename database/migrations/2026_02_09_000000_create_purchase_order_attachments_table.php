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
        if (!Schema::hasTable('purchase_order_attachments')) {
            Schema::create('purchase_order_attachments', function (Blueprint $table) {
                $table->id('po_attachment_id');
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('po_attachment_porder_ms');
                $table->string('po_attachment_original_name', 255);
                $table->string('po_attachment_path', 500);
                $table->string('po_attachment_mime', 120)->nullable();
                $table->unsignedBigInteger('po_attachment_size')->default(0);
                $table->unsignedBigInteger('po_attachment_createby')->nullable();
                $table->dateTime('po_attachment_createdate')->nullable();
                $table->tinyInteger('po_attachment_status')->default(1);

                $table->index('company_id');
                $table->index('po_attachment_porder_ms');
                $table->index(['po_attachment_porder_ms', 'po_attachment_status'], 'idx_po_attachment_order_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_attachments');
    }
};
