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
        Schema::table('credores', function (Blueprint $table) {
            // Logo
            $table->string('logo')->nullable()->after('cnpj');
            
            // Dados do Contato
            $table->string('nome_contato')->nullable()->after('nome_fantasia');
            $table->string('cpf_contato', 14)->nullable()->after('nome_contato');
            
            // Dados Bancários
            $table->string('banco')->nullable()->after('cep');
            $table->string('agencia', 20)->nullable()->after('banco');
            $table->string('conta', 30)->nullable()->after('agencia');
            $table->string('chave_pix')->nullable()->after('conta');
            $table->string('nome_favorecido_pix')->nullable()->after('chave_pix');
            $table->enum('tipo_chave_pix', ['cpf', 'cnpj', 'email', 'telefone', 'aleatoria'])->nullable()->after('nome_favorecido_pix');
            
            // Dados Adicionais
            $table->string('inscricao_estadual')->nullable()->after('tipo_chave_pix');
            $table->string('celular', 20)->nullable()->after('telefone');
            $table->string('whatsapp_financeiro', 20)->nullable()->after('celular');
            
            // Equipe
            $table->string('operador')->nullable()->after('whatsapp_financeiro');
            $table->string('supervisor')->nullable()->after('operador');
            $table->string('gerente')->nullable()->after('supervisor');
            
            // Endereço Completo
            $table->string('numero')->nullable()->after('endereco');
            $table->string('bairro')->nullable()->after('numero');
            
            // Email Financeiro
            $table->string('email_financeiro')->nullable()->after('email');
            
            // Taxas e Condições
            $table->decimal('implantacao', 10, 2)->default(0)->after('desconto_maximo');
            $table->integer('quantidade_parcelas')->default(0)->after('implantacao');
            $table->decimal('desconto_a_vista', 5, 2)->nullable()->after('quantidade_parcelas');
            $table->decimal('desconto_a_prazo', 5, 2)->nullable()->after('desconto_a_vista');
            
            // Plano
            $table->string('plano')->nullable()->after('desconto_a_prazo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credores', function (Blueprint $table) {
            $table->dropColumn([
                'logo',
                'nome_contato',
                'cpf_contato',
                'banco',
                'agencia',
                'conta',
                'chave_pix',
                'nome_favorecido_pix',
                'tipo_chave_pix',
                'inscricao_estadual',
                'celular',
                'whatsapp_financeiro',
                'operador',
                'supervisor',
                'gerente',
                'numero',
                'bairro',
                'email_financeiro',
                'implantacao',
                'quantidade_parcelas',
                'desconto_a_vista',
                'desconto_a_prazo',
                'plano',
            ]);
        });
    }
};
