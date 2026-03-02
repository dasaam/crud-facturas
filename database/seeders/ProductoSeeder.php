<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('productos')->insert([
            [
                'codigo' => 'PROD-001',
                'nombre' => 'Consultoria Tecnica',
                'descripcion' => 'Servicio de consultoria tecnica por hora',
                'unidadMedida' => 'hora',
                'precioBase' => 850.00,
                'porcentajeImpuesto' => 16.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'PROD-002',
                'nombre' => 'Licencia de Software',
                'descripcion' => 'Licencia anual de uso de software administrativo',
                'unidadMedida' => 'licencia',
                'precioBase' => 3200.00,
                'porcentajeImpuesto' => 16.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'PROD-003',
                'nombre' => 'Mantenimiento Preventivo',
                'descripcion' => 'Mantenimiento preventivo para equipo de computo',
                'unidadMedida' => 'servicio',
                'precioBase' => 1250.00,
                'porcentajeImpuesto' => 16.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'PROD-004',
                'nombre' => 'Capacitacion de Personal',
                'descripcion' => 'Curso de capacitacion para usuarios finales',
                'unidadMedida' => 'curso',
                'precioBase' => 4500.00,
                'porcentajeImpuesto' => 16.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'PROD-005',
                'nombre' => 'Soporte Remoto',
                'descripcion' => 'Soporte remoto para incidencias tecnicas',
                'unidadMedida' => 'evento',
                'precioBase' => 600.00,
                'porcentajeImpuesto' => 16.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
