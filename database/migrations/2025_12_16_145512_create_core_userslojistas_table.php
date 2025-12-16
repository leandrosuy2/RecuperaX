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
        if (Schema::hasTable('core_userslojistas')) {
            return;
        }
        
        Schema::create('core_userslojistas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('empresa_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('google_id')->nullable()->comment('Para autenticação Google');
            $table->integer('credit')->default(0)->comment('Créditos');
            $table->integer('email_credit')->default(0)->comment('Créditos de email');
            $table->string('whatsapp_credit')->nullable()->comment('Créditos WhatsApp');
            $table->text('address')->nullable();
            $table->string('image')->nullable()->comment('URL da imagem');
            $table->string('password')->comment('Senha hash');
            $table->boolean('status')->default(true)->comment('Ativo/inativo');
            $table->timestamps();
            
            $table->index('empresa_id');
            $table->index('email');
        });
        
        // Adiciona foreign key se a tabela core_empresa existir
        if (Schema::hasTable('core_empresa')) {
            Schema::table('core_userslojistas', function (Blueprint $table) {
                $table->foreign('empresa_id')
                      ->references('id')
                      ->on('core_empresa')
                      ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_userslojistas');
    }
};
