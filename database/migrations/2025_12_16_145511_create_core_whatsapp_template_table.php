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
        if (Schema::hasTable('core_whatsapp_template')) {
            return;
        }
        
        Schema::create('core_whatsapp_template', function (Blueprint $table) {
            $table->id();
            $table->string('template')->comment('Nome/tipo do template');
            $table->text('mensagem')->comment('Conteúdo com variáveis');
            $table->bigInteger('empresa_id')->nullable()->comment('Para templates gerais');
            $table->foreignId('atualizado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('criado_em');
            $table->dateTime('atualizado_em');
            
            // Foreign key será adicionada depois se a tabela existir
            if (Schema::hasTable('core_empresa')) {
                $table->foreign('empresa_id')
                      ->references('id')
                      ->on('core_empresa')
                      ->onDelete('set null');
            }
            
            $table->index('empresa_id');
            $table->index('template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_whatsapp_template');
    }
};
