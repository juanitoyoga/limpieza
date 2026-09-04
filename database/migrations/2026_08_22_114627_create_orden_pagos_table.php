<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ordenes_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_servicio_id')->constrained('contratos_servicios');
            $table->enum('tipo', ['anticipo', 'hito', 'saldo_final']);
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['Pendiente', 'Autorizada', 'Pagada', 'Anulada'])->default('Pendiente');

            // Registro (Dirigente)
            $table->foreignId('registrado_por')->constrained('users');
            $table->timestamp('fecha_registro')->useCurrent();

            // Autorización (Presidente)
            $table->foreignId('autorizado_por')->nullable()->constrained('users');
            $table->timestamp('fecha_autorizacion')->nullable();

            // Pago (Dirigente o Presidente)
            $table->foreignId('pagado_por')->nullable()->constrained('users');
            $table->timestamp('fecha_pago')->nullable();
            $table->string('referencia_pago')->nullable();

            // Anulación
            $table->foreignId('anulado_por')->nullable()->constrained('users');
            $table->timestamp('fecha_anulacion')->nullable();
            $table->text('motivo_anulacion')->nullable();

            $table->text('observaciones')->nullable();

            // Blockchain (mismo patrón que ContratoServicio/HitoContratoServicio)
            $table->string('hash_orden')->nullable();
            $table->timestamp('blockchain_registrado_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_pagos');
    }
};
