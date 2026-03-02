<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacturaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'folio' => $this->folio,
            'clienteId' => $this->clienteId,
            'cliente' => $this->whenLoaded('cliente', function () {
                return new ClienteResource($this->cliente);
            }),
            'usuarioId' => $this->usuarioId,
            'usuario' => $this->whenLoaded('usuario', function () {
                return [
                    'id' => $this->usuario?->id,
                    'name' => $this->usuario?->name,
                    'email' => $this->usuario?->email,
                ];
            }),
            'fechaEmision' => optional($this->fechaEmision)->format('Y-m-d'),
            'fechaVencimiento' => optional($this->fechaVencimiento)->format('Y-m-d'),
            'moneda' => $this->moneda,
            'subtotal' => (float) $this->subtotal,
            'impuesto' => (float) $this->impuesto,
            'descuento' => (float) $this->descuento,
            'total' => (float) $this->total,
            'estado' => $this->estado,
            'notas' => $this->notas,
            'items' => FacturaItemResource::collection($this->whenLoaded('items')),
            'createdAt' => optional($this->created_at)->toISOString(),
            'updatedAt' => optional($this->updated_at)->toISOString(),
        ];
    }
}
