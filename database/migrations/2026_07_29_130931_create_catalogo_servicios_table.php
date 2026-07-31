<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_servicios', function (Blueprint $table) {
            $table->id();

            // Identificación del servicio (clave para referenciar en resoluciones y ofertas)
            $table->string('codigo')->unique();   // ej: LIMP-001, se autogenera si no se envía
            $table->string('nombre');             // nombre legible para mostrar en documentos/ofertas
            $table->text('descripcion')->nullable();

            // Clasificación
            // Nota: tipo/subtipo/ambito/nivel_intervencion se acotan a un largo menor
            // porque forman parte del índice único compuesto de más abajo. Con utf8mb4
            // (4 bytes/carácter) 4 columnas a VARCHAR(255) suman 4080 bytes, superando
            // el límite de 3072 bytes por índice de InnoDB. Con estos largos el índice
            // queda en 4 * (100+100+100+50) = 1400 bytes.
            $table->string('tipo', 100);                    // limpieza_viaria, parques, edificios, etc.
            $table->string('subtipo', 100)->nullable();     // barrido, baldeo, jardinería, etc.
            $table->string('ambito', 100)->nullable();      // calle, parque, iglesia, cancha, etc.
            $table->string('frecuencia')->nullable();  // diaria, semanal, mensual
            $table->string('nivel_intervencion', 50)->nullable(); // básico, medio, integral
            $table->string('equipamiento')->nullable(); // hidrolavadora, barredora, etc.

            // Costeo (pensado para las futuras ofertas de servicio)
            $table->string('unidad_medida')->nullable();          // m2, ml, hora, unidad
            $table->decimal('costo_referencial', 10, 2)->nullable();

            // Control de listado
            $table->unsignedInteger('orden')->default(0); // orden de despliegue en dropdowns/listados
            $table->boolean('estado')->default(true);     // activo / inactivo

            $table->timestamps();
            $table->softDeletes(); // evita romper resoluciones/ofertas que ya referencian el servicio

            // Evita duplicar la misma combinación de clasificación
            // Nota: MySQL trata NULL como valores distintos en índices únicos,
            // así que igual conviene mantener la validación de duplicados a nivel de app
            // (como ya hacen en BarrioAtributo).
            $table->unique(
                ['tipo', 'subtipo', 'ambito', 'nivel_intervencion'],
                'catalogo_servicios_combinacion_unique'
            );

            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_servicios');
    }
};
