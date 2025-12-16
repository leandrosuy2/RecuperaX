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
        if (Schema::hasTable('core_mensagemwhatsapp')) {
            return;
        }
        
        Schema::create('core_mensagemwhatsapp', function (Blueprint $table) {
            $table->id();
            $table->text('mensagem');
            $table->string('categoria')->comment('Pendentes, Negociados, Boletos, Cobrança boleto atrasado, Novo cliente');
            $table->timestamps();
            
            $table->index('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_mensagemwhatsapp');
    }
};
