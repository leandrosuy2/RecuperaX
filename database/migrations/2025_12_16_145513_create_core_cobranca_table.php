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
        if (Schema::hasTable('core_cobranca')) {
            return;
        }
        
        Schema::create('core_cobranca', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('empresa_id');
            $table->date('data_cobranca');
            $table->decimal('valor_comissao', 15, 2)->default(0);
            $table->boolean('pago')->default(false);
            $table->string('tipo_anexo')->nullable()->comment('documento ou link');
            $table->string('documento')->nullable()->comment('Upload para cobrancas/');
            $table->string('link')->nullable();
            $table->timestamps();
            
            $table->index('empresa_id');
            $table->index('data_cobranca');
            $table->index('pago');
        });
        
        // Adiciona foreign key se a tabela core_empresa existir
        if (Schema::hasTable('core_empresa')) {
            Schema::table('core_cobranca', function (Blueprint $table) {
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
        Schema::dropIfExists('core_cobranca');
    }
};
