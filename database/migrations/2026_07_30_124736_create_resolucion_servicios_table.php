<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resolucion_servicios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resolucion_id')
                ->constrained('resoluciones')
                ->cascadeOnDelete();

            // OJO: antes apuntaba a 'tipo_servicios', tabla que no existe.
            // El catálogo real es 'catalogo_servicios'.
            $table->foreignId('catalogo_servicio_id')
                ->constrained('catalogo_servicios')
                ->restrictOnDelete(); // no permite borrar un servicio del catálogo si ya está en una resolución

            $table->unsignedInteger('cantidad')->nullable();
            $table->string('prioridad')->nullable();  // baja, media, alta, urgente
            $table->text('observaciones')->nullable();
            $table->string('estado')->default('Pendiente');

            // Snapshot del costo al momento de crear la línea, para que la resolución
            // (y en su momento la oferta) no cambie si luego se actualiza costo_referencial en el catálogo
            $table->decimal('costo_unitario', 10, 2)->nullable();

            $table->timestamps();

            $table->unique(['resolucion_id', 'catalogo_servicio_id']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolucion_servicios');
    }
};
