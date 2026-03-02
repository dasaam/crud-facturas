<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuardarFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'clienteId' => ['required', 'integer', 'exists:clientes,id'],
            'fechaEmision' => ['required', 'date'],
            'fechaVencimiento' => ['nullable', 'date', 'after_or_equal:fechaEmision'],
            'moneda' => ['required', 'string', 'size:3'],
            'notas' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.productoId' => ['nullable', 'integer', 'exists:productos,id'],
            'items.*.descripcion' => ['required', 'string', 'max:255'],
            'items.*.cantidad' => ['required', 'numeric', 'gt:0'],
            'items.*.precioUnitario' => ['required', 'numeric', 'gte:0'],
            'items.*.porcentajeImpuesto' => ['nullable', 'numeric', 'between:0,100'],
            'items.*.porcentajeDescuento' => ['nullable', 'numeric', 'between:0,100'],
            'estado' => ['nullable', Rule::in(['borrador', 'emitida'])],
        ];
    }
}
