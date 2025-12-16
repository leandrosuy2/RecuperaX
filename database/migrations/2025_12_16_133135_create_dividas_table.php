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
        Schema::create('dividas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credor_id')->constrained('credores')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('consultor_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('numero_documento')->unique();
            $table->string('descricao')->nullable();
            $table->decimal('valor_original', 15, 2);
            $table->decimal('valor_atual', 15, 2);
            $table->date('data_vencimento');
            $table->date('data_emissao');
            
            // Status: a_vencer, vencida, em_negociacao, quitada, cancelada
            $table->enum('status', ['a_vencer', 'vencida', 'em_negociacao', 'quitada', 'cancelada'])->default('a_vencer');
            
            // Configurações de juros e multa (podem sobrescrever as do credor)
            $table->decimal('juros_mensal', 5, 2)->nullable();
            $table->decimal('multa', 5, 2)->nullable();
            
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['credor_id', 'status']);
            $table->index(['consultor_id', 'status']);
            $table->index('data_vencimento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dividas');
    }
};
