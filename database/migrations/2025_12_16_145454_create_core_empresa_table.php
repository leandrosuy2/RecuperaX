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
        if (Schema::hasTable('core_empresa')) {
            return;
        }
        
        Schema::create('core_empresa', function (Blueprint $table) {
            $table->id();
            $table->string('razao_social');
            $table->string('nome_fantasia');
            $table->string('cnpj', 18)->unique();
            $table->string('nome_contato')->nullable();
            $table->string('cpf_contato', 14)->nullable();
            $table->string('banco')->nullable();
            $table->string('ie')->nullable()->comment('Inscrição Estadual');
            $table->string('telefone')->nullable();
            $table->string('celular')->nullable();
            $table->string('whatsapp_financeiro')->nullable();
            $table->string('operador')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('gerente')->nullable();
            $table->unsignedBigInteger('plano_id')->nullable()->comment('FK para core_tabelaremuneracao');
            $table->string('cep', 10)->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cidade')->nullable();
            $table->string('email')->nullable();
            $table->string('email_financeiro')->nullable();
            $table->string('valor_adesao')->nullable();
            $table->string('usuario')->nullable();
            $table->string('senha')->nullable();
            $table->string('logo')->nullable()->comment('Upload para logos/');
            $table->string('nome_favorecido_pix')->nullable();
            $table->string('tipo_pix')->nullable()->comment('CPF, CNPJ, Email, Telefone, Chave Aleatória');
            $table->boolean('status_empresa')->default(true);
            $table->timestamps();
            
            $table->index('cnpj');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_empresa');
    }
};
