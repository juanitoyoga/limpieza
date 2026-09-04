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
        Schema::create('frequencies', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)
                ->unique()
                ->comment('Código único de la frecuencia');

            $table->string('name', 150)
                ->unique()
                ->comment('Nombre de la frecuencia');

            $table->string('frequency_type', 30)
                ->index()
                ->comment('Tipo de frecuencia');

            $table->unsignedInteger('interval_value')
                ->nullable()
                ->comment('Cantidad del intervalo');

            $table->string('interval_unit', 20)
                ->nullable()
                ->comment('Unidad del intervalo');

            $table->unsignedInteger('times_per_period')
                ->default(1)
                ->comment('Número de veces dentro del período');

            $table->text('description')
                ->nullable()
                ->comment('Descripción de la frecuencia');

            $table->unsignedInteger('sort_order')
                ->default(0)
                ->comment('Orden de presentación');

            $table->boolean('active')
                ->default(true)
                ->index()
                ->comment('Indica si la frecuencia está disponible');

            $table->timestamps();
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequencies');
    }
};
