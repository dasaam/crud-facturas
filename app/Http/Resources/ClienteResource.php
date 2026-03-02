<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'razonSocial' => $this->razonSocial,
            'nombreComercial' => $this->nombreComercial,
            'rfc' => $this->rfc,
            'correoElectronico' => $this->correoElectronico,
            'telefono' => $this->telefono,
            'direccionFiscal' => $this->direccionFiscal,
            'activo' => (bool) $this->activo,
        ];
    }
}
