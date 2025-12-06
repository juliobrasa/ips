<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->boolean('two_factor_enabled')->default(false)->after('phone_verified_at');
            $table->enum('status', ['pending', 'active', 'suspended', 'banned'])->default('pending')->after('two_factor_enabled');
            $table->enum('role', ['user', 'admin', 'support'])->default('user')->after('status');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'first_name',
                'last_name',
                'phone_verified_at',
                'two_factor_enabled',
                'status',
                'role',
                'last_login_at',
                'last_login_ip'
            ]);
        });
    }
};
