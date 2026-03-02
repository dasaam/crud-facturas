<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarFacturaRequest;
use App\Http\Requests\GuardarFacturaRequest;
use App\Http\Resources\FacturaResource;
use App\Models\Factura;
use App\Services\FacturaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FacturaApiController extends Controller
{
    public function __construct(private readonly FacturaService $facturaService)
    {
    }

    public function index(Request $request)
    {
        $cantidadPorPagina = (int) $request->input('cantidadPorPagina', 10);
        $cantidadPorPagina = in_array($cantidadPorPagina, [10, 15, 25, 50], true) ? $cantidadPorPagina : 10;

        $facturasPaginadas = Factura::query()
            ->with(['cliente', 'usuario'])
            ->orderByDesc('id')
            ->paginate($cantidadPorPagina);

        return FacturaResource::collection($facturasPaginadas);
    }

    public function store(GuardarFacturaRequest $request): JsonResponse
    {
        $facturaCreada = $this->facturaService->crearFactura(
            $request->validated(),
            (int) $request->user()->id
        );

        return (new FacturaResource($facturaCreada))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Factura $factura): FacturaResource
    {
        $factura->load(['cliente', 'usuario', 'items.producto']);

        return new FacturaResource($factura);
    }

    public function update(ActualizarFacturaRequest $request, Factura $factura): JsonResponse
    {
        if ($factura->estado === 'cancelada') {
            return response()->json([
                'message' => 'No se puede editar una factura cancelada.',
            ], 422);
        }

        $facturaActualizada = $this->facturaService->actualizarFactura(
            $factura,
            $request->validated()
        );

        return (new FacturaResource($facturaActualizada))
            ->response()
            ->setStatusCode(200);
    }

    public function facturar(Factura $factura): JsonResponse
    {
        if ($factura->estado !== 'borrador') {
            return response()->json([
                'message' => 'Solo se pueden facturar documentos en estado borrador.',
            ], 422);
        }

        $facturaActualizada = $this->facturaService->facturarFactura($factura);

        return (new FacturaResource($facturaActualizada))
            ->response()
            ->setStatusCode(200);
    }

    public function cancelar(Factura $factura): JsonResponse
    {
        $facturaActualizada = $this->facturaService->cancelarFactura($factura);

        return (new FacturaResource($facturaActualizada))
            ->response()
            ->setStatusCode(200);
    }
}
