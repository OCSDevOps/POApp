<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('attachments')) {
            Schema::create('attachments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                
                // Polymorphic relationship fields
                $table->unsignedBigInteger('attachable_id');
                $table->string('attachable_type', 255);
                $table->index(['attachable_id', 'attachable_type'], 'idx_attachable');
                
                // File information
                $table->string('original_name', 255);
                $table->string('stored_name', 255);
                $table->string('file_path', 500);
                $table->string('disk', 50)->default('public');
                $table->string('mime_type', 120)->nullable();
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('file_extension', 20)->nullable();
                $table->string('file_hash', 64)->nullable()->comment('SHA-256 hash for deduplication');
                
                // Attachment metadata
                $table->string('category', 50)->nullable()->comment('e.g., invoice, quote, contract, photo');
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('sort_order')->default(0);
                
                // User tracking
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamp('uploaded_at')->nullable();
                
                // Status: 1=active, 0=deleted
                $table->tinyInteger('status')->default(1)->index();
                
                $table->timestamps();
                
                // Additional indexes
                $table->index(['company_id', 'status'], 'idx_company_status');
                $table->index('file_hash');
                $table->index('category');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
