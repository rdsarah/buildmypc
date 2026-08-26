<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        
        // Create Admin User
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $this->call([
            ProductCatalogSeeder::class,
            OrderSeeder::class,
            BuildTemplateSeeder::class,
        ]);
    }
}