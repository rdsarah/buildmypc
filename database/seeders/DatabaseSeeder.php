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
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('abcd1234'),
                'is_admin' => true, // Ensure your users table has an is_admin column or role field
                'role' => 'admin',   // Included if your app uses a role column instead
            ]
        );
        $this->call([
            ProductCatalogSeeder::class,
            OrderSeeder::class,
            BuildTemplateSeeder::class,
        ]);
    }
}