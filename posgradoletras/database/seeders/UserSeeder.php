<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuario administrador inicial.
 *
 * Antes creaba también un `user@test.com` con la contraseña «password»: una
 * cuenta conocida y trivial de adivinar que habría acabado en producción.
 *
 * Se usa `firstOrCreate` porque `create` a secas rompía la cadena entera de
 * seeders al reejecutarla —violación de clave única en `users.email`— y los
 * seeders posteriores no llegaban a correr.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@posgrado.unmsm.edu.pe'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $this->command?->warn('Usuario admin creado con la contraseña por defecto «admin123».');
            $this->command?->line('Cámbiala antes de exponer el sitio.');
        }
    }
}
