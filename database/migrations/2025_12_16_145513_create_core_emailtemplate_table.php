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
        if (Schema::hasTable('core_emailtemplate')) {
            return;
        }
        
        Schema::create('core_emailtemplate', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_envio')->comment('Quitação Parcela, Quitação Contrato, Nova Empresa, Negociação, Boleto');
            $table->text('mensagem')->comment('Template HTML/texto');
            $table->timestamps();
            
            $table->index('tipo_envio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_emailtemplate');
    }
};
