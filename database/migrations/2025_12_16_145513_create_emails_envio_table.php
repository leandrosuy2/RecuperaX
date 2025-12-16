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
        if (Schema::hasTable('emails_envio')) {
            return;
        }
        
        Schema::create('emails_envio', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('autenticacao')->comment('SSL ou TLS');
            $table->integer('porta');
            $table->string('servidor_smtp');
            $table->string('tipo_envio')->comment('Quitação Parcela, Quitação Contrato, Nova Empresa, Negociação, Boleto');
            $table->string('provedor')->nullable();
            $table->string('senha');
            $table->timestamps();
            
            $table->index('tipo_envio');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails_envio');
    }
};
