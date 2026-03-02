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
        Schema::create('factura_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facturaId')->constrained('facturas')->cascadeOnDelete();
            $table->unsignedBigInteger('productoId')->nullable();
            $table->unsignedInteger('orden')->default(1);
            $table->string('descripcion');
            $table->decimal('cantidad', 12, 3)->default(1);
            $table->decimal('precioUnitario', 12, 2);
            $table->decimal('porcentajeImpuesto', 5, 2)->default(0);
            $table->decimal('porcentajeDescuento', 5, 2)->default(0);
            $table->decimal('totalLinea', 12, 2);
            $table->timestamps();

            $table->index('orden');
            $table->index('productoId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_items');
    }
};
