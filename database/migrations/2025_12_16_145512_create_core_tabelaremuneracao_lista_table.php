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
        if (Schema::hasTable('core_tabelaremuneracao_lista')) {
            return;
        }
        
        Schema::create('core_tabelaremuneracao_lista', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tabela_remuneracao_id');
            $table->integer('de_dias')->comment('Dias inicial da faixa');
            $table->integer('ate_dias')->comment('Dias final da faixa');
            $table->decimal('percentual_remuneracao', 5, 2)->comment('Percentual de comissão');
            $table->timestamps();
            
            $table->foreign('tabela_remuneracao_id')
                  ->references('id')
                  ->on('core_tabelaremuneracao')
                  ->onDelete('cascade');
            
            $table->index('tabela_remuneracao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_tabelaremuneracao_lista');
    }
};
