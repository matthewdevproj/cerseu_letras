<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@posgrado.unmsm.edu.pe',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create a regular user for testing
        User::create([
            'name' => 'Usuario Test',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
        ]);
    }
}
