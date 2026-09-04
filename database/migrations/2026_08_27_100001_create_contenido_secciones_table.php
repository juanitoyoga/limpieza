<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contenido_secciones', function (Blueprint $table) {
            $table->id();

            // Identidad ESTABLE de la zona de la página. Ej.: 'banner'
            // (slides del carrousel), 'noticia', 'banner_publicitario'
            // (auspiciantes), 'auspiciador' (logos), 'mejor_barrio'.
            // No cambia aunque la especificación evolucione.
            $table->string('area');

            // Correlativo de especificación dentro de la misma área.
            // Empieza en 1. Solo sube cuando alguien cambia los campos
            // o dimensiones de esta zona (ver 'activo' más abajo).
            $table->unsignedInteger('version_spec')->default(1);

            // Solo una especificación por área puede estar activa a la
            // vez — es la que usan las nuevas propuestas. Las anteriores
            // quedan inactivas pero NO se borran: los items/versiones
            // históricos que las usaron siguen siendo consultables.
            $table->boolean('activo')->default(true);

            // Cómo se comporta esta zona en cuanto a cantidad de items:
            // - 'unico': un solo item activo a la vez (poco común aquí).
            // - 'coleccion_limitada': cantidad fija de items (carrousel = 5).
            // - 'coleccion_libre': cualquier cantidad (noticias, logos...).
            $table->enum('multiplicidad', ['unico', 'coleccion_limitada', 'coleccion_libre'])
                ->default('coleccion_libre');

            // Solo relevante si multiplicidad = 'coleccion_limitada'.
            $table->unsignedInteger('max_items')->nullable();

            $table->enum('plataforma', ['web', 'movil', 'ambas'])->default('web');
            $table->string('descripcion')->nullable(); // uso interno del panel admin

            $table->timestamps();

            $table->unique(['area', 'version_spec']);
            $table->index(['area', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contenido_secciones');
    }
};
