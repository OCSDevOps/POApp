<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates tables for accounting system integrations (Sage, QuickBooks, etc.)
     */
    public function up(): void
    {
        // Main integration configuration table
        Schema::create('accounting_integrations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->index();
            $table->string('integration_type', 50); // 'sage', 'quickbooks', 'xero', etc.
            $table->string('integration_name', 255); // User-friendly name
            $table->tinyInteger('status')->default(1)->comment('1=active,0=inactive');
            
            // OAuth/API credentials (encrypted)
            $table->text('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            
            // Integration-specific config (JSON)
            $table->text('settings')->nullable()->comment('JSON config for integration-specific settings');
            
            // Sync preferences
            $table->boolean('auto_sync_po')->default(false);
            $table->boolean('auto_sync_invoices')->default(false);
            $table->boolean('auto_sync_vendors')->default(false);
            $table->boolean('auto_sync_items')->default(false);
            
            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        // Sync logs for tracking integration operations
        Schema::create('integration_sync_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('integration_id')->index();
            $table->unsignedBigInteger('company_id')->index();
            
            $table->string('sync_type', 50); // 'purchase_order', 'invoice', 'vendor', 'item'
            $table->string('operation', 20); // 'export', 'import', 'update'
            $table->string('status', 20); // 'pending', 'success', 'failed', 'partial'
            
            // Entity details
            $table->string('entity_type', 50)->nullable(); // 'PurchaseOrder', 'Supplier', etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('external_id', 255)->nullable(); // ID in external system
            
            // Sync details
            $table->integer('records_attempted')->default(0);
            $table->integer('records_succeeded')->default(0);
            $table->integer('records_failed')->default(0);
            
            // Error tracking
            $table->text('error_message')->nullable();
            $table->text('error_details')->nullable(); // JSON array of individual errors
            
            // Performance
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            
            $table->timestamps();
            
            $table->foreign('integration_id')->references('id')->on('accounting_integrations')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        // Field mappings for customizing data export
        Schema::create('integration_field_mappings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('integration_id')->index();
            $table->string('entity_type', 50); // 'purchase_order', 'vendor', 'item'
            $table->string('internal_field', 100); // Field name in POApp
            $table->string('external_field', 100); // Field name in external system
            $table->string('transformation', 50)->nullable(); // 'uppercase', 'date_format', 'currency', etc.
            $table->text('transformation_params')->nullable(); // JSON params for transformation
            $table->timestamps();
            
            $table->foreign('integration_id')->references('id')->on('accounting_integrations')->onDelete('cascade');
            $table->unique(['integration_id', 'entity_type', 'internal_field'], 'unique_field_mapping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_field_mappings');
        Schema::dropIfExists('integration_sync_logs');
        Schema::dropIfExists('accounting_integrations');
    }
};
