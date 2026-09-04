<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_servicio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_servicio_detalle_id')->constrained('contrato_servicio_detalles');
            $table->string('tipo'); // ej. 'SERVICIO_TERMINADO'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_servicio');
    }
};
