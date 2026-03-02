<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'clienteId',
        'usuarioId',
        'folio',
        'fechaEmision',
        'fechaVencimiento',
        'moneda',
        'subtotal',
        'impuesto',
        'descuento',
        'total',
        'estado',
        'notas',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fechaEmision' => 'date',
            'fechaVencimiento' => 'date',
            'subtotal' => 'decimal:2',
            'impuesto' => 'decimal:2',
            'descuento' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'clienteId');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuarioId');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FacturaItem::class, 'facturaId')->orderBy('orden');
    }
}
