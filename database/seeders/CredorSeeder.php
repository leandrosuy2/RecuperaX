<?php

namespace Database\Seeders;

use App\Models\Credor;
use Illuminate\Database\Seeder;

class CredorSeeder extends Seeder
{
    public function run(): void
    {
        Credor::create([
            'razao_social' => 'Banco Financeiro S.A.',
            'nome_fantasia' => 'Banco Financeiro',
            'cnpj' => '12.345.678/0001-90',
            'email' => 'contato@bancofinanceiro.com.br',
            'telefone' => '(11) 3456-7890',
            'endereco' => 'Av. Paulista, 1000',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01310-100',
            'juros_padrao' => 1.50,
            'multa_padrao' => 2.00,
            'desconto_maximo' => 30.00,
            'ativo' => true,
        ]);

        Credor::create([
            'razao_social' => 'Loja Virtual E-commerce Ltda',
            'nome_fantasia' => 'Loja Virtual',
            'cnpj' => '98.765.432/0001-10',
            'email' => 'financeiro@lojavirtual.com.br',
            'telefone' => '(11) 2345-6789',
            'endereco' => 'Rua das Flores, 500',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01234-567',
            'juros_padrao' => 1.00,
            'multa_padrao' => 2.00,
            'desconto_maximo' => 25.00,
            'ativo' => true,
        ]);

        Credor::create([
            'razao_social' => 'Operadora de Telefonia S.A.',
            'nome_fantasia' => 'Telefonia Plus',
            'cnpj' => '11.222.333/0001-44',
            'email' => 'cobranca@telefoniaplus.com.br',
            'telefone' => '(11) 4000-1234',
            'endereco' => 'Av. Faria Lima, 2000',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01452-000',
            'juros_padrao' => 1.20,
            'multa_padrao' => 2.50,
            'desconto_maximo' => 20.00,
            'ativo' => true,
        ]);
    }
}
