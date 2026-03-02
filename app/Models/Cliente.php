<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'razonSocial',
        'nombreComercial',
        'rfc',
        'correoElectronico',
        'telefono',
        'direccionFiscal',
        'activo',
    ];

    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'clienteId');
    }
}
