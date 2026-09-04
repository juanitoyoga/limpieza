<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contenido_versiones', function (Blueprint $table) {
            $table->id();

            // Antes: contenido_seccion_id. Ahora: cada versión es una
            // revisión de UN item concreto, no de la zona entera.
            $table->foreignId('contenido_item_id')->constrained('contenido_items');

            // Correlativo por item (1, 2, 3...) — ahora sí tiene sentido
            // real: es el historial de ESE slide/noticia/logo puntual.
            $table->unsignedInteger('numero_version');

            $table->json('valores');
            $table->json('archivos')->nullable();

            $table->timestamp('fecha_inicio_vigencia')->nullable();
            $table->timestamp('fecha_fin_vigencia')->nullable();

            $table->enum('auth_status', ['Pendiente', 'Aprobada', 'Rechazada', 'Publicada', 'Archivada'])
                ->default('Pendiente');

            $table->foreignId('propuesto_por')->constrained('users');
            $table->timestamp('fecha_propuesta');

            $table->foreignId('aprobado_por')->nullable()->constrained('users');
            $table->timestamp('fecha_aprobacion')->nullable();

            $table->foreignId('rechazado_por')->nullable()->constrained('users');
            $table->timestamp('fecha_rechazo')->nullable();
            $table->text('motivo_rechazo')->nullable();

            $table->text('observaciones')->nullable();

            $table->string('tx_hash')->nullable();
            $table->unsignedBigInteger('tx_block')->nullable();
            $table->timestamp('blockchain_timestamp')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['contenido_item_id', 'numero_version']);
            $table->index('auth_status');
        });

        // Ahora sí se cierra la FK circular de contenido_items.
        Schema::table('contenido_items', function (Blueprint $table) {
            $table->foreign('version_publicada_id')
                ->references('id')->on('contenido_versiones')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contenido_items', function (Blueprint $table) {
            $table->dropForeign(['version_publicada_id']);
        });

        Schema::dropIfExists('contenido_versiones');
    }
};
