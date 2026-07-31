<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ofertas', function (Blueprint $table) {
            $table->id();

            // Relaciones principales
            $table->foreignId('resolucion_id')->constrained('resoluciones')->cascadeOnDelete();
            $table->foreignId('proveedor_id')->constrained('proveedores')->cascadeOnDelete();

            // Atributos de la oferta
            $table->string('codigo')->nullable()->unique();
            $table->string('estado')->default('Pendiente'); // Pendiente, Presentada, Aceptada, Rechazada
            $table->decimal('monto_total', 12, 2)->default(0.00);
            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índice para filtrar rápido en el panel de operaciones (listados por estado)
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ofertas');
    }
};
