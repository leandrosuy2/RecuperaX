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
        if (Schema::hasTable('core_acordo')) {
            return;
        }
        
        Schema::create('core_acordo', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('empresa_id');
            $table->foreignId('devedor_id')->constrained('devedores')->onDelete('cascade');
            $table->foreignId('titulo_id')->constrained('titulo')->onDelete('cascade');
            $table->decimal('entrada', 15, 2)->default(0)->comment('Valor da entrada');
            $table->date('data_entrada')->nullable();
            $table->integer('qtde_prc')->default(0)->comment('Quantidade de parcelas');
            $table->decimal('valor_total_negociacao', 15, 2)->default(0);
            $table->integer('diferenca_dias')->default(0)->comment('Diferença em dias para cálculo de juros');
            $table->date('data_baixa')->nullable();
            $table->date('venc_primeira_parcela')->nullable();
            $table->decimal('valor_por_parcela', 15, 2)->default(0);
            $table->string('contato')->nullable()->comment('Telefone/email usado no acordo');
            $table->integer('tipo_doc_id')->nullable();
            $table->integer('forma_pag_Id')->nullable();
            $table->timestamps();
            
            $table->index('empresa_id');
            $table->index('devedor_id');
            $table->index('titulo_id');
        });
        
        // Adiciona foreign key se a tabela core_empresa existir
        if (Schema::hasTable('core_empresa')) {
            Schema::table('core_acordo', function (Blueprint $table) {
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
        Schema::dropIfExists('core_acordo');
    }
};
