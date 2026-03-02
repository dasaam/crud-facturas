<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\FacturaApiController;
use App\Http\Controllers\Api\ProductoApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthTokenController::class, 'login'])->name('api.v1.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthTokenController::class, 'logout'])->name('api.v1.logout');

        Route::get('/clientes', [ClienteApiController::class, 'index'])->name('api.v1.clientes.index');
        Route::get('/productos', [ProductoApiController::class, 'index'])->name('api.v1.productos.index');

        Route::get('/facturas', [FacturaApiController::class, 'index'])->name('api.v1.facturas.index');
        Route::post('/facturas', [FacturaApiController::class, 'store'])->name('api.v1.facturas.store');
        Route::get('/facturas/{factura}', [FacturaApiController::class, 'show'])->name('api.v1.facturas.show');
        Route::put('/facturas/{factura}', [FacturaApiController::class, 'update'])->name('api.v1.facturas.update');
        Route::patch('/facturas/{factura}/facturar', [FacturaApiController::class, 'facturar'])->name('api.v1.facturas.facturar');
        Route::patch('/facturas/{factura}/cancelar', [FacturaApiController::class, 'cancelar'])->name('api.v1.facturas.cancelar');
    });
});
