<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacturaItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'productoId' => $this->productoId,
            'producto' => $this->whenLoaded('producto', function () {
                return [
                    'id' => $this->producto?->id,
                    'codigo' => $this->producto?->codigo,
                    'nombre' => $this->producto?->nombre,
                ];
            }),
            'orden' => $this->orden,
            'descripcion' => $this->descripcion,
            'cantidad' => (float) $this->cantidad,
            'precioUnitario' => (float) $this->precioUnitario,
            'porcentajeImpuesto' => (float) $this->porcentajeImpuesto,
            'porcentajeDescuento' => (float) $this->porcentajeDescuento,
            'totalLinea' => (float) $this->totalLinea,
        ];
    }
}
