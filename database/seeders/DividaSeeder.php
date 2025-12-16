<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Credor;
use App\Models\Divida;
use App\Models\User;
use Illuminate\Database\Seeder;

class DividaSeeder extends Seeder
{
    public function run(): void
    {
        $credor1 = Credor::first();
        $credor2 = Credor::skip(1)->first();
        $credor3 = Credor::skip(2)->first();

        $consultor1 = User::where('role', 'consultor')->first();
        $consultor2 = User::where('role', 'consultor')->skip(1)->first();

        $clientes = Cliente::all();

        // Dívidas do Credor 1
        Divida::create([
            'credor_id' => $credor1->id,
            'cliente_id' => $clientes[0]->id,
            'consultor_id' => $consultor1->id,
            'numero_documento' => 'DIV-' . str_pad(1, 6, '0', STR_PAD_LEFT),
            'descricao' => 'Empréstimo pessoal',
            'valor_original' => 5000.00,
            'valor_atual' => 5500.00,
            'data_vencimento' => now()->subDays(15),
            'data_emissao' => now()->subMonths(3),
            'status' => 'vencida',
            'juros_mensal' => 1.50,
            'multa' => 2.00,
        ]);

        Divida::create([
            'credor_id' => $credor1->id,
            'cliente_id' => $clientes[1]->id,
            'consultor_id' => $consultor1->id,
            'numero_documento' => 'DIV-' . str_pad(2, 6, '0', STR_PAD_LEFT),
            'descricao' => 'Cartão de crédito',
            'valor_original' => 3200.00,
            'valor_atual' => 3200.00,
            'data_vencimento' => now()->addDays(5),
            'data_emissao' => now()->subMonths(1),
            'status' => 'a_vencer',
            'juros_mensal' => 1.50,
            'multa' => 2.00,
        ]);

        // Dívidas do Credor 2
        Divida::create([
            'credor_id' => $credor2->id,
            'cliente_id' => $clientes[2]->id,
            'consultor_id' => $consultor2->id,
            'numero_documento' => 'DIV-' . str_pad(3, 6, '0', STR_PAD_LEFT),
            'descricao' => 'Compra parcelada',
            'valor_original' => 1500.00,
            'valor_atual' => 1650.00,
            'data_vencimento' => now()->subDays(30),
            'data_emissao' => now()->subMonths(4),
            'status' => 'vencida',
            'juros_mensal' => 1.00,
            'multa' => 2.00,
        ]);

        Divida::create([
            'credor_id' => $credor2->id,
            'cliente_id' => $clientes[3]->id,
            'consultor_id' => $consultor2->id,
            'numero_documento' => 'DIV-' . str_pad(4, 6, '0', STR_PAD_LEFT),
            'descricao' => 'Compra à vista',
            'valor_original' => 800.00,
            'valor_atual' => 880.00,
            'data_vencimento' => now()->subDays(10),
            'data_emissao' => now()->subMonths(2),
            'status' => 'em_negociacao',
            'juros_mensal' => 1.00,
            'multa' => 2.00,
        ]);

        // Dívidas do Credor 3
        Divida::create([
            'credor_id' => $credor3->id,
            'cliente_id' => $clientes[4]->id,
            'consultor_id' => $consultor1->id,
            'numero_documento' => 'DIV-' . str_pad(5, 6, '0', STR_PAD_LEFT),
            'descricao' => 'Fatura telefone',
            'valor_original' => 250.00,
            'valor_atual' => 275.00,
            'data_vencimento' => now()->subDays(20),
            'data_emissao' => now()->subMonths(2),
            'status' => 'vencida',
            'juros_mensal' => 1.20,
            'multa' => 2.50,
        ]);

        Divida::create([
            'credor_id' => $credor3->id,
            'cliente_id' => $clientes[0]->id,
            'consultor_id' => $consultor2->id,
            'numero_documento' => 'DIV-' . str_pad(6, 6, '0', STR_PAD_LEFT),
            'descricao' => 'Fatura internet',
            'valor_original' => 150.00,
            'valor_atual' => 150.00,
            'data_vencimento' => now()->addDays(10),
            'data_emissao' => now()->subMonths(1),
            'status' => 'a_vencer',
            'juros_mensal' => 1.20,
            'multa' => 2.50,
        ]);
    }
}
