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
        if (Schema::hasTable('template_mensagem_whatsapp')) {
            return;
        }
        
        Schema::create('template_mensagem_whatsapp', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('categoria')->comment('Pendentes, Negociados, Boletos, Cobrança boleto atrasado, Novo cliente');
            $table->text('template_mensagem')->comment('Template com variáveis');
            $table->boolean('follow_up_automatico')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index('categoria');
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_mensagem_whatsapp');
    }
};
