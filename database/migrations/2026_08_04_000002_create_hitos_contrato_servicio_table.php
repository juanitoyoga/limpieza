<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hitos_contrato_servicio', function (Blueprint $table) {
            $table->id();

            // uuid generado en el dispositivo Android al capturar el hito en campo.
            // Es la clave de idempotencia para el sync (updateOrCreate por uuid).
            $table->uuid('uuid')->unique();

            $table->foreignId('contrato_servicio_detalle_id')
                ->constrained('contrato_servicio_detalles')
                ->cascadeOnDelete();

            $table->string('nombre')->nullable();
            $table->unsignedInteger('orden')->default(1);

            // Captura en campo (contratista)
            $table->foreignId('creado_por')->constrained('users');
            $table->timestamp('capturado_en_campo_at'); // timestamp del dispositivo, no del servidor
            $table->timestamp('sincronizado_at')->nullable(); // cuándo llegó al servidor

            // Verificación (Dirigente del barrio)
            $table->foreignId('verificado_por')->nullable()->constrained('users');
            $table->timestamp('verificado_at')->nullable();

            // Aprobación (Presidente del barrio)
            $table->foreignId('aprobado_por')->nullable()->constrained('users');
            $table->timestamp('aprobado_at')->nullable();

            // Decisión manual del Presidente al aprobar el hito que cierra el servicio.
            // Solo tiene valor no-nulo en el hito que efectivamente disparó el cierre.
            $table->boolean('generar_orden_pago')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['contrato_servicio_detalle_id', 'orden'], 'hitos_detalle_orden_unique');
            $table->index('capturado_en_campo_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hitos_contrato_servicio');
    }
};
