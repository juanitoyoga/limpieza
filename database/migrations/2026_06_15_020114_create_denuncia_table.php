<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denuncias', function (Blueprint $table) {
            $table->id();

            // Relaciones core
            $table->foreignId('vecino_id')->constrained('vecinos');
            $table->foreignId('barrio_id')->constrained('barrios');
            $table->foreignId('ordenanza332_id')->nullable()->constrained('ordenanza332');

            // Datos de la denuncia
            $table->string('direccion')->nullable();
            $table->string('direccion_gps')->nullable();
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_denuncia');
            $table->string('estado')->default('pendiente');
            $table->decimal('multa_calculada', 10, 2)->nullable();

            // Evidencia
            $table->string('evidencia_path')->nullable();
            $table->string('evidencia_tipo')->nullable();

            // Geolocalización
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            // Verificación
            $table->unsignedBigInteger('verificado_por_id')->nullable();
            $table->string('verificado_por_rol')->nullable();
            $table->timestamp('verificado_at')->nullable();

            // Aprobación
            $table->unsignedBigInteger('aprobado_por_id')->nullable();
            $table->string('aprobado_por_rol')->nullable();
            $table->timestamp('aprobado_at')->nullable();

            // Rechazo
            $table->unsignedBigInteger('rechazado_por_id')->nullable();
            $table->string('rechazado_por_rol')->nullable();
            $table->timestamp('rechazado_at')->nullable();
            $table->text('motivo_rechazo')->nullable();

            // Metadatos del dispositivo
            $table->string('app_uuid')->nullable();
            $table->string('device_id')->nullable();
            $table->string('os_version')->nullable();
            $table->string('app_version')->nullable();

            // Sincronización
            $table->boolean('synced')->default(false);
            $table->timestamp('synced_at')->nullable();

            // Blockchain
            $table->string('file_hash')->nullable();
            $table->string('tx_hash')->nullable();
            $table->string('blockchain_status')->nullable();
            $table->boolean('verified_on_chain')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denuncias');
    }
};
