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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'username')) {
                    $table->string('username')->nullable()->unique()->after('name');
                }

                if (!Schema::hasColumn('users', 'company_id')) {
                    $table->unsignedBigInteger('company_id')->nullable()->after('email');
                    $table->index('company_id');
                }

                if (!Schema::hasColumn('users', 'u_type')) {
                    $table->tinyInteger('u_type')->default(0)->after('company_id');
                    $table->index('u_type');
                }

                if (!Schema::hasColumn('users', 'u_status')) {
                    $table->tinyInteger('u_status')->default(1)->after('u_type');
                    $table->index('u_status');
                }

                if (!Schema::hasColumn('users', 'pt_id')) {
                    $table->unsignedBigInteger('pt_id')->nullable()->after('u_status');
                    $table->index('pt_id');
                }

                if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                    $table->boolean('two_factor_enabled')->default(false);
                    $table->index('two_factor_enabled');
                }

                if (!Schema::hasColumn('users', 'two_factor_secret')) {
                    $table->text('two_factor_secret')->nullable();
                }

                if (!Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                    $table->dateTime('two_factor_confirmed_at')->nullable();
                }

                if (!Schema::hasColumn('users', 'last_login_at')) {
                    $table->dateTime('last_login_at')->nullable();
                }

                if (!Schema::hasColumn('users', 'last_login_ip')) {
                    $table->string('last_login_ip', 45)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'two_factor_enabled')) {
                    $table->dropIndex(['two_factor_enabled']);
                    $table->dropColumn('two_factor_enabled');
                }

                if (Schema::hasColumn('users', 'two_factor_secret')) {
                    $table->dropColumn('two_factor_secret');
                }

                if (Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                    $table->dropColumn('two_factor_confirmed_at');
                }

                if (Schema::hasColumn('users', 'last_login_at')) {
                    $table->dropColumn('last_login_at');
                }

                if (Schema::hasColumn('users', 'last_login_ip')) {
                    $table->dropColumn('last_login_ip');
                }
            });
        }
    }
};
