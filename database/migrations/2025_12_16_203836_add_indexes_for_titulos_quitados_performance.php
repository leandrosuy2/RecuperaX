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
        // Índices para otimizar a query de títulos quitados

        // Tabela titulo
        Schema::table('titulo', function (Blueprint $table) {
            $table->index('devedor_id', 'idx_titulo_devedor_id');
            $table->index('data_baixa', 'idx_titulo_data_baixa');
            $table->index('valorRecebido', 'idx_titulo_valor_recebido');
            $table->index('idTituloRef', 'idx_titulo_id_titulo_ref');
            $table->index(['data_baixa', 'id'], 'idx_titulo_data_baixa_id_desc');
        });

        // Tabela devedores
        Schema::table('devedores', function (Blueprint $table) {
            $table->index('empresa_id', 'idx_devedores_empresa_id');
            $table->index('nome', 'idx_devedores_nome');
        });

        // Tabela core_empresa
        Schema::table('core_empresa', function (Blueprint $table) {
            $table->index('status_empresa', 'idx_core_empresa_status');
            $table->index('operador', 'idx_core_empresa_operador');
            $table->index('supervisor', 'idx_core_empresa_supervisor');
            $table->index('nome_fantasia', 'idx_core_empresa_nome_fantasia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover índices

        Schema::table('titulo', function (Blueprint $table) {
            $table->dropIndex('idx_titulo_devedor_id');
            $table->dropIndex('idx_titulo_data_baixa');
            $table->dropIndex('idx_titulo_valor_recebido');
            $table->dropIndex('idx_titulo_id_titulo_ref');
            $table->dropIndex('idx_titulo_data_baixa_id_desc');
        });

        Schema::table('devedores', function (Blueprint $table) {
            $table->dropIndex('idx_devedores_empresa_id');
        });

        Schema::table('core_empresa', function (Blueprint $table) {
            $table->dropIndex('idx_core_empresa_status');
            $table->dropIndex('idx_core_empresa_operador');
            $table->dropIndex('idx_core_empresa_supervisor');
        });
    }
};
