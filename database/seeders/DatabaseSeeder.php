<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'System Admin',
                'password' => 'password123',
                'role' => 'admin',
            ]
        );

        $this->call([
            ProductCatalogSeeder::class,
            OrderSeeder::class,
            BuildTemplateSeeder::class,
        ]);
    }
}