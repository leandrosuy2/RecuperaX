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
        Schema::create('historico_cobranca', function (Blueprint $table) {
            $table->id();
            $table->foreignId('divida_id')->constrained('dividas')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('consultor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('followup_id')->nullable()->constrained('followups')->onDelete('set null');
            // Foreign keys para acordos e pagamentos serão adicionadas depois que essas tabelas forem criadas
            $table->foreignId('acordo_id')->nullable();
            $table->foreignId('pagamento_id')->nullable();
            
            // Tipo de ação: followup_criado, followup_concluido, negociacao_iniciada, acordo_criado, acordo_quebrado, pagamento_recebido, status_alterado
            $table->enum('tipo_acao', [
                'followup_criado',
                'followup_concluido',
                'negociacao_iniciada',
                'acordo_criado',
                'acordo_quebrado',
                'pagamento_recebido',
                'status_alterado',
                'observacao_adicionada'
            ]);
            
            $table->text('descricao');
            $table->json('dados_anteriores')->nullable()->comment('Snapshot dos dados antes da ação');
            $table->json('dados_novos')->nullable()->comment('Snapshot dos dados após a ação');
            
            $table->timestamps();
            
            $table->index(['divida_id', 'created_at']);
            $table->index(['consultor_id', 'created_at']);
            $table->index('tipo_acao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_cobranca');
    }
};
