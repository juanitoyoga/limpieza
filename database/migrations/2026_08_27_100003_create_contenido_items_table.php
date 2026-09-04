<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contenido_items', function (Blueprint $table) {
            $table->id();

            // A qué especificación pertenece este item actualmente.
            // Si la especificación de su área cambia de versión, este
            // item puede migrarse a la nueva fila de contenido_secciones
            // (decisión del admin, no automática).
            $table->foreignId('contenido_seccion_id')->constrained('contenido_secciones');

            // Identificador legible SOLO para items de slot fijo
            // (ej. 'slide_1'..'slide_5' del carrousel). Null para items
            // de colección libre (noticias, logos, etc.).
            $table->string('identificador')->nullable();

            // Posición/orden: posición fija en el carrousel, o ranking
            // curado manualmente (ej. "mejor barrio"). Null = sin orden
            // explícito (colecciones cronológicas como noticias).
            $table->unsignedInteger('orden')->nullable();

            // FK a la versión actualmente publicada de ESTE item
            // específico (antes vivía, incorrectamente, en
            // contenido_secciones).
            $table->foreignId('version_publicada_id')->nullable();

            // Permite ocultar un item sin borrar su historial.
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index(['contenido_seccion_id', 'activo']);
        });

        // FK circular con contenido_versiones — se agrega después de
        // crear ambas tablas.
    }

    public function down(): void
    {
        Schema::dropIfExists('contenido_items');
    }
};
