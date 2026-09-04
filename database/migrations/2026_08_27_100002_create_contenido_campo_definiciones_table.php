<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contenido_campo_definiciones', function (Blueprint $table) {
            $table->id();

            // FK real — antes era un string 'tipo' suelto sin relación
            // declarada en BD, lo que permitía que se desincronizara.
            // Ahora cada definición pertenece a UNA especificación
            // concreta (una fila de contenido_secciones).
            $table->foreignId('contenido_seccion_id')->constrained('contenido_secciones')->cascadeOnDelete();

            // Nombre técnico del campo dentro del JSON de
            // contenido_versiones.valores / .archivos.
            $table->string('clave');

            $table->string('etiqueta');

            // 'texto' | 'texto_largo' | 'url' | 'imagen' | 'documento_pdf'
            $table->string('tipo_dato');

            $table->boolean('requerido')->default(false);

            // Solo aplica si tipo_dato = 'url'.
            $table->boolean('url_externa_obligatoria')->default(false);

            // Solo aplica si tipo_dato = 'imagen'.
            $table->unsignedInteger('imagen_ancho')->nullable();
            $table->unsignedInteger('imagen_alto')->nullable();

            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->unique(['contenido_seccion_id', 'clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contenido_campo_definiciones');
    }
};
