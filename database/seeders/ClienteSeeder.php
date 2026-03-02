<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('clientes')->insert([
            [
                'razonSocial' => 'Comercializadora del Centro SA de CV',
                'nombreComercial' => 'Comercial Centro',
                'rfc' => 'CCE120101AB1',
                'correoElectronico' => 'compras@comercialcentro.mx',
                'telefono' => '5550011001',
                'direccionFiscal' => 'Av. Reforma 120, Col. Centro, Ciudad de Mexico',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'razonSocial' => 'Servicios Industriales del Norte SA de CV',
                'nombreComercial' => 'SINorte',
                'rfc' => 'SIN150505CD2',
                'correoElectronico' => 'facturacion@sinorte.mx',
                'telefono' => '8180012002',
                'direccionFiscal' => 'Blvd. Industria 450, Parque Industrial, Monterrey',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
