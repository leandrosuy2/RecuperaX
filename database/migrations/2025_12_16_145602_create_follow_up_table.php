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
        if (Schema::hasTable('follow_up')) {
            return;
        }
        
        Schema::create('follow_up', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('empresa_id')->nullable();
            $table->foreignId('devedor_id')->constrained('devedores')->onDelete('cascade');
            $table->text('texto')->comment('Descrição do follow-up');
            $table->dateTime('created_at');
            
            $table->index('empresa_id');
            $table->index('devedor_id');
            $table->index('created_at');
        });
        
        // Adiciona foreign key se a tabela core_empresa existir
        if (Schema::hasTable('core_empresa')) {
            Schema::table('follow_up', function (Blueprint $table) {
                $table->foreign('empresa_id')
                      ->references('id')
                      ->on('core_empresa')
                      ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up');
    }
};
