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
        if (!Schema::hasTable('core_empresa') || !Schema::hasTable('core_tabelaremuneracao')) {
            return;
        }
        
        // Tenta adicionar a foreign key, ignora se já existir
        try {
            Schema::table('core_empresa', function (Blueprint $table) {
                $table->foreign('plano_id')->references('id')->on('core_tabelaremuneracao')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign key já existe ou outro erro, ignora
            if (strpos($e->getMessage(), 'Duplicate key name') === false && 
                strpos($e->getMessage(), 'already exists') === false) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_empresa', function (Blueprint $table) {
            $table->dropForeign(['plano_id']);
        });
    }
};
