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
        if (Schema::hasTable('core_parcelamento')) {
            return;
        }
        
        Schema::create('core_parcelamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acordo_id')->constrained('core_acordo')->onDelete('cascade');
            $table->integer('parcela_numero');
            $table->date('data_vencimento')->nullable();
            $table->date('data_vencimento_parcela')->nullable();
            $table->date('data_baixa')->nullable()->comment('Data de pagamento');
            $table->decimal('valor', 15, 2)->default(0);
            $table->string('comprovante')->nullable()->comment('Upload para comprovantes/');
            $table->string('status')->default('PENDENTE')->comment('PENDENTE ou PAGO');
            $table->string('forma_pagamento')->nullable()->comment('PIX, BOLETO, DINHEIRO, CARTAO_CREDITO, CARTAO_DEBITO, CHEQUE, PAGAMENTO_LOJA');
            $table->timestamps();
            
            $table->index('acordo_id');
            $table->index('status');
            $table->index('data_vencimento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_parcelamento');
    }
};
