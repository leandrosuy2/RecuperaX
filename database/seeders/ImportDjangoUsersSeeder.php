<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ImportDjangoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $djangoUsers = [
            [
                "id" => 8,
                "password" => 'pbkdf2_sha256$600000$Bgn7sM48hwqedTwM2P84Nf$CL7xzf4NtAJaGASftBErcVGAzn82a2JcHpQzPj0vcK0=',
                "last_login" => "2025-12-15 00:54:44.740508",
                "is_superuser" => 1,
                "username" => "admin",
                "first_name" => "Meu Nome",
                "last_name" => "",
                "email" => "admin@admin.com",
                "is_staff" => 1,
                "is_active" => 1,
                "date_joined" => "2025-01-30 18:40:08.789861"
            ],
            [
                "id" => 19,
                "password" => 'pbkdf2_sha256$600000$pNcOJQ7ibYo9eATMrCVlMd$5RIbySW0uGk5UYBQuO7y508ON2Ek+qH9Zg2jZue03tc=',
                "last_login" => "2025-12-15 11:23:43.228061",
                "is_superuser" => 0,
                "username" => "Bordin.operador",
                "first_name" => "Francisco Bordin",
                "last_name" => "",
                "email" => "fcobordin.stm@gmail.com",
                "is_staff" => 0,
                "is_active" => 1,
                "date_joined" => "2025-09-04 15:33:29.733121"
            ],
            [
                "id" => 20,
                "password" => 'pbkdf2_sha256$600000$Xw3bFR0UzqrkzDqpNOCnWM$uN1sq5pAcJvsKFlxQBItC4Q88XJ/2t8xOvYgp8DPUzE=',
                "last_login" => "2025-11-07 08:13:18.638767",
                "is_superuser" => 0,
                "username" => "ANTHONY",
                "first_name" => "Anthony",
                "last_name" => "",
                "email" => "ANTHONY@anthony.com",
                "is_staff" => 0,
                "is_active" => 1,
                "date_joined" => "2025-09-04 17:26:02.596340"
            ],
            [
                "id" => 24,
                "password" => 'pbkdf2_sha256$600000$4D3YAELTCuAQOTJ0LjF7tT$Wr4VrzZKBAVmhSQuCYuhDBOJ8rO0ekTZ3PxS4KpD+bs=',
                "last_login" => "2025-12-02 14:02:21.525483",
                "is_superuser" => 0,
                "username" => "ELYSA",
                "first_name" => "ELYSA BORDIN",
                "last_name" => "",
                "email" => "elysabordin@negociarcobrancas.com.br",
                "is_staff" => 0,
                "is_active" => 1,
                "date_joined" => "2025-10-03 18:12:42.667229"
            ],
            [
                "id" => 25,
                "password" => 'pbkdf2_sha256$600000$elrmkBmedQXA8qE98n8cbE$Ec0nhkJSlAy4aNP02X/bML0Q+3xq5YbDDffYNRm/4Sg=',
                "last_login" => "2025-12-09 13:44:59.831650",
                "is_superuser" => 0,
                "username" => "Tiago",
                "first_name" => "Tiago Santos",
                "last_name" => "",
                "email" => "Tiago@negociarcobrancas.com",
                "is_staff" => 0,
                "is_active" => 1,
                "date_joined" => "2025-11-01 12:34:32.993202"
            ],
            [
                "id" => 26,
                "password" => 'pbkdf2_sha256$600000$RQ1z6yhsk7uFBUeYlMRkbm$Hji7i0zmaf3wx4+vZjBnYXgEbEKTs2L4yV20sGatwrM=',
                "last_login" => "2025-12-13 11:27:38.441744",
                "is_superuser" => 0,
                "username" => "ELIENE 004",
                "first_name" => "Eliene Reis",
                "last_name" => "",
                "email" => "operarador004@negociarcobrancas.com",
                "is_staff" => 0,
                "is_active" => 1,
                "date_joined" => "2025-11-07 08:18:38.185841"
            ],
            [
                "id" => 27,
                "password" => 'pbkdf2_sha256$600000$FeTSx62BovhVHvwDaFYlu4$ZMzdEIbnhZWKm1Cnesv5APnb/0tyFw6WVROufY1PVH0=',
                "last_login" => "2025-12-08 18:34:03.382068",
                "is_superuser" => 0,
                "username" => "operado005",
                "first_name" => "Mara Reis",
                "last_name" => "",
                "email" => "operado005@negociarcobrancas.com.br",
                "is_staff" => 0,
                "is_active" => 1,
                "date_joined" => "2025-11-07 08:21:08.299710"
            ],
            [
                "id" => 28,
                "password" => 'pbkdf2_sha256$600000$h7zVNN9pqVE6fLnq2mEJDo$3tO6YFbQCP3qkZmbmTx9GO6lb0EI1e+B6UBT6ljMoE=',
                "last_login" => "2025-11-10 18:44:53.158898",
                "is_superuser" => 0,
                "username" => "Operador006",
                "first_name" => "Bianca Oliveira",
                "last_name" => "",
                "email" => "006@gmail.com.br",
                "is_staff" => 0,
                "is_active" => 1,
                "date_joined" => "2025-11-10 14:48:40.199695"
            ],
        ];

        foreach ($djangoUsers as $djangoUser) {
            // Verifica se o usuário já existe
            $user = User::where('email', $djangoUser['email'])
                ->orWhere('username', $djangoUser['username'])
                ->first();

            if (!$user) {
                // Determina o role baseado em is_superuser e is_staff
                $role = 'consultor';
                if ($djangoUser['is_superuser']) {
                    $role = 'admin';
                } elseif ($djangoUser['is_staff']) {
                    $role = 'gestor';
                }

                // Cria o nome completo
                $name = trim(($djangoUser['first_name'] ?? '') . ' ' . ($djangoUser['last_name'] ?? ''));

                User::create([
                    'id' => $djangoUser['id'],
                    'username' => $djangoUser['username'],
                    'name' => $name ?: $djangoUser['username'],
                    'email' => $djangoUser['email'],
                    'password' => Hash::make('senha123'), // Senha temporária - usuários precisarão redefinir
                    'django_password' => $djangoUser['password'],
                    'role' => $role,
                    'is_superuser' => (bool) $djangoUser['is_superuser'],
                    'is_staff' => (bool) $djangoUser['is_staff'],
                    'is_active' => (bool) $djangoUser['is_active'],
                    'last_login' => $djangoUser['last_login'] ? date('Y-m-d H:i:s', strtotime($djangoUser['last_login'])) : null,
                    'date_joined' => $djangoUser['date_joined'] ? date('Y-m-d H:i:s', strtotime($djangoUser['date_joined'])) : null,
                    'email_verified_at' => now(),
                ]);

                $this->command->info("Usuário {$djangoUser['username']} importado com sucesso!");
            } else {
                $this->command->warn("Usuário {$djangoUser['username']} já existe, pulando...");
            }
        }
    }
}
