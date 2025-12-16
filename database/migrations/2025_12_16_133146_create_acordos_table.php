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
        Schema::create('acordos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divida_id')->constrained('dividas')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('consultor_id')->constrained('users')->onDelete('cascade');
            
            $table->string('numero_acordo')->unique();
            $table->decimal('valor_original', 15, 2)->comment('Valor original da dívida');
            $table->decimal('valor_acordado', 15, 2)->comment('Valor total do acordo');
            $table->decimal('desconto_percentual', 5, 2)->default(0)->comment('Percentual de desconto aplicado');
            $table->decimal('desconto_valor', 15, 2)->default(0)->comment('Valor do desconto');
            
            $table->integer('quantidade_parcelas');
            $table->decimal('valor_parcela', 15, 2);
            $table->date('data_primeira_parcela');
            $table->integer('dia_vencimento')->comment('Dia do mês para vencimento das parcelas');
            
            // Status: pendente_aprovacao, ativo, quitado, quebrado, cancelado
            $table->enum('status', ['pendente_aprovacao', 'ativo', 'quitado', 'quebrado', 'cancelado'])->default('pendente_aprovacao');
            
            $table->text('observacoes')->nullable();
            $table->text('condicoes_especiais')->nullable();
            
            // Aprovação
            $table->foreignId('aprovado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('data_aprovacao')->nullable();
            
            // Quebra do acordo
            $table->dateTime('data_quebra')->nullable();
            $table->text('motivo_quebra')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['divida_id', 'status']);
            $table->index(['consultor_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acordos');
    }
};
