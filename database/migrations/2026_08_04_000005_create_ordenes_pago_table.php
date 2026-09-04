<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_servicio_detalle_id')->constrained('contrato_servicio_detalles');
            $table->foreignId('movimiento_servicio_id')->constrained('movimientos_servicio');
            $table->decimal('monto', 10, 2);

            $table->enum('estado', ['GENERADA', 'VERIFICADA', 'APROBADA', 'RECHAZADA', 'PAGADA'])
                ->default('GENERADA');

            $table->foreignId('generado_por')->constrained('users');
            $table->text('observacion')->nullable();

            $table->foreignId('verificado_por')->nullable()->constrained('users'); // Funcionario
            $table->timestamp('verificado_at')->nullable();

            $table->foreignId('aprobado_por')->nullable()->constrained('users'); // Supervisor/Admin
            $table->timestamp('aprobado_at')->nullable();

            $table->text('motivo_rechazo')->nullable();

            // trazabilidad al regenerar tras rechazo (queda cerrada, no se reabre)
            $table->foreignId('orden_pago_anterior_id')->nullable()
                ->constrained('ordenes_pago')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_pago');
    }
};
