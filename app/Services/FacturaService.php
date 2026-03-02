<?php

namespace App\Services;

use App\Models\Factura;
use Illuminate\Support\Facades\DB;

class FacturaService
{
    public function crearFactura(array $datosFactura, int $usuarioId): Factura
    {
        return DB::transaction(function () use ($datosFactura, $usuarioId) {
            $totalesCalculados = $this->calcularTotales($datosFactura['items']);

            $factura = Factura::create([
                'clienteId' => $datosFactura['clienteId'],
                'usuarioId' => $usuarioId,
                'folio' => $this->generarSiguienteFolio(),
                'fechaEmision' => $datosFactura['fechaEmision'],
                'fechaVencimiento' => $datosFactura['fechaVencimiento'] ?? null,
                'moneda' => strtoupper((string) $datosFactura['moneda']),
                'subtotal' => $totalesCalculados['subtotal'],
                'impuesto' => $totalesCalculados['impuesto'],
                'descuento' => $totalesCalculados['descuento'],
                'total' => $totalesCalculados['total'],
                'estado' => 'borrador',
                'notas' => $datosFactura['notas'] ?? null,
            ]);

            $factura->items()->createMany($totalesCalculados['items']);

            return $factura->fresh(['cliente', 'items.producto', 'usuario']);
        });
    }

    public function actualizarFactura(Factura $factura, array $datosFactura): Factura
    {
        return DB::transaction(function () use ($factura, $datosFactura) {
            $totalesCalculados = $this->calcularTotales($datosFactura['items']);

            $factura->update([
                'clienteId' => $datosFactura['clienteId'],
                'fechaEmision' => $datosFactura['fechaEmision'],
                'fechaVencimiento' => $datosFactura['fechaVencimiento'] ?? null,
                'moneda' => strtoupper((string) $datosFactura['moneda']),
                'subtotal' => $totalesCalculados['subtotal'],
                'impuesto' => $totalesCalculados['impuesto'],
                'descuento' => $totalesCalculados['descuento'],
                'total' => $totalesCalculados['total'],
                'notas' => $datosFactura['notas'] ?? null,
            ]);

            $factura->items()->delete();
            $factura->items()->createMany($totalesCalculados['items']);

            return $factura->fresh(['cliente', 'items.producto', 'usuario']);
        });
    }

    public function cancelarFactura(Factura $factura): Factura
    {
        if ($factura->estado !== 'cancelada') {
            $factura->update(['estado' => 'cancelada']);
        }

        return $factura->fresh(['cliente', 'items.producto', 'usuario']);
    }

    public function facturarFactura(Factura $factura): Factura
    {
        if ($factura->estado === 'borrador') {
            $factura->update(['estado' => 'emitida']);
        }

        return $factura->fresh(['cliente', 'items.producto', 'usuario']);
    }

    private function calcularTotales(array $itemsRecibidos): array
    {
        $subtotalFactura = 0.0;
        $impuestoFactura = 0.0;
        $descuentoFactura = 0.0;
        $itemsNormalizados = [];

        foreach ($itemsRecibidos as $indice => $itemRecibido) {
            $cantidad =  $itemRecibido['cantidad'];
            $precioUnitario =  $itemRecibido['precioUnitario'];
            $porcentajeImpuesto =  ($itemRecibido['porcentajeImpuesto'] ?? 0);
            $porcentajeDescuento =  ($itemRecibido['porcentajeDescuento'] ?? 0);

            $subtotalLinea = $cantidad * $precioUnitario;
            $descuentoLinea = $subtotalLinea * ($porcentajeDescuento / 100);
            $baseCalculada = $subtotalLinea - $descuentoLinea;
            $impuestoLinea = $baseCalculada * ($porcentajeImpuesto / 100);
            $totalLinea = $baseCalculada + $impuestoLinea;

            $subtotalFactura += $subtotalLinea;
            $impuestoFactura += $impuestoLinea;
            $descuentoFactura += $descuentoLinea;

            $itemsNormalizados[] = [
                'productoId' => !empty($itemRecibido['productoId']) ? (int) $itemRecibido['productoId'] : null,
                'orden' => $indice + 1,
                'descripcion' => (string) $itemRecibido['descripcion'],
                'cantidad' => round($cantidad, 3),
                'precioUnitario' => round($precioUnitario, 2),
                'porcentajeImpuesto' => round($porcentajeImpuesto, 2),
                'porcentajeDescuento' => round($porcentajeDescuento, 2),
                'totalLinea' => round($totalLinea, 2),
            ];
        }

        return [
            'subtotal' => round($subtotalFactura, 2),
            'impuesto' => round($impuestoFactura, 2),
            'descuento' => round($descuentoFactura, 2),
            'total' => round($subtotalFactura - $descuentoFactura + $impuestoFactura, 2),
            'items' => $itemsNormalizados,
        ];
    }

    private function generarSiguienteFolio(): string
    {
        $ultimoId = (int) Factura::query()->max('id');
        $consecutivo = $ultimoId + 1;

        return 'FAC-'.str_pad((string) $consecutivo, 6, '0', STR_PAD_LEFT);
    }
}
