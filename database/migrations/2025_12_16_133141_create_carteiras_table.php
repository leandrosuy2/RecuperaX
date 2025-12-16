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
        Schema::create('carteiras', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('credor_id')->nullable()->constrained('credores')->onDelete('cascade');
            $table->foreignId('consultor_id')->constrained('users')->onDelete('cascade');
            
            // Critérios de filtro para a carteira
            $table->integer('dias_atraso_min')->nullable()->comment('Dias mínimo de atraso');
            $table->integer('dias_atraso_max')->nullable()->comment('Dias máximo de atraso');
            $table->decimal('valor_min', 15, 2)->nullable();
            $table->decimal('valor_max', 15, 2)->nullable();
            $table->enum('status_filtro', ['a_vencer', 'vencida', 'em_negociacao'])->nullable();
            
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index(['consultor_id', 'ativo']);
            $table->index('credor_id');
        });
        
        // Tabela pivot para relacionar carteiras com dívidas
        Schema::create('carteira_divida', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carteira_id')->constrained('carteiras')->onDelete('cascade');
            $table->foreignId('divida_id')->constrained('dividas')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['carteira_id', 'divida_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carteira_divida');
        Schema::dropIfExists('carteiras');
    }
};
