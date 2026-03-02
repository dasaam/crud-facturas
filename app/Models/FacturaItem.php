<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaItem extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'facturaId',
        'productoId',
        'orden',
        'descripcion',
        'cantidad',
        'precioUnitario',
        'porcentajeImpuesto',
        'porcentajeDescuento',
        'totalLinea',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:3',
            'precioUnitario' => 'decimal:2',
            'porcentajeImpuesto' => 'decimal:2',
            'porcentajeDescuento' => 'decimal:2',
            'totalLinea' => 'decimal:2',
        ];
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class, 'facturaId');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'productoId');
    }
}
