<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CredorSeeder::class,
            UserSeeder::class,
            ClienteSeeder::class,
            DividaSeeder::class,
        ]);
    }
}
