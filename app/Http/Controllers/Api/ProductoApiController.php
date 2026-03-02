<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoApiController extends Controller
{
    public function index(Request $request)
    {
        $cantidadPorPagina = (int) $request->input('cantidadPorPagina', 10);
        $cantidadPorPagina = in_array($cantidadPorPagina, [10, 15, 25, 50], true) ? $cantidadPorPagina : 10;

        $productosPaginados = Producto::query()
            ->orderBy('nombre')
            ->paginate($cantidadPorPagina);

        return ProductoResource::collection($productosPaginados);
    }
}
