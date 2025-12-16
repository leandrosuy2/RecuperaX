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
        if (Schema::hasTable('devedores')) {
            return;
        }
        
        Schema::create('devedores', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('empresa_id')->nullable();
            $table->char('tipo_pessoa', 1)->comment('F = Física, J = Jurídica');
            $table->string('cpf', 14)->nullable();
            $table->string('cnpj', 18)->nullable();
            $table->string('nome')->nullable()->comment('Para pessoa física');
            $table->string('razao_social')->nullable()->comment('Para pessoa jurídica');
            $table->string('nome_fantasia')->nullable();
            $table->string('nome_mae')->nullable();
            $table->string('rg')->nullable();
            $table->string('nome_socio')->nullable();
            $table->string('cpf_socio', 14)->nullable();
            $table->string('rg_socio')->nullable();
            
            // Telefones (até 10)
            $table->string('telefone')->nullable();
            $table->string('telefone_valido')->nullable()->default('NAO VERIFICADO')->comment('SIM, NAO, NAO VERIFICADO');
            $table->string('telefone2')->nullable();
            $table->string('telefone2_valido')->nullable()->default('NAO VERIFICADO');
            $table->string('telefone3')->nullable();
            $table->string('telefone3_valido')->nullable()->default('NAO VERIFICADO');
            $table->string('telefone4')->nullable();
            $table->string('telefone4_valido')->nullable()->default('NAO VERIFICADO');
            $table->string('telefone5')->nullable();
            $table->string('telefone5_valido')->nullable()->default('NAO VERIFICADO');
            $table->string('telefone6')->nullable();
            $table->string('telefone6_valido')->nullable()->default('NAO VERIFICADO');
            $table->string('telefone7')->nullable();
            $table->string('telefone7_valido')->nullable()->default('NAO VERIFICADO');
            $table->string('telefone8')->nullable();
            $table->string('telefone8_valido')->nullable()->default('NAO VERIFICADO');
            $table->string('telefone9')->nullable();
            $table->string('telefone9_valido')->nullable()->default('NAO VERIFICADO');
            $table->string('telefone10')->nullable();
            $table->string('telefone10_valido')->nullable()->default('NAO VERIFICADO');
            
            $table->string('email1')->nullable();
            $table->string('email2')->nullable();
            
            // Endereço
            $table->string('cep', 10)->nullable();
            $table->string('endereco')->nullable();
            $table->string('bairro')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cidade')->nullable();
            
            $table->text('observacao')->nullable();
            $table->string('operadora')->nullable();
            $table->string('module')->default('admin');
            $table->unsignedSmallInteger('status_code')->default(0);
            
            $table->timestamps();
            
            $table->index(['cpf', 'cnpj']);
            $table->index('empresa_id');
        });
        
        // Adiciona foreign key se a tabela core_empresa existir
        if (Schema::hasTable('core_empresa')) {
            Schema::table('devedores', function (Blueprint $table) {
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
        Schema::dropIfExists('devedores');
    }
};
