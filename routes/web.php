<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FacturaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('facturas', FacturaController::class)->except(['show', 'destroy']);
    Route::patch('/facturas/{factura}/cancelar', [FacturaController::class, 'cancelar'])->name('facturas.cancelar');
    Route::patch('/facturas/{factura}/facturar', [FacturaController::class, 'facturar'])->name('facturas.facturar');
});

require __DIR__.'/auth.php';
