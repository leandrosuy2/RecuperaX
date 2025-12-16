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
        if (Schema::hasTable('titulo')) {
            return;
        }
        
        Schema::create('titulo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devedor_id')->constrained('devedores')->onDelete('cascade');
            $table->bigInteger('empresa_id');
            $table->integer('idTituloRef')->nullable()->comment('ID de referência externa');
            $table->integer('num_titulo');
            $table->foreignId('tipo_doc_id')->nullable()->constrained('tipo_doc_titulo')->onDelete('set null');
            $table->date('dataEmissao')->nullable();
            $table->date('dataVencimento')->nullable();
            $table->date('dataVencimentoReal')->nullable();
            $table->date('dataVencimentoPrimeira')->nullable();
            $table->date('data_baixa')->nullable()->comment('Data de quitação');
            $table->date('primeiro_vencimento')->nullable();
            $table->decimal('valor', 15, 2)->default(0)->comment('Valor original');
            $table->decimal('juros', 15, 2)->default(0);
            $table->decimal('valorRecebido', 15, 2)->default(0)->comment('Valor efetivamente recebido');
            $table->decimal('total_parcelamento', 15, 2)->default(0);
            $table->decimal('total_acordo', 15, 2)->default(0);
            $table->decimal('parcelar_valor', 15, 2)->default(0);
            $table->integer('qtde_parcelas')->default(0);
            $table->integer('nPrc')->nullable()->comment('Número de parcelas');
            $table->integer('dias_atraso')->default(0);
            $table->integer('intervalo_dias')->nullable()->comment('Intervalo entre parcelas');
            $table->integer('forma_pag_Id')->nullable()->comment('ID da forma de pagamento');
            $table->integer('statusBaixa')->default(0)->comment('0=Pendente, 2=Quitado, 3=Negociado');
            $table->integer('statusBaixaGeral')->nullable();
            $table->boolean('acordoComfirmed')->default(false)->comment('Acordo confirmado');
            $table->string('id_cobranca')->nullable()->comment('ID da cobrança PIX/Boleto');
            $table->string('email_enviado')->nullable()->default('NAO')->comment('SIM/NAO');
            $table->date('data_envio_whatsapp')->nullable();
            $table->string('telefone_enviado')->nullable();
            $table->date('ultima_acao')->nullable();
            $table->string('comprovante')->nullable()->comment('Upload para comprovantes/');
            $table->string('contrato')->nullable()->comment('Upload para contratos/');
            $table->string('operador')->nullable()->comment('Nome do operador responsável');
            $table->timestamps();
            
            $table->index('devedor_id');
            $table->index('empresa_id');
            $table->index('idTituloRef');
            $table->index('statusBaixa');
            $table->index('num_titulo');
        });
        
        // Adiciona foreign keys se as tabelas existirem
        if (Schema::hasTable('core_empresa')) {
            Schema::table('titulo', function (Blueprint $table) {
                $table->foreign('empresa_id')
                      ->references('id')
                      ->on('core_empresa')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titulo');
    }
};
