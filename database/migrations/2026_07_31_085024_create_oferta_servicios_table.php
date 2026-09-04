<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oferta_servicios', function (Blueprint $table) {
            $table->id();

            // Llaves foráneas
            $table->foreignId('oferta_id')->constrained('ofertas')->cascadeOnDelete();
            $table->foreignId('catalogo_servicio_id')->constrained('catalogo_servicios')->cascadeOnDelete();

            // Opcional: Para saber exactamente a qué ítem de la resolución responde la oferta
            $table->foreignId('resolucion_servicio_id')
                ->nullable()
                ->constrained('resolucion_servicios')
                ->nullOnDelete();

            // Costos y métricas de la oferta por servicio
            $table->integer('cantidad')->default(1);
            $table->decimal('costo_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->text('observaciones')->nullable();

            $table->timestamps();

            // Evita que la misma oferta registre el mismo servicio dos veces
            $table->unique(['oferta_id', 'catalogo_servicio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oferta_servicios');
    }
};
