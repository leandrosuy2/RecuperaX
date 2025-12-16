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
        if (Schema::hasTable('core_boleto')) {
            return;
        }
        
        Schema::create('core_boleto', function (Blueprint $table) {
            $table->id();
            $table->integer('empresa_id');
            $table->string('codigo_solicitacao');
            $table->string('seu_numero')->nullable();
            $table->string('situacao')->comment('Status do boleto');
            $table->date('data_situacao')->nullable();
            $table->date('data_emissao')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->decimal('valor_nominal', 15, 2)->default(0);
            $table->decimal('valor_total_recebido', 15, 2)->default(0);
            $table->string('origem_recebimento')->nullable()->comment('INTER, CORA');
            $table->string('tipo_cobranca')->nullable()->comment('BOLETO, PIX');
            $table->string('pagador_nome')->nullable();
            $table->string('pagador_cpf_cnpj')->nullable();
            $table->string('nosso_numero')->nullable();
            $table->string('linha_digitavel')->nullable();
            $table->string('codigo_barras')->nullable();
            $table->text('pix_copia_e_cola')->nullable()->comment('QR Code PIX');
            $table->string('txid')->nullable()->comment('ID da transação PIX');
            $table->string('cobranca_enviada_whatsapp')->nullable()->default('NAO')->comment('SIM/NAO');
            $table->dateTime('atualizado_em')->nullable();
            $table->timestamps();
            
            $table->index('empresa_id');
            $table->index('codigo_solicitacao');
            $table->index('situacao');
            $table->index('data_vencimento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_boleto');
    }
};
