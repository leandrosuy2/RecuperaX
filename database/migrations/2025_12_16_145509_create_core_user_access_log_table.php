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
        if (Schema::hasTable('core_user_access_log')) {
            return;
        }
        
        Schema::create('core_user_access_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('path');
            $table->string('method', 10)->comment('GET, POST, etc.');
            $table->dateTime('timestamp');
            
            $table->index('user_id');
            $table->index('timestamp');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_user_access_log');
    }
};
