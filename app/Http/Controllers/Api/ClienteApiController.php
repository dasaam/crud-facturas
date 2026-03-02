<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteApiController extends Controller
{
    public function index(Request $request)
    {
        $cantidadPorPagina = (int) $request->input('cantidadPorPagina', 10);
        $cantidadPorPagina = in_array($cantidadPorPagina, [10, 15, 25, 50], true) ? $cantidadPorPagina : 10;

        $clientesPaginados = Cliente::query()
            ->orderBy('razonSocial')
            ->paginate($cantidadPorPagina);

        return ClienteResource::collection($clientesPaginados);
    }
}
