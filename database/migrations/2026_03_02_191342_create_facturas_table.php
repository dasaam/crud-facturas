<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clienteId')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('usuarioId')->constrained('users')->restrictOnDelete();
            $table->string('folio')->unique();
            $table->date('fechaEmision');
            $table->date('fechaVencimiento')->nullable();
            $table->string('moneda', 3)->default('MXN');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('estado', ['borrador', 'emitida', 'pagada', 'cancelada'])->default('borrador');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('fechaEmision');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
