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
        if (Schema::hasTable('core_agendamento')) {
            return;
        }
        
        Schema::create('core_agendamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devedor_id')->constrained('devedores')->onDelete('cascade');
            $table->bigInteger('empresa_id');
            $table->dateTime('data_abertura');
            $table->dateTime('data_retorno');
            $table->text('assunto');
            $table->integer('acordo_id')->nullable()->comment('ID do acordo relacionado');
            $table->string('operador')->nullable();
            $table->string('telefone')->nullable();
            $table->string('status')->default('Pendente')->comment('Pendente ou Finalizado');
            $table->timestamps();
            
            $table->index('devedor_id');
            $table->index('empresa_id');
            $table->index('data_retorno');
            $table->index('status');
        });
        
        // Adiciona foreign key se a tabela core_empresa existir
        if (Schema::hasTable('core_empresa')) {
            Schema::table('core_agendamento', function (Blueprint $table) {
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
        Schema::dropIfExists('core_agendamento');
    }
};
