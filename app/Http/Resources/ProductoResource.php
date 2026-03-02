<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'unidadMedida' => $this->unidadMedida,
            'precioBase' => (float) $this->precioBase,
            'porcentajeImpuesto' => (float) $this->porcentajeImpuesto,
            'activo' => (bool) $this->activo,
        ];
    }
}
