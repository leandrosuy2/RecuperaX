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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('id');
            $table->boolean('is_superuser')->default(false)->after('email_verified_at');
            $table->boolean('is_staff')->default(false)->after('is_superuser');
            $table->boolean('is_active')->default(true)->after('is_staff');
            $table->dateTime('last_login')->nullable()->after('is_active');
            $table->dateTime('date_joined')->nullable()->after('last_login');
            $table->text('django_password')->nullable()->after('password')->comment('Senha original do Django para migração');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'is_superuser',
                'is_staff',
                'is_active',
                'last_login',
                'date_joined',
                'django_password',
            ]);
        });
    }
};
