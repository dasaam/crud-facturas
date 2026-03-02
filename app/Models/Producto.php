<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'unidadMedida',
        'precioBase',
        'porcentajeImpuesto',
        'activo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'precioBase' => 'decimal:2',
            'porcentajeImpuesto' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    public function facturaItems(): HasMany
    {
        return $this->hasMany(FacturaItem::class, 'productoId');
    }
}
