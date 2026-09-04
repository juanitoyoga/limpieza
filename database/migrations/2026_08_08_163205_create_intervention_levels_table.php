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
        Schema::create('intervention_levels', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('level')
                ->unique()
                ->comment('Número del nivel de intervención');

            $table->string('code', 30)
                ->unique()
                ->comment('Código único del nivel');

            $table->string('name', 150)
                ->unique()
                ->comment('Nombre del nivel de intervención');

            $table->string('intervention_type', 40)
                ->index()
                ->comment('Tipo general de intervención');

            $table->text('description')
                ->nullable()
                ->comment('Descripción del nivel');

            $table->boolean('requires_specialist')
                ->default(false)
                ->comment('Indica si requiere personal especializado');

            $table->boolean('requires_equipment')
                ->default(false)
                ->comment('Indica si requiere equipos específicos');

            $table->boolean('requires_authorization')
                ->default(false)
                ->comment('Indica si requiere autorización previa');

            $table->unsignedInteger('sort_order')
                ->default(0)
                ->comment('Orden de presentación');

            $table->boolean('active')
                ->default(true)
                ->index()
                ->comment('Indica si el nivel está disponible');

            $table->timestamps();
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_levels');
    }
};
