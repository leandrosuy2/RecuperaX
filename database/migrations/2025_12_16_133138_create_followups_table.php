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
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divida_id')->constrained('dividas')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('consultor_id')->constrained('users')->onDelete('cascade');
            
            // Tipo: ligacao, email, retorno, negociacao, observacao
            $table->enum('tipo', ['ligacao', 'email', 'retorno', 'negociacao', 'observacao']);
            
            $table->date('data_prevista');
            $table->dateTime('data_executada')->nullable();
            
            // Status: pendente, concluido, reagendado, cancelado
            $table->enum('status', ['pendente', 'concluido', 'reagendado', 'cancelado'])->default('pendente');
            
            $table->text('observacoes')->nullable();
            $table->text('resultado')->nullable()->comment('Resultado do contato quando executado');
            
            // Para follow-ups encadeados
            $table->foreignId('followup_anterior_id')->nullable()->constrained('followups')->onDelete('set null');
            
            // Indica se foi gerado automaticamente pela régua
            $table->boolean('automatico')->default(false);
            
            $table->timestamps();
            
            $table->index(['consultor_id', 'status', 'data_prevista']);
            $table->index(['divida_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followups');
    }
};
