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
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();

            $table->string('code', 10)
                ->unique()
                ->comment('Código corto del tipo de servicio');

            $table->string('name', 150)
                ->unique()
                ->comment('Nombre del tipo de servicio');

            $table->text('description')
                ->nullable()
                ->comment('Descripción del servicio');

            $table->boolean('active')
                ->default(true)
                ->index()
                ->comment('Indica si el tipo de servicio está disponible');

            $table->timestamps();
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
