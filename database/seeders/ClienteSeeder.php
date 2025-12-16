<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::create([
            'nome' => 'Carlos Alberto da Silva',
            'cpf' => '123.456.789-00',
            'email' => 'carlos.silva@email.com',
            'telefone' => '(11) 9876-5432',
            'celular' => '(11) 98765-4321',
            'endereco' => 'Rua das Acácias',
            'numero' => '123',
            'complemento' => 'Apto 45',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01000-000',
            'ativo' => true,
        ]);

        Cliente::create([
            'nome' => 'Ana Paula Oliveira',
            'cpf' => '987.654.321-00',
            'email' => 'ana.oliveira@email.com',
            'telefone' => '(11) 8765-4321',
            'celular' => '(11) 98765-1234',
            'endereco' => 'Av. das Palmeiras',
            'numero' => '456',
            'bairro' => 'Jardim América',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01234-567',
            'ativo' => true,
        ]);

        Cliente::create([
            'nome' => 'Roberto Mendes',
            'cpf' => '456.789.123-00',
            'email' => 'roberto.mendes@email.com',
            'telefone' => '(11) 7654-3210',
            'celular' => '(11) 98765-5678',
            'endereco' => 'Rua dos Pinheiros',
            'numero' => '789',
            'bairro' => 'Pinheiros',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '05422-000',
            'ativo' => true,
        ]);

        Cliente::create([
            'nome' => 'Empresa XYZ Ltda',
            'cnpj' => '12.345.678/0001-99',
            'email' => 'contato@empresaxyz.com.br',
            'telefone' => '(11) 3456-7890',
            'endereco' => 'Av. Brigadeiro Faria Lima',
            'numero' => '1500',
            'complemento' => 'Sala 1001',
            'bairro' => 'Itaim Bibi',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01452-000',
            'ativo' => true,
        ]);

        Cliente::create([
            'nome' => 'Fernanda Lima',
            'cpf' => '321.654.987-00',
            'email' => 'fernanda.lima@email.com',
            'telefone' => '(11) 6543-2109',
            'celular' => '(11) 98765-9012',
            'endereco' => 'Rua Augusta',
            'numero' => '200',
            'bairro' => 'Consolação',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01305-100',
            'ativo' => true,
        ]);
    }
}
