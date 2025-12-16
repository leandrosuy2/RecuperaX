<?php

namespace Database\Seeders;

use App\Models\Credor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $credor1 = Credor::first();
        $credor2 = Credor::skip(1)->first();

        // Admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@recuperax.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Gestor
        User::create([
            'name' => 'Gestor de Cobrança',
            'email' => 'gestor@recuperax.com',
            'password' => Hash::make('password'),
            'role' => 'gestor',
        ]);

        // Consultores
        User::create([
            'name' => 'João Silva',
            'email' => 'joao@recuperax.com',
            'password' => Hash::make('password'),
            'role' => 'consultor',
        ]);

        User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@recuperax.com',
            'password' => Hash::make('password'),
            'role' => 'consultor',
        ]);

        User::create([
            'name' => 'Pedro Costa',
            'email' => 'pedro@recuperax.com',
            'password' => Hash::make('password'),
            'role' => 'consultor',
        ]);

        // Credores
        User::create([
            'name' => 'Banco Financeiro',
            'email' => 'credor1@bancofinanceiro.com',
            'password' => Hash::make('password'),
            'role' => 'credor',
            'credor_id' => $credor1->id,
        ]);

        User::create([
            'name' => 'Loja Virtual',
            'email' => 'credor2@lojavirtual.com',
            'password' => Hash::make('password'),
            'role' => 'credor',
            'credor_id' => $credor2->id,
        ]);
    }
}
