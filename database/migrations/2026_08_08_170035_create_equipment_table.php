<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();

            $table->string('code', 40)
                ->unique()
                ->comment('Código único del equipo o herramienta');

            $table->string('name', 150)
                ->unique()
                ->comment('Nombre del equipo o herramienta');

            $table->string('equipment_type', 40)
                ->index()
                ->comment('Categoría del equipo');

            $table->text('description')
                ->nullable()
                ->comment('Descripción y uso principal');

            $table->boolean('is_consumable')
                ->default(false)
                ->comment('Indica si se consume durante el servicio');

            $table->boolean('requires_training')
                ->default(false)
                ->comment('Indica si requiere capacitación para utilizarlo');

            $table->boolean('requires_safety_equipment')
                ->default(false)
                ->comment('Indica si exige elementos de protección adicionales');

            $table->unsignedInteger('sort_order')
                ->default(0)
                ->comment('Orden de presentación');

            $table->boolean('active')
                ->default(true)
                ->index()
                ->comment('Indica si el equipo está disponible');

            $table->timestamps();
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
