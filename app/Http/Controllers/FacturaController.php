<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarFacturaRequest;
use App\Http\Requests\GuardarFacturaRequest;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use App\Services\FacturaService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FacturaController extends Controller
{
    public function __construct(private readonly FacturaService $facturaService)
    {
    }

    public function index(): Response
    {
        $facturasPaginadas = Factura::query()
            ->with('cliente:id,razonSocial')
            ->orderByDesc('id')
            ->paginate(10)
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
            });

        return Inertia::render('Facturas/Index', [
            'facturasPaginadas' => $facturasPaginadas,
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

        $this->facturaService->crearFactura($datosFactura, (int) $request->user()->id);

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

        $this->facturaService->actualizarFactura($factura, $datosFactura);

        return redirect()->route('facturas.index');
    }

    public function cancelar(Factura $factura): RedirectResponse
    {
        $this->facturaService->cancelarFactura($factura);

        return redirect()->route('facturas.index');
    }

    public function facturar(Factura $factura): RedirectResponse
    {
        $this->facturaService->facturarFactura($factura);

        return redirect()->route('facturas.index');
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
