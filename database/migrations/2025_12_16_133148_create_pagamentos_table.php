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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divida_id')->nullable()->constrained('dividas')->onDelete('cascade');
            $table->foreignId('acordo_id')->nullable()->constrained('acordos')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('consultor_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('numero_transacao')->unique()->nullable();
            $table->decimal('valor', 15, 2);
            $table->date('data_pagamento');
            $table->date('data_recebimento')->nullable();
            
            // Forma de pagamento: dinheiro, pix, boleto, transferencia, cartao_credito, cartao_debito, cheque
            $table->enum('forma_pagamento', [
                'dinheiro',
                'pix',
                'boleto',
                'transferencia',
                'cartao_credito',
                'cartao_debito',
                'cheque'
            ]);
            
            // Status: pendente, confirmado, cancelado
            $table->enum('status', ['pendente', 'confirmado', 'cancelado'])->default('pendente');
            
            // Para pagamentos parcelados (acordos)
            $table->integer('numero_parcela')->nullable()->comment('Número da parcela do acordo');
            $table->date('data_vencimento_parcela')->nullable();
            
            $table->text('observacoes')->nullable();
            $table->string('comprovante')->nullable()->comment('Caminho do arquivo de comprovante');
            
            $table->timestamps();
            
            $table->index(['divida_id', 'status']);
            $table->index(['acordo_id', 'status']);
            $table->index(['cliente_id', 'data_pagamento']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
