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
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('subdomain')->unique()->nullable();
                $table->string('company_code', 50)->unique()->nullable();
                $table->string('email', 255)->nullable();
                $table->string('phone', 50)->nullable();
                $table->text('address')->nullable();
                $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive');
                $table->json('settings')->nullable()->comment('JSON: timezone, currency, date_format, etc.');
                $table->string('subscription_tier', 50)->default('free')->comment('free, pro, enterprise');
                $table->dateTime('subscription_expires')->nullable();
                $table->string('logo_path', 255)->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                $table->index('status');
                $table->index('subscription_tier');
            });

            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'company_code')) {
                $table->string('company_code', 50)->nullable()->unique()->after('subdomain');
            }
            if (!Schema::hasColumn('companies', 'subscription_tier')) {
                $table->string('subscription_tier', 50)->default('free')->after('settings');
                $table->index('subscription_tier');
            }
            if (!Schema::hasColumn('companies', 'subscription_expires')) {
                $table->dateTime('subscription_expires')->nullable()->after('subscription_tier');
            }
            if (!Schema::hasColumn('companies', 'logo_path')) {
                $table->string('logo_path', 255)->nullable()->after('subscription_expires');
            }
            if (!Schema::hasColumn('companies', 'created_by')) {
                $table->integer('created_by')->nullable()->after('logo_path');
            }
            if (!Schema::hasColumn('companies', 'updated_by')) {
                $table->integer('updated_by')->nullable()->after('created_by');
            }
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
