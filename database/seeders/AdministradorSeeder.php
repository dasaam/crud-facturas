<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministradorSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@facturas.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin12345'),
                'email_verified_at' => now(),
            ]
        );
    }
}
