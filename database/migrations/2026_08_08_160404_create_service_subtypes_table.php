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
        Schema::create('service_subtypes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_type_id')
                ->constrained('service_types')
                ->restrictOnDelete()
                ->comment('Tipo de servicio al que pertenece');

            $table->string('code', 20)
                ->unique()
                ->comment('Código único del subtipo');

            $table->string('name', 180)
                ->comment('Nombre del subtipo');

            $table->text('description')
                ->nullable()
                ->comment('Descripción del subtipo');

            $table->unsignedInteger('sort_order')
                ->default(0)
                ->comment('Orden de presentación');

            $table->boolean('active')
                ->default(true)
                ->index()
                ->comment('Indica si el subtipo está disponible');

            $table->timestamps();

            $table->index(['service_type_id', 'active']);
            $table->unique(['service_type_id', 'name']);
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_subtypes');
    }
};
