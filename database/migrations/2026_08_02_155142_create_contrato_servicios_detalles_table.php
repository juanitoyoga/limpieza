<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato_servicio_detalles', function (Blueprint $table) {
            $table->id();

            // Si se borra el contrato, sus líneas de detalle se borran con él
            // (cascade tiene sentido aquí: el detalle no existe sin su contrato).
            $table->foreignId('contrato_servicio_id')
                ->constrained('contratos_servicios')
                ->cascadeOnDelete();

            // El catálogo de servicios es un catálogo maestro: no debe poder
            // borrarse mientras haya contratos que lo referencien.
            $table->foreignId('catalogo_servicio_id')
                ->constrained('catalogo_servicios')
                ->restrictOnDelete();

            $table->integer('cantidad');
            $table->decimal('costo_unitario', 15, 2);
            $table->decimal('subtotal', 15, 2);

            $table->timestamps();

            $table->index('contrato_servicio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_servicio_detalles');
    }
};
