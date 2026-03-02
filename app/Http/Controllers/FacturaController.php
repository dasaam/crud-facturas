<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarFacturaRequest;
use App\Http\Requests\GuardarFacturaRequest;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FacturaController extends Controller
{
    public function index(Request $request): Response
    {
        $filtroBusqueda = trim((string) $request->string('filtroBusqueda', ''));
        $filtroEstado = trim((string) $request->string('filtroEstado', ''));
        $filtroClienteId = $request->integer('filtroClienteId');
        $cantidadPorPagina = (int) $request->input('cantidadPorPagina', 10);
        $cantidadPorPagina = in_array($cantidadPorPagina, [10, 15, 25, 50], true) ? $cantidadPorPagina : 10;

        $consultaFacturas = Factura::query()
            ->with('cliente:id,razonSocial')
            ->when($filtroBusqueda !== '', function ($consulta) use ($filtroBusqueda) {
                $consulta->where('folio', 'like', "%{$filtroBusqueda}%")
                    ->orWhereHas('cliente', function ($consultaCliente) use ($filtroBusqueda) {
                        $consultaCliente->where('razonSocial', 'like', "%{$filtroBusqueda}%");
                    });
            })
            ->when($filtroEstado !== '', function ($consulta) use ($filtroEstado) {
                $consulta->where('estado', $filtroEstado);
            })
            ->when($filtroClienteId > 0, function ($consulta) use ($filtroClienteId) {
                $consulta->where('clienteId', $filtroClienteId);
            })
            ->orderByDesc('id');

        $facturasPaginadas = $consultaFacturas
            ->paginate($cantidadPorPagina)
            ->through(function (Factura $factura) {
                return [
                    'id' => $factura->id,
                    'folio' => $factura->folio,
                    'fechaEmision' => optional($factura->fechaEmision)->format('Y-m-d'),
                    'total' => (float) $factura->total,
                    'estado' => $factura->estado,
                    'cliente' => [
                        'razonSocial' => $factura->cliente?->razonSocial,
                    ],
                ];
            })
            ->withQueryString();

        $clientes = Cliente::query()
            ->select(['id', 'razonSocial'])
            ->orderBy('razonSocial')
            ->get();

        return Inertia::render('Facturas/Index', [
            'facturasPaginadas' => $facturasPaginadas,
            'clientes' => $clientes,
            'filtros' => [
                'filtroBusqueda' => $filtroBusqueda,
                'filtroEstado' => $filtroEstado,
                'filtroClienteId' => $filtroClienteId > 0 ? (string) $filtroClienteId : '',
                'cantidadPorPagina' => (string) $cantidadPorPagina,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Facturas/Form', [
            'modoEdicion' => false,
            'factura' => null,
            'clientes' => $this->obtenerClientes(),
            'productos' => $this->obtenerProductos(),
        ]);
    }

    public function store(GuardarFacturaRequest $request): RedirectResponse
    {
        $datosFactura = $request->validated();

        DB::transaction(function () use ($datosFactura, $request) {
            $totalesCalculados = $this->calcularTotales($datosFactura['items']);

            $factura = Factura::create([
                'clienteId' => $datosFactura['clienteId'],
                'usuarioId' => (int) $request->user()->id,
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
        });

        return redirect()->route('facturas.index');
    }

    public function edit(Factura $factura): Response|RedirectResponse
    {
        if ($factura->estado === 'cancelada') {
            return redirect()->route('facturas.index');
        }

        $factura->load(['items', 'cliente']);

        return Inertia::render('Facturas/Form', [
            'modoEdicion' => true,
            'factura' => [
                'id' => $factura->id,
                'folio' => $factura->folio,
                'clienteId' => (string) $factura->clienteId,
                'fechaEmision' => optional($factura->fechaEmision)->format('Y-m-d'),
                'fechaVencimiento' => optional($factura->fechaVencimiento)->format('Y-m-d'),
                'moneda' => $factura->moneda,
                'estado' => $factura->estado,
                'notas' => $factura->notas,
                'items' => $factura->items->map(function ($item) {
                    return [
                        'productoId' => $item->productoId ? (string) $item->productoId : '',
                        'descripcion' => $item->descripcion,
                        'cantidad' => (string) $item->cantidad,
                        'precioUnitario' => (string) $item->precioUnitario,
                        'porcentajeImpuesto' => (string) $item->porcentajeImpuesto,
                        'porcentajeDescuento' => (string) $item->porcentajeDescuento,
                    ];
                }),
            ],
            'clientes' => $this->obtenerClientes(),
            'productos' => $this->obtenerProductos(),
        ]);
    }

    public function update(ActualizarFacturaRequest $request, Factura $factura): RedirectResponse
    {
        if ($factura->estado === 'cancelada') {
            return redirect()->route('facturas.index');
        }

        $datosFactura = $request->validated();

        DB::transaction(function () use ($datosFactura, $factura) {
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
        });

        return redirect()->route('facturas.index');
    }

    public function cancelar(Factura $factura): RedirectResponse
    {
        if ($factura->estado !== 'cancelada') {
            $factura->update(['estado' => 'cancelada']);
        }

        return redirect()->route('facturas.index');
    }

    public function facturar(Factura $factura): RedirectResponse
    {
        if ($factura->estado === 'borrador') {
            $factura->update(['estado' => 'emitida']);
        }

        return redirect()->route('facturas.index');
    }

    /**
     * @param array<int, array<string, mixed>> $itemsRecibidos
     * @return array<string, mixed>
     */
    private function calcularTotales(array $itemsRecibidos): array
    {
        $subtotalFactura = 0.0;
        $impuestoFactura = 0.0;
        $descuentoFactura = 0.0;
        $itemsNormalizados = [];

        foreach ($itemsRecibidos as $indice => $itemRecibido) {
            $cantidad = (float) $itemRecibido['cantidad'];
            $precioUnitario = (float) $itemRecibido['precioUnitario'];
            $porcentajeImpuesto = (float) ($itemRecibido['porcentajeImpuesto'] ?? 0);
            $porcentajeDescuento = (float) ($itemRecibido['porcentajeDescuento'] ?? 0);

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

    private function obtenerClientes()
    {
        return Cliente::query()
            ->select(['id', 'razonSocial'])
            ->where('activo', true)
            ->orderBy('razonSocial')
            ->get()
            ->map(function (Cliente $cliente) {
                return [
                    'id' => (string) $cliente->id,
                    'razonSocial' => $cliente->razonSocial,
                ];
            });
    }

    private function obtenerProductos()
    {
        return Producto::query()
            ->select(['id', 'codigo', 'nombre', 'descripcion', 'precioBase', 'porcentajeImpuesto'])
            ->where('activo', true)
            ->orderBy('nombre')
            ->get()
            ->map(function (Producto $producto) {
                return [
                    'id' => (string) $producto->id,
                    'codigo' => $producto->codigo,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precioBase' => (string) $producto->precioBase,
                    'porcentajeImpuesto' => (string) $producto->porcentajeImpuesto,
                ];
            });
    }
}
